<?php
class ordering {
	function __construct(){
		$this->CI =& get_instance();
		$this->form = get_instance()->form;
	}
	public function input($fieldKey,$fieldData){
		$model = (isset($fieldData->model) && $fieldData->model)?$fieldData->model:'Category_Model';
		$items = $this->CI->$model->items_count($fieldData->where);
		$fieldData->options =  array(
			'1'=>'-1-',
		);
		if($items > 0){
			for($i=2;$i<=$items;$i++){
				$fieldData->options[$i]="-$i-";
			}
		}


		return $this->form->input('inputSelect',$fieldKey,$fieldData);
	}

}
