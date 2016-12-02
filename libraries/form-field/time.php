<?php
class time extends form {
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
			$script=' $( "#alt_example_4" ).datetimepicker({'
            .'altField: "#'.self::protection($fieldKey).'",'
			.'altFieldTimeOnly: false,altFormat: "yy-mm-dd",altTimeFormat: "HH:m",'
			
        .'}).css({"display": "block","padding":"30px 0 0 210", "position": "relative"})';
			if($fieldData->value){
				$script.='.datetimepicker("setDate", new Date('.(strtotime($fieldData->value)*1000 ).'))';
			}
			$script.=';';
			$this->CI->template->add_js_ready($script);
			return "<input value=\"".date("Y-m-d H:i", strtotime($fieldData->value) )."\" id=\"".self::protection($fieldKey)."\" type=\"text\" name=\"".self::protection($fieldKey)."\" aria-label=\"".$lable."\"  placeholder=\"".$lable."\"  class=\"date-input ".$this->CI->form->inputClass."\" $attribute style=\"width:260px\"  />"
			.'<span id="alt_example_4" ></span>'
			;
		}
		
	}
	
}
