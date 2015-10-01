<?php
/**
In database fields
*/
class MBFileTag extends MBBasicModel
{
	
	function __construct()
	{
		parent::__construct();
	}

	function getTagsForFile($file_id){
		$file_id=$this->pdo->quote($file_id,PDO::PARAM_INT);
		$sql="SELECT 
		    tag, COUNT(editor_id) count
		FROM
		    mb_file_tag
		WHERE
		    file_id = {$file_id}
		GROUP BY tag
		order by count desc";
		$result=$this->pdo->getAll($sql);
		$tags=array();
		foreach ($result as $res) {
			$tags[$res['tag']]=$res['count'];
		}
		return $tags;
	}

	function createTagForFile($file_id,$tag,$editor_id){
		$file_id=$this->pdo->quote($file_id,PDO::PARAM_INT);
		$tag=$this->pdo->quote($tag);
		$editor_id=$this->pdo->quote($editor_id,PDO::PARAM_INT);
		$sql="INSERT INTO `MeinBlog`.`mb_file_tag`
			(
				`file_id`,
				`tag`,
				`editor_id`,
				`create_time`
			)
			VALUES
			(
				{$file_id},
				{$tag},
				{$editor_id},
				NOW()
			)
		";
		// MeinBlog::log("MBFileTag.createTagForFile($file_id,$tag,$editor_id).sql=".$sql);
		$afx=$this->pdo->exec($sql);
		// MeinBlog::log("MBFileTag.createTagForFile.exec=".$afx);
		return $afx;
	}
}
?>