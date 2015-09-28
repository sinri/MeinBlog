<?php
/**
* 
*/
class MeinBlogSession 
{
	protected $userAgent=null;
	protected $user_id=null;
	protected $user_info=null;

	// Instance

	public static function sharedInstance(){
		static $instance=null;
		if(!$instance){
			$instance=new MeinBlogSession();
		}
		return $instance;
	}
	
	function __construct()
	{
		$this->refreshSession();
	}

	// Refresh

	public function login($name,$password){
		$this->user_id=$this->getUserAgent()->authUser($name,md5($password));
		// MeinBlog::log("Event.Login({$name})->password({$password})={$user_id}");
		$_SESSION['user_id']=$user_id;
	}

	public function logout(){
		session_destroy();
	}

	public function refreshSession(){
		session_start();
		if(empty($_SESSION['user_id'])){
			$this->user_id=null;
			$this->user_info=null;
		}else{
			$this->user_id=$_SESSION['user_id'];
			$this->user_info=$this->getUserAgent()->getUser($this->user_id);
		}
	}

	// Getter and Setter

	public function getUserAgent(){
		if(empty($this->userAgent)){
			$this->userAgent=new MBUser();
		}
		return $this->userAgent;
	}

	public function getUserId(){
		return $this->user_id;
	}

	public function getUserInfo(){
		return $this->user_info;
	}


}





?>