<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo config_item('site_name');?></title>
<script type="text/javascript" src="<?php echo config_item_add('themes_modules_js','jquery-1.8.2.min.js');?>"></script>
<link href="<?php echo config_item_add('themes_modules_css','style.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo config_item_add('themes_modules_css','common.css');?>" rel="stylesheet" type="text/css" />
<!--Calendar-->
<link href="<?php echo config_item_add('themes_modules_css','jquery-ui-1.8.16.custom.css');?>" rel="stylesheet" type="text/css" />
<script src="<?php echo config_item_add('assets','uploadify/jquery.uploadify.js');?>" type="text/javascript"></script>
<link href="<?php echo config_item_add('assets','uploadify/uploadify.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript">
var i = 0;
    $(function() {
		$("#post_btn").css({display:"none"});
		$("#imgUploadDiv").css({display:"none"});
		
        $('#photo_upload').uploadify({
            
			'swf'         : '<?php echo config_item_add('assets','uploadify/uploadify.swf'); ?>',
			'uploader'    : '<?php echo config_item_add('assets','uploadify/uploadify.php'); ?>',
			'formData'    : {gallery_image : 'gallery_image',targetFolder:'uploads/images/'},
			'method'      : 'post',
			'cancelImg'   : '<?php echo config_item_add('assets','uploadify/uploadify-cancel.png');?>',
			'auto'        : true,
			'multi'       : true,	
			'hideButton'  : false,
			'buttonText'  : 'Click Here to Upload Photo',
			'width'       : 200,
			'height'	  : 25,
			'removeCompleted' : true,
			'progressData' : 'speed',
			'uploadLimit'  : 4,
			'fileTypeExts' : '*.gif; *.jpg; *.jpeg;  *.png; *.GIF; *.JPG; *.JPEG; *.PNG;',
			 'buttonClass' : 'btn choose-select btn-orange',
			'onUploadSuccess' : function(file, data, response) {
				var filename =  data;
				$("#imgUploadDiv").css({display:"block"});
				$("#preview"+i+"").html('<img src="themes/modules/images/indicator.GIF" style="padding: 55px 55px 0px 60px; width: 16px">').show();
				$.post('<?php echo base_url();?>profile/uplodify_image',{imagefile:filename},function(msg){
				var form = document.forms['ImageAddForm'];
				var el = document.createElement("input");
				el.type = "hidden";
				el.name = "data[Image][name]["+i+"]";
				el.id = "ImageFile"+i+"";
				el.className = "hidden_image"
				el.value = file.name;
				form.appendChild(el);
				$("#preview"+i+"").html(msg).show();
				i++;
				if(i!=4)
				$("#image_list").append(' <li id="preview'+i+'"><img src="<?php echo config_item_add('themes_modules_images','upload.png');?>" /></li>');

				  
				}); 
			},
			'onDialogOpen'      : function(event,ID,fileObj) {		
			},
			'onCancel'    		: function(event, ID, fileObj, data) {
    								console.log('canceled');
    	    },
			'onUploadError' 	: function(file, errorCode, errorMsg, errorString) {
				   					alert(errorMsg);
				},
			'onUploadComplete' 	: function(file) {
				  				$("#post_btn").css({display:"block"});
								
				} 	
	  
				});
	    });
	
	//Image Submit Ajax Function-----------------
	$(document).ready(function () {
    	$("#ImageAddForm").submit(function() {
		var url = "<?php echo  base_url();?>profile/upload_photo"; 
		var params= new Object();
		var err = 0;
		
		params['image1'] = $("#ImageFile0").val();
		params['image2'] = $("#ImageFile1").val();
		params['image3'] = $("#ImageFile2").val();
		params['image4'] = $("#ImageFile3").val();
		params['post_description'] = $("#post_description").val();
		params['album_id'] = $("#album_id").val();
		params['user_id'] = $("#user_id").val();

		//validation--	
		if(params['image1'] == "")
			err=1;
			
		if(err)	
		{
			return false; 
		}
		else
		{
			$.ajax({
				   type: "POST",
				   url: url,
				   datatype : "json",
				   data: params,
				   success: function(msg)
				   {
					 $('#post_photo').prepend(msg);
					 $("#imgUploadDiv").css({display:"none"});
					 $('#photo_upload').uploadify('settings','ResetUploadsSuccessful','0');
					 $( ".hidden_image" ).remove();
					 $("#preview0,#preview1,#preview2,#preview3").remove();
					 $("#image_list").append(' <li id="preview0"><img src="<?php echo config_item_add('themes_modules_images','upload.png');?>" /></li>'); i =0;

					}
				 });
				 return false; 
		 }
			
	});

});

		function likepics(id){
					
	
			}
