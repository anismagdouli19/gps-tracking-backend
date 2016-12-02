<?php
class inputCapcha extends form {
	function __construct(){
		$this->CI =& get_instance();
	}
	public function input($fieldKey,$fieldData){
		
		$this->CI->load->helper('captcha');
		$vals = array(
			'img_path'	 => BASEPATH.'../assets/captcha/',
			'img_url'	 => subdomain('assets_url').'/captcha/',
			'font_path'=>BASEPATH.'fonts/FFF_Tusj.ttf',
		);
		
		$cap = create_captcha($vals);
// 		bug($cap);exit;
		$data = array(
			'captcha_time'	=> $cap['time'],
			'ip_address'	=> $this->CI->input->ip_address(),
			'word'	 => $cap['word']
		);
		
		$this->CI->db->where('captcha_time < ', time()-7200); // Two hour limit
		$this->CI->db->delete('captcha');
		
		$query = $this->CI->db->insert_string('captcha', $data);
		$this->CI->db->query($query);
		$html =$cap['image'];
		$html .="<input type=\"text\" name=\"".self::protection($fieldKey)."\" value=\"\" class=\"".$this->CI->form->inputClass."\" style=\" display: block; width: 250px;\" />";
		
		return $html;
	
		
	}
	
}
