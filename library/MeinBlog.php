<?php
/*
 * MeinBlog Initialization
 * =======================
 * Initialize all configurations and components.
 * Sinri Edogawa 2015-09-24
 */

date_default_timezone_set('Asia/Shanghai');

include_once(__DIR__.'/../files/MeinBlogLanguagePackage.php');

include_once(__DIR__.'/MeinBlogConfig.php');
include_once(__DIR__.'/MeinBlogLogger.php');
include_once(__DIR__.'/MeinBlogPDO.php');

// Model Classes
include_once(__DIR__.'/../model/MBAutoload.php');

// Session
include_once(__DIR__.'/MeinBlogSession.php');

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
		$MBConfig=MeinBlog::getConfig();
		// global $MBDatabase;
		$MBDatabase=new MeinBlogPDO(
			$MBConfig->property('deploy_level'), //$deploy_level,
			$MBConfig->property('db_username'), //$username,
			$MBConfig->property('db_password'), //$password,
			$MBConfig->property('db_host'), //$host,
			$MBConfig->property('db_port'), //$port,
			$MBConfig->property('db_scheme'), //$database,
			$MBConfig->property('db_charset') //$charset='utf8'
		);
		return $MBDatabase;
	}

	public static function getRequest($name,$default=null){
		if(isset($_REQUEST[$name])){
			return $_REQUEST[$name];
		}else{
			return $default;
		}
	}

	public static function log($str){
		static $logger=null;
		if($logger==null){
			$logger=new MeinBlogLogger();
		}
		$logger->log($str);
	}

	public static function safeEmptyEcho($var,$default_for_empty=''){
		if(empty($var)){
			echo $default_for_empty;
		}else{
			echo $var;
		}
	}

	public static function lang($string_id,$language='',$not_echo=false){
		static $langAgent=null;
		if($langAgent==null){
			$langAgent=new MeinBlogLanguagePackage();
		}
		$str = $langAgent->getStringInLanguage($string_id,$language);
		if($not_echo){
			return $str;
		}else{
			echo $str;
		}
	}
}

?>