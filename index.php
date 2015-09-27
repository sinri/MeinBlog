<?php
require_once(__DIR__.'/library/MeinBlog.php');

// Start or resume one session 
session_start();

$userAgent=new MBUser();
$fileListAgent=new MBFileList();

if('login'==MeinBlog::getRequest('act')){
	$name=MeinBlog::getRequest('name');
	$password=MeinBlog::getRequest('password');
	$user_id=$userAgent->authUser($name,md5($password));
	// MeinBlog::log("Event.Login({$name})->password({$password})={$user_id}");
	$_SESSION['user_id']=$user_id;
}elseif('logout'==MeinBlog::getRequest('act')){
	session_destroy();
	header("location: index.php");
}

if(empty($_SESSION['user_id'])){
	$user_id=null;
}else{
	$user_id=$_SESSION['user_id'];
	$user_info=$userAgent->getUser($user_id);
	// MeinBlog::log("Event.getUser({$user_id})=".json_encode($user_info));
}

// list top files
$conditions=array();
$role=MeinBlog::getRequest('file_list_role','OUTSIDER');
$page=MeinBlog::getRequest('file_list_page',1);
$page_size=MeinBlog::getRequest('file_list_page_size',10);

$pages=0;
$fileheader_list=$fileListAgent->getList($conditions,$role,$page,$page_size,$pages);

?>
<!DOCTYPE html>
<html>
<head>
	<title>MeinBlog</title>
	<link rel="stylesheet" type="text/css" href="css/github.css">
	<link rel="stylesheet" type="text/css" href="css/MeinBlogGeneral.css">
	<link rel="stylesheet" type="text/css" href="css/Pager.css">
	<style type="text/css">
	div.left_div {
		margin: 5px;
		width: 74%;
		height: auto;
		min-height: 500px;
		float: left;
		border-right: 1px solid gray;
	}
	div.right_div {
		margin: 0px;
		padding: 0px;
		width: 24%;
		height: auto;
		min-height: 500px;
		float: right;
	}
	div.widget_div {
		float: right;
		width: 90%;
		margin: 5px;
		padding: 5px;
		/*border:2px solid;*/
		/*border-radius:5px;*/
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
	div.paging_div{
		width: 90%;	
		border-top: 1px solid lightgray;
		padding: 10px;
		text-align: right;
	}
	div.paging_div ul {
		padding: 0;
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
				<h2>FILES</h2>
			</div>
			<!-- HERE FILES -->
			<div>
			<?php if(empty($fileheader_list)){
				echo "<p>No Files Exist.</p>";
			}else{
				foreach ($fileheader_list as $file_header) {
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
					</p>
					<blockquote class="file_header_abstract">
						<?php MeinBlog::safeEmptyEcho($file_header->property('abstract'),'No abstract found.'); ?>
					</blockquote>
			<?php if($user_info['role']=='ADMIN'){
			?>
					<p>
						<a href="FileEdit.php?file_id=<?php echo $file_header->property('file_id'); ?>">Edit</a>
					</p>
			<?php
			} ?>
				</div>
			<?php
				}
			} ?>
			</div>	
			<!-- PAGING -->
			<div class="paging_div pagination">
				<ul>
				<?php if($pages>1){
					for ($page_id=max(1,$pages-5); $page_id <= min($pages,$page+5); $page_id++) { 
						if($page_id==$page){
							//this page
							echo "<li><a href='javascript:void(0);' style='color:black'>{$page}</a></li>";
						}else{
				?>
				<li>
					<a href="index.php?file_list_page=<?php echo $page_id; ?>"><?php echo $page_id; ?></a>
				</li>
				<?php
						}
					}
				} ?>
				<li><a href="javascript:void(0);" style='color:#c7254e;'> <?php echo $pages; ?> Pages</a></li>
				</ul>
			</div>
			<div class="clear"></div>
		</div>
		<div class="right_div">
			<?php if(empty($user_id)){ ?>
			<div class="widget_div" style="text-align:center;">
				<h2>LOGIN</h2>
				<form method="POST" id="login_form">
					<dl class="dl-horizontal">
						<dt>
							Username:
						</dt>
						<dd>
							<input type="text" name="name" style="width: 100px;">
						</dd>
						<dt>
							Password:
						</dt>
						<dd>
							<input type="password" name="password"  style="width: 100px;">
						</dd>
					</dl>
					<p>
						<input type="hidden" name="act" value="login">
						<span class="btn_span"><a href="UserRegister.php" class="btn">Register</a></span>
						&nbsp;
						<span class="btn_span"><a href="javascript:void(0);" class="btn" onclick="document.getElementById('login_form').submit()">Login</a></span>
					</p>
				</form>
			</div>
			<?php }else{ ?>
			<div class="widget_div" style="text-align:center;">
				<h2>USER</h2>
				<p>Welcome, <?php echo $user_info['name']; ?>!</p>
				<p>User Group: <?php echo $user_info['role']; ?></p>
				<p>
					<span class="btn_span"><a href="FileEdit.php" class="btn btn-full-width">Write New Blog</a></span>
				</p>
				<p>
					<span class="btn_span"><a href="index.php?act=logout" class="btn btn-full-width">Logout</a></span>
				</p>
			</div>
			<?php } ?>
			<div class="clear"></div>
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