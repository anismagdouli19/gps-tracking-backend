<?php
class owner extends form {
	function __construct(){
		$this->CI =& get_instance();
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
		$html.=parent::inputHidden($fieldKey,$fieldData->value);
		return $html;
	}
	
}
