<?php
/**
* MeinBlogLogger
* --------------
* 20150926 
* Copyright 2015 Sinri Edogawa 
*/
class MeinBlogLogger 
{
	
	var $log_name;

	function __construct($log_file=null){
		if(empty($log_file)){
			$log_file=__DIR__.'/../log/'.strftime("%Y%m%d",time()).'.log';
		}
		$this->log_name=$log_file;
	}

	function  log_result($file,$word) 
	{
	    $fp = fopen($file,"a");
	    flock($fp, LOCK_EX) ;
	    fwrite($fp,"".strftime("%Y-%m-%d-%H:%M:%S",time())." | ".$word.PHP_EOL);
	    flock($fp, LOCK_UN);
	    fclose($fp);
	}

	function log($word){
		if(is_array($word) || is_object($word)){
			$this->log_result($this->log_name,json_encode($word));
		}else{
			$this->log_result($this->log_name,$word);
		}
	}
}
?>