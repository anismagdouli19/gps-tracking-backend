<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Adv_Model extends CI_Model {
	function __construct(){
		parent::__construct();
		$this->adv = $this->load->database('mapgps',true);
	}
	
	public function items_ajax($limitF=0,$limitTo=5,$order='',$where=''){
		$limitTo = ($limitTo)?$limitTo:10;
		$dataReturn=array();
		$this->adv->select('a.*')->from('advertising AS a')->where( array('a.publish !='=>-1,'a.adv_type !='=>0) );
// 		$this->user->join('portfolio_lang AS l', 'l.tagetid = po.id', 'left');
// 		$this->user->join('category AS c', 'c.id = po.category', 'left');
		if(is_array($where)){
			foreach($where AS $key=>$item)
				$this->adv->where($key,$item);
		}
		
		if(is_array($order)){
			foreach($order AS $o){
				$this->adv->order_by($o[0],$o[1]);
			}
		} else {
			$this->adv->order_by('a.ordering ASC ');
		}
		
		$this->adv->flush_cache();
		$this->adv->limit($limitTo,$limitF);
		$data = $this->adv->get()->result();
		
		$this->adv->select('a.*')->from('advertising AS a')->where(array('a.publish !='=>-1,'a.adv_type !='=>0) );
		if(is_array($where)){
			foreach($where AS $key=>$item)
				$this->adv->where($key,$item);
		}
		$this->adv->stop_cache();
		$dataReturn['totalRecords']=$this->adv->count_all_results();
		 
		$dataReturn['data']=array();
		
		foreach($data AS $key=>$v){
			switch ($v->publish){
				//case -1: $status = 'removed'; break;
				case 0: $status = 'unpublish'; break;
				default : $status = 'publish'; break;
			}
			$publishButton = $this->backend->tableButtonAction($status);
			$area = 'No Area';
			if( !method_exists($this, 'Category_Model') ){
				$this->load->model('Category_Model');
			}
			$area = $this->Category_Model->getCategory($v->adv_type);
			if($area){
				$area = $area->title;
			}
			$dataReturn['data'][] = array($v->id,anchor('advertising/manager/update/'.$v->id,$v->title),$area,$publishButton,$v->click,null);
		
		}
		return $dataReturn;
	}
	
	public function items_count($where=''){
		$this->adv->select('COUNT(*) as total')->from('advertising');
		if($where){
			$this->adv->where($where);
		}
		$data = $this->adv->get()->row();
		if($data && isset($data->total)){
			return $data->total;
		} else return 0;
	}
	
	public function getAdv($id){
		if( !$id) return false;
		$this->adv->select('*')->from('advertising')->where(array('id'=>$id));
		return $this->adv->get()->row();
	}
	
	public function updateAdv($data){
// 		bug($data);exit;
		$data['id'] = (isset($data['id']))?$data['id']:0;
		if(isset($data['tags'])){
			$tags = $data['tags'];
			unset($data['tags']);
		} else {
			$tags=null;
		}
		
		
		if(isset($data['id']) && $data['id'] > 0 ){
			$data['modified']=date("Y-m-d H:i:s");
			$data['modified_by']=$this->session->userdata('uid');
			$this->adv->where('id', $data['id']);
			$this->adv->update('advertising', $data);
			//echo $this->adv->last_query();exit;
			$this->Tag_Model->updateRelationships($tags,$data['id'],'advertising');
			return true;
		} else {
			$data['created']=date("Y-m-d H:i:s");
 			$data['created_by']=$this->session->userdata('uid');
			$this->adv->insert('advertising', $data);
			$this->Tag_Model->updateRelationships($tags,$this->adv->insert_id(),'advertising');
			return true;
		}
	}
	
	public function slide_ajax($limitF=0,$limitTo=5,$order='',$where=''){
		$limitTo = ($limitTo)?$limitTo:10;
		$dataReturn=array();
		$this->adv->select('a.*')->from('advertising AS a')->where( array('a.publish !='=>-1,'a.adv_type'=>0));
		if(is_array($where)){
			foreach($where AS $key=>$item)
				$this->adv->where($key,$item);
		}
		
		if(is_array($order)){
			foreach($order AS $o){
				$this->adv->order_by($o[0],$o[1]);
			}
		}
		$this->adv->order_by('a.ordering ASC ');
		$this->adv->flush_cache();
		$this->adv->limit($limitTo,$limitF);
		$data = $this->adv->get()->result();
		
		$this->adv->select('a.*')->from('advertising AS a')->where( array('a.publish !='=>-1,'a.adv_type'=>0));
		if(is_array($where)){
			foreach($where AS $key=>$item)
				$this->adv->where($key,$item);
		}
		$this->adv->stop_cache();
		$dataReturn['totalRecords']=$this->adv->count_all_results();
		 
		$dataReturn['data']=array();
		
		foreach($data AS $key=>$v){
			switch ($v->publish){
				//case -1: $status = 'removed'; break;
				case 0: $status = 'unpublish'; break;
				default : $status = 'publish'; break;
			}
			$publishButton = $this->backend->tableButtonAction($status);
			
			$dataReturn['data'][] = array($v->id,img_resoure('slides-show/demo/slide-'.$v->id.'.png',250,65,'resource_url',false),anchor('advertising/slides/update/'.$v->id,$v->title),$publishButton,$v->click,null);
		
		}
		return $dataReturn;
	}
	
	public function getSlide($id){
		if( !$id) return false;
		$this->adv->select('*')->from('advertising')->where(array('id'=>$id,'adv_type'=>0));
		return $this->adv->get()->row();
	}
	
	
}