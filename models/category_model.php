<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Category_Model extends CI_Model {
	function __construct(){
		parent::__construct();
		$this->cate = $this->load->database('mapgps',true);
	}
	
	public function load_items($type='art',$url='category/',$parent=0,$level = 1){
		$dataReturn=array();
		$data = self::items($type,$parent,'*');
		foreach($data AS $key=>$v){
			switch ($v->publish){
				//case -1: $status = 'removed'; break;
				case 0: $status = 'unpublish'; break;
				default : $status = 'publish'; break;
			}
			$publishButton = $this->backend->tableButtonAction($status);
			switch ($level){
				case '2': $levelTitle = ' -- '; break;
				default : $levelTitle = ''; break;
					
			}
			$dataReturn[] = array($v->id,anchor($url.'update/'.$v->id,$levelTitle.$v->title),$publishButton,0,null);
// 			$dataReturn[] = $v;
			if( self::items($type,$v->id,'*') ){
// 				$level++;
				
				$dataReturn = array_merge($dataReturn, self::load_items($type,$url,$v->id,$level+1) );
// 				$dataReturn = array_( $dataReturn, self::load_items($type,$url,$v->id,$level+1) );
// 				bug('get item id='.$v->id);
// 				bug($dataReturn);
// 				bug(self::load_items($type,$url,$v->id,$level));
// 				exit('call me');
			}
	
		}
		return $dataReturn;
	}
	
	protected function items($type='art',$parent=0,$publish = '*'){
		$this->cate->select('c.*')->from('category AS c');
		if($publish == '-1'){
			$this->cate->where('c.publish',-1);
		} else {
			$this->cate->where('c.publish !=',-1);
		}
		$this->cate->where('c.parent',$parent);
		$this->cate->where('c.type',$type);
		$this->cate->order_by('c.ordering ASC');
		return $this->cate->get()->result();
	}
	
	public function getCategory($id=0){
		if(!$id) return false;
		
		$this->cate->select('*')->from('category')->where(array('id'=>$id));
		return $this->cate->get()->row();
		
	}
	
	public function updateCategory($data){
// 		bug($data);exit;
		$data['id'] = (isset($data['id']))?$data['id']:0;
		$data['alias'] = (isset($data['alias']))?$data['alias']:url_title($data['title']);
		$data['parent'] = ( isset($data['parent']) )?$data['parent']:0;
		if($data['title'] == ''){
			return array('type'=>'error','text'=>'Must input Title for Category');
		} else {
			$checkExist = self::checkExist($data['alias'],$data['parent'],$data['id']);
			if($checkExist != false ){
				return $checkExist;
			}
		}
		
		if(isset($data['id']) && $data['id'] > 0 ){
			$data['modified']=date("Y-m-d H:i:s");
			$data['modified_by']=$this->session->userdata('uid');
			$this->cate->where('id', $data['id']);
			$this->cate->update('category', $data);
			return true;
		} else {
			$data['created']=date("Y-m-d H:i:s");
			$data['created_by']=$this->session->userdata('uid');
			$this->cate->insert('category', $data);
			return true;
		}
	}
	
	public function changeData($data){
		$data['modified']=date("Y-m-d H:i:s");
		$data['modified_by']=$this->session->userdata('uid');
		$this->cate->where('id', $data['id']);
		$this->cate->update('category', $data);
		return true;
	}
	
	protected function checkExist($alias,$parent,$id){
		$this->cate->select('*')->from('category')->where(array('id !='=>$id,'parent'=>$parent,'alias'=>$alias));
		$data = $this->cate->get()->row();
		if($data){
			return array('type'=>'error','text'=>'Category exist');
				
		}
		return false;
	}
	
	public function items_count($where=''){
		$this->cate->select('COUNT(*) as total')->from('category');
		if($where){
			$this->cate->where($where);
		}
		$data = $this->cate->get()->row();
		if($data && isset($data->total)){
			return $data->total;
		} else return 0;
	}
}