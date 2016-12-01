<?php
class adv_type extends form {
	function __construct(){
		$this->CI =& get_instance();
	}
	public function input($fieldKey,$fieldData){
		if( !method_exists($this->CI, 'Category_Model') ){
			$this->CI->load->model('Category_Model');
		}
		$items = $this->CI->Category_Model->load_items('adv');
		$fieldData->options =  array(
			'0'=>' - Chọn Vị Trí - ',
		);
		foreach($items AS $cate){
			$fieldData->options[$cate[0]]=$cate[1];
		}
		
		return parent::input('inputSelect',$fieldKey,$fieldData);
	}
	
}
