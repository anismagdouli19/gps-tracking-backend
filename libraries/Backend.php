<?php

class Backend extends form {
	function __construct(){
		$this->CI =& get_instance();
	}
	function checkLogin(){
		if (!$this->CI->session->userdata('uid')){
			if($this->CI->uri->uri_string()!='administrator-login'){
				redirect('administrator-login','refresh',null,'r='.urlencode(current_url()) );
			}
		}
		if(!$this->CI->session->userdata('uid') && $this->CI->uri_string()!='administrator-login'){
			redirect('administrator-login','refresh');
		}
	}

	public function controllersActions($opt=null,$action=null){
		switch($action){
			case 'add-new':
			case 'update':
				self::form($opt); break;
			case 'remove':
				$this->CI->$opt['model']->$opt['model-update'](array('id'=>$opt['id'],'publish'=>-1));
				redirect($this->CI->uri->segment(1).DS.$this->CI->uri->segment(2), 'refresh');
				break;
			case 'view':
				self::formView($opt); break;
			default:
				exit('unknow action'); break;
		}
	}

	public function getSystemInfo(){
		$admin = $this->CI->Account_Model->userInfo($this->CI->session->userdata('uid'));
		return $admin;
	}
	public function pageTitle(){
		$title = 'Page title';
		if(is_array($this->CI->page_title) && count($this->CI->page_title) > 0 ){
			$title= $this->CI->page_title[count($this->CI->page_title)-1];
		} else if (isset($this->CI->page_title)){
			$title = $this->CI->page_title;
		}
		return $title;
	}

	public function notification($msg){
		$html='';
		if($msg && is_array($msg)){
			foreach($msg AS $note){
				$html.=	'<div class="notification note-attention">'
						.'<a title="Close notification" class="close" href="#">close</a>'
				;
				if($note['type']=='error' || $note['type']=='confirm-replace' )
					$html.='<p><strong>'.$this->CI->lang->line('Error').':</strong> '.$note['text'].'</p>';
				else if($note['type']=='message')
					$html.='<div class="ui-widget ui-msg"><div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-notice" style="float: left; margin-right: .3em;"></span><strong>'.$this->CI->lang->line('Message').':</strong> '.$note['text'].'</p></div></div>';

				$html.='</div>';
			}
		}
		return $html;
	}

	public function dataTableAjax($aColumns,$model,$getDataFunction,$sWhere=null,$editControl=''){
		//$aColumns = array('id','a.image','a.company','a.create','a.click');
		$sStart = $sLength =0;
		$sSearch = null;
		$sStart = $this->CI->input->post('iDisplayStart');
		$sLength = $this->CI->input->post('iDisplayLength');
		$sOrder = array();
		//if ( $this->CI->input->post('iSortCol_0')  ){
		for ( $i=0 ; $i< intval( $this->CI->input->post('iSortingCols') ) ; $i++ ){
			$indexSort = intval($this->CI->input->post('iSortCol_'.$i));
			if ( $this->CI->input->post("bSortable_$indexSort" ) == "true" ){
				//$sOrder[]=array( $aColumns[ $indexSort ],$this->CI->input->post('sSortDir_'.$i) );
				$sOrder[$aColumns[ $indexSort ]]=$this->CI->input->post('sSortDir_'.$i);
			}
		}
// 		bug($sOrder); exit('bug function');
		//}
		//$sWhere = "";
		// 		if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" ){
		// 			$sWhere .= mysql_real_escape_string( $_GET['sSearch'] );
		// 		}
		if( $this->CI->input->post('sSearch') ){
			$sSearch = $this->CI->input->post('sSearch');
		}

		$data = $this->CI->$model->$getDataFunction($sStart,$sLength,$sOrder,$sWhere,$editControl,$sSearch);
		$dataReturn =  array(
				"sEcho" => $this->CI->input->post('sEcho'),
				"iTotalRecords" => $data['totalRecords'],
				"iTotalDisplayRecords" => $data['totalRecords'],
				"aaData" => $data['data']
		);

		return jsonData($dataReturn);
	}

