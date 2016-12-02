<?php
function setJSvalue(){
	$CI =& get_instance();
	$script = 'var vt ={'
			."protect : '".$CI->security->get_csrf_token_name()."',"
			."site:'".config_item('base_url')."/',"
			.'};'
	;
	if( config_item('assets_url') ){
		$assets = config_item('assets_url');
		$script .="vt.assets = '".$assets[0]."';";
	}
	if( config_item('resource_domain') ){
		$resource = config_item('resource_domain');
		$script .="vt.resource = '".$resource[0]."';";
	}



    $CI->template->write('scripts_head',$script);
}

function load_backend(){
    $ci = get_instance();

    $ci->msg = NULL;
}