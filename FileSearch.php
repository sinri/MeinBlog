<?php
require_once(__DIR__.'/library/MeinBlog.php');
require_once(__DIR__.'/library/Parsedown.php');

// Start or resume one session 
$sessionAgent=MeinBlogSession::sharedInstance();
$user_id=$sessionAgent->getUserId();
$user_info=$sessionAgent->getUserInfo();

$fileListAgent=new MBFileList();

$keyword=MeinBlog::getRequest('keyword','');

$role='OUTSIDER';
if(isset($user_info['role'])){
	$role=$user_info['role'];
}

$limit=10;
$offset=0;
if(!empty($keyword)){
	$file_list=$fileListAgent->search($keyword,$role,$limit,$offset);
	//getList($conditions,$role,$page,$page_size,$page_count);
}else{
	$file_list=null;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>MeinBlog</title>
	<script src="js/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css/github.css">
	<link rel="stylesheet" type="text/css" href="css/MeinBlogGeneral.css">
	<style type="text/css">
	div.left_div {
		margin: 5px;
		width: 80%;
		height: auto;
		min-height: 500px;
		float: left;
		border-right: 1px solid gray;
	}
	div.right_div {
		margin: 0px;
		padding: 0px;
		width: 18%;
		height: auto;
		min-height: 500px;
		float: right;
	}
	input[type='text'].search {
		width: 300px;
	    margin: 5px;
	    font-family: Consolas, 'Courier New', Courier, Monaco, monospace;
	    padding: 6px 12px;
	    height: 20px;
	    line-height: 20px;
	    font-size: 16px;
	    display: inline-block;
	}
	#search_form {
		display: inline-block;
	}
	div.file_header_div{
		height: auto;
		width: 90%;	
		padding: 10px;
		border-top: 1px solid lightgray;
	}
	div.file_header_div:hover{
		background-color: #f8f8f8;
		height:auto;
		width: 90%;	
		padding: 10px;
		border-top: 1px solid lightgray;
	}
	.file_header_title {
		font-size: 20px;
		margin: 5px;
	}
	.file_header_info {
		font-size: 15px;
		font-style: italic;
		margin: 5px 20px;
	}
	.file_header_abstract {
		font-size: 15px;
		margin: 5px 20px;
		height: 30px;
	}
	</style>
</head>
<body>
	<div id="header">
		<h1>MeinBlog<br><small>A Simple Blog System in PHP</small></h1>
	</div>
	<div id="middle">
		<div class="left_div">
			<div>
				<h2 style="display:inline-block;">Search</h2>
				<form method="POST" id="search_form">
					<input type="text" name="keyword" value="<?php echo $keyword; ?>" class="form-control search">
					<span class="btn_span">
						<a class="btn" href="javascript:void(0)" onclick="$('#search_form').submit();">Search</a>
					</span>
				</form>
			</div>
			<div>
			<?php 
			if($file_list===null){
				//
			}
			elseif(empty($file_list)){
				echo "<p>No Files Exist.</p>";
			}else{
				foreach ($file_list as $file_header) {
			?>
				<div class="file_header_div">
					<p class="file_header_title">
						<code><?php MeinBlog::safeEmptyEcho($file_header->property('category_name'),'Default'); ?></code>
						<a href="FileView.php?file_id=<?php echo $file_header->property('file_id'); ?>"><?php echo $file_header->property('title'); ?></a> 
					</p>
					<p class="file_header_info">
						Author: <?php echo $file_header->property('user_name'); ?>
						&nbsp;&nbsp;
						Since: <?php echo $file_header->property('create_time'); ?>
						&nbsp;&nbsp;
						Final: <?php echo $file_header->property('update_time'); ?>
						&nbsp;&nbsp;
						<?php if($user_info['role']=='ADMIN' || $user_id==$file_header->property('main_editor_id')){ ?>
						<span class="btn_span" style="padding: 3px 12px;">
							<a class="btn" href="FileEdit.php?file_id=<?php echo $file_header->property('file_id'); ?>">Edit</a>
						</span>
						<?php } ?>
					</p>
					<blockquote class="file_header_abstract">
						<?php MeinBlog::safeEmptyEcho($file_header->property('abstract'),'No abstract found.'); ?>
					</blockquote>
				</div>
			<?php
				}
			} ?>
			</div>	
		</div>
		<div class="right_div">
			<h2>Links</h2>
			<p><span class="btn_span"><a href="index.php" class="btn btn-full-width">Home</a></span></p>
		</div>
		<div class="clear"></div>
	</div>
	<div id="footer">
		<p>
			Copyright 2015 Sinri Edogawa :: Powered by Project <a href="https://github.com/sinri/MeinBlog">MeinBlog</a>
		</p>
	</div>
</body>
</html>