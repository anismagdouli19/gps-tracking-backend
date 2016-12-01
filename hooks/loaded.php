<?php
function setJSvalue(){
	$CI =& get_instance();
	$script = 'var vt ={'
			."protect : '".$CI->security->get_csrf_token_name()."',"
			."site:'".site_url()."',"
			.'};'
	;
	if( config('assets_url') ){
		$assets = config('assets_url');
		$script .="vt.assets = '".$assets[0]."';";
	}
	if( config('resource_domain') ){
		$resource = config('resource_domain');
		$script .="vt.resource = '".$resource[0]."';";
	}
			
			
		
		//.'protect = "'.$CI->form->inputToken().'";'
		//.'site_url = "'.site_url().'";'

// if ($CI->session->userdata('logged_in') != TRUE){
// 	$script.='user.email = \''.$CI->form->input(null,'email','','text',false,true).'\';'
// 			.'user.password = \''.$CI->form->input(null,'password','','password',false,true).'\';';
// }
$CI->template->write('scripts_head',$script);
}