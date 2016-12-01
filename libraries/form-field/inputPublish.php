<?php
class inputPublish extends form {
	function __construct(){
		$this->CI =& get_instance();
	}
	public function input($fieldKey,$fieldData){
		$fieldData->options =  array(
			'1'=>'Hiển Thị',
			'0'=>'Không Hiển Thị'
		);
		
		return parent::input('inputSelect',$fieldKey,$fieldData);
	}
	
}
