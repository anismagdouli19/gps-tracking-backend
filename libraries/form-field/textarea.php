<?php
class textarea extends form {
	function __construct(){
		$this->CI =& get_instance();
	}
	public function input($fieldKey,$fieldData){
		$lable = preg_replace("/<.*?>/", "", $fieldData->lable);
		
//		if( isset($fieldData->attributes) ){
//			
//			$fieldData->attributes = parent::attributes($fieldData->attributes);
//			
//		} else {
//			$fieldData->attributes = '';
//		}
		//bug($fieldData->attributes);exit;
		
		if(!isset($fieldData->dir) || $fieldData->dir=='') {
			$fieldData->dir = 'content';
		}
		$wysiwygScript = 'jQuery("textarea[name='.parent::protection($fieldKey).']").tinymce({imageDIR:encodeURI("'.base_url("resource/images_list?dir=$fieldData->dir").'")});';
		
		if(isset($fieldData->wysiwyg) && $fieldData->wysiwyg==true) {
			$this->CI->template->add_js_ready($wysiwygScript);
		}
		
		$auto_resize = ( (  isset($fieldData->auto_resize)&& $fieldData->auto_resize)?'auto-resize':'' );
		$html = '<textarea id="'.parent::protection($fieldKey).'" name="'.parent::protection($fieldKey).'" class="field-textarea '.$auto_resize.'" >'.$fieldData->value.'</textarea>';
		
		//return self::rowForm($controller,$fieldKey,$input);
		
		//$html.=parent::inputHidden($fieldKey,$fieldData->value);
		return $html;
	}
	
}
