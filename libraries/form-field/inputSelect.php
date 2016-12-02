<?php
class inputSelect extends form {
	static  $value = array(
		'1'=>'Yes',
		'0'=>'No'
	);
	function __construct(){
		$this->CI =& get_instance();
		
	}
	
	public function input($fieldKey,$fieldData,$options=null){
		$readonly = (isset($fieldData->disabled))?$fieldData->disabled:false;
		$lable = preg_replace("/<.*?>/", "", $fieldData->lable);
		$attribute = '';
		$valueOptions = ($fieldData->options && is_array($fieldData->options))?$fieldData->options:self::$value;
		
		$html="<select class='dropdown ' name='".self::protection($fieldKey)."' >";
		foreach($valueOptions AS $key=>$val)
			$html.="<option value='$key' ".(($fieldData->value==$key)?" selected='selected' ":'').">$val</option>";
		$html.="</select>";
		if($readonly)
			return '<span>'.$fieldData->value.'</span>';
		else 
			//return "<input type=\"text\" name=\"".self::protection($fieldKey)."\" aria-label=\"".$lable."\"  placeholder=\"".$lable."\" value=\"$fieldData->value\" class=\"".$this->CI->form->inputClass."\" $attribute id=\"".self::protection($fieldKey)."\" />";
			return $html;
	}
	
}
