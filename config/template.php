<?php
$template['active_template'] = 'default';
$template['default']['template'] = 'template/terminator';
$template['default']['regions'] = array(
	'title','header','content','scripts','scripts_ready','scripts_head',
	'_scripts'=>array(
		//'http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js',
		'jquery-ui-1.9.2/js/jquery-1.8.3.js',
		'backend.js',
		'js/jquery.jcarousel.js',
		'tinymce_3.5/tiny_mce.js',
		// 	'jquery/tinymce_3.5/tiny_mce_src.js',
		'tinymce_3.5/jquery.tinymce.src.js',
		// 	'http://giaiphapict.com/resource/libs/tinymce_3.5.8/tiny_mce_src.js',
		// 	'http://giaiphapict.com/resource/libs/tinymce_3.5.8/jquery.tinymce.js',
		// 	'jquery/tinymce_3.5/jquery.tinymce.src.js',
		'tinymce_3.5/plugins/ict/image_plugin.js',
		'tinymce_3.5/plugins/ict/link_plugin.js',
		'js/viettracker.js',
			'plugin/timepicker-addon/jquery-ui-timepicker-addon.js',
	),
	'_styles'=>array(
		'backend.css',
			'jquery-ui-1.9.2/css/cupertino/jquery-ui-1.9.2.custom.css',
		'plugin/timepicker-addon/jquery-ui-timepicker-addon.css',
	),
);

$template['column55']['template'] = 'template/column55';
$template['column55']['regions'] = array_merge(
		$template['default']['regions'],
		array('rightcontent','leftcontent')
);


// bug($template);exit;
