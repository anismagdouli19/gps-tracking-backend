<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Tag_model extends CI_Model {
	function Tag_model(){
		parent::__construct();
		$this->tag = $this->load->database('mapgps',TRUE);
	}
	
	public function updateTag($data){
		if(!$data) return false;
		foreach($data AS $key=>$field){
			if($field->value !=null)
				$queryData[$key] = $field->value;
		}
		$exist = self::checkTagExist($queryData); 
		if($exist){
			return $exist;
		}
		$content = array(
			'title'=>$queryData['title'],
			'language'=>$queryData['language'],
		);
		unset($queryData['title']);
		unset($queryData['language']);
		
		if(isset($queryData['id']) && $queryData['id'] > 0 ){
			
			$this->tag->where('id', $queryData['id']);
			$this->tag->update('tag', $queryData);
		
			$this->tag->select('l.*')->from('tag_lang AS l')->where(array('l.tag_id'=>$queryData['id'],'l.language'=>$content['language']));
			$query = $this->tag->get();
		
			$content['tag_id']=$queryData['id'];
			if($query->row()){
				$this->tag->where(array('tag_id'=>$queryData['id'],'language'=>$content['language']));
				$this->tag->update('tag_lang', $content);
			}else {
				$this->tag->insert('tag_lang', $content);
			}
			return $queryData['id'];
		} else {
			$queryData['created']=date("Y-m-d H:i:s");
			$queryData['created_by']=$this->session->userdata('uid');
			$query['run']  = $this->tag->insert('tag', $queryData);
		
			$content['tag_id']=$this->tag->insert_id();
			$this->tag->insert('tag_lang', $content);
			return $content['tag_id'];
		}
		return false;
	}
	
	public function checkTagExist($tag){
		$this->tag->select('t.*')->from('tag AS t')->where(array('t.alias'=>$tag['alias'],'t.category'=>$tag['category']));
		$data = $this->tag->get()->row();
		if($data) return $data->id;
		else return false;
	}
	
	public function getTag($id,$language){
		$this->tag->select('t.*')->from('tag AS t')->where(array('t.id'=>$id));
		$data = $this->tag->get()->row();
		if($data){
			$this->tag->select('l.*')->from('tag_lang AS l')->where(array('l.tag_id'=>$id,'l.language'=>$language));
			$content = $this->tag->get()->row();
			if($content){
				return array('id'=>$data->id,'title'=>$content->title);
			}
			return false;
		}else {
			return false;
		}
	}
	
	public function getTags($where,$language=''){
		$this->tag->select('t.id, l.title AS title')->from('tag AS t');
		$this->tag->join('tag_lang AS l', 'l.tag_id = t.id', 'left');
		$this->tag->where($where);
// 		$this->tag->where('l.language',$language);
		$data = $this->tag->get()->result();
// 		bug($this->tag->last_query());exit('bug quqery');
		return $data;
	}
	
	public function updateRelationships($tags,$taget_id,$table){
		if(!$taget_id || !$table ) 
			return false;
		
		else if(is_array($tags) && $tags){
			$this->tag->where(array('taget_id'=>$taget_id,'table'=>$table));
			$this->tag->where_not_in('tag',$tags);
			$this->tag->delete('tag_relationships');
			foreach($tags AS $key=>$t){
				$this->tag->select('*')->from('tag_relationships')->where(array('tag'=>$t,'taget_id'=>$taget_id,'table'=>$table));
				$data = $this->tag->get()->row();
// 				echo 'in adv </br>';
// 				bug($this->tag->last_query()); exit('in tag relationship');
				if(!$data){
					$data = array(
						'created'=>date("Y-m-d H:i:s"),
						'created_by'=>$this->session->userdata('uid'),
						'tag'=>$t,
						'taget_id'=>$taget_id,
						'table'=>$table
					);
					$query['run']  = $this->tag->insert('tag_relationships',$data);
				}
			}
			return true;
		}
		return false;
	}
	
	public function getRelationships($where){
		$this->tag->select('t.id, l.title AS title')->from('tag_relationships AS r');
		$this->tag->join('tag AS t', 't.id = r.tag', 'left');
		$this->tag->join('tag_lang AS l', 'l.tag_id = r.tag', 'left');
		$this->tag->where($where);
		$data = $this->tag->get()->result();
// 		bug($this->tag->last_query());exit('bug quqery');
		return $data;
	}
	
	
	
	function updateTags($tags, $contentId,$type = 'article'){
		//if($type == 'website') $this->db->where('website =',$contentId);
		//else $this->db->where('article =', $contentId);
		$this->db->where($type,$contentId);
		$this->db->where_not_in('tag', $tags);
		$query  = $this->db->delete($this->db->dbprefix('tags_relationships'));
		$this->db->flush_cache();
			foreach($tags AS $tag){
				//if($type == 'website') $query = $this->db->get_where($this->db->dbprefix('tags_relationships'),array('tag'=>$tag,'website'=> $contentId));
				//else $query = $this->db->get_where($this->db->dbprefix('tags_relationships'),array('tag'=>$tag,'article'=> $contentId));
				$query = $this->db->get_where($this->db->dbprefix('tags_relationships'),array('tag'=>$tag,$type=> $contentId));
				
				if(!is_object($query->first_row())) {
					if($tag != ''){
						//if($type == 'website') $data = array('tag' => $tag,'website' => $contentId);
						//else $data = array('tag' => $tag,'article' => $contentId);
						$data = array('tag' => $tag,$type => $contentId);
						$query = $this->db->insert($this->db->dbprefix('tags_relationships'), $data);
					}
					
				}
			}
	}
	function tagsContentLoad($contentId,$type='article'){
		$this->db->select('tag.id AS id, tag.title AS title');
		$this->db->from($this->db->dbprefix('tags_relationships').' AS rela');
		$this->db->join($this->db->dbprefix('tags').' AS tag', 'tag.id = rela.tag', 'left');
		//if($type == 'web') $this->db->where('website',$contentId);
		//else $this->db->where('article',$contentId);
		$this->db->where($type,$contentId);
		$query = $this->db->get();
		return  $query->result();
	}
	function loadTagsExist($type= ''){
		if($type != '') $this->db->where('type',$type);
		$query = $this->db->get($this->db->dbprefix('tags'));
		return  $query->result();
	}
	function insertTag($tag,$type){
		if($type != '') $tag['type']=$type;
		$sql = $this->db->insert_string('tags', $tag);
		$query = $this->db->query($sql);
		return $this->db->insert_id();
	}
	
}