<?php
class owner {
	function __construct(){
		$this->CI =& get_instance();
		$this->form = get_instance()->form;
	}
	public function input($fieldKey,$fieldData){
		$lable = preg_replace("/<.*?>/", "", $fieldData->lable);
		$attribute = '';
		$fullname =$this->CI->lang->line('tag user first');
		if($fieldData->value && $fieldData->value > 0){
			$user = $this->CI->User_Model->getUser($fieldData->value);
			if($user && $user->fullname){
				$fullname = $user->fullname;
			}
		}
		$html = "<input type=\"text\" value=\"$fullname\" class=\"".$this->CI->form->inputClass."\" $attribute disabled />";
		$html.=$this->form->inputHidden($fieldKey,$fieldData->value);
		return $html;
	}

}
