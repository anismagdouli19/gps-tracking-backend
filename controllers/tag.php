<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Tag extends CI_Controller {
	function Tag(){
		parent::__construct();
		$this->backend->checkLogin();
		$this->load->model('Tag_Model');
		$this->tag = self::tagFields();
	}
	public function ajax(){
		if($this->input->post()){
			$this->tag->title->value = $this->input->post('tag');
			$this->tag->alias->value = url_title($this->input->post('tag'));
			$this->tag->category->value = $this->input->post('category');
			$addNew = $this->Tag_Model->updateTag($this->tag);
			if($addNew){
				$tag = $this->Tag_Model->getTag($addNew,$this->tag->language->value);
				$tag['action'] = true;
			} else {
				$tag = array('action'=>false);
			}
			return jsonData($tag);
		} else if($this->input->get('category')){
			$data['tags'] = $this->Tag_Model->getTags(array('t.category'=>$this->input->get('category')));
			$data['taged'] = $this->Tag_Model->getRelationships(array('r.table'=>$this->input->get('table'),'r.taget_id'=>$this->input->get('id')));
			if($data['tags']){
				$data['action'] = true;
				return jsonData($data);
			}else {
				return jsonData(array('action'=>false));
			}
		}
	}
	
	protected function tagFields(){
		$fields = array(
				'id'=>array('type'=>'hidden','value'=>0),
				'alias'=>'',
				'title'=>'',
				'category'=>'adv',
				'publish'=>array('type'=>'publish'),
// 				'table'=>array('type'=>'hidden','value'=>'adv'),
		);
		if($this->input->get('lang')=='en'){
			$fields['language']['value']='en';
		}else {
			$fields['language']['value']='vi';
		}
		return (object)$this->form->bindFields($fields);
	}
}