<?php
/**
* 
*/
class MBBasicModel
{
	protected $properties = array();

	protected $pdo=null;

	function __construct($preset=array())
	{
		if(!empty($preset)){
			$this->properties=$preset;
		}else{
			$this->properties=array();
		}

		$this->pdo=MeinBlog::getPDO();
	}

	public function property($name, $value = null) {
        if (func_num_args() > 1) {
            return $this->properties[$name] = $value;
        } else {
            return isset($this->properties[$name]) ? $this->properties[$name] : null;
        }
    }

}
?>