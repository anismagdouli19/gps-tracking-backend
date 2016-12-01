<?php
class inputCategory extends form {
	function __construct(){
		$this->CI =& get_instance();
	}
	public function input($fieldKey,$fieldData){
		if( !method_exists($this->CI, 'Category_Model') ){
			$this->CI->load->model('Category_Model');
		}
		$fieldData->cate_type = (isset($fieldData->cate_type))?$fieldData->cate_type:'art';
		$items = $this->CI->Category_Model->load_items($fieldData->cate_type);
// 		bug($items); exit;
		$fieldData->options =  array(
			'0'=>'- '.lang('No Category').' -',
		);
		foreach($items AS $cate){
			$fieldData->options[$cate[0]]=$cate[1];
		}
		
		return parent::input('inputSelect',$fieldKey,$fieldData);
	}
	
}
