<?php
class multi_image extends form {
	function __construct(){
		$this->CI =& get_instance();
	}
	public function input($fieldKey,$fieldData){
		$lable = preg_replace("/<.*?>/", "", $fieldData->lable);
		$attribute = '';
		
		if(!isset($fieldData->dir) || $fieldData->dir=='') {
			$fieldData->dir = 'content';
		}
		$resourceURL = $this->CI->config->item('resource_domain');
		$directory = ($fieldData->dir!='')?"/".$fieldData->dir!='':'';
		$subDir=($fieldData->dir!='')?"?dir=$fieldData->dir":'';
		$script = "img.input = '".parent::protection($fieldKey)."[]';" 
				.'img.gets($(\'#'.$fieldKey.'-select-image\'));'
				;
		$this->CI->template->add_js_ready($script);
		
		
		$html = '<div class="input-field" >';
		
		if($fieldData->value || $fieldData->value !='' ){
			//$imgs = ;
			foreach(json_decode($fieldData->value) AS $v){
				$html.='<input type="text" name="'.parent::protection($fieldKey).'[]" value="'.$v.'" class="field-image text" readonly  />'
					.parent::button('remove','button',array('class'=>'removeinput button grey'));
			}
		} 
// 			.'<input type="text" name="'.parent::protection($fieldKey).'[]" value="" class="field-image text" readonly  />'
// 			.parent::button('remove','button',array('class'=>'removeinput button grey'))
// 			.'<input type="text" name="'.parent::protection($fieldKey).'[]" value="" class="field-image text" readonly  />'
// 			.parent::button('remove','button',array('class'=>'removeinput button grey'))
		$html.='<input type="text" name="'.parent::protection($fieldKey).'[]" value="" class="field-image text" readonly  />'
			.parent::button('select image','button',array('id'=>"$fieldKey-select-image",'class'=>'imagebutton button grey'))
			.'</div>'
		;
		
		//$html.= ;
		
		return $html;
	}
	
}
