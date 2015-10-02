<?php
require_once(__DIR__.'/library/MeinBlog.php');
require_once(__DIR__.'/library/Parsedown.php');

// Start or resume one session 
$sessionAgent=MeinBlogSession::sharedInstance();
$user_id=$sessionAgent->getUserId();
$user_info=$sessionAgent->getUserInfo();

$userAgent=new MBUser();
$fileAgent=new MBFileHeader();
$contentAgent=new MBFileContent();
$tagAgent=new MBFileTag();
$commentAgent=new MBFileComment();

$file_id=MeinBlog::getRequest('file_id');

if(!empty($file_id)){
	$header=$fileAgent->getFileHeader($file_id);
	if($header){
		if(MeinBlog::getRequest('act')=='add_tag'){
			$tag=MeinBlog::getRequest('tag');
			if(!empty($tag)){
				$tagAgent->createTagForFile($file_id,$tag,$user_id);
			}
			header("location: FileView.php?file_id=".$file_id);
		}elseif(MeinBlog::getRequest('act')=='new_comment'){
			$to_comment_id=MeinBlog::getRequest('to_comment_id',0);
			$content=MeinBlog::getRequest('content');
			if(!empty($content)){
				$commentAgent->createComment($file_id,$user_id,$content,$to_comment_id);
			}
			header("location: FileView.php?file_id=".$file_id);
		}

		$title=$header['title'];
		$abstract=$header['abstract'];
		$category=$header['category_id'];
		$editor_id=$header['main_editor_id'];
		$content=$contentAgent->getFileContent($file_id);

		$first_edition_time=$header['create_time'];
		$last_edition_time=$header['update_time'];

		$tags=$tagAgent->getTagsForFile($file_id);
		$comments=$commentAgent->getCommentsOfFile($file_id);

		$writer_info=$userAgent->getUser($editor_id);
	}else{
		$file_id=null;
	}
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
	div.left_div {
		margin: 5px;
		width: 75%;
		height: auto;
		min-height: 500px;
		float: left;
		border-right: 1px solid gray;
	}
	div.right_div {
		margin: 0px;
		padding: 0px;
		width: 23%;
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
	#tag_div {
		height: auto;
		/*border-top: 1px solid lightgray;*/
		border-bottom: 1px solid lightgray;
	}
	#comment_div {
		height: auto;
	}
	div.comment_row{
		height: auto;
		width: 90%;
		margin: 0px;
		border-bottom: 1px solid lightgray;
	}
	#new_comment_div {
		height: auto;
		width: 90%;
		margin: 0px;
		padding: 10px 0;
		/*border-top: 1px solid lightgray;*/
	}
	#new_comment_div textarea {
		width: 90%;
		height: 40px;
		padding: 10px;
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
			<div id="comment_div">
				<div class="comment_row">
					<h2>Comments</h2>
				</div>
				<?php if(!empty($comments)){
					foreach ($comments as $comment) {
				?>
				<div class="comment_row" id="comment_row_for_id_<?php echo $comment['comment_id']; ?>">
					<div style="margin:5px">
						<div class="left">
							[#<?php echo $comment['comment_id']; ?>]
							<?php echo $comment['name']; ?> commented
							<?php if(empty($comment['to_comment_id'])){
								echo "this file";
							} else{
								echo "<a href='#comment_row_for_id_".$comment['to_comment_id']."'>[#".$comment['to_comment_id']."]</a>";
							} ?>
							on <?php echo $comment['create_time']; ?>
							<!-- &nbsp;&nbsp;&nbsp;&nbsp; -->
						</div>
						<div class="right">
							<?php if(!empty($user_id)){ ?>
							<span class="btn_span"><a href="#new_comment_form" class="btn" onclick="requireReplyComment('<?php echo $comment['comment_id']; ?>');">Reply</a></span>
							<?php } ?>
						</div>
						<div class="clear"></div>
					</div>
					<p>
						<?php echo $comment['content']; ?>
					</p>
				</div>
				<?php
					}
				} ?>
			</div>
			<?php if(!empty($user_id) && $user_info['role']!='OUTSIDER'){ ?>
			<div id="new_comment_div">
				<form method="POST" id="new_comment_form">
					<div>
						<h3>Write Comment</h3>
						<div style="margin: 10px 0px;">
							<div class="left">
								<span id="to_comment_target">Comment this file with Markdown format content.</span>
								<!-- &nbsp;&nbsp;&nbsp;&nbsp; -->
							</div>
							<div class="right">
								<!-- <button>Submit</button> -->
								<span class="btn_span">
									<a href="javascript:void(0);" class="btn" onclick="$('#new_comment_form').submit();">Submit</a>
								</span>
								<input type="hidden" name="to_comment_id" id="to_comment_id_input" value="0">
							</div>
							<div class="clear"></div>
						</div>
					</div>
					<div>
						<input type="hidden" name="act" value="new_comment">
						<input type="hidden" name="file_id" value="<?php echo $file_id; ?>">
						<textarea name="content" placeholder="Comment here"></textarea>
					</div>
				</form>
				<script type="text/javascript">
				function requireReplyComment(origin_comment_id){
					$("#to_comment_id_input").val(origin_comment_id);
					if(origin_comment_id!=0){
						$("#to_comment_target").html('Comment [#'+origin_comment_id+'] with Markdown format content. <a href="javascript:void(0);" onclick="requireReplyComment(0)">I would rather comment the file.</a>');
					}else{
						$("#to_comment_target").html('Comment this file with Markdown format content.');
					}
				}
				</script>
			</div>
			<?php } ?>
		</div>
		<div class="right_div">
			<h2>File Info</h2>
			<p>Written by <?php echo $writer_info['name']; ?></p>
			<p>Email: <?php echo $writer_info['email']; ?></p>
			<p>Since: <?php echo $first_edition_time; ?></p>
			<p>Final: <?php echo $last_edition_time; ?></p>

			<?php if($user_info['role']=='ADMIN' || $user_id==$editor_id){ ?>
				<p><span class="btn_span"><a href="FileEdit.php?file_id=<?php echo $file_id; ?>" class="btn btn-full-width">Edit</a></span></p>
			<?php } ?>

			<h2>Tags</h2>
			<p>
			<?php if(!empty($tags)){
				foreach ($tags as $tag => $tag_count) {
					echo "<a href='javascript:void(0);' style='text-decoration:none;' title='Click to agree' onclick='agreeTag(\"".$tag."\")'>";
					echo "<code>";
					echo $tag;
					echo " ×".$tag_count;
					echo "</code></a> &nbsp; ";
				}
			} ?>
			<script type="text/javascript">
			function agreeTag(tag){
				$("#new_tag_input").val(tag);
				$("#new_tag_form").submit();
			}
			</script>
			</p>
			<?php if(!empty($user_id) && $user_info['role']!='OUTSIDER'){ ?>
			<div style="height:30px;margin:10px 0px;">
				<form method="POST" style="margin:auto 0px;display:table-cell;vertical-align:middle;" id="new_tag_form">
					New:
					<input type="hidden" name="act" value="add_tag">
					<input type="hidden" name="file_id" value="<?php echo $file_id; ?>">
					<input type="text" id="new_tag_input" name="tag" placeholder="Tag" style="height:15px;width:100px;font-size:12px;border-radius:0px;margin:0px;vertical-align:baseline;border: none;background-color: #f8f8f8;">
					<span class="btn_span">
						<a href="javascript:void(0);" class="btn" onclick="$('#new_tag_form').submit();">Add</a>
					</span>
				</form>
			</div>
			<?php } ?>

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