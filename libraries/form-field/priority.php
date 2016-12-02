<?php
class priority {
	function __construct(){
		$this->CI =& get_instance();
		$this->form = get_instance()->form;
	}
	public function input($fieldKey,$fieldData){
		$fieldData->options =  array(
			'1'=>'Ưu Tiên',
			'0'=>'Mặc Định'
		);

		return $this->form->input('inputSelect',$fieldKey,$fieldData);
	}

}
