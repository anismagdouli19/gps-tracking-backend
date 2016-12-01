<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class config extends CI_Controller {
	function config(){
		parent::__construct();
		$this->backend->checkLogin();
		$this->load->model('System_Model');
	}
	function index(){
		$this->page_title[] = $this->lang->line('Product Manager');
		$this->template->write_view('content','pages/under-construction');
		$this->template->write('title', $this->backend->pageTitle() );
		$this->template->render();
	}
	
	public function system(){
		$this->page_title[] = $this->lang->line('System Config');
		$this->backend->formField = self::configFields();
		$opt = array(
			'id'=>1,
			'item-name'=>'User',
			'model'=>'System_Model',
			'model-get'=>'getConfig',
			'model-update'=>'updateConfig',
			'uri-back'=>2
		);
		$this->backend->form($opt);
		$this->template->write('title', $this->backend->pageTitle() );
		$this->template->render();
	}
	
	private function configFields(){
		$fields = array(
			'home_title'=>array('type'=>'text','title'=>'Home Title'),
			'company_phone'=>array('type'=>'number','title'=>'ĐT Cố Định'),
			'email'=>array('type'=>'email'),
			'yahoo'=>'',
			'mobile'=>array('type'=>'number'),
			'yahoo_2'=>'',
			'mobile_2'=>array('type'=>'number'),
			//'email_2'=>array('type'=>'email'),
			'site-slogan'=>'',
			'company-name'=>'',
			'hotline'=>'',
			'address'=>'',
			'coordinates'=>array('type'=>'coordinates'),
			'ky-thuat-yahoo'=>array('type'=>'text','title'=>'Yahoo KỹT GPS XeMáy'),
			'ky-thuat-mobile'=>array('type'=>'number','title'=>'Mobile KỹT GPS XeMáy'),
			'ky-thuat-email'=>array('type'=>'email','title'=>'E-Mail KỹT GPS XeMáy'),
			//'home-title'=>NULL,
		);
		return (object)$this->form->bindFields($fields);
	}
}