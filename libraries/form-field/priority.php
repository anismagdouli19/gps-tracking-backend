<?php
class priority extends form {
	function __construct(){
		$this->CI =& get_instance();
	}
	public function input($fieldKey,$fieldData){
		$fieldData->options =  array(
			'1'=>'Ưu Tiên',
			'0'=>'Mặc Định'
		);
		
		return parent::input('inputSelect',$fieldKey,$fieldData);
	}
	
}
