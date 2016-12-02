<?php 
class Article extends CI_Controller {
	function Article(){
		parent::__construct();
		$this->backend->checkLogin();
		$this->lang->load('article', 'vietnamese');
		$this->load->model('Article_Model');
		$this->load->model('Tag_Model');
		$this->load->model('Category_Model');
	}
	function index(){
		redirect('article/manager','refresh');
	}
	
	public function manager(){
		$this->page_title[] = lang('Article Manager');
		$action = $this->uri->segment(3);
		if($action){
			$this->backend->formField = self::articleFields();
			$opt = array(
					'item-name'=>'Article',
					'model'=>'Article_Model',
					'model-get'=>'getArticle',
					'model-update'=>'updateArticle',
					'uri-back'=>2
			);
			if($action=='add-new'){
				$this->page_title[] = lang('Article Add New');
				$this->backend->form($opt);
			}else if($action=='update'){
				$opt['id'] = $this->uri->segment(4);
				$this->page_title[] = lang('Article Update');
				$this->backend->form($opt);
			} else if ( ($action=='publish' || $action=='remove') && $this->input->post() ){
				$updateData = array( 'id'=>$this->input->post('id'), 'publish'=>$this->input->post('publish') );
				jsonData(array('action'=>( ($this->Article_Model->changeData($updateData) )?true:false )));
			}
		} else if ($this->input->get('format') =='json'){
			if($this->input->get('publish') !=1){
				$sWhere = array();
			} else {
				$sWhere['a.publish'] = 1;
			}
			
			if( $this->input->get('category') ){
				$sWhere['a.category'] = $this->input->get('category');
			}
			if($this->input->get('status')=='removed'){
				$sWhere['a.publish'] = '-1';
			} else {
				$sWhere['a.publish !='] = '-1';
			}
			$sWhere['c.type'] = 'art';
			return $this->backend->dataTableAjax(array(),'Article_Model','items_ajax',$sWhere);
		} else {
			$data['table'] = array(
					'id'=>array('ID',false,20,'center'),
					'title'=>array(lang('Title'),true,300),
					'category'=>array(lang('Category'),true,50),
					'publish_up'=>array(lang('Publish_up_title'),true,50,'center'),
					'status'=>array(lang('Status'),false,50,'center'),
					'view'=>array(lang('View Count'),false,60,'center'),
					'actions'=>array('Actions',false,100,'center'),
			);
			
			if( $this->input->get('category') ){
				$data['actions'][] = array('all'=>'All Article');
			}
			
			$data['actions'][] = array('add-new'=>'Article Add New');
			
			$data['title'] =  $this->lang->line('Article Data');
			$this->template->write_view('content','pages/data_table',$data);
			$this->template->write('title', $this->backend->pageTitle() );
		}
		$this->template->render();
	}
	
	public function news(){
		$this->page_title[] = $this->lang->line('News Manager');
		$action = $this->uri->segment(3);
		if($action){
			$this->backend->formField = self::articleFields();
			$this->backend->formField->category->cate_type = 'new';
			unset($this->backend-> formField->image_product);
// 			bug($this->backend->formField); exit;
			$opt = array(
					'item-name'=>'User',
					'model'=>'Article_Model',
					'model-get'=>'getArticle',
					'model-update'=>'updateArticle',
					'uri-back'=>2
			);
			if($action=='add-new'){
				$this->page_title[] = $this->lang->line('Add News');
				$this->backend->form($opt);
			}else if($action=='update'){
				$opt['id'] = $this->uri->segment(4);
				$this->page_title[] = $this->lang->line('News Update');
				$this->backend->form($opt);
			} else if ( ($action=='publish' || $action=='remove') && $this->input->post() ){
				$updateData = array( 'id'=>$this->input->post('id'), 'publish'=>$this->input->post('publish') );
				jsonData(array('action'=>( ($this->Article_Model->changeData($updateData) )?true:false )));
			}
		} else if ($this->input->get('format') =='json'){
			if($this->input->get('publish') !=1){
				$sWhere = array();
			} else {
				$sWhere['a.publish'] = 1;
			}
			$sWhere['c.type'] = 'new';
			if($this->input->get('status')=='removed'){
				$sWhere['a.publish'] = '-1';
			} else {
				$sWhere['a.publish !='] = '-1';
			}
			return $this->backend->dataTableAjax(array(),'Article_Model','items_ajax',$sWhere,'article/news/update/');
		} else {
			$data['table'] = array(
					'id'=>array('ID',false,20,'center'),
					'title'=>array(lang('Title'),true,300),
					'category'=>array(lang('Category'),true,50),
					'publish_up'=>array(lang('Publish_up_title'),true,50,'center'),
					'status'=>array(lang('Status'),false,50,'center'),
					'view'=>array(lang('View Count'),false,60,'center'),
					'actions'=>array('Actions',false,100,'center'),
			);
				
			if( $this->input->get('category') ){
				$data['actions'][] = array('all'=>'All Article');
			}
				
			$data['actions'][] = array('add-new'=>'Article Add New');
				
			$data['title'] =  $this->lang->line('User Data');
			$this->template->write_view('content','pages/data_table',$data);
			$this->template->write('title', $this->backend->pageTitle() );
		}
		$this->template->render();
	}
	
	private function articleFields(){
		$fields = array(
			'id'=>array('type'=>'hidden','value'=>0),
			'title'=>'',
			'alias'=>array('type'=>'alias'),
			'category'=>array('request'=>true, 'type'=>'category','cate_type'=>'art'),
			'image'=>array('type'=>'image'),
			'image_product'=>array('type'=>'multi_image'),
			'publish'=>array('type'=>'publish','value'=>1),
			'publish_up'=>array('type'=>'date'),
			'tags'=>array('type'=>'tags','category'=>'seo','table'=>'article','add_new'=>true),
			
			'meta_title'=>array('type'=>'textarea','title'=>'SEO Title'),
			'meta_desc'=>array('type'=>'textarea','title'=>'SEO Description'),
			'desciption'=>array('type'=>'textarea','auto_resize'=>true),
			'content'=>array('type'=>'textarea','wysiwyg'=>true),
			
		);
		return (object)$this->form->bindFields($fields);
	}
	
	public function category(){
		$this->page_title[] = $this->lang->line('Category Manager');
		$action = $this->uri->segment(3);
		if($action){
			$this->backend->formField = self::categoryFields();
			$opt = array(
					'item-name'=>'Category',
					'model'=>'Category_Model',
					'model-get'=>'getCategory',
					'model-update'=>'updateCategory',
					'uri-back'=>2
			);
			if($action=='add-new'){
				$this->page_title[] = lang('Category Add New');
				$this->backend->form($opt);
			}else if($action=='update'){
				$opt['id'] = $this->uri->segment(4);
				$this->page_title[] = $this->lang->line('Category Update');
				$this->backend->form($opt);
			} else if ( ($action=='publish' || $action=='remove') && $this->input->post() ){
				$updateData = array( 'id'=>$this->input->post('id'), 'publish'=>$this->input->post('publish') );
				jsonData(array('action'=>( ($this->Category_Model->changeData($updateData) )?true:false )));
			} 
		} else if ($this->input->get('format') =='json'){
			if($this->input->get('publish') !=1){
				$sWhere = array();
			} else {
				$sWhere['s.publish'] = 1;
			}
			return $this->backend->dataTableAjax(array(),'Category_Model','items_ajax',$sWhere);
		} else {
			$data['table'] = array(
				'id'=>array('ID',false,20,'center'),
				'title'=>array(lang('Title'),false,300),
				'status'=>array($this->lang->line('Status'),false,50,'center'),
				'count'=>array(lang('Article Count'),false,50,'center'),
				'actions'=>array('Actions',false,100,'center'),
			);
			$data['actions'][] = array('add-new'=>'Category Add New');
			$data['title'] =  lang('Category Manager');
			$data['items'] = $this->Category_Model->load_items('art','article/category/');
// 			bug($data['items']);exit;
			$this->template->write_view('content','pages/data_table_noajax',$data);
			$this->template->write('title', $this->backend->pageTitle() );
		}
		$this->template->render();
	}
	
	private function categoryFields(){
		$fields = array(
			'id'=>array('type'=>'hidden','value'=>0),
			'title'=>'',
			'type'=>array('type'=>'hidden','value'=>'art'),
			'alias'=>array('type'=>'alias'),
			'parent'=>array('type'=>'category','title'=>lang('Category Parent')),
			//'image'=>array('type'=>'image'),
			'desciption'=>array('type'=>'textarea'),
			'ordering'=>array('type'=>'ordering','where'=>array('type'=>'art')),
				
			'meta_title'=>array('type'=>'textarea','title'=>'SEO Title'),
			'meta_desc'=>array('type'=>'textarea','title'=>'SEO Description'),
			//'meta_keys'=>array('type'=>'textarea','title'=>'Meta Keyword'),
			'publish'=>array('type'=>'publish','title'=>lang('Publish')),
		);
		return (object)$this->form->bindFields($fields);
	}
	
	public function question(){
		$this->page_title[] = $this->lang->line('Question Manager');
		$action = $this->uri->segment(3);
		if($action){
			$this->backend->formField = self::questionFields();
			$opt = array(
					'item-name'=>'Question',
					'model'=>'Article_Model',
					'model-get'=>'getQuestion',
					'model-update'=>'updateQuestion',
					'uri-back'=>2
			);
			if($action=='add-new'){
				$this->page_title[] = $this->lang->line('Question Add New');
				$this->backend->form($opt);
			}else if($action=='update'){
				$opt['id'] = $this->uri->segment(4);
				$this->page_title[] = $this->lang->line('Question Update');
				$this->backend->form($opt);
			} else if ( ($action=='publish' || $action=='remove') && $this->input->post() ){
				$updateData = array( 'id'=>$this->input->post('id'), 'publish'=>$this->input->post('publish') );
				jsonData(array('action'=>( ($this->Article_Model->changeQuestion($updateData) )?true:false )));
			}
		} else if ($this->input->get('format') =='json'){
			
			if( $this->input->get('category') ){
				$sWhere['a.category'] = $this->input->get('category');
			}
			if($this->input->get('status')=='removed'){
				$sWhere['a.publish'] = '-1';
			} else {
				$sWhere['a.publish !='] = '-1';
			}
			return $this->backend->dataTableAjax(array(),'Article_Model','question_ajax',$sWhere);
		} else {
			$data['table'] = array(
					'id'=>array('ID',false,20,'center'),
					'title'=>array(lang('Title'),true,300),
					'category'=>array(lang('Category'),true,100),
					'status'=>array(lang('Status'),false,50,'center'),
					'actions'=>array('Actions',false,100,'center'),
			);
			if( $this->input->get('category') ){
				$data['actions'][] = array('all'=>'All Question');
			}
			
			$data['actions'][] = array('add-new'=>'Article Add New');
			$data['title'] =  $this->lang->line('User Data');
			$this->template->write_view('content','pages/data_table',$data);
			$this->template->write('title', $this->backend->pageTitle() );
		}
		$this->template->render();
	}
	
	private function questionFields(){
		$fields = array(
				'id'=>array('type'=>'hidden','value'=>0),
				'title'=>'',
				'alias'=>array('type'=>'alias'),
				'category'=>array('request'=>true, 'type'=>'category','cate_type'=>'que'),
				'content'=>array('type'=>'textarea','wysiwyg'=>true),
				'publish'=>array('type'=>'publish','value'=>1),
				'priority'=>array('type'=>'priority','value'=>1,'title'=>lang('Priority')),
		);
		return (object)$this->form->bindFields($fields);
	}
}