<?php 
class CronJob extends CI_Controller {
	function CronJob(){
		parent::__construct();
	}
	function motor(){
		$today = strtotime(date('Y-m-d'));
		if ( ! @is_dir($root = BASEPATH.'../../backup')){
			mkdir($root, 0700);
		}
		if ( ! @is_dir($today = $root.DS.$today)){
			mkdir($today, 0700);
		}
		exit('call me here');
	}
	
	function geocode(){
		$lat = floatval($this->input->get('la'));
		$lon = floatval($this->input->get('lo'));
		if($lat > -90 || $lat < 90 || $lon> -180 || $lon < 180){
			$url =  'http://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lon.'&sensor=false&language=vi';
	
			$data = json_decode(file_get_contents($url));
			if($data->status =='OK' && isset($data->results[0]) && isset($data->results[0]->formatted_address) ){
				$str = $data->results[0]->formatted_address;
				foreach($data->results[0]->address_components AS $k=>$val){
					if($val->types[0] == 'country') {
						$str= str_replace(', '.$val->long_name, '', $str);
					}
				}
				echo ucwords(convert_accented_characters($str));
			}
		}
	
		exit;
	
	}
	
	function test(){
		exit('test string');
	}
}