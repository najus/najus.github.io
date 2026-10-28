#!/usr/bin/env python3
"""Refresh the sujanthapa.com homepage photo grid with the latest 6 posts
from VSCO (vsco.co/najus).

VSCO has no official API/embed. The public gallery page embeds JSON with
permanent im.vsco.co CDN URLs and /najus/media/<hash> permalinks; this
script reads that JSON, compares the latest 6 hashes with the ones in
index.html (between the PHOTOS:START / PHOTOS:END markers), and when they
differ: downloads + optimizes local copies, rewrites the grid, commits
and pushes (GitHub Pages then redeploys).

Output contract (for the scheduled job):
  NOCHANGE            - grid already shows the latest 6; nothing touched
  CHANGED <n> posts   - grid updated, committed and pushed
  ERROR <message>     - something failed; repo left untouched

Flags:
  --dry-run   fetch + compare only, report NOCHANGE / WOULD-CHANGE
  --simulate  do everything except git commit/push (for testing on a copy)
"""
import colorsys
import re
import subprocess
import sys
import urllib.request
from collections import Counter
from pathlib import Path

REPO = Path(__file__).resolve().parent.parent
INDEX = REPO / "index.html"
PHOTOS_DIR = REPO / "images" / "photos"
PROFILE = "https://vsco.co/najus"
UA = {"User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36"}
COUNT = 6


BROWSER_HEADERS = [
    "-H", "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) "
          "AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36",
    "-H", "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,"
          "image/avif,image/webp,*/*;q=0.8",
    "-H", "Accept-Language: en-US,en;q=0.9",
    "-H", "Sec-Fetch-Dest: document",
    "-H", "Sec-Fetch-Mode: navigate",
    "-H", "Sec-Fetch-Site: none",
]


def fetch(url: str) -> bytes:
    """Fetch via curl (VSCO's CDN rejects plain urllib TLS fingerprints
    with 403, notably from datacenter IPs like GitHub Actions runners)."""
    try:
        r = subprocess.run(
            ["curl", "-sS", "-L", "--compressed", "--max-time", "40",
             *BROWSER_HEADERS, url],
            capture_output=True, check=True, timeout=60,
        )
        return r.stdout
    except FileNotFoundError:
        req = urllib.request.Request(url, headers=UA)
        with urllib.request.urlopen(req, timeout=40) as r:
            return r.read()


def latest_posts() -> list[dict]:
    html = fetch(PROFILE).decode("utf-8", "ignore")
    dec = html.replace("\\u002F", "/").replace("\\/", "/")
    seen: dict[str, dict] = {}
    for m in re.finditer(
        r"im\.vsco\.co/aws-us-west-2/([0-9a-f]+)/(\d+)/([0-9a-f]+)/([A-Za-z0-9_]+\.jpg)", dec
    ):
        region, site, h, fn = m.groups()
        if h not in seen:
            seen[h] = {
                "hash": h,
                "cdn": f"https://im.vsco.co/aws-us-west-2/{region}/{site}/{h}/{fn}",
                "fallback": f"https://img.vsco.co/{region}/{site}/{h}/{fn}",
                "post": f"https://vsco.co/najus/media/{h}",
            }
    posts = list(seen.values())
    if len(posts) < COUNT:
        raise RuntimeError(f"only {len(posts)} photos found on VSCO profile")
    return posts[:COUNT]


def current_grid(html: str) -> tuple[list[str], dict[str, str]]:
    m = re.search(r"<!-- PHOTOS:START.*?-->(.*?)<!-- PHOTOS:END -->", html, re.S)
    if not m:
        raise RuntimeError("PHOTOS markers not found in index.html")
    block = m.group(1)
    pairs = re.findall(
        r'vsco\.co/najus/media/([0-9a-f]+)"[^>]*><img[^>]*alt="([^"]*)"', block
    )
    return [h for h, _ in pairs], dict(pairs)


