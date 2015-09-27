<?php
require_once(__DIR__.'/library/MeinBlog.php');

// Start or resume one session 
session_start();

$file_id="";
$title="";
$content="";
$abstract='';
$category='new';

$userAgent=new MBUser();
$fileAgent=new MBFileHeader();
$categoryAgent=new MBCategory();
$contentAgent=new MBFileContent();

$message='';

if(empty($_SESSION['user_id'])){
	$user_id=null;
	$message="User Session Error";
}else{
	$user_id=$_SESSION['user_id'];
	$user_info=$userAgent->getUser($user_id);

	$file_id=MeinBlog::getRequest('file_id');	

	if('edit'==MeinBlog::getRequest('act')){
		$title=MeinBlog::getRequest('title');
		$content=MeinBlog::getRequest('content');
		$category=MeinBlog::getRequest('category');
		$abstract=MeinBlog::getRequest('abstract');

		if($category=='new'){
			$new_category=MeinBlog::getRequest('new_category');
			if(empty($new_category)){
				$message.="Please give new category name.";
			}
			$category=$categoryAgent->create($new_category,'OUTSIDER');
			if(empty($category)){
				$message.="Failed to create category.";
			}
		}

		if(empty($title) || empty($content)){
			// Cannot edit
			$message.="Please fill all required fields.";
		}
		if(empty($message)){
			// Write
			if(empty($file_id)){ //new
				$new_id=$fileAgent->create($title,$user_id,$content,$category,$abstract);
				if(!empty($new_id)){
					header("location: FileView.php?file_id=".$new_id);
				}else{
					//Failed
					$message="Failed to save new file.";
				}
			}else{ //update
				$done=$fileAgent->update($file_id,$user_id,$title,$abstract,$category,$content);
				if($done){
					header("location: FileView.php?file_id=".$file_id);
				}else{
					//Failed
					$message="Failed to update file.";
				}
			}
			
		}
	}elseif(!empty($file_id)){
		$header=$fileAgent->getFileHeader($file_id);
		$title=$header['title'];
		$abstract=$header['abstract'];
		$category=$header['category_id'];

		$content=$contentAgent->getFileContent($file_id);
	}
}

$category_list=$categoryAgent->getCategoryList();

?>
<!DOCTYPE html>
<html>
<head>
	<title>MeinBlog</title>
	<script src="js/jquery.min.js"></script>
	<script src="js/marked.js"></script>
	<script src="js/highlight.pack.js"></script>
	<script type="text/javascript">
		$(function() {
		    marked.setOptions({
		        langPrefix: ''
		    });
		    $('#editor').keyup(function() {
		        var src = $(this).val();
		        var html = marked(src);
		        $('#md_preview').html(html);
		        $('pre code').each(function(i, block) {
		            hljs.highlightBlock(block);
		        });
		    });
		});
		$('document').ready(function(){
			$('#editor').trigger('keyup');
			switch_md_preview();
			category_input_switch();
		});
	</script>
	<script type="text/javascript">
		function switch_md_preview(){
			if($("#preview_div").css('display')=='none'){
				//show it
				$("#preview_div").css('display','block');
				$("#editor").css('width','90%');
				$("#editor_div").css('width','50%');
			}else{
				//hide it
				$("#preview_div").css('display','none');
				$("#editor").css('width','100%');
				$("#editor_div").css('width','95%');
			}
		}

		function category_input_switch(){
			if($("#category").val()=='new'){
				$("#new_category").css('display','inline-block');
				console.log('category_input_switch -> on');
			}else{
				$("#new_category").css('display','none');
				console.log('category_input_switch -> off');
			}
		}
	</script>
	<link rel="stylesheet" type="text/css" href="css/github.css">
	<link rel="stylesheet" type="text/css" href="css/MeinBlogGeneral.css">
	<link rel="stylesheet" type="text/css" href="css/MeinBlogEditor.css">
</head>
<body>
	<div id="header">
		<h1>MeinBlog<br><small>A Simple Blog System in PHP</small></h1>
	</div>
	<div id="middle">
		<?php if(empty($user_id)){ ?>
		<div class="message_box">
			<h3>
				Message:
			</h3>
			<p>
				You have not logined into MeinBlog. Now directing to login...
			</p>
			<script type="text/javascript">
			function jump(){
				window.location.href = './index.php';
				console.log('Let it go');
			}
			setTimeout(jump,3000);
			</script>
		</div>
		<?php }else{ ?>
		<div id="edit_box">
			<?php
			if(!empty($message)){
			?>
			<div class="message_box">
				<h3>
					Message:
				</h3>
				<p>
					<?php echo $message; ?>
				</p>
			</div>
			<?php } ?>
			<form id="editor_form" method="POST">
				<?php if(empty($file_id)){ ?>
				<h2>
					Create new file in the name of <span style="font-style:italic;"><?php echo $user_info['name']; ?></span>.
				</h2>
				<?php }else{ ?>
				<h2>
					Update the file in the name of <span style="font-style:italic;"><?php echo $user_info['name']; ?></span>.
				</h2>
				<?php } ?>
				<div class="span9">
					<input type="text" class="form-control title_input" name="title" value="<?php echo $title; ?>" placeholder="Title">
					<select class="form-control category_select" id="category" name="category" onchange="category_input_switch()">
						<?php if(!empty($category_list)){
							echo '<optgroup label="Existed Category">';
							foreach ($category_list as $category_item) {
								echo '<option value="'.$category_item['category_id'].'" '.($category_item['category_id']==$category?'selected="selected"':'').'>'.$category_item['category_name'].'</option>';
							}
							echo '</optgroup>';
						}?>
						<optgroup label="New Category">
							<option value="new" <?php if($category=='new')echo "selected='selected'"; ?>>Create...</option>
						</optgroup>	
					</select>
					<input type="text" class="form-control new_category_input" id="new_category" name="new_category" placeholder="Category Name" value="">
					<input type="hidden" name="file_id" value="<?php echo $file_id; ?>">
					<input type="hidden" name="act" value="edit">
				</div>
				<div class="span3">
					<p style="padding: 0px 6px;height: 18px;line-height: 18px;font-size: 18px;text-align:right;color:#468847">
						Preview with Markdown 
						<span class="btn_span"><a href="javascript:void(0);" class="btn" style="padding: 0px 6px;" onclick="switch_md_preview()">◣</a></span>
					</p>
				</div>
				<div class="clear"></div>
				<div id="editor_div" class="span6">
					<textarea id="editor" class="form-control" name="content" placeholder="Content (Markdown Syntax Supported)"><?php echo $content; ?></textarea>
					<div class="clear"></div>
				</div>
				<div id="preview_div" class="span6">
					<div id="md_preview" class="md"></div>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
				<div class="span12" style="margin: 10px 5px 10px 0px; width:100%">
					<div class="span9">
						<textarea class="form-control" style="height:22px" name="abstract" placeholder="Abstract in plain text"><?php echo $abstract; ?></textarea>
					</div>
					<div class="span3" style="text-align: right;">
						<span class="btn_span">
							<a href="javascript:void(0)" class="btn" onclick="document.getElementById('editor_form').submit()">Save this edition</a>
						</span>
					</div>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</form>
		</div>
		<?php } ?>
	</div>
	<div id="footer">
		<p>
			Copyright 2015 Sinri Edogawa :: Powered by Project <a href="https://github.com/sinri/MeinBlog">MeinBlog</a>
		</p>
	</div>
</body>
</html>