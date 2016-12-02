<?php
class multi_image {
	function __construct(){
		$this->CI =& get_instance();
		$this->form = get_instance()->form;
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
		$script = "img.input = '".$this->form->protection($fieldKey)."[]';"
				.'img.gets($(\'#'.$fieldKey.'-select-image\'));'
				;
		$this->CI->template->add_js_ready($script);


		$html = '<div class="input-field" >';

		if($fieldData->value || $fieldData->value !='' ){
			//$imgs = ;
			foreach(json_decode($fieldData->value) AS $v){
				$html.='<input type="text" name="'.$this->form->protection($fieldKey).'[]" value="'.$v.'" class="field-image text" readonly  />'
					.$this->form->button('remove','button',array('class'=>'removeinput button grey'));
			}
		}
// 			.'<input type="text" name="'.$this->form->protection($fieldKey).'[]" value="" class="field-image text" readonly  />'
// 			.$this->form->button('remove','button',array('class'=>'removeinput button grey'))
// 			.'<input type="text" name="'.$this->form->protection($fieldKey).'[]" value="" class="field-image text" readonly  />'
// 			.$this->form->button('remove','button',array('class'=>'removeinput button grey'))
		$html.='<input type="text" name="'.$this->form->protection($fieldKey).'[]" value="" class="field-image text" readonly  />'
			.$this->form->button('select image','button',array('id'=>"$fieldKey-select-image",'class'=>'imagebutton button grey'))
			.'</div>'
		;

		//$html.= ;

		return $html;
	}

}
