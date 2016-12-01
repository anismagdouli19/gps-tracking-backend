<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class product extends CI_Controller {
	function product(){
		parent::__construct();
		$this->backend->checkLogin();
// 		$this->load->model('User_Model');
	}
	function index(){
		redirect('product/manager','refresh');
	}
	
	public function manager(){
		$this->page_title[] = $this->lang->line('Product Manager');
		$this->template->write_view('content','pages/under-construction');
		$this->template->write('title', $this->backend->pageTitle() );
		$this->template->render();
	}
}