<?php
class date extends form {
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
			$script='
			
        $( "input[name='.self::protection($fieldKey).']" ).datepicker({
            showOtherMonths: true, selectOtherMonths: true, showButtonPanel: true, changeMonth: true, changeYear: true,
            showOn: "both",  buttonImageOnly: true, buttonText:"Select Date",dateFormat: "yy-mm-dd",
            buttonImage: "'.subdomain('assets_url').'/images/date-times.png",
            maxDate: "'.$max.'", minDate: "'.$min.'"
        });
			
    ';
			$this->CI->template->add_js_ready($script);
			return "<input type=\"text\" name=\"".self::protection($fieldKey)."\" aria-label=\"".$lable."\"  placeholder=\"".$lable."\" value=\"$fieldData->value\" class=\"date-input ".$this->CI->form->inputClass."\" $attribute id=\"".self::protection($fieldKey)."\" />";
		}
		
	}
	
}
