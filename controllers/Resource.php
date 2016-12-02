<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Resource extends CI_Controller {
	function Resource(){
		parent::__construct();
		$this->dataView['typeView'] = 'admin';
		$this->dataView['pageTitle']='Quản Lý Tài nguyên';
		//echo 'pase 2='.$this->uri->segment(2);
		$subController = array('images','images_list');
		if(!in_array($this->uri->segment(2),$subController)) return self::showResource();
//		exit;
	}
	

	public function images(){
		//$this->dataView['js']= array('js/jquery-ui-1.8.13.custom.min.js','js/elfinder/elfinder.min.js');
		//$this->dataView['css'][] = 'js/elfinder/css/elfinder.css';
		
		$this->parser->parse('pages/resource/image_manager', $this->dataView);
	}
	public function images_list(){
		if (!empty($_POST['dir'])) {
			$dir = trim($_POST['dir']);
		} elseif (!empty($_GET['dir'])) {
			$dir = trim($_GET['dir']);
		}
		$subDir = (isset($dir))?"/$dir/":'/';
//		echo $this->config->item('resource_url');exit;
		
		$opts = array(
			'locale' => 'en_US.UTF-8',
			'bind' => array(
				'*' => 'logger'
				// 'mkdir mkfile rename duplicate upload rm paste' => 'logger'
			),
			'debug' => false,
			'roots' => array(
				array(
					'driver'     => 'LocalFileSystem',
					'path'       => '../resource/'.$subDir,
					'startPath'  => '../files/test/',
					'URL'        => $this->config->item('resource_domain')."$subDir",
					// 'alias'      => 'File system',
					'mimeDetect' => 'internal',
					'tmbPath'    => '-tmb-',
					'copyOverwrite' => false,
					'utf8fix'    => true,
					'tmbCrop'    => false,
					'tmbBgColor' => 'transparent',
					'accessControl' => 'access',
					'attributes' => array(
							array( // hide readmes
									'pattern' => '/-tmb-/',
									'read' => false,
									'write' => false,
									'hidden' => true,
									'locked' => false
							),
							array( // hide readmes
									'pattern' => '/.quarantine/',
									'read' => false,
									'write' => false,
									'hidden' => true,
									'locked' => false
							),
							array(
									'pattern' => '/error_log/',
									'read' => false,
									'write' => false,
									'hidden' => true,
									'locked' => false
							),
							
							array( // restrict access to png files
									'pattern' => '/\.php$/',
									'read' => false,
									'write' => false,
									'hidden' => true,
									'locked' => false
							),
							array( // restrict access to png files
									'pattern' => '/\.html$/',
									'read' => false,
									'write' => false,
									'hidden' => true,
									'locked' => false
							)
					),
					// 'uploadDeny' => array('application', 'text/xml')
			),
		)
					
			);
			
			// sleep(3);
			$this->load->library('elFinder');
			$this->load->library('elFinderConnector');
//			header('Access-Control-Allow-Origin: *');
			
			$connector = new elFinderConnector(new elFinder($opts), true);
			$connector->run();

	}
	
	protected function showResource(){
	$size = $this->uri->segment(2);
		$imgSize = explode('x',$size);
		$imgageDir = str_replace($this->uri->segment(1).'/','',$this->uri->uri_string());
		if(is_array($imgSize) && count($imgSize)==2) {
			$imgageDir = str_replace($this->uri->segment(2).'/','',$imgageDir);
			return self::showImage($imgageDir,$imgSize[0],$imgSize[1]);
		}else {
			return self::showImage($imgageDir); 
		}
	}
	protected function showImage($imageDir='',$maxwidth=0,$maxheight=0){
//		exit(BASEPATH);
//		exit('$maxheight='.$maxheight);
//		exit('/assets/_resource/'.$imageDir);
		if(file_exists('../_resource/'.$imageDir)===true){
			$fileDir = '../_resource/'.$imageDir;
		} else if(file_exists('/assets/_resource/images/'.$imageDir)!==true){
			$fileDir = './assets/_resource/images/'.$imageDir;
		} else {
			exit('no media');
		}
//		if (file_exists($fileDir)===false) exit('dont exist');
		list($width, $height, $type ) = getimagesize($fileDir);

//		$maxheight = 50;
//		$newwidth = 50;
//		exit('type='.$type);
		$newwidth = ($maxwidth>$width)?$width:$maxwidth;
		if ($maxheight) {
			$newheight = $maxheight;
		} else {
			$newheight = ($newwidth/$width)*$height;
		}
	
//		exit('$width'.$width);
		switch($type){
			case 1: $source = imagecreatefromgif($fileDir);$imageType='image/gif';
			break;
			case 2: $source = imagecreatefromjpeg($fileDir); $imageType='image/jpeg'; break;
			case 3: $source = imagecreatefrompng($fileDir); $imageType='image/png'; break;
			case 6:
				$imageUrl = 'http://gpfc.local/_resource/'.$fileDir; 
				$imageType='';
				header("Location: $imageUrl"); exit;
				exit($imageUrl.$fileDir); break;
			//default :$source();break;
		}
//		print_r($imageTmp);exit;
	//	$thumb = imagecreate($newwidth, $newheight);
		$imageContent = imagecreatetruecolor($newwidth, $newheight);
		
		
		$transparent_index = imagecolortransparent($source);
		if ($transparent_index >= 0)
		{
		    imagepalettecopy($source, $imageContent);
		    imagefill($imageContent, 0, 0, $transparent_index);
		    imagecolortransparent($imageContent, $transparent_index);
		    imagetruecolortopalette($imageContent, true, 256);
		} else if ($type == 3) {
		    imagealphablending($imageContent, false);
		    $transparent_index = imagecolorallocatealpha($source, 0, 0, 0, 127);
		    imagefill($imageContent, 0, 0, $transparent_index);
		    imagesavealpha($imageContent, true);
		}
		
		if($this->input->get('stamp')){
			if($this->input->get('stamp')=='add'){
				$stampFile = './assets/_resource/images/add.png';
			} else if ($this->input->get('stamp')=='edit'){
				$stampFile = './assets/_resource/images/pencil.png';
			}
			
			$stamp = imagecreatefrompng($stampFile);
			list($s_width, $s_height) = getimagesize($stampFile);
			$stamp_width = $s_width;
			$stamp_height = $s_height; 
//			$stamp_width = $newwidth/2;
//			$stamp_height = ($stamp_width/$s_width)*$s_height;
			
			$stamp_using = imagecreatetruecolor($stamp_width, $stamp_height);
//			imagesavealpha($stamp_using, true);
//			$trans_colour = imagecolorallocatealpha($stamp_using, 0, 0, 0, 127);
//		    imagefill($stamp_using, 0, 0, $trans_colour);
		   	//$red = imagecolorallocate($stamp_using, 255, 0, 0);
		    //imagefilledellipse($stamp_using, 400, 300, 400, 300, $red);
		    
		    $transparent_index = imagecolortransparent($stamp);
		    imagealphablending($stamp_using, false);
		    $transparent_index = imagecolorallocatealpha($stamp, 0, 0, 0, 127);
		    imagefill($stamp_using, 0, 0, $transparent_index);
		    imagesavealpha($stamp_using, true);
		    
//		    imagepalettecopy($stamp, $stamp_using);
//		    imagefill($stamp_using, 0, 0, $transparent_index);
//		    imagecolortransparent($stamp_using, $transparent_index);
//		    imagetruecolortopalette($stamp_using, true, 256);
		    
			imagecopyresized($stamp_using, $stamp, 0, 0, 0, 0, $stamp_width, $stamp_height, $s_width, $s_height);
			
//			$source = imagecreatefromjpeg($img);
//			$imageContent = imagecreatetruecolor($newwidth, $newheight);
			imagecopyresized($imageContent, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	
			$marge_right = 0;
			$marge_bottom = 0;
			$sx = imagesx($stamp_using);
			$sy = imagesy($stamp_using);
			imagecopy($imageContent, $stamp_using, imagesx($imageContent) - $sx - $marge_right, imagesy($imageContent) - $sy - $marge_bottom, 0, 0, imagesx($stamp_using), imagesy($stamp_using));
		} else {
			imagecopyresampled($imageContent, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
//			imagecopyresized($$stamp_using, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		}
		
		
		header("Content-type: $imageType");
		switch($type){
			case 1: imagegif($imageContent); break;
			case 2: imagejpeg($imageContent); break;
			case 3: imagepng($imageContent); break;
			case 6:
				exit($imageUrl.$fileDir); break;
			//default :$source();break;
		}
		imagedestroy($imageContent);
	}
}