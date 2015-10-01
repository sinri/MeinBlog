<?php
require_once(__DIR__.'/library/MeinBlog.php');
require_once(__DIR__.'/library/Parsedown.php');

// Start or resume one session 
$sessionAgent=MeinBlogSession::sharedInstance();
$user_id=$sessionAgent->getUserId();
$user_info=$sessionAgent->getUserInfo();

$userAgent=new MBUser();
$fileAgent=new MBFileHeader();
$categoryAgent=new MBCategory();

$message='';

$isAdmin=false;

if($user_id && $user_info){
	if($user_info['role']=='ADMIN'){
		$isAdmin=true;
	}

	if($isAdmin){
		if(MeinBlog::getRequest('act')=='modify_category'){
			$c_id=MeinBlog::getRequest('target_category_id');
			$c_name=MeinBlog::getRequest('new_category_name');
			$c_open_level=MeinBlog::getRequest('new_open_level');

			if(empty($c_id) || empty($c_name) || empty($c_open_level)){
				$message="Please fill the required fields.";
			}
			else{
				$afx=$categoryAgent->update($c_id,$c_name,$c_open_level);
				if(empty($afx)){
					$message="Failed to save modification.";
				}else{
					$message="Saved modification.";
				}
			}
		}
	}

	$category_status_list=$categoryAgent->getCategoryStatusList();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
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
	td,th {
		text-align: center !important;
	}
	.dl-horizontal dt {
		width: 150px !important;
		padding-right: 10px !important;
	}
	.dl-horizontal dd {
		width: 250px !important;
		margin-left: 170px !important;
	}
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
		<div class="left_div">
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
			<?php }else{ 
			?>
			<div>
				<h2>Categories</h2>
			<?php 
				if(!empty($category_status_list)){
					echo "<table>
						<thead>
							<tr>
								<th>Category Name</th>
								<th>Open Level</th>
								<th>File Count</th>
								<th>Recent Files</th>
							</tr>
						</thead>
						<tbody>
					";
					foreach ($category_status_list as $category_status_item) {
						echo "<tr><!-- category_id=".$category_status_item['category_id']." -->";
						echo "<td>".$category_status_item['category_name']."</td>";
						echo "<td>".$category_status_item['open_level']."</td>";
						echo "<td>".$category_status_item['file_count']."</td>";
						echo "<td>";
						$file_ids=explode(',', $category_status_item['file_ids']);
						for ($i=0; $i < min(3,count($file_ids)); $i++) { 
							$sample_file_id=$file_ids[$i];
							$sample_file_header=$fileAgent->getFileHeader($sample_file_id);
							echo "<span style='margin: 0px 5px;'><a href='FileView.php?file_id=".$sample_file_header['file_id']."'>".$sample_file_header['title']."</a></span>";
						}
						echo "</td>";
						echo "</tr>";
					}
					echo "</tbody>
					</table>
					";
				}else{
					echo "<p>No category exists now.</p>";
				}
			?>
			</div>
			<?php 
					if($isAdmin && !empty($category_status_list)){
			?>
			<div>
				<h2>Modify Category</h2>
				<?php if (!empty($message)) { ?>
				<div class="message_box">
					<h3>Message</h3>
					<p><?php echo $message; ?></p>
				</div>
				<?php } ?>
				<div style="font-size: 18px;">
					<form id="modify_category_form" method='POST'>
						<input type='hidden' name='act' value='modify_category'>
						<dl class="dl-horizontal">
							<dt>
								Modify 
							</dt>
							<dd>
								<select id="target_category_id" name="target_category_id" class="form-control-inline" style="width:150px;" onchange="target_category_changed()">
									<option value="">Select One</option>
			<?php
						foreach ($category_status_list as $category_status_item) {
							echo "<option value='".$category_status_item['category_id']."'>".$category_status_item['category_name']."</option>";
						}
			?>
								</select>
							</dd>
							<dt>
								New Name
							</dt>
							<dd>
								<input type="text" class="form-control-inline" style="width:150px;" id="new_category_name" name="new_category_name" value="">
							</dd>
							<dt>
								Open Level
							</dt>
							<dd>
								<select class="form-control-inline" style="width:150px;" id="new_open_level" name="new_open_level">
									<option value="ADMIN">ADMIN</option>
									<option value="USER">USER</option>
									<option value="GUEST">GUEST</option>
									<option value="OUTSIDER" selected="selected">OUTSIDER</option>
								</select>
							</dd>
							<dt>
							</dt>
							<dd>
								<span class="btn_span"><a href="javascript:void(0);" class="btn" onclick="$('#modify_category_form').submit()">Save Modification</a></span>
							</dd>
						</dl>
						<script type="text/javascript">
						var existed_category_data=JSON.parse('<?php echo json_encode($category_status_list); ?>');
						function target_category_changed(){
							$("#new_category_name").val('');
							// $("#new_category_name option[value='']").attr('selected','selected');
							$("#new_open_level").val('').trigger('change');;
							if($("#target_category_id").val()!=''){
								//load data
								for (var i = existed_category_data.length - 1; i >= 0; i--) {
									if(existed_category_data[i].category_id==$("#target_category_id").val()){
										//it is
										console.log('selected: cn='+existed_category_data[i].category_name+" ol="+existed_category_data[i].open_level);
										$("#new_category_name").val(existed_category_data[i].category_name);
										// $("#new_open_level option[value='"+existed_category_data[i].open_level+"']").attr('selected','selected');
										$("#new_open_level").val(existed_category_data[i].open_level).trigger('change');;
									}
								};
							}
						}
						</script>
					</form>
				</div>
			</div>
			<?php
					}
				} 
			?>
			<div class="clear"></div>
		</div>
		<div class="right_div">
			<h2>Links</h2>
			<span class="btn_span"><a class="btn btn-full-width" href="index.php">Home</a></span>
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