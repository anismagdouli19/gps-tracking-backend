<?php
class Using extends form {
	function __construct(){
		$this->CI =& get_instance();
	}
	public function input($fieldKey,$fieldData){
		$fieldData->options =  array(
			'1'=>lang('Enabled'),
			'0'=>lang('Disabled')
		);
		
		return parent::input('inputSelect',$fieldKey,$fieldData);
	}
	
}
