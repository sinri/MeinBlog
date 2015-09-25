<?php
/**
Return an list of MBFileHeader
*/
class MBFileList extends MBBasicModel
{
	
	function __construct()
	{
		parent::__construct();
	}

	/**
	@param conditions Key-Value array, admitted keys are title, editor_id, start_time, end_time, category
	@return an array of MBFileHeader
	*/
	public function getList($conditions=array(),$page=1,$page_size=10){
		$sql_con="";
		$limit=$page_size>0?$page_size:10;
		$offset=(($page-1)*$limit)<0?0:(($page-1)*$limit);
		if(!empty($conditions) && is_array($conditions)){
			foreach ($conditions as $key => $value) {
				if($key=='title'){
					$sql_con.=" AND title LIKE CONCAT('%',".$this->pdo->quote($value).",'%') ";
				}elseif($key=='editor_id'){
					$sql_con.=" AND main_editor_id = ".$this->pdo->quote($value,PDO::PARAM_INT);
				}elseif($key=='start_time'){
					$sql_con.=" AND create_time >= ".$this->pdo->quote($value)." ";
				}elseif($key=='end_time'){
					$sql_con.=" AND update_time =< ".$this->pdo->quote($value)." ";
				}elseif($key=='category'){
					$sql_con.=" AND category_id = ".$this->pdo->quote($value,PDO::PARAM_INT);
				}
			}

		}
		$sql="SELECT 
		    *
		FROM
		    mb_file_header
		WHERE
		    1 {$sql_con}
		LIMIT {$limit} OFFSET {$offset}
        ";
        $list=$this->pdo->getAll($sql);

        $file_header_array=array();
        if(!empty($list)){
        	foreach ($list as $item) {
        		$file_header=new MBFileHeader($item);
        		$file_header_array[]=$file_header;
        	}
        }

        return $file_header_array;
	}
}
?>