<?php
/**
In database fields
*/
class MBUser extends MBBasicModel
{
	
	function __construct()
	{
		parent::__construct();
	}

	public function authUser($username,$password){
		$username=$this->pdo->quote($username);
		$password=$this->pdo->quote($password);
		$sql="SELECT 
				`mb_user`.`user_id`
			FROM `MeinBlog`.`mb_user`
			WHERE (name={$username} OR email={$username}) AND password={$password}
			LIMIT 1
		";
		// MeinBlog::log('MBUser.authUser.sql='.$sql);
		$r = $this->pdo->getOne($sql);
		// MeinBlog::log('MBUser.authUser.result='.$r);
		return $r;
	}

	public function authEmailAndCode($email,$code){
		//Not implemented. Should return role.
		$RCAgent=new MBRegisterCode();
		return $RCAgent->query($email,$code);
	}

	public function getUser($user_id){
		$user_id=$this->pdo->quote($user_id,PDO::PARAM_INT);
		$sql="SELECT 
				`mb_user`.`user_id`,
			    `mb_user`.`name`,
			    `mb_user`.`email`,
			    `mb_user`.`role`,
			    `mb_user`.`create_time`
			FROM `MeinBlog`.`mb_user`
			WHERE user_id = {$user_id}
			LIMIT 1
		";
		// MeinBlog::log('MBUser.getUser.sql='.$sql);
		$r = $this->pdo->getRow($sql);
		// MeinBlog::log('MBUser.getUser.result='.$r);
		return $r;
	}

	public function queryUser($fields=array(),$sort='user_id',$sort_order='ASC'){
		$con_sql="";
		if(!empty($fields)){
			foreach ($fields as $key => $value) {
				$con_sql.=" AND $key=".$this->pdo->quote($value)." ";
			}
		}
		if(!in_array($sort, array('user_id','name','email','role','create_time'))){
			$sort="user_id";
		}
		if($sort_order!='DESC' && $sort_order!='ASC'){
			$sort_order="";
		}
		$sql="SELECT 
				`mb_user`.`user_id`,
			    `mb_user`.`name`,
			    `mb_user`.`email`,
			    `mb_user`.`role`,
			    `mb_user`.`create_time`
			FROM `MeinBlog`.`mb_user`
			WHERE 1 {$con_sql}
			ORDER BY `{$sort}` {$sort_order}
		";
		return $this->pdo->getAll($sql);
	}

	public function create($name,$email,$role,$password){
		$name=$this->pdo->quote($name);
		$email=$this->pdo->quote($email);
		$role=$this->pdo->quote($role);
		$password=$this->pdo->quote($password);
		$sql="INSERT INTO `MeinBlog`.`mb_user`
			(
				`user_id`,
				`name`,
				`password`,
				`email`,
				`role`,
				`create_time`
			)
			VALUES
			(
				NULL,
				{$name},
				{$password},
				{$email},
				{$role},
				NOW()
			)
		";
		$new_id=$this->pdo->insert($sql);
		return $new_id;
	}

	public function update($user_id,$name,$email,$role,$new_password,$old_password){
		$user_id=$this->pdo->quote($user_id,PDO::PARAM_INT);
		$name=$this->pdo->quote($name);
		$email=$this->pdo->quote($email);
		$role=$this->pdo->quote($role);
		$new_password=$this->pdo->quote($new_password);
		$old_password=$this->pdo->quote($old_password);
		$sql="UPDATE `MeinBlog`.`mb_user` 
			SET name={$name},
				password={$new_password},
				email={$email},
				role={$role}
			WHERE user_id={$user_id}
			AND password={$old_password}
		";
		$afx=$this->pdo->exec($sql);
		return $afx;
	}

	public function modify($user_id,$name,$email,$role,$password){
		$user_id=$this->pdo->quote($user_id,PDO::PARAM_INT);
		$name=$this->pdo->quote($name);
		$email=$this->pdo->quote($email);
		$role=$this->pdo->quote($role);
		if(!empty($password)){
			$password=$this->pdo->quote($password);
			$password_sql="password={$password},";
		}else{
			$password_sql="";
		}
		$sql="UPDATE `MeinBlog`.`mb_user` 
			SET name={$name},
				{$password_sql}
				email={$email},
				role={$role}
			WHERE user_id={$user_id}
		";
		$afx=$this->pdo->exec($sql);
		return $afx;
	}
}