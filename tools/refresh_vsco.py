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
import re
import subprocess
import sys
import urllib.request
from pathlib import Path

REPO = Path(__file__).resolve().parent.parent
INDEX = REPO / "index.html"
PHOTOS_DIR = REPO / "images" / "photos"
PROFILE = "https://vsco.co/najus"
UA = {"User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36"}
COUNT = 6


def fetch(url: str) -> bytes:
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


def build_block(posts: list[dict], old_alts: dict[str, str]) -> str:
    lines = ['    <div class="photos">']
    for i, p in enumerate(posts, 1):
        alt = old_alts.get(p["hash"], "Photo by Sujan Thapa on VSCO")
        lines.append(
            f'      <a href="{p["post"]}" target="_blank" rel="noopener">'
            f'<img src="/images/photos/vsco-{i}.jpg" alt="{alt}" loading="lazy"></a>'
        )
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
        if cur_hashes == new_hashes:
            print("NOCHANGE")
            return 0
        if dry:
            print(f"WOULD-CHANGE current={cur_hashes} latest={new_hashes}")
            return 0
        PHOTOS_DIR.mkdir(parents=True, exist_ok=True)
        for i, p in enumerate(posts, 1):
            download_optimized(p, PHOTOS_DIR / f"vsco-{i}.jpg")
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
