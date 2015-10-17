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
<script src="<?php echo config_item_add('assets','uploadify/jquery.uploadify.min.js');?>" type="text/javascript"></script>
<link href="<?php echo config_item_add('assets','uploadify/uploadify.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript">
    $(function() {
		var i = 0;
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
			'uploadLimit'  : 3,
			'fileTypeExts' : '*.gif; *.jpg; *.jpeg;  *.png; *.GIF; *.JPG; *.JPEG; *.PNG;',
			 'buttonClass' : 'btn choose-select btn-orange',
			'onUploadSuccess' : function(file, data, response) {
				var filename =  data;
				$("#preview"+i+"").html('<img src="themes/modules/images/indicator.GIF" style="padding: 55px 55px 0px 60px;">').show();
				$.post('<?php echo base_url();?>profile/uplodify_image',{imagefile:filename},function(msg){
				var form = document.forms['ImageAddForm'];
				var el = document.createElement("input");
				el.type = "hidden";
				el.name = "data[Image][name]["+i+"]";
				el.id = "ImageFile["+i+"]";
				el.value = file.name;
				form.appendChild(el);
				$("#preview"+i+"").html(msg).show();
				i++;
				  
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
				  //alert('The file ' + file.name + ' was successfully uploaded');
				} 	
		  
				});
				
    });
	
	
	$(document).ready(function () {
    	$('#infoMessage').hide();
			var message="<?php if(isset($message))echo $message;?>";	
			if(message != "") {
				$('#infoMessage').show();
				setTimeout(function() { $("#infoMessage").fadeOut(); }, 2000);
			}
			$('#close_msg').click(function(){
				$('#infoMessage').fadeOut('slow');
			});
   });
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
      <li><a href="#">Jennifer Hawkins</a></li>
      <li><a href="#">Log Out</a></li>
    </ul>
    <div class="clear"></div>
  </div>
</div>
<div class="innerWrap">
  <div class="innerLeft">
    <div class="profileWrap">
      <div class="profileImg"><img src="<?php echo config_item_add('themes_modules_images','profile.jpg');?>" /></div>
      <div class="profileName">Jennifer Hawkins<br />
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
      <div class="profile">
        <div class="titleWrap">
          <div class="title">Edit Profile</div>
          <div class="clear"></div>
        </div>
        <?php $attributes = array('class' => 'addEditForm', 'id' => 'addEditForm');?>
        <?php echo validation_errors(); ?> <?php echo form_open(base_url().'profile/edit',$attributes); ?>
        <div class="blocks">
          <div class="block1 fl">
            <div class="machine">Firstname: </div>
            <div class="machineInput"> <?php echo form_input(array('name'=>'model_no', 'id'=>'model_no', 'class'=>'validate[required,custom[integer]]', 'maxlength'=>50,'value'=>@$machine->model_no));?> </div>
            <div class="clear"></div>
          </div>
          <div class="block1 fl">
            <div class="machine">LastName: </div>
            <div class="machineInput"> <?php echo form_input(array('name'=>'model_no', 'id'=>'model_no', 'class'=>'validate[required,custom[integer]]', 'maxlength'=>50,'value'=>@$machine->model_no));?> </div>
            <div class="clear"></div>
          </div>
          <div class="block1 fl">
            <div class="machine">Address: </div>
            <div class="machineInput"> <?php echo form_input(array('name'=>'date_checked', 'id'=>'date_checked', 'class'=>'validate[required] text-input datepicker', 'maxlength'=>50,'value'=>@$machine->date_checked));?> </div>
            <div class="clear"></div>
          </div>
          <div class="block1 fl">
            <div class="machine">DOB: </div>
            <div class="machineInput"> <?php echo form_input(array('name'=>'date_checked', 'id'=>'date_checked', 'class'=>'validate[required] text-input datepicker', 'maxlength'=>50,'value'=>@$machine->date_checked));?> </div>
            <div class="clear"></div>
          </div>
          <div class="block1 fl">
            <div class="machine">Gender: </div>
            <div class="machineInput"> <?php echo form_input(array('name'=>'date_checked', 'id'=>'date_checked', 'class'=>'validate[required] text-input datepicker', 'maxlength'=>50,'value'=>@$machine->date_checked));?> </div>
            <div class="clear"></div>
          </div>
          <div class="block1 fl">
            <div class="machine">Phone: </div>
            <div class="machineInput"> <?php echo form_input(array('name'=>'date_checked', 'id'=>'date_checked', 'class'=>'validate[required] text-input datepicker', 'maxlength'=>50,'value'=>@$machine->date_checked));?> </div>
            <div class="clear"></div>
          </div>
          <div class="block1 fl">
            <div class="machine">Profile Picture: </div>
            <div class="machineInput"> <?php echo form_input(array('name'=>'date_checked', 'id'=>'date_checked', 'class'=>'validate[required] text-input datepicker', 'maxlength'=>50,'value'=>@$machine->date_checked));?> </div>
            <div class="clear"></div>
          </div>
          
        </div>
      </div>
      <div class="clear"></div>
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
