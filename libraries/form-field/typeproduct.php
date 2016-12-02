<?php
class typeproduct extends form {
	static $type = array(
		'motor'=>'- Thiết bị VietTracker Nanowatt-'
	);
	function __construct(){
		$this->CI =& get_instance();
	}
	public function input($fieldKey,$fieldData,$max='+5Y',$min='-12M'){
		
		$readonly = (isset($fieldData->disabled))?$fieldData->disabled:false;
		$lable = preg_replace("/<.*?>/", "", $fieldData->lable);
		$attribute = '';
	
		if($readonly)
			return '<span>'.$fieldData->value.'</span>';
		else {
			$data = config('producttype');
			
			$fieldData->options =  (isset($data) && is_array($data) )?$data:self::$type;
			
			return parent::input('inputSelect',$fieldKey,$fieldData);
		}
		
	}
	
}
