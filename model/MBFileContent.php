<?php
/**
In database fields
*/
class MBFileContent extends MBBasicModel
{
	
	function __construct()
	{
		parent::__construct();
	}

	function getFileContent($file_id){
		$file_id=$this->pdo->quote($file_id,PDO::PARAM_INT);
		$sql="SELECT content FROM `mb_file_content` WHERE file_id={$file_id} LIMIT 1";
		return $this->pdo->getOne($sql);
	}

	public function create($file_id,$content,$editor_id){
		$file_id=$this->pdo->quote($file_id,PDO::PARAM_INT);
		$content=$this->pdo->quote($content);
		$editor_id=$this->pdo->quote($editor_id,PDO::PARAM_INT);
		$sql="INSERT INTO `mb_file_content` (
			`file_id`,
			`content`,
			`editor_id`,
			`create_time`,
			`update_time`
		) VALUES (
			{$file_id},
			{$content},
			{$editor_id},
			NOW(),
			NOW()
		);
		";
		// MeinBlog::log('MBFileContent.create.sql: '.$sql);
		$afx=$this->pdo->exec($sql);
		return $afx;
	}

	public function update($file_id,$content,$editor_id){
		$file_id=$this->pdo->quote($file_id,PDO::PARAM_INT);
		$content=$this->pdo->quote($content);
		$editor_id=$this->pdo->quote($editor_id,PDO::PARAM_INT);
		$sql="UPDATE mb_file_content 
			SET content = {$content}, editor_id = {$editor_id}, update_time = NOW()
			WHERE file_id = {$file_id}
		";
		$afx=$this->pdo->exec($sql);
		return $afx;
	}
}
?>