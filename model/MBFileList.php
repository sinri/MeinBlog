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
	public function getList($conditions=array(),$role='OUTSIDER',$page=1,$page_size=10,&$page_count){
		$sql_con="";
		$limit=$page_size>0?$page_size:10;
		$offset=(($page-1)*$limit)<0?0:(($page-1)*$limit);
		if(!empty($conditions) && is_array($conditions)){
			foreach ($conditions as $key => $value) {
				if($key=='title'){
					$sql_con.=" AND fh.title LIKE CONCAT('%',".$this->pdo->quote($value).",'%') ";
				}elseif($key=='editor_id'){
					$sql_con.=" AND fh.main_editor_id = ".$this->pdo->quote($value,PDO::PARAM_INT);
				}elseif($key=='start_time'){
					$sql_con.=" AND fh.create_time >= ".$this->pdo->quote($value)." ";
				}elseif($key=='end_time'){
					$sql_con.=" AND fh.update_time =< ".$this->pdo->quote($value)." ";
				}elseif($key=='category'){
					$sql_con.=" AND fh.category_id = ".$this->pdo->quote($value,PDO::PARAM_INT);
				}elseif($key=='abstract'){
					$sql_con.=" AND fh.abstract LIKE CONCAT('%',".$this->pdo->quote($value).",'%') ";
				}
			}
		}
		$sql_open_level="";
		if($role=='ADMIN'){
			$sql_open_level=" AND (c.open_level is null or c.open_level in ('ADMIN','USER','GUEST','OUTSIDER')) ";
		}elseif($role=='USER'){
			$sql_open_level=" AND (c.open_level is null or c.open_level in ('USER','GUEST','OUTSIDER')) ";
		}elseif($role=='GUEST'){
			$sql_open_level=" AND (c.open_level is null or c.open_level in ('GUEST','OUTSIDER')) ";
		}else{
			$sql_open_level=" AND (c.open_level is null or c.open_level in ('OUTSIDER')) ";
		}
		$sql="SELECT 
		    fh.*,
		    c.category_name,
		    u.name user_name,
		    u.email user_email
		FROM
		    mb_file_header fh
		LEFT JOIN mb_category c ON fh.category_id=c.category_id
		LEFT JOIN mb_user u ON fh.main_editor_id=u.user_id
		WHERE
		    1 {$sql_con}
		    {$sql_open_level}
		LIMIT {$limit} OFFSET {$offset}
        ";
        // MeinBlog::log("MBFileList->getList()->sql: ".$sql);
        $list=$this->pdo->getAll($sql);

        $sql = "SELECT 
		    count(fh.file_id)
		FROM
		    mb_file_header fh
		LEFT JOIN mb_category c ON fh.category_id=c.category_id
		LEFT JOIN mb_user u ON fh.main_editor_id=u.user_id
		WHERE
		    1 {$sql_con}
		    {$sql_open_level}
		";
		$rows=$this->pdo->getOne($sql);
		$page_count=ceil(1.0*$rows/$page_size);

        $file_header_array=array();
        if(!empty($list)){
        	foreach ($list as $item) {
        		$file_header=new MBFileHeader($item);
        		$file_header_array[]=$file_header;
        	}
        }

        return $file_header_array;
	}

	public function search($keyword,$role='OUTSIDER',$limit=10,$offset=0){
		$keyword=$this->pdo->quote($keyword);
		$limit=$this->pdo->quote($limit,PDO::PARAM_INT);
		$offset=$this->pdo->quote($offset,PDO::PARAM_INT);
		$sql_open_level="";
		if($role=='ADMIN'){
			$sql_open_level=" AND (c.open_level is null or c.open_level in ('ADMIN','USER','GUEST','OUTSIDER')) ";
		}elseif($role=='USER'){
			$sql_open_level=" AND (c.open_level is null or c.open_level in ('USER','GUEST','OUTSIDER')) ";
		}elseif($role=='GUEST'){
			$sql_open_level=" AND (c.open_level is null or c.open_level in ('GUEST','OUTSIDER')) ";
		}else{
			$sql_open_level=" AND (c.open_level is null or c.open_level in ('OUTSIDER')) ";
		}
		$sql="SELECT 
		    fh.*, c.category_name, u.name user_name, u.email user_email
		FROM
		    mb_file_header fh
		        INNER JOIN
		    mb_category c ON fh.category_id = c.category_id
		        INNER JOIN
		    mb_user u ON fh.main_editor_id = u.user_id
		        INNER JOIN
		    mb_file_content fc ON fc.file_id = fh.file_id
		        INNER JOIN
		    mb_file_tag ft ON ft.file_id = fh.file_id
		WHERE
			(
		    fh.title LIKE CONCAT('%', {$keyword}, '%')
		        OR fh.abstract LIKE CONCAT('%', {$keyword}, '%')
		        OR c.category_name LIKE CONCAT('%', {$keyword}, '%')
		        OR fc.content LIKE CONCAT('%', {$keyword}, '%')
		        OR ft.tag LIKE CONCAT('%', {$keyword}, '%')
		    )
			{$sql_open_level}
		GROUP BY fh.file_id
		LIMIT {$limit} OFFSET {$offset}
		";
		// MeinBlog::log("MBFileList->search()->sql: ".$sql);
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