<?php
require_once(__DIR__.'/library/MeinBlog.php');

// Start or resume one session 
$sessionAgent=MeinBlogSession::sharedInstance();
$user_id=$sessionAgent->getUserId();
$user_info=$sessionAgent->getUserInfo();

$userAgent=new MBUser();
$codeAgent=new MBRegisterCode();

if(!empty($user_id) && $user_info['role']=='ADMIN'){
	if(MeinBlog::getRequest('act')=='new_code'){
		$code=MeinBlog::getRequest('code');
		$role=MeinBlog::getRequest('role');
		$object=MeinBlog::getRequest('object');
		$start_time=MeinBlog::getRequest('start_time');
		$end_time=MeinBlog::getRequest('end_time');

		if(!empty($code) && !empty($role)){
			if(empty($object)){
				$object='ANY';
			}

			$rc_id=$codeAgent->insert($object,$code,$role,$start_time,$end_time);
			if($rc_id){
				header("location: RegisterCodeAgency.php");
			}else{
				$new_code_message="Failed to create new register code.";
			}
		}else{
			$new_code_message="Conditions not correct.";	
		}
	}

	$code_list=$codeAgent->getRecordList();
}else{
	header("location: index.php");
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
	</style>
</head>
<body>
	<div id="header">
		<h1>
			<?php echo MeinBlog::getConfig()->property('MeinBlog_Title'); ?>
			<br>
			<small>
				<?php echo MeinBlog::getConfig()->property('MeinBlog_Subtitle'); ?>
			</small>
		</h1>
	</div>
	<div id="middle">
		<div class="left_div">
			<h2>Register Codes</h2>
			<?php if(!empty($code_list)){
				// print_r($code_list);
			?>
			<table>
				<thead>
					<tr>
						<th>rc_id</th>
						<th>status</th>
						<th>code</th>
						<th>object</th>
						<th>role</th>
						<th>start_time</th>
						<th>end_time</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					foreach ($code_list as $item) {
					?>
					<tr>
						<td><?php echo $item['rc_id']; ?></td>
						<td><?php echo $item['status']; ?></td>
						<td><?php echo $item['code']; ?></td>
						<td><?php echo $item['object']; ?></td>
						<td><?php echo $item['role']; ?></td>
						<td><?php echo $item['start_time']; ?></td>
						<td><?php echo $item['end_time']; ?></td>
					</tr>
					<?php
					}
					?>
				</tbody>
			</table>
			<?php
			}else{
				echo "<p>"."No code exists."."</p>";
			} ?>
			<h2>Create Code</h2>
			<?php if(!empty($new_code_message)){echo "<p>".$new_code_message."</p>";} ?>
			<form id="new_code_form">
				<p>
					Code: 
					&nbsp;&nbsp;
					<input type="text" name="code" value="">
					&nbsp;&nbsp;
					Role: 
					&nbsp;&nbsp;
					<select name="role">
						<option value="ADMIN">ADMIN</option>
						<option value="USER" selected="selected">USER</option>
						<!-- <option value="GUEST">GUEST</option> -->
						<!-- <option value="OUTSIDER">OUTSIDER</option> -->
					</select>
				</p>
				<p>
					For certain email: 
					&nbsp;&nbsp;
					<input type="text" name="object" value="">
					&nbsp;&nbsp;
					Or keep it empty if the code would be open to any register.
				</p>
				<p>
					From 
					&nbsp;&nbsp;
					<input type="date" name="start_time">
					&nbsp;&nbsp;
					To 
					&nbsp;&nbsp;
					<input type="date" name="end_time">
				</p>
				<p>
					Please save after you confirm the above settings.
					<span class="btn_span"><a class="btn" href="javascript:void(0);" onclick="$('#new_code_form').submit();">Save</a></span>
					<input type="hidden" name="act" value="new_code">
				</p>
			</form>
		</div>
		<div class="right_div">
			<h2>Links</h2>
			<p>
				<span class="btn_span"><a class="btn btn-full-width" href="index.php">Home</a></span>
			</p>
			<p>
				<span class="btn_span"><a class="btn btn-full-width" href="UserAgency.php">Users</a></span>
			</p>
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