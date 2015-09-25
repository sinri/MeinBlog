<?php
/*
 * MeinBlog Initialization
 * =======================
 * Initialize all configurations and components.
 * Sinri Edogawa 2015-09-24
 */

require_once(__DIR__.'/MeinBlogConfig.php');
require_once(__DIR__.'/MeinBlogPDO.php');

/**
* 
*/
class MeinBlog
{

	public static function getConfig(){
		// global $MBConfig;
		$MBConfig=new MeinBlogConfig();
		return $MBConfig;
	}

	public static function getPDO(){
		// global $MBDatabase;
		$MBDatabase=new MeinBlogPDO(
			$MBConfig->property('deploy_level'), //$deploy_level,
			$MBConfig->property('db_username'), //$username,
			$MBConfig->property('db_password'), //$password,
			$MBConfig->property('db_host'), //$host,
			$MBConfig->property('db_port'), //$port,
			$MBConfig->property('db_scheme'), //$database,
			$MBConfig->property('db_charset'), //$charset='utf8'
		);
		return $MBDatabase;
	}

}

// Model Classes
require_once(__DIR__.'/../model/MBAutoload.php');

?>