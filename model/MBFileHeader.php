<?php
/**
Contains the abstract information of one blog.
In DB Table, fields are:
	file_id,
	title,
	abstract,
	main_editor_id,
	create_time,
	update_time,
*/
class MBFileHeader extends MBBasicModel
{

	function __construct($fields=array())
	{
		parent::__construct($fields);
	}

	function getFileHeader($file_id){
		$file_id=$this->pdo->quote($file_id,PDO::PARAM_INT);
		$sql="SELECT * FROM `mb_file_header` WHERE file_id={$file_id} LIMIT 1";
		return $this->pdo->getRow($sql);
	}

	function create($title,$main_editor_id,$content,$category_id=0,$abstract=''){
		if(empty($title) || empty($main_editor_id) || empty($content))return false;

		$this->pdo->beginTransaction();

		try {
			$title=$this->pdo->quote($title);
			$main_editor_id=$this->pdo->quote($main_editor_id,PDO::PARAM_INT);
			$abstract=$this->pdo->quote($abstract);
			$sql="INSERT INTO `mb_file_header` (
					`file_id`,
					`title`,
					`abstract`,
					`main_editor_id`,
					`category_id`,
					`create_time`,
					`update_time`
				)VALUES(
					NULL,
					{$title},
					{$abstract},
					{$main_editor_id},
					{$category_id},
					NOW(),
					NOW()
				);
			";
			// MeinBlog::log("create file header sql: ".$sql);
			$file_id=$this->pdo->insert($sql);

			if(empty($file_id)){
				throw new Exception("Error on creating file header.", 1);
			}

			$FC=new MBFileContent();
			$afx=$FC->create($file_id,$content,$main_editor_id);

			if(empty($afx)){
				throw new Exception("Error on creating file content.", 1);
			}

			$this->pdo->commit();

			return $file_id;
		} catch (Exception $e) {
			$this->pdo->rollBack();
			MeinBlog::log($e->getMessage());
		}
		return false;
	}

	function update($file_id,$editor_id,$title=false,$abstract=false,$category_id=false,$content=false){
		// MeinBlog::log("update($file_id,$editor_id,$title=false,$abstract=false,$category_id=false,$content=false)");
		if(empty($file_id) || empty($editor_id))return false;
		$this->pdo->beginTransaction();
		try {
			if($content!==false){
				$afx=MBFileContent::update($file_id,$content,$editor_id);
				if($afx!==1){
					throw new Exception("Error in modifying file content.", 1);
				}
			}

			$sql_set=" update_time=NOW(), main_editor_id = ".$this->pdo->quote($editor_id,PDO::PARAM_INT);
			if($title!==false){
				$sql_set.=', title = '.$this->pdo->quote($title);
			}
			if($abstract!==false){
				if($abstract===null){
					$sql_set.=', abstract = NULL ';
				}else{
					$sql_set.=', abstract = '.$this->pdo->quote($abstract);
				}
			}
			if($category_id!==false){
				$sql_set.=', category_id = '.$this->pdo->quote($category_id,PDO::PARAM_INT);
			}
			$sql="UPDATE mb_file_header SET {$sql_set} WHERE file_id=".$this->pdo->quote($file_id,PDO::PARAM_INT);
			$afx=$this->pdo->exec($sql);

			if($afx!==1){
				throw new Exception("Error in modifying file header.", 1);
			}
			
			$this->pdo->commit();

			return true;
		} catch (Exception $e) {
			$this->pdo->rollBack();
			MeinBlog::log($e->getMessage());
		}
		return false;
	}
}
?>