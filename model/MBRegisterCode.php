<?php
/**
In database fields
*/
class MBRegisterCode extends MBBasicModel
{
	
	function __construct()
	{
		parent::__construct();
	}

	function query($object,$code){
		$object=$this->pdo->quote($object);
		$code=$this->pdo->quote($code);
		$sql="SELECT 
		    role
		FROM
		    mb_register_code
		WHERE
		    object = {$object} AND code = {$code}
		        AND (start_time IS NULL OR start_time < NOW())
		        AND (end_time IS NULL OR end_time > NOW())
        ";
        $role=$this->pdo->getOne($sql);
        if(!empty($role)){
        	return $role;
        }else{
        	$sql="SELECT 
			    role
			FROM
			    mb_register_code
			WHERE
			    object = 'ANY' AND code = {$code}
			        AND (start_time IS NULL OR start_time < NOW())
			        AND (end_time IS NULL OR end_time > NOW())
	        ";
	        $role=$this->pdo->getOne($sql);
	        if(!empty($role)){
	        	return $role;
	        }
        }

        // Final response the default value
        return 'GUEST';
	}

	function getRecord($rc_id){
		$rc_id=$this->pdo->quote($rc_id,PDO::PARAM_INT);
		$sql="SELECT 
		    *
		FROM
		    mb_register_code
		WHERE
		    rc_id = ".$rc_id;
		return $this->pdo->getRow($sql);
	}

	function getRecordList(){
		$rc_id=$this->pdo->quote($rc_id,PDO::PARAM_INT);
		$sql="SELECT 
		    *,
			IF(
				(start_time is not NULL and start_time>now()) ,
				1,
				0
			) as is_future,
			IF(
				(end_time is not NULL and end_time<now()),
				1,
				0
			) as is_over
		FROM
		    mb_register_code
		WHERE
		    1
		";
		$list = $this->pdo->getAll($sql);
		if(!empty($list)){
			foreach ($list as $key => $item) {
				$is_future=$item['is_future'];
				$is_over=$item['is_over'];
				if($is_over && $is_future){
					$list[$key]['status']='NEVER';
				}elseif(!$is_over && $is_future){
					$list[$key]['status']='PENDING';
				}elseif($is_over && !$is_future){
					$list[$key]['status']='ENDED';
				}else{
					$list[$key]['status']='OK';
				}
			}
		}
		return $list;
	}

	function insert($object,$code,$role,$start_time,$end_time){
		$object=$this->pdo->quote($object);
		$code=$this->pdo->quote($code);
		$role=$this->pdo->quote($role);
		if(!empty($start_time)){
			$start_time=$this->pdo->quote($start_time);
		}else{
			$start_time='NULL';
		}
		if(!empty($end_time)){
			$end_time=$this->pdo->quote($end_time);
		}else{
			$end_time='NULL';
		}

		$sql="INSERT INTO `mb_register_code`
		(
			`rc_id`,
			`object`,
			`code`,
			`role`,
			`start_time`,
			`end_time`
		)
		VALUES
		(
			NULL,
			{$object},
			{$code},
			{$role},
			{$start_time},
			{$end_time}
		)
		";
		// MeinBlog::log("MBRegisterCode->insert($object,$code,$role,$start_time,$end_time)->sql: ".$sql);
		$rc_id=$this->pdo->insert($sql);
		// MeinBlog::log("MBRegisterCode->insert=".$rc_id);
		return $rc_id;
	}

	function update($rc_id,$object,$code,$role,$start_time,$end_time){
		$rc_id=$this->pdo->quote($rc_id,PDO::PARAM_INT);
		$object=$this->pdo->quote($object);
		$code=$this->pdo->quote($code);
		$role=$this->pdo->quote($role);
		if(!empty($start_time)){
			$start_time=$this->pdo->quote($start_time);
		}else{
			$start_time='NULL';
		}
		if(!empty($end_time)){
			$end_time=$this->pdo->quote($end_time);
		}else{
			$end_time='NULL';
		}

		$sql="UPDATE `mb_register_code` 
		SET 
		    object = {$object},
		    code = {$code},
		    role = {$role},
		    start_time = {$start_time},
		    end_time = {$end_time}
		WHERE
		    rc_id = ".$rc_id;
		$afx=$this->pdo->exec($sql);
		return $afx;
	}
}
?>