<?php
class tags extends form {
	function __construct(){
		$this->CI =& get_instance();
	}
	public function input($fieldKey,$fieldData){
		$lable = preg_replace("/<.*?>/", "", $fieldData->lable);
		$attribute = '';
		$fieldData->category = ( isset($fieldData->category) )?$fieldData->category:'adv';
		$fieldData->table = ( isset($fieldData->table) )?$fieldData->table:'advertising';
		
		$scriptHead = ''
					.'var tag = {'
						.'item:function(id,title,type,hide){'
							.'$hide = (hide==true)?\' style="display: none;" \': " ";'
							.'var out = \'<span tag_id="\'+id+\'" \'+$hide+\'><a class="ntdelbutton" ></a>\'+title; '
							.'if(type=="using")'
								.'out+=\'<input type="hidden" name="'.self::protection($fieldKey.'[]').'" value="\'+id+\'" />\';'
							.'out+=\'</span>\';'
							.' return out;'
						.'},'
						.'action:function(){'
							.'$(".tags_item a.ntdelbutton").click(function(){'
								.'id= $(this).parent("span").attr("tag_id");'
								.'$(".tags_item_added span[tag_id="+id+"]").show();'
								.' $(this).parent("span").remove();'
							.'});'
							.'$(".tags_item_added span").click(function(e){'
								.'id= $(this).attr("tag_id");'
								.'if( id >0  &&  $(".tags_item span[tag_id="+id+"]").length == 0) {'
									.'$(this).hide();'
									.'$(".tags_item").append(tag.item($(this).attr("tag_id"),$(this).text(),"using"));'
									.'e.preventDefault();'
									.'tag.action();'
								.'}'
							.'});'
						.'},'
						.'post_new:function(newTag){'
							.'$.ajax({ type : "POST", dataType : "json", url : "'.site_url('tag/ajax').'?type=post", '
	         						.'data : { "'.$this->CI->config->item('csrf_token_name').'":$("input[name='.$this->CI->config->item('csrf_token_name').']").val(),'
	         								.' "'.$this->CI->form->protection('tag').'":newTag,';
			if(isset($fieldData->category)) 
				$scriptHead.=				' "'.$this->CI->form->protection('category').'": "'.$fieldData->category.'",';         								 
			$scriptHead.= '},'
	         						.'success: function(response) {'
	         							.' if(response.action == true) {'
	         								.'$(".tags_item").append(tag.item(response.id,response.title,"using"));'
	         								.'$(".tags_item_added").append(tag.item(response.id,response.title,"",true));'
	         							.'} '
	         						.'},'
	         						.'complete: function() { tag.action();},'
	         					.'});'
						.'},'
						.'load_tags:function(){'
							.'var $data = {"category":"'.$fieldData->category.'","table":"'.$fieldData->table.'"};'
							.'$taget_id = $("input[type=hidden][name='.$this->CI->form->protection('id').']").val();'
							.'if($taget_id !=0 ) $data["id"] = $taget_id;'
							.'$.ajax({ type : "GET", dataType : "json", url : "'.site_url('tag/ajax').'", data:$data,  '
								.'success: function(response) {'
         							.' if(response.action == true) {'
         								.'$.each(response.tags, function(i, item) { $(".tags_item_added").append(tag.item(item.id,item.title)); });'
         								.'$.each(response.taged, function(i, item) { '
         									.'$(".tags_item").append(tag.item(item.id,item.title,"using")); '
         									.'$(".tags_item_added span[tag_id="+item.id+"]").hide();'
         								.'});'
         							.'} '
         						.'},'
         						.'complete: function() { tag.action();},'
							.'});'
						.'},'
					.'};'
					.'$(document).ready(function() {'
						.'tag.load_tags();'
// 						.'tag.action();'
					.'});'
					.'$("button#new_post_tag").click(function(e){'
						.'$newTag = $("input[name=\'newtag\']").val();'
						.'if($newTag!=""){ tag.post_new($newTag); }'
						.'$("input[name=\'newtag\']").val("");'
						.'e.preventDefault();'
					.'});'
		;
		
		$this->CI->template->add_js_ready($scriptHead);
		
		$html = '';
		if($this->CI->session->userdata('uid')==1){
			$html.='<input type="text" value="" autocomplete="off" class="text" name="newtag" >'
			.'<button class="red dark send_right img_icon has_text text_only" id="new_post_tag" ><span>Add</span></button> ';
		}
			
		$html.='<div class="tags_item clearfix"></div>'
			.'<fieldset class="tags_item_added clearfix"><legend>Tag Exist</legend></fieldset>'
		;
		return $html;
	}
	
}