	public function tableButtonAction($action){
		switch ($action){
			case 'publish':
				$title = $this->CI->lang->line('Unpublish'); break;
			case 'unpublish':
				$title = $this->CI->lang->line('Publish'); break;
			case 'remove':
				$title = $this->CI->lang->line('Remove'); break;
			case 'removed':
			case 'new-car':
				$title = $this->CI->lang->line('Add Vehicle'); break;
			case 'changepass':
				$title = $this->CI->lang->line('Change Password'); break;
			default:
				$title = $this->CI->lang->line('Edit'); break;
		}
		$class_add = NULL;
		if( $this->CI->uri->segment(1)=='user' AND $this->CI->uri->segment(3)=='view' AND $action=='edit' ){
		    $class_add .= ' motor-edit';
		    return NULL;
		}
		return '<button class="icon_only item-'.$action.' '.$class_add.'" title="'.$title.'" alt="'.$title.'" action="item-'.$action.'"  ></button>';;
	}

	public function form($opt=array('item-name'=>'')){
		if($this->CI->input->post())  {
			self::formSubmit($opt);
		} else {
			if(isset($opt['id']) ){
				$title='Update '.$opt['item-name'];
				if(isset($this->CI->backend->formField->language) && $this->CI->backend->formField->language->value ){
					$item=$this->CI->$opt['model']->$opt['model-get']($opt['id'],$this->CI->backend->formField->language->value);
				} else {
					$item=$this->CI->$opt['model']->$opt['model-get']($opt['id']);
				}


				foreach($this->CI->backend->formField AS $key=>$val){
					if(isset($item->$key)&& $item->$key)
						$this->CI->backend->formField->$key->value = $item->$key;
				}
			} else {
				$title=$opt['item-name'].' Add New '.$opt['item-name'];
			}
// 			bug($this->CI->backend->formField); exit;
			if(count($this->CI->msg) > 0 && $this->CI->msg[0]['type'] == 'confirm-replace' ) {
				$this->CI->backend->formField->confirm = (object)array('type'=>'hidden','value'=>1);
				$data['buttons'][] = array('title'=>'Replace','type'=>'submit');
// 				bug($this->CI->backend->formField);exit;
			}
			$data['form']= $this->CI->backend->formField;
			$data['form_title']=$opt['item-name'].' data';
			$this->CI->template->write_view('content',(isset($opt['view']))?$opt['view']:'pages/form-edit',$data);
			$this->CI->template->write('title',$title);
		}
	}

	public function formSubmit($opt=array() ){
		foreach($this->CI->backend->formField AS $key=>$field){
			if( isset($field->request) && $field->request && $this->CI->input->post($key)==''){
				$this->CI->msg[] =array('type'=>'error','text'=>lang('Request input '.$key) );
			} else if(in_array($field->type,array('lengthUnit','dataUnit') )){
				$this->CI->backend->formField->$key->value = $this->CI->input->post($key).'_'.$this->CI->input->post($key.'_unit');
			} else if($field->type=='resolutionUnit'){
				$this->CI->backend->formField->$key->value = $this->CI->input->post($key.'_x').'x'.$this->CI->input->post($key.'_y');
			} else if( $field->type =='dimensionUnit' ){
				$this->CI->backend->formField->$key->value = $this->CI->input->post($key.'_x').'x'.$this->CI->input->post($key.'_y').'x'.$this->CI->input->post($key.'_z');
			} else if ( $field->type =='images' ){
				$this->CI->backend->formField->$key->value= self::doUpload($this->CI->form->protection($key),'../_resource/'.$this->CI->backend->formField->$key->dir);
			} else if ($key == 'alias'){
				$aliasValue = ($this->CI->input->post($key)) ? $this->CI->input->post($key): $this->CI->input->post('title');
				$this->CI->backend->formField->$key->value = url_title($aliasValue,'-',TRUE);
			} else
				$this->CI->backend->formField->$key->value = $this->CI->input->post($key);
		}
		if(count($this->CI->msg) > 0){
			$this->CI->input->unset_post();
			self::form($opt);
		} else {
			$dataValue = self::dataValue($this->CI->backend->formField);
			$action = $this->CI->$opt['model']->$opt['model-update']($dataValue);
			if(is_array($action) ){
				$this->CI->msg[] = $action;
				$this->CI->input->unset_post();
				self::form($opt);
			} else {
				redirect(return_last_uri($opt['uri-back']), 'refresh');
			}

		}
	}

