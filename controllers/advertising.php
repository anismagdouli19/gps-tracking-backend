<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Advertising extends CI_Controller {
	function Advertising(){
		parent::__construct();
		$this->backend->checkLogin();
		$this->load->model('Adv_Model');
		$this->load->model('Tag_Model');
	}
	function index(){
		redirect('advertising/manager','refresh');
	}
	
	public function manager(){
		$this->page_title[] = $this->lang->line('Adv Manager');
		$action = $this->uri->segment(3);
		if($action){
			$this->backend->formField = self::advFields();
			$opt = array(
					'item-name'=>'Adv',
					'model'=>'Adv_Model',
					'model-get'=>'getAdv',
					'model-update'=>'updateAdv',
					'uri-back'=>2
			);
			if($action=='add-new'){
				$this->page_title[] = $this->lang->line('Adv Add New');
				$this->backend->form($opt);
			}else if($action=='update'){
				$opt['id'] = $this->uri->segment(4);
				$this->page_title[] = $this->lang->line('Adv Update');
				$this->backend->form($opt);
			} else if ( ($action=='publish' || $action=='remove') && $this->input->post() ){
				$updateData = array( 'id'=>$this->input->post('id'), 'publish'=>$this->input->post('publish') );
				jsonData(array('action'=>( ($this->Adv_Model->updateAdv($updateData) )?true:false )));
			} 
		} else if ($this->input->get('format') =='json'){
			if($this->input->get('publish') !=1){
				$sWhere = array();
			} else {
				$sWhere['s.publish'] = 1;
			}
			return $this->backend->dataTableAjax(array(),'Adv_Model','items_ajax',$sWhere);
		} else {
			$data['table'] = array(
					'id'=>array('ID',false,20,'center'),
					'title'=>array(lang('Title'),true,300),
					'type'=>array(lang('Adv_type'),true,100),
					'status'=>array($this->lang->line('Status'),false,50,'center'),
					'click'=>array(lang('Click Count'),true,50,'center'),
					'actions'=>array('Actions',false,100,'center'),
			);
			$data['actions'][] = array('add-new'=>'Adv Add New');
// 			$data['title'] =  $this->lang->line('Adv Data');
			$this->template->write_view('content','pages/data_table',$data);
			$this->template->write('title', $this->backend->pageTitle() );
		}
		$this->template->render();
	}
	
	private function advFields(){
		$fields = array(
			'id'=>array('type'=>'hidden','value'=>0),
			'title'=>'',
			'url'=>'',
			'adv_type'=>array('type'=>'category','cate_type'=>'adv'),
			'tags'=>array('type'=>'tags','add_new'=>false),
			'image'=>array('type'=>'image'),
			'content'=>array('type'=>'textarea','auto_resize'=>true),
			'publish'=>array('type'=>'publish','value'=>1),
		);
		return (object)$this->form->bindFields($fields);
	}
	
	public function slides(){
		$this->page_title[] = $this->lang->line('Slides Manager');
		$action = $this->uri->segment(3);
		if($action){
			$this->backend->formField = self::slideFields();
			$opt = array(
					'item-name'=>'Adv',
					'model'=>'Adv_Model',
					'model-get'=>'getSlide',
					'model-update'=>'updateAdv',
					'uri-back'=>2
			);
			if($action=='add-new'){
				$this->page_title[] = $this->lang->line('Slide Add New');
				$this->backend->form($opt);
			}else if($action=='update'){
				$opt['id'] = $this->uri->segment(4);
				$this->page_title[] = $this->lang->line('Slide Update');
				$this->backend->form($opt);
			} else if ( ($action=='publish' || $action=='remove') && $this->input->post() ){
				$updateData = array( 'id'=>$this->input->post('id'), 'publish'=>$this->input->post('publish') );
				jsonData(array('action'=>( ($this->Adv_Model->updateAdv($updateData) )?true:false )));
			}
		} else if ($this->input->get('format') =='json'){
			if($this->input->get('publish') !=1){
				$sWhere = array();
			} else {
				$sWhere['s.publish'] = 1;
			}
			return $this->backend->dataTableAjax(array(),'Adv_Model','slide_ajax',$sWhere);
		} else {
			$data['table'] = array(
					'id'=>array('ID',false,20,'center'),
					'screen'=>array(lang('Screen'),false,250,'center'),
					'title'=>array(lang('Title'),true,150),
					'status'=>array($this->lang->line('Status'),false,50,'center'),
					'click'=>array(lang('Click Count'),true,50,'center'),
					'actions'=>array('Actions',false,100,'center'),
			);
			$data['actions'][] = array('add-new'=>'Slide - Add New');
			// 			$data['title'] =  $this->lang->line('Adv Data');
			$this->template->write_view('content','pages/data_table',$data);
			$this->template->write('title', $this->backend->pageTitle() );
		}
		$this->template->render();
	}
	
	private function slideFields(){
		$fields = array(
			'id'=>array('type'=>'hidden','value'=>0),
			'adv_type'=>array('type'=>'hidden','value'=>0),
			'title'=>'',
			'url'=>'',
			'content'=>array('type'=>'textarea','auto_resize'=>true),
			'image'=>array('type'=>'textarea','auto_resize'=>true),
			'script'=>array('type'=>'textarea','auto_resize'=>true),
			'loop_time'=>array('type'=>'number','value'=>1000,'title'=>lang('Loop Time')),
			'ordering'=>array('type'=>'ordering','where'=>array('adv_type'=>0),'model'=>'Adv_Model'),
			'publish'=>array('type'=>'publish','value'=>1),
		);
		
		return (object)$this->form->bindFields($fields);
	}
}
	?>