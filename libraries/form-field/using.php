<?php
class Using {
	function __construct(){
		$this->CI =& get_instance();
		$this->form = get_instance()->form;
	}
	public function input($fieldKey,$fieldData){
		$fieldData->options =  array(
			'1'=>lang('Enabled'),
			'0'=>lang('Disabled')
		);

		return $this->form->input('inputSelect',$fieldKey,$fieldData);
	}

}