	public function dataValue($data){
		$dataReturn = array();
		foreach($data AS $key=>$field){
// 			if(isset($field->value) && $field->value) $dataReturn[$key] = $field->value;
			if(isset($field->value)) $dataReturn[$key] = $field->value;
		}
		return $dataReturn;
	}

	public function build($fields=null,$button=''){
		$this->CI->form->inputClass = 'text fl-space2';
		$html='<form action="" method="post" >';
		if($fields){
			if(is_object($fields)){
				$this->CI->form->fields = $fields;
			} else if (is_array($fields)){
				$this->CI->form->fields = self::bindFields($fields);
			}
		}
		if($this->CI->form->fields){
			foreach($this->CI->form->fields AS $key=>$input){
				if($input->type=='hidden')
					$html .= self::inputHidden($key,$input->value);
				else
					$html .= self::rowInput($key,$input);
			}
		}
		if(is_array($button)){
			$html .='<div class="rule2"></div><div class="form-field clear">';
			foreach($button AS $b){
				$html .= self::button($b['title'],$b['type']);
			}

			$html .='</div>';
		} else {
			$html .='<div class="rule2"></div><div class="form-field clear">'
						.self::button('Save Data','submit')
						.self::button('Cancel','button',' class="button cancel" ')
					.'</div>';
		}
		$html .=self::inputToken();
		$html .='</form>';
		$script = ''
			." $('button.cancel').click(function(e){"
				.'window.history.back(-1); e.preventDefault();'
			."});"
		;
		$this->CI->template->add_js_ready($script);
		return $html;

	}

	public function showButtons($buttons){
		$html = '';
		if(is_array($buttons)){
			foreach($buttons AS $buton){
				$keyButton = array_keys($buton);
				if($keyButton && $keyButton[0] !=''){
					$html .= self::button($buton[$keyButton[0]],'button',' action="'.$keyButton[0].'" class="button green  fl" ');
				}
			}
		}
		return $html;
	}

	public function button($name,$type="button",$attributes = ''){
		if ($attributes != ''){
			$attributes = _parse_attributes($attributes);
		} else {
			$attributes = 'class="button red fl"';
		}
		switch($type){
			case 'submit':
				$html =	 '<input type="submit" value="'.$this->CI->lang->line($name).'" '.$attributes.' >';break;
			case 'button':
			default:
				$html =	 '<button  '.$attributes.' >'.$this->CI->lang->line($name).'</button>';break;
		}

		return $html;
	}

	public function rowInput($key,$fieldData){
		$styleRow='';

		$desc = '';

		$html = '<div class="form-field clear">'
				.'<label class="form-label size-120 fl-space2" for="'.self::protection($key).'">'.$fieldData->title.'';

		if( isset($fieldData->request) && $fieldData->request ){
			$html.='<span class="required">*</span>';
		}

		$html .='</label>'
				;
		$method = ( strtolower($fieldData->type) );
// 		echo FCPATH.'libraries'.DS.'form-field'.DS.$method.'.php' ;
		if( $fieldData->type == 'password'){
			$html .= parent::inputText($key,$fieldData);
// 		} else if (file_exists( FCPATH.'libraries'.DS.'form-field'.DS.$method.'.php' )){
// 			if (!class_exists($method)) {
// 				include_once FCPATH.'libraries'.DS.'form-field'.DS.$method.'.php';
// 			}

// 			$className = new $method;
// 			$html .= $className->input($key,$fieldData);
		} else {
			$html .= parent::input($method,$key,$fieldData);
		}
		$html.='</div>';
		return $html;
	}
}