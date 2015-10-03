<?php
/*
 * MeinBlog Config File
 * ====================
 * To set all parameters for database, file system, and any other configurations.
 * Sinri Edogawa 2015-09-24
 */

/**
* Change the $properties pre-definitions as your set.
*/
class MeinBlogConfig
{
	
	private $properties = array(
		// Database: for PDO
		'db_host'=>'127.0.0.1',
		'db_port'=>'3306',
		'db_charset'=>'utf8',
		'db_scheme'=>'MeinBlog',
		'db_username'=>'root',
		'db_password'=>'123456',
        // Language
        'default_language'=>'en',

        // MeinBlog Page Header
        'MeinBlog_Title'=>'MeinBlog',
        'MeinBlog_Subtitle'=>'A Simple Blog System in PHP',

		// Depolyment: level as DEV, TEST, PROD, etc.
		'deploy_level'=>'DEV',
	);

	public function property($name, $value = null) {
        if (func_num_args() > 1) {
            return $this->properties[$name] = $value;
        } else {
            return isset($this->properties[$name]) ? $this->properties[$name] : null;
        }
    }

    function __construct(){
    	// Empty Construct Function
    }

    public static function getInstance(){
    	static $instance=null;
    	if($instance==null)$instance=new MeinBlogConfig();
    	return $instance;
    }
}
?>