</script>

<!--hover script-->
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery(".mainImg").mouseenter(function() {
            jQuery(".hovermainImg").fadeIn();
        });

        jQuery(".mainImg").mouseleave(function() {
            jQuery(".hovermainImg").fadeOut();
        });
    }); 
</script>
<!--hover script-->
</head>

<!--Info message-->
<div id="infoMessage">
  <p><img class="tick" src="<?php echo config_item_add('themes_modules_images','tick.png');?>" /><?php echo @$message; ?><a href="javascript::void(0)" id="close_msg"><img class="cross" src="<?php echo config_item_add('themes_modules_images','cross.png');?>" /></a></p>
</div>
<!--Info message-->
<body class="innerBody">
<div class="topbanner">
  <div class="topMenu">
    <div class="logo"><a href="<?php echo config_item('site_url');?>"><img src="<?php echo config_item_add('themes_modules_images','logo.png');?>" /></a></div>
    <div class="searchWrap">
      <input class="search" value="Search" holder="Search" />
      <input class="searchBtn" type="button" />
    </div>
    <ul>
      <li><a href="<?php echo config_item('site_url');?>">Home</a></li>
      <li><a href="#">Find Friends</a></li>
      <li><a href="<?php echo base_url();?>profile"><?php echo FIRSTNAME.' '.LASTNAME;?> </a></li>
      <li><a href="#">Log Out</a></li>
    </ul>
    <div class="clear"></div>
  </div>
</div>
<div class="innerWrap">
  <div class="innerLeft">
    <div class="profileWrap">
      <div class="profileImg"><img src="<?php echo config_item_add('themes_modules_images','noimg.png');?>" height="61px" width="77px" /></div>
      <div class="profileName"><?php echo FIRSTNAME.' '.LASTNAME;?><br />
        <a href="#">Edit Profile</a></div>
      <div class="clear"></div>
    </div>
    <div class="quoteWrap">
      <div class="title">Quotes About Fashion</div>
      <div class="quote1Wrap">
        <div class="quote1"> <img class="qImg" src="<?php echo config_item_add('themes_modules_images','qImg.jpg');?>" /> <img src="<?php echo config_item_add('themes_modules_images','coma.png');?>" /> A girl should be two things : classy and fabulous. </div>
        <div class="clear"></div>
        <div class="qName">Coco Chanel</div>
      </div>
      <div class="quote1Wrap">
        <div class="quote1"> <img class="qImg" src="<?php echo config_item_add('themes_modules_images','qImg.jpg');?>" /> <img src="<?php echo config_item_add('themes_modules_images','coma.png');?>" /> Every generation laughs at the old fashions, but follows religiously the new. </div>
        <div class="clear"></div>
        <div class="qName">Coco Chanel</div>
      </div>
      <div class="quote1Wrap">
        <div class="quote1"> <img class="qImg" src="<?php echo config_item_add('themes_modules_images','qImg.jpg');?>" /> <img src="<?php echo config_item_add('themes_modules_images','coma.png');?>" /> A girl should be two things : classy and fabulous. </div>
        <div class="clear"></div>
        <div class="qName">Coco Chanel</div>
      </div>
      <div class="qLink"><a href="#">Read More</a></div>
    </div>
    <div class="modelImg"><a href="#"><img src="<?php echo config_item_add('themes_modules_images','model.jpg');?>" /></a></div>
  </div>
  <div class="innerMid">
    <div class="uploadWrap">
      <div class="clickPhoto1"> <?php echo form_input(array('name'=>'photo_upload', 'id'=>'photo_upload', 'class'=>'','type'=>'file'));?> <span></span>
        <div class="clear"></div>
      </div>
      <!--      <div class="addPhoto">
        <button class="btn">Add More Photos</button>
      </div>
      <div class="clear"></div>
-->
      <?php $attributes = array('class' => 'ImageAddForm', 'id' => 'ImageAddForm');?>
      <?php echo validation_errors(); ?> <?php echo form_open(base_url().'profile/upload_photo',$attributes); ?>
      <div class="imgUpload" id="imgUploadDiv" >
        <textarea name="post_description" id="post_description" placeholder="Your view on these pictures........." style="outline:none"></textarea>
        <ul id="image_list">
          <li id="preview0"><img src="<?php echo config_item_add('themes_modules_images','upload.png');?>" /></li>
        </ul>
        <div class="clear"></div>
        <div class="postPhoto">
          <input type="hidden" name="user_id" id="user_id" value="<?php echo PROFILE;?>" />
          <input type="hidden" name="album_id" id="album_id" value="1" />
          <button class="btn post" name="submit" id="post_btn">Post Photos</button>
        </div>
        <div class="clear"></div>
      </div>
      <?php echo form_close();?>
      <div class="imgUploaded">
        <div class="title">Your Post</div>
       
        <?php foreach($post as $data):
		
		switch ($data['total']){
			 case 1:
			 		$style = 'style="width: 400px; height: 256px;"';
					break;
			 case 2:
			 		$style = 'style="width: 325px;height: 219px;"';
					break;
			 case 3:
			 		$style = 'style="width: 213px;height: 194px;"';
					break;
			 case 4:
			 		$style = 'style="width: 157px;height: 150px;"';
					break;
		 }	
		?>
         <div class="afterUpload" id="post_photo" rel="<?php echo $data['post_details_id'];?>">
          <ul>
            <?php foreach($image as $data_image):
			if($data['post_details_id'] == $data_image['post_details_id']){
			?>
            <li><a href="#"><img src="<?php echo config_item_add('uploads_images',$data_image['image']);?>" <?php echo $style;?> /></a>
              <div class="hoverComment">
                <div class="like"><a href="#"> Like</a></div>
                <div class="comment"><a href="#"> Comment</a></div>
              </div>
            </li>
            <?php } endforeach;?>
          </ul>
          <div class="clear"></div>
          <div class="commentBox">
            <div class="imgLike"><a href="javascript:likepics(<?php echo $data['post_details_id']?>)"><img src="<?php echo config_item_add('themes_modules_images','likeBlue.png');?>" /></a></div>
            <div class="nameLike"><a href="#">Jennifer Hawkins</a>, <a href="#">Coco Chanel</a></div>
            <div class="clear"></div>
            <div class="sttime">48 seconds ago</div>
            <div class="afterComment"> <a href="#">Coco Chanel</a><br />
              Every generation laughs at the old fashions, but follows religiously the new. </div>
            <div class="afterComment"> <a href="#">Coco Chanel</a><br />
              Every generation laughs at the old fashions, but follows religiously the new. </div>
          </div>
          <div class="commentBox">
            <div class="commentImg"><img src="<?php echo config_item_add('themes_modules_images','commentImg.jpg');?>" /></div>
            <div class="commentBoxText">
              <textarea holder="Write a comment...">Write a comment...</textarea>
            </div>
            <div class="clear"></div>
          </div></div>
          <?php endforeach;?>
        
      </div>
    </div>
  </div>
  <div class="innerRight">
    <div class="add"><img src="<?php echo config_item_add('themes_modules_images','add.jpg');?>">
      <div class="addText">A Platform for Brands to Release Monetizeable</div>
    </div>
    <div class="add"><img src="<?php echo config_item_add('themes_modules_images','add1.jpg');?>">
      <div class="addText">A Platform for Brands to Release Monetizeable</div>
    </div>
    <div class="add"><img src="<?php echo config_item_add('themes_modules_images','add2.jpg');?>">
      <div class="addText">A Platform for Brands to Release Monetizeable</div>
    </div>
  </div>
  <div class="clear"></div>
</div>
</body>
</html>