def download_optimized(post: dict, dest: Path) -> None:
    from PIL import Image
    import io

    data = None
    for url in (post["cdn"] + "?w=900", post["fallback"]):
        try:
            data = fetch(url)
            Image.open(io.BytesIO(data)).verify()
            break
        except Exception:
            data = None
    if data is None:
        raise RuntimeError(f"could not download photo {post['hash']}")
    im = Image.open(io.BytesIO(data)).convert("RGB")
    if im.width > 760:
        im = im.resize((760, round(im.height * 760 / im.width)), Image.LANCZOS)
    im.save(dest, quality=76, progressive=True, optimize=True)


def dominant_color(path: Path) -> tuple[str, float, float]:
    """Most-common colour of the photo (same idea as the photos site's
    Colour story). Returns (hex, hue, saturation)."""
    from PIL import Image

    im = Image.open(path).convert("RGB").resize((72, 72))
    q = im.quantize(colors=6)
    pal = q.getpalette()
    idx, _ = Counter(q.getdata()).most_common(1)[0]
    r, g, b = pal[idx * 3: idx * 3 + 3]
    h, s, _v = colorsys.rgb_to_hsv(r / 255, g / 255, b / 255)
    return f"#{r:02x}{g:02x}{b:02x}", h, s


def display_order(posts: list[dict]) -> list[dict]:
    """Colour-story order: hue flow (red wraps to the front), neutrals last."""
    def key(p: dict):
        _hex, h, s = p["color"]
        wrapped = h if h <= 0.95 else h - 1.0
        return (0, wrapped) if s >= 0.12 else (1, wrapped)

    return sorted(posts, key=key)


def build_block(posts: list[dict], old_alts: dict[str, str]) -> str:
    ordered = display_order(posts)
    lines = ['    <div class="photos">']
    for p in ordered:
        alt = old_alts.get(p["hash"], "Photo by Sujan Thapa on VSCO")
        lines.append(
            f'      <a href="{p["post"]}" target="_blank" rel="noopener" '
            f'style="--c:{p["color"][0]}">'
            f'<img src="/images/photos/vsco-{p["file"]}.jpg" alt="{alt}" loading="lazy"></a>'
        )
    lines.append("    </div>")
    lines.append('    <div class="colorbar" aria-hidden="true">')
    for p in ordered:
        lines.append(f'      <span style="background:{p["color"][0]}"></span>')
    lines.append("    </div>")
    return "\n".join(lines)


def git(*args: str) -> None:
    subprocess.run(["git", "-C", str(REPO), *args], check=True,
                   capture_output=True, text=True)


def main() -> int:
    dry = "--dry-run" in sys.argv
    simulate = "--simulate" in sys.argv
    try:
        posts = latest_posts()
        html = INDEX.read_text(encoding="utf-8")
        cur_hashes, old_alts = current_grid(html)
        new_hashes = [p["hash"] for p in posts]
        # The grid displays in colour order, so change detection is by set.
        if set(cur_hashes) == set(new_hashes):
            print("NOCHANGE")
            return 0
        if dry:
            print(f"WOULD-CHANGE current={cur_hashes} latest={new_hashes}")
            return 0
        PHOTOS_DIR.mkdir(parents=True, exist_ok=True)
        for i, p in enumerate(posts, 1):
            p["file"] = i
            dest = PHOTOS_DIR / f"vsco-{i}.jpg"
            download_optimized(p, dest)
            p["color"] = dominant_color(dest)
        block = build_block(posts, old_alts)
        new_html = re.sub(
            r"(<!-- PHOTOS:START.*?-->).*?(<!-- PHOTOS:END -->)",
            lambda m: f"{m.group(1)}\n{block}\n    {m.group(2)}",
            html, flags=re.S,
        )
        INDEX.write_text(new_html, encoding="utf-8")
        if simulate:
            print(f"SIMULATED-CHANGE latest={new_hashes}")
            return 0
        git("add", "index.html", "images/photos")
        git("commit", "-m", "Refresh homepage photos from VSCO (latest 6)")
        git("push", "origin", "master")
        print(f"CHANGED {len(posts)} posts: {', '.join(new_hashes)}")
        return 0
    except Exception as e:  # leave the repo untouched on any failure
        print(f"ERROR {e}")
        return 1


if __name__ == "__main__":
    sys.exit(main())
