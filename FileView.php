<?php
require_once(__DIR__.'/library/MeinBlog.php');
require_once(__DIR__.'/library/Parsedown.php');

// Start or resume one session 
session_start();

$userAgent=new MBUser();
$fileAgent=new MBFileHeader();
$contentAgent=new MBFileContent();

$file_id=MeinBlog::getRequest('file_id');

if(empty($_SESSION['user_id'])){
	$user_id=null;
}else{
	$user_id=$_SESSION['user_id'];
	$user_info=$userAgent->getUser($user_id);
}

if(!empty($file_id)){
	$header=$fileAgent->getFileHeader($file_id);
	if($header){
		$title=$header['title'];
		$abstract=$header['abstract'];
		$category=$header['category_id'];
		$editor_id=$header['main_editor_id'];
		$content=$contentAgent->getFileContent($file_id);

		$first_edition_time=$header['create_time'];
		$last_edition_time=$header['update_time'];

		$writer_info=$userAgent->getUser($editor_id);
	}else{
		$file_id=null;
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>MeinBlog</title>
	<script src="js/jquery.min.js"></script>
	<script src="js/marked.js"></script>
	<script src="js/highlight.pack.js"></script>
	<script type="text/javascript">
		$('document').ready(function(){
			$('pre code').each(function(i, block) {
		        hljs.highlightBlock(block);
		    });
		});
	</script>
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
	#title_div {
		font-size: 36px;
		text-align: center;
		margin: 10px 0px;
	}
	#content_div {
		min-height: 400px;
	}
	</style>
</head>
<body>
	<div id="header">
		<h1>MeinBlog<br><small>A Simple Blog System in PHP</small></h1>
	</div>
	<div id="middle">
		<div class="left_div">
			<div id="title_div"><?php echo $title; ?></div>
			<div id="content_div">
				<?php 
				$ParsedownInstance=Parsedown::instance();
				echo $ParsedownInstance->text($content);
				?>
			</div>
		</div>
		<div class="right_div">
			<h2>File Info</h2>
			<p>Written by <?php echo $writer_info['name']; ?></p>
			<p>Email: <?php echo $writer_info['email']; ?></p>
			<p>Since: <?php echo $first_edition_time; ?></p>
			<p>Final: <?php echo $last_edition_time; ?></p>
			<h2>Links</h2>
			<a href="index.php">Home</a>
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