<?php
/**
In database fields
*/
class MBCategory extends MBBasicModel
{
	
	function __construct()
	{
		parent::__construct();
	}

	public function getCategoryList(){
		$sql="SELECT * FROM mb_category";
		$categoryList=$this->pdo->getAll($sql);
		return $categoryList;
	}

	public function getCategoryStatusList(){
		$sql="SELECT 
		    c.*,
		    COUNT(fh.file_id) file_count,
		    GROUP_CONCAT(fh.file_id
		        ORDER BY file_id DESC) file_ids
		FROM
		    mb_category c
		        LEFT JOIN
		    mb_file_header fh ON c.category_id = fh.category_id
		";
		$categoryStatusList=$this->pdo->getAll($sql);
		return $categoryStatusList;
	}

	public function getCategory($category_id){
		$sql="SELECT * FROM mb_category WHERE category_id=".$this->pdo->quote($category_id,PDO::PARAM_INT);
		$category=$this->pdo->getRow($sql);
		return $category;
	}

	public function create($category_name,$open_level){
		$category_name=$this->pdo->quote($category_name);
		$open_level=$this->pdo->quote($open_level);
		$sql="INSERT INTO `mb_category` (
				`category_id`,
				`category_name`,
				`open_level`
			) VALUES (
				NULL,
				{$category_name},
				{$open_level}
			)
		";
		$new_id=$this->pdo->insert($sql);
		return $new_id;
	}

	public function update($category_id,$category_name=false,$open_level=false){
		if($category_name===false && $open_level===false)return false;
		
		$set_sqls=array();
		if($category_name!==false){
			$category_name=$this->pdo->quote($category_name);
			$set_sqls[]=" category_name = ".$category_name." ";
		}
		if($open_level!==false){
			$open_level=$this->pdo->quote($open_level);
			$set_sqls[]=" open_level = ".$open_level." ";
		}
		$sql="UPDATE `mb_category` SET ".implode(',', $set_sqls)." WHERE category_id=".$this->pdo->quote($category_id,PDO::PARAM_INT);
		return $this->pdo->exec($sql);
	}

	public function delete($category_id){
		$sql="DELETE FROM `mb_category` WHERE category_id=".$this->pdo->quote($category_id,PDO::PARAM_INT);
		return $this->pdo->exec($sql);
	}
}
?>