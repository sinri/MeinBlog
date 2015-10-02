<?php
require_once(__DIR__.'/library/MeinBlog.php');

// Start or resume one session 
$sessionAgent=MeinBlogSession::sharedInstance();
$user_id=$sessionAgent->getUserId();
$user_info=$sessionAgent->getUserInfo();

$userAgent=new MBUser();
$fields=array();
$sort='user_id';
$sort_order='ASC';

if(!empty($user_id) ){

	if($user_info['role']=='ADMIN'){
		if(MeinBlog::getRequest('act')=='modify_user'){
			$target_user_id=MeinBlog::getRequest('target_user_id');
			$new_name=MeinBlog::getRequest('new_name');
			$new_email=MeinBlog::getRequest('new_email');
			$new_password=MeinBlog::getRequest('new_password');
			$new_role=MeinBlog::getRequest('new_role');
			
			if(!empty($target_user_id) && !empty($new_name) && !empty($new_email)){
				if($target_user_id==1){
					$new_role='ADMIN';
				}
				$done=$userAgent->modify($target_user_id,$new_name,$new_email,$new_role,(empty($new_password)?'':md5($new_password)));
				if($done){
					header("location: UserAgency.php");
				}else{
					$modify_user_message="Failed to modify.";
				}
			}else{
				$done=false;
				$modify_user_message="Conditions are not correct.";
			}
		}

		$user_list=$userAgent->queryUser($fields,$sort,$sort_order);
	}else{
		//
	}
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
		<h1>MeinBlog<br><small>A Simple Blog System in PHP</small></h1>
	</div>
	<div id="middle">
		<?php if(empty($user_id)){ ?>
		<div class="message_box">
			<h3>
				Message:
			</h3>
			<p>
				You have not logined into MeinBlog, or you are not in Admin Group. Now directing to homepage in 3 seconds.
			</p>
			<script type="text/javascript">
			function jump(){
				window.location.href = './index.php';
				console.log('Let it go');
			}
			setTimeout(jump,3000);
			</script>
		</div>
		<?php }else{ 
			// print_r($user_list);
		?>
		<div class="left_div">
			<?php if($user_info['role'] == 'ADMIN'){ ?>
			<h2>Users</h2>
			<table>
				<thead>
					<tr>
						<th>User ID</th>
						<th>Name</th>
						<th>Email</th>
						<th>Role</th>
						<th>Since</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($user_list as $user_item) { ?>
					<tr>
						<td><?php echo $user_item['user_id']; ?></td>
						<td><?php echo $user_item['name']; ?></td>
						<td><?php echo $user_item['email']; ?></td>
						<td><?php echo $user_item['role']; ?></td>
						<td><?php echo $user_item['create_time']; ?></td>
						<td>
							<span class="btn_span"><a href="javascript:void(0)" class="btn" onclick="callToModifyUser('<?php echo $user_item['user_id']; ?>','<?php echo $user_item['name']; ?>','<?php echo $user_item['email']; ?>','<?php echo $user_item['role']; ?>')">Modify</a></span>
						</td>
					</tr>
					<?php }?>
				</tbody>
			</table>
			<?php }else{
			?>
			<h2>My User Info</h2>
			<table>
				<thead>
					<tr>
						<th>User ID</th>
						<th>Name</th>
						<th>Email</th>
						<th>Role</th>
						<th>Since</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php echo $user_info['user_id']; ?></td>
						<td><?php echo $user_info['name']; ?></td>
						<td><?php echo $user_info['email']; ?></td>
						<td><?php echo $user_info['role']; ?></td>
						<td><?php echo $user_info['create_time']; ?></td>
						<td>
							<span class="btn_span"><a href="javascript:void(0)" class="btn" onclick="callToModifyUser('<?php echo $user_info['user_id']; ?>','<?php echo $user_info['name']; ?>','<?php echo $user_info['email']; ?>','<?php echo $user_info['role']; ?>')">Modify</a></span>
						</td>
					</tr>
				</tbody>
			</table>
			<?php
			}
			?>
			<script type="text/javascript">
			function callToModifyUser(uid,uname,uemail,urole){
				$('#modify_user_form select[name="target_user_id"]').val(uid).trigger('change');
				$('#modify_user_form input[name="new_name"]').val(uname);
				$('#modify_user_form input[name="new_password"]').val();
				$('#modify_user_form input[name="new_email"]').val(uemail);
				$('#modify_user_form select[name="new_role"]').val(urole).trigger('change');
				if(uid==1){
					$('#modify_user_form select[name="new_role"]').attr("disabled",'disabled');
					// $('#modify_user_form input[name="new_name"]').attr("disabled",'disabled');
				}else{
					$('#modify_user_form select[name="new_role"]').attr("disabled",false);
					// $('#modify_user_form input[name="new_name"]').attr("disabled",false);
				}
				// $('#modify_user_form select[name="target_user_id"]').attr("disabled",'disabled');
				modify_user_form_switch('block');
			}
			</script>
			<!-- <h2>Modify <a href="javascript:void(0);" onclick="modify_user_form_switch('inv')" id="modify_user_form_switch_a">Show</a></h2> -->
			<?php if(!empty($modify_user_message)){echo "<p>".$modify_user_message."</p>";} ?>
			<form id="modify_user_form" method="POST" style="display:none;">
				<h2>Modify <a href="javascript:void(0);" onclick="modify_user_form_switch('none')">Hide</a></h2>
				<p>
					<label style="width:100px;display:inline-block;">Target User:</label>
					<select name="target_user_id">
						<option value="0">Not Selected</option>
						<?php 
						if(!empty($user_list)){
							foreach ($user_list as $user_item) { 
								echo "<option value='".$user_item['user_id']."'>".$user_item['user_id']." - ".$user_item['name'].($user_item['user_id']==$user_info['user_id']?' (SELF)':'')."</option>";
							}
						}else{
							echo "<option value='".$user_info['user_id']."'>".$user_info['user_id']." - ".$user_info['name']." (SELF)</option>";
						}
						?>
					</select>
				</p>
				<p>
					<label style="width:100px;display:inline-block;">New Name:</label>
					<input type="text" name="new_name" value="">
				</p>
				<p>
					<label style="width:100px;display:inline-block;">New Password:</label>
					<input type="password" name="new_password" value="">
				</p>
				<p>
					<label style="width:100px;display:inline-block;">New Email:</label>
					<input type="text" name="new_email" value="">
				</p>
				<p>
					<label style="width:100px;display:inline-block;">Role:</label>
					<select name="new_role">
						<option value="ADMIN">ADMIN</option>
						<option value="USER">USER</option>
						<option value="GUEST" selected="selected">GUEST</option>
						<option value="OUTSIDER">OUTSIDER</option>
					</select>
				</p>
				<p>
					<label style="width:100px;display:inline-block;"></label>
					<input type="hidden" name="act" value="modify_user">
					<span class="btn_span"><a href="javascript:void(0)" class="btn" onclick="$('#modify_user_form').submit();">Save Modification</a></span>
				</p>
			</form>
			<script type="text/javascript">
			function modify_user_form_switch(tobe){
				if(tobe=='inv'){
					if($("#modify_user_form").css('display')=='block'){
						$("#modify_user_form").css('display','none');
					}else{
						$("#modify_user_form").css('display','block');
					}
				}else{
					$("#modify_user_form").css('display',tobe);
				}
				if($("#modify_user_form").css('display')=='block'){
					$("#modify_user_form_switch_a").html('Hide');
				}else{
					$("#modify_user_form_switch_a").html('Show');
				}
			}
			</script>
		</div>
		<div class="right_div">
			<h2>Links</h2>
			<p>
				<span class="btn_span"><a class="btn btn-full-width" href="index.php">Home</a></span>
			</p>
			<p>
				<span class="btn_span"><a class="btn btn-full-width" href="RegisterCodeAgency.php">Register Codes</a></span>
			</p>
		</div>
		<?php } ?>
		<div class="clear"></div>
	</div>
	<div id="footer">
		<p>
			Copyright 2015 Sinri Edogawa :: Powered by Project <a href="https://github.com/sinri/MeinBlog">MeinBlog</a>
		</p>
	</div>
</body>
</html>