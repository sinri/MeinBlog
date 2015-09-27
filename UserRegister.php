<?php
require_once(__DIR__.'/library/MeinBlog.php');

// Start or resume one session 
session_start();

$message="";
$user_id="";

$name="";
$email="";

if('user_register'==MeinBlog::getRequest('act')){
	$name=MeinBlog::getRequest('name');
	$password=MeinBlog::getRequest('password');
	$email=MeinBlog::getRequest('email');
	$code=MeinBlog::getRequest('code');

	$role="GUEST";

	if(empty($name) || empty($password) || empty($email)){
		$message="Please fulfill all required fields.";
	}else{
		$userAgent=new MBUser();
		if($userAgent->authEmailAndCode($email,$code)){
			$user_id=$userAgent->create($name,$email,$role,md5($password));
			if(empty($user_id)){
				$message="Failed to create user.";
			}else{
				$message="Successfully created user, now directing...";
				$_SESSION['user_id']=$user_id;
			}
		}else{
			$message="Code is not correct.";
		}
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>MeinBlog</title>
	<link rel="stylesheet" type="text/css" href="css/MeinBlogGeneral.css">
</head>
<body>
	<div id="header">
		<h1>MeinBlog<br><small>A Simple Blog System in PHP</small></h1>
	</div>
	<div id="middle">
		<h2>Register</h2>
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
		<?php
			if(!empty($user_id)){
		?>
			<script type="text/javascript">
			function jump(){
				window.location.href = './index.php';
				console.log('Let it go');
			}
			setTimeout(jump,3000);
			</script>
		<?php
			}else{
		?>
		<form method="POST" id="register_form">
			<dl class="dl-horizontal">
				<dt>
					Username:
				</dt>
				<dd>
					<input type="text" name="name" value="<?php echo $name; ?>">
				</dd>
				<dt>
					Password:
				</dt>
				<dd>
					<input type="password" name="password">
				</dd>
				<dt>
					Email:
				</dt>
				<dd>
					<input type="text" name="email" value="<?php echo $email; ?>">
				</dd>
				<dt>
					Code:
				</dt>
				<dd>
					<input type="text" name="code">
				</dd>
				<dt>
					<input type="hidden" name="act" value="user_register">
				</dt>
				<dd>
					<span class="btn_span"><a href="index.php" class="btn">Cancel</a></span>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<span class="btn_span"><a href="javascript:void(0)" class="btn" onclick="document.getElementById('register_form').submit()">Register</a></span>
				</dd>
			</dl>
		</form>
		<?php } ?>
	</div>
	<div id="footer">
		<p>
			Copyright 2015 Sinri Edogawa :: Powered by Project <a href="https://github.com/sinri/MeinBlog">MeinBlog</a>
		</p>
	</div>
</body>
</html>