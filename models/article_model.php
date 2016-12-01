<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Article_Model extends CI_Model {
	function __construct(){
		parent::__construct();
		$this->a = $this->load->database('mapgps',true);
	}
	
	public function items_ajax($limitF=0,$limitTo=5,$order='',$where='',$editControl=''){
		$editControl = (!$editControl)?'article/manager/update/':$editControl;
		$limitTo = ($limitTo)?$limitTo:10;
		$dataReturn=array();
		$this->a->select('a.*, c.title AS category_title')->from('article AS a')->where('a.publish !=',-1);
		$this->a->join('category AS c', 'c.id = a.category', 'left');
		if(is_array($where)){
			foreach($where AS $key=>$item)
				$this->a->where($key,$item);
		} //else {
		//	$this->a->where('a.publish !=',-1);
		//}
		
		if(is_array($order)){
			foreach($order AS $o){
				$this->a->order_by($o[0],$o[1]);
			}
		} else {
			$this->a->order_by('a.publish_up DESC ');
		}
		
		
		
		$this->a->flush_cache();
		$this->a->limit($limitTo,$limitF);
		$data = $this->a->get()->result();
		
		$this->a->select('a.*')->from('article AS a')->where('a.publish !=',-1);
		$this->a->join('category AS c', 'c.id = a.category', 'left');
		if(is_array($where)){
			foreach($where AS $key=>$item)
				$this->a->where($key,$item);
		} else {
			$this->a->where('a.publish !=',-1);
		}
		$this->a->stop_cache();
		$dataReturn['totalRecords']=$this->a->count_all_results();
		 
		$dataReturn['data']=array();
		
		foreach($data AS $key=>$v){
			switch ($v->publish){
				//case -1: $status = 'removed'; break;
				case 0: $status = 'unpublish'; break;
				default : $status = 'publish'; break;
			}
			$publishButton = $this->backend->tableButtonAction($status);
			
			$dataReturn['data'][] = array($v->id,anchor($editControl.$v->id,$v->title),anchor('article/manager',$v->category_title,null,'category='.$v->category),$v->publish_up,$publishButton,$v->view,null);
		
		}
		return $dataReturn;
	}
	
	public function getArticle($id){
		if( !$id) return false;
		$this->a->select('*')->from('article')->where(array('id'=>$id));
		return $this->a->get()->row();
	}
	
	public function updateArticle($data){
		
		$data['id'] = (isset($data['id']))?$data['id']:0;
		$data['alias'] = (isset($data['alias']))?$data['alias']:url_title($data['title']);
		if(isset($data['image_product']) && is_array($data['image_product'])){
			foreach($data['image_product'] AS $index=> $v){
				if(!$v || $v == null ){
					unset($data['image_product'][$index]);
				}
			}
			$data['image_product'] = json_encode($data['image_product']);
		}
// 		bug($data);exit;
		if($data['title'] == ''){
			return array('type'=>'error','text'=>'Must input Title for Artist');
		} else {
			$checkExist = self::checkExist($data['alias'],$data['category'],$data['id']);
			if($checkExist != false ){
				return $checkExist;
			}
		}
		if(!isset($data['publish_up']) || !$data['publish_up']){
			$data['publish_up'] = date("Y-m-d H:i:s");
		}
		
		if(isset($data['tags'])){
			$tags = $data['tags'];
			unset($data['tags']);
		} else {
			$tags=null;
		}
		
		if(isset($data['id']) && $data['id'] > 0 ){
			$data['modified']=date("Y-m-d H:i:s");
			$data['modified_by']=$this->session->userdata('uid');
			$this->a->where('id', $data['id']);
			$this->a->update('article', $data);
			$this->Tag_Model->updateRelationships($tags,$data['id'],'article');
			return true;
		} else {
			$data['created']=date("Y-m-d H:i:s");
 			$data['created_by']=$this->session->userdata('uid');
			$this->a->insert('article', $data);
			$this->Tag_Model->updateRelationships($tags,$this->a->insert_id(),'article');
			return true;
		}
	}
	
	public function changeData($data){
		$data['modified']=date("Y-m-d H:i:s");
		$data['modified_by']=$this->session->userdata('uid');
		$this->a->where('id', $data['id']);
		$this->a->update('article', $data);
		return true;
	}
	
	protected function checkExist($alias,$category,$id){
		//$this->CI =& get_instance();
		/*
		$this->user->select('*')->from('user')->where(array('id !='=>$id,'email'=>$mail));
		$data = $this->user->get()->row();
		if($data){
			return array('type'=>'error','text'=>'User using this email exist');
			 
		}
		*/
		$this->a->select('*')->from('article')->where(array('id !='=>$id,'category'=>$category,'alias'=>$alias));
		$data = $this->a->get()->row();
		if($data){
			return array('type'=>'error','text'=>'Article exist');
			
		}
		return false;
	}
	
	
	public function question_ajax($limitF=0,$limitTo=5,$order='',$where=''){
		$limitTo = ($limitTo)?$limitTo:10;
		$dataReturn=array();
		$this->a->select('a.*, c.title AS category_title')->from('question AS a');
		$this->a->join('category AS c', 'c.id = a.category', 'left');
		if(is_array($where)){
			foreach($where AS $key=>$item)
				$this->a->where($key,$item);
		} else {
			$this->a->where('a.publish !=',-1);
		}
		
		if(is_array($order)){
			foreach($order AS $o){
				$this->a->order_by($o[0],$o[1]);
			}
		} else {
			$this->a->order_by('a.title DESC ');
		}
		
		
		
		$this->a->flush_cache();
		$this->a->limit($limitTo,$limitF);
		$data = $this->a->get()->result();
		
		$this->a->select('a.*')->from('question AS a')->where('a.publish !=',-1);
		if(is_array($where)){
			foreach($where AS $key=>$item)
				$this->a->where($key,$item);
		} else {
			$this->a->where('a.publish !=',-1);
		}
		$this->a->stop_cache();
		$dataReturn['totalRecords']=$this->a->count_all_results();
		 
		$dataReturn['data']=array();
		
		foreach($data AS $key=>$v){
			switch ($v->publish){
				//case -1: $status = 'removed'; break;
				case 0: $status = 'unpublish'; break;
				default : $status = 'publish'; break;
			}
			$publishButton = $this->backend->tableButtonAction($status);
			
			$dataReturn['data'][] = array($v->id,anchor('article/question/update/'.$v->id,$v->title),anchor('article/question',$v->category_title,null,'category='.$v->category),$publishButton,0,null);
		
		}
		return $dataReturn;
	}
	
	public function getQuestion($id){
		if( !$id) return false;
		$this->a->select('*')->from('question')->where(array('id'=>$id));
		return $this->a->get()->row();
	}
	
	public function updateQuestion($data){
		$data['id'] = (isset($data['id']))?$data['id']:0;
		$data['alias'] = (isset($data['alias']))?$data['alias']:url_title($data['title']);
		if($data['title'] == ''){
			return array('type'=>'error','text'=>'Must input Title for Question');
		} else {
			$checkExist = self::checkExist($data['alias'],$data['category'],$data['id']);
			if($checkExist != false ){
				return $checkExist;
			}
		}
		
		if(isset($data['id']) && $data['id'] > 0 ){
			$data['modified']=date("Y-m-d H:i:s");
			$data['modified_by']=$this->session->userdata('uid');
			$this->a->where('id', $data['id']);
			$this->a->update('question', $data);
			return true;
		} else {
			$data['created']=date("Y-m-d H:i:s");
 			$data['created_by']=$this->session->userdata('uid');
			$this->a->insert('question', $data);
			return true;
		}
	}
	
	public function changeQuestion($data){
		$data['modified']=date("Y-m-d H:i:s");
		$data['modified_by']=$this->session->userdata('uid');
		$this->a->where('id', $data['id']);
		$this->a->update('question', $data);
		return true;
	}
	
	protected function checkQuestionExist($alias,$category,$id){
		$this->a->select('*')->from('question')->where(array('id !='=>$id,'category'=>$category,'alias'=>$alias));
		$data = $this->a->get()->row();
		if($data){
			return array('type'=>'error','text'=>'Question exist');
		}
		return false;
	}
}