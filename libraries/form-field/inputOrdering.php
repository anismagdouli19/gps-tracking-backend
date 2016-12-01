<?php
class inputOrdering extends form {
	function __construct(){
		$this->CI =& get_instance();
	}
	public function input($fieldKey,$fieldData){
		$items = $this->CI->Category_Model->items_count($fieldData->where);
		$fieldData->options =  array(
			'1'=>'-1-',
		);
		if($items > 0){
			for($i=2;$i<=$items;$i++){
				$fieldData->options[$i]="-$i-";
			}
		}
		
		
		return parent::input('inputSelect',$fieldKey,$fieldData);
	}
	
}
