<?php
class image {
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
		$script = ''
				.'jQuery("#'.$fieldKey.'-select-image").click(function(e){ '
					."e.preventDefault();"
					."var fm = $('<div/>').dialogelfinder({"
					."url : '".subdomain('assets_url')."/elfinder-2.0/php/connector.php',"
					."lang : 'en', width : 840,destroyOnClose : true,"
					."getFileCallback : function(files, fm) {"
						."jQuery('input[type=text][name=".$this->form->protection($fieldKey)."]').val(files.replace('".subdomain('resource_url')."/', ''));"
						." $('.ui-widget-overlay').remove(); "
					."},commandsOptions : {getfile : { oncomplete : 'close',folders : false}}"
					."}).dialogelfinder('instance');"
					.'return false;'
				.'});'
				;
		$this->CI->template->add_js_ready($script);

		$html = '<input type="text" name="'.$this->form->protection($fieldKey).'" value="'.$fieldData->value.'" class="field-image text" readonly  />';
		$html.= $this->form->button('select image','button',array('id'=>"$fieldKey-select-image",'class'=>'imagebutton button grey'));

		return $html;
	}

}
