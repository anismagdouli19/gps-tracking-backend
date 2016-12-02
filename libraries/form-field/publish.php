<?php
class publish {
	function __construct(){
		$this->CI =& get_instance();
		$this->form = get_instance()->form;
	}
	public function input($fieldKey,$fieldData){
		$fieldData->options =  array(
			'1'=>'Hiển Thị',
			'0'=>'Không Hiển Thị'
		);

		return $this->form->input('inputSelect',$fieldKey,$fieldData);
	}

}
