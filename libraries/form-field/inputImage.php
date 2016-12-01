<?php
class inputImage extends form {
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
		$script = ''
				.'jQuery("#'.$fieldKey.'-select-image").click(function(e){ '
					."e.preventDefault();"
					."var fm = $('<div/>').dialogelfinder({"
					."url : '".$this->CI->config->item('assets_url')."/elfinder-2.0/php/connector.php',"
					."lang : 'en', width : 840,destroyOnClose : true,"
					."getFileCallback : function(files, fm) {"
						//.'alert(files);'
						."jQuery('input[type=text][name=".parent::protection($fieldKey)."]').val(files.replace('".$this->CI->config->item('resource_domain')."/', ''));"
					."},commandsOptions : {getfile : { oncomplete : 'close',folders : false}}"
					."}).dialogelfinder('instance');"
					.'return false;'
				.'});'
				;
		$this->CI->template->add_js_ready($script);
		
		$html = '<input type="text" name="'.parent::protection($fieldKey).'" value="'.$fieldData->value.'" class="field-image text" readonly  />';
		$html.= parent::button('select image','button',array('id'=>"$fieldKey-select-image",'class'=>'imagebutton button grey'));
		
		return $html;
	}
	
}
