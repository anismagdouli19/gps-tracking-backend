<?php
class fuel_type {
	static  $value = array(
		'ron-95'=>'Xăng RON 95',
		'ron-92'=>'Xăng RON 92',
		'diezen-005s'=>'Điêzen 0,05 S',
		'diezen-025s'=>'Điêzen 0,25 S'
	);
	function __construct(){
		$this->CI =& get_instance();
		$this->form = get_instance()->form;

	}

	public function input($fieldKey,$fieldData){
		$readonly = (isset($fieldData->disabled))?$fieldData->disabled:false;
		$lable = preg_replace("/<.*?>/", "", $fieldData->lable);
		$attribute = '';

		$html="<select class='dropdown ' name='".$this->form->protection($fieldKey)."' >";
		foreach(self::$value AS $key=>$val)
			$html.="<option value='$key' ".(($fieldData->value==$key)?" selected='selected' ":'').">$val</option>";
		$html.="</select>";
		if($readonly)
			return '<span>'.self::$value[$fieldData->value].'</span>';
		else
			//return "<input type=\"text\" name=\"".self::protection($fieldKey)."\" aria-label=\"".$lable."\"  placeholder=\"".$lable."\" value=\"$fieldData->value\" class=\"".$this->CI->form->inputClass."\" $attribute id=\"".self::protection($fieldKey)."\" />";
			return $html;
	}

}
