<?php
class inputUnit extends form {
	function __construct(){
		$this->CI =& get_instance();
	}
	public function input($fieldKey,$fieldData){
		$readonly = (isset($fieldData->disabled))?$fieldData->disabled:false;
		$lable = preg_replace("/<.*?>/", "", $fieldData->title);
		$attribute = '';
		$lableUnit = (isset($fieldData->lable) && $fieldData->lable!='' )?$fieldData->lable:'';
		$number = (isset($fieldData->unit) && $fieldData->unit =='number' )?'number':'';
		if($readonly)
			return '<span>'.$fieldData->value.'</span>';
		else 
			return "<input type=\"text\" name=\"".self::protection($fieldKey)."\" aria-label=\"".$lable."\"  placeholder=\"".$lable."\" value=\"$fieldData->value\" class=\"".$this->CI->form->inputClass." unit $number\" $attribute id=\"".self::protection($fieldKey)."\" />".$lableUnit;
	}
	
}
