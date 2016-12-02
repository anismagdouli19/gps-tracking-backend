<?php


function img_resoure($dir,$width=0,$height=0,$domain='resource_url',$returnDirOnly=true,$alt=''){
	$CI_ =& get_instance();
	if(!$dir){
		$dir = 'no-image.jpg';
	}
	$path = pathinfo($dir);

	if( !is_uri($dir) ){
		$resourcePath =  subdomain($domain).'/';
		if($path['dirname'] && $path['dirname'] !='.' ){
			$resourcePath.= base64_encode($path['dirname']).'/';
		}
		if($width && $width >0){
			$resourcePath.= "w$width/";
		}
		$resourcePath.=$path['basename'];
	} else {
		$resourcePath = $dir;
	}


	if($returnDirOnly==true){
		return $resourcePath;
	} else {
		return '<img alt="'.$alt.'" src="'.$resourcePath.'"  />';
	}
}

function showResouce(){
	$uri = $_SERVER['REQUEST_URI'];
	if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0){
		$uri = substr($uri, strlen($_SERVER['SCRIPT_NAME']));
		//print_r($uri);exit('bug uri 1');
	} elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
		$uri = substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
		// 	$uri = $_GET["f"];
		// 	print_r($uri);exit('bug uri 2');
	} else if( isset($_GET["f"]) ){
		$uri = $_GET["f"];
		//print_r($uri);exit('bug uri 3');
	}

//	base64_encode()
	if (strncmp($uri, '?/', 2) === 0){
		$uri = substr($uri, 2);
	}
	$uri = parse_url($uri, PHP_URL_PATH);
	$uri = str_replace(array('//', '../'), '/', trim($uri, '/'));

	$imgInfo = explode('/',$uri);
	if($imgInfo && is_array($imgInfo) && count($imgInfo) >=2 ){
		$dir = (base64_decode($imgInfo[0]) != '.')?base64_decode($imgInfo[0]).'/':'';
		if(count($imgInfo) == 3){
			if(substr($imgInfo[1],0,1)=='w' && is_numeric(substr($imgInfo[1],1))){
				showFileContent($dir.urldecode($imgInfo[2]),substr($imgInfo[1],1));
			}
		}else {
			showFileContent($dir.$imgInfo[1]);
		}
	} else {
//		exit('have error');
		showFileContent('no-image.jpg');
	}

//
//	if(count($imgInfo) == 3){
//		if(substr($imgInfo[1],0,1)=='w' && is_numeric(substr($imgInfo[1],1))){
//			showFileContent($dir.urldecode($imgInfo[2]),substr($imgInfo[1],1));
//		}
//	} else { // 2 value (dir + filename)
//
//		showFileContent($dir.$imgInfo[1]);
//	}
}

function subdomain($type='resource_url',$th=0){
	//if($type=='assets_url' ){
		$config = config_item($type);
		if( !isset($config ) ){
			return config_item('base_url');
		} else if(is_array($config) && isset($config[$th]) ){
			return $config[$th];
		} else {
			return $config;
		}

	//}

}

function showFileContent($fileOnDisk,$newwidth=0,$maxheight=0){
//	print_r($fileOnDisk);exit;
	$fileOnDisk = BASEPATH.	urldecode($fileOnDisk);
	$fileOnDisk = file_exists($fileOnDisk)?$fileOnDisk:BASEPATH.'/no-image.jpg';

//	print_r($fileOnDisk);exit;

	list($width, $height, $type ) = getimagesize($fileOnDisk);
	$newwidth = ($newwidth > $width || $newwidth==0)?$width:$newwidth;
	$newheight = ($newwidth/$width)*$height;
	// 	}
	//print_r('width='.$width);
	//print_r('height='.$height); exit('bug image');

	switch($type){
		case 1: $imageTmp = imagecreatefromgif($fileOnDisk);$imageType='image/gif';break;
		case 2: $imageTmp = imagecreatefromjpeg($fileOnDisk); $imageType='image/jpeg'; break;
		case 3: $imageTmp = imagecreatefrompng($fileOnDisk); $imageType='image/png'; break;
		case 6:
			$imageUrl = 'http://gpfc.local/_resource/'.$fileOnDisk;
			$imageType='';
			header("Location: $imageUrl"); exit;
			exit($imageUrl.$fileOnDisk); break;
			//default :$source();break;
	}
	// 	print_r($imageTmp);exit;
	//	$thumb = imagecreate($newwidth, $newheight);
	$stamp_using = imagecreatetruecolor($newwidth, $newheight);

	$transparent_index = imagecolortransparent($imageTmp);
	if ($transparent_index >= 0){
		imagepalettecopy($imageTmp, $stamp_using);
		imagefill($stamp_using, 0, 0, $transparent_index);
		imagecolortransparent($stamp_using, $transparent_index);
		imagetruecolortopalette($stamp_using, true, 256);
	} else if ($type == 3) {
		imagealphablending($stamp_using, false);
		$transparent_index = imagecolorallocatealpha($imageTmp, 0, 0, 0, 127);
		imagefill($stamp_using, 0, 0, $transparent_index);
		imagesavealpha($stamp_using, true);
	}
	imagecopyresampled($stamp_using, $imageTmp, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
//	echo $type;
//print_r($fileOnDisk);exit;
	session_start();
	header("Pragma: public");
	//     header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: public");
	header("Cache-Control: private, max-age=10800, pre-check=10800");
	header("Content-Description: File Transfer");


	// //	header("Pragma: private");
	// 	header("Expires: " . date(DATE_RFC822,strtotime(" 2 day")));

	header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($fileOnDisk)).' GMT');
	header("Cache-Control: must-revalidate");
	//header('Expires: '.gmdate('D, d M Y H:i:s', filemtime($fileOnDisk)));
	// //	header("Etag: ".md5(filemtime($fileOnDisk).$fileOnDisk));
	// 	//header("ETag: \"{$hash}\"");

	header("Content-type: $imageType");
	//  header('Content-transfer-encoding: binary');
	//     header('Content-length: '.filesize($fileOnDisk));

	switch($type){
		case 1: imagegif($stamp_using); break;
		case 2: imagejpeg($stamp_using); break;
		case 3: imagepng($stamp_using); break;
		case 6:
			exit($imageUrl.$fileOnDisk); break;
			//default :$source();break;
	}
	//return
	print_r(imagedestroy($stamp_using));

}
