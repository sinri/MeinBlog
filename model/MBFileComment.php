<?php
/**
In database fields
*/
class MBFileComment extends MBBasicModel
{
	
	function __construct()
	{
		parent::__construct();
	}

	public function getCommentsOfFile($file_id){
		$file_id=$this->pdo->quote($file_id,PDO::PARAM_INT);
		$sql="SELECT 
		    fc.*, u.name, u.email
		FROM
		    mb_file_comment fc
		        LEFT JOIN
		    mb_user u ON fc.editor_id = u.user_id
		WHERE
		    file_id = {$file_id}
		ORDER BY comment_id DESC
		";
		$comment_list=$this->pdo->getAll($sql);
		return $comment_list;
	}

	public function createComment($file_id,$editor_id,$content,$to_comment_id=0){
		$file_id=$this->pdo->quote($file_id,PDO::PARAM_INT);
		$editor_id=$this->pdo->quote($editor_id,PDO::PARAM_INT);
		$content=$this->pdo->quote($content);
		$to_comment_id=$this->pdo->quote($to_comment_id,PDO::PARAM_INT);
		$sql="INSERT INTO `mb_file_comment`
			(
				`comment_id`,
				`file_id`,
				`to_comment_id`,
				`editor_id`,
				`content`,
				`create_time`,
				`update_time`
			)
			VALUES
			(
				NULL,
				{$file_id},
				{$to_comment_id},
				{$editor_id},
				{$content},
				NOW(),
				NOW()
			)
		";
		$new_id=$this->pdo->insert($sql);
		// MeinBlog::log("MBFileComment->createComment sql : ".$sql." --> ".$new_id);
		return $new_id;
	}
}
?>