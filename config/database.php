<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$active_group = 'mapgps';
$active_record = TRUE;
$db['mapgps']['hostname'] = 'localhost';
$db['mapgps']['username'] = 'root';
$db['mapgps']['password'] = '';
$db['mapgps']['database'] = 'giaiphapict_gps';
$db['mapgps']['dbdriver'] = 'mysqli';
$db['mapgps']['dbprefix'] = 'mapgps_';
$db['mapgps']['pconnect'] = FALSE;
$db['mapgps']['db_debug'] = TRUE;
$db['mapgps']['cache_on'] = FALSE;
$db['mapgps']['cachedir'] = '';
$db['mapgps']['char_set'] = 'utf8';
$db['mapgps']['dbcollat'] = 'utf8_general_ci';
$db['mapgps']['swap_pre'] = '';
$db['mapgps']['autoinit'] = TRUE;
$db['mapgps']['stricton'] = FALSE;


$db['account']['hostname'] = 'localhost';
$db['account']['username'] = 'root';
$db['account']['password'] = '';
$db['account']['database'] = 'giaiphapict_gps';
$db['account']['dbdriver'] = 'mysqli';
$db['account']['dbprefix'] = 'mapgps_account_';
$db['account']['char_set'] = 'utf8';
$db['account']['dbcollat'] = 'utf8_general_ci';
$db['account']['pconnect'] = FALSE;
$db +=array(
	'node'=>array(
		'hostname'=>'localhost',
		'username'=>'root',
		'password'=>'',
		'database'=>'gps0xx',
		'dbdriver'=>'mysql',
		'dbprefix'=>'',
		'pconnect'=>FALSE,
		'stricton'=>FALSE,
		'db_debug'=>FALSE,
		'cache_on'=>FALSE,
		'cachedir'=>'',
	),
	'nodedemo'=>array(
		'hostname'=>'localhost',
		'username'=>'root',
		'password'=>'',
		'database'=>'giaiphapict_gps',
		'dbdriver'=>'mysql',
		'dbprefix'=>'motordemo_',
		'pconnect'=>FALSE,
		'stricton'=>FALSE,
		'db_debug'=>FALSE,
		'cache_on'=>FALSE,
		'cachedir'=>'',
	),
	'car'=>array(
		'hostname'=>'localhost',
		'username'=>'vteck',
		'password'=>'',
		'database'=>'gpscar',
		'dbdriver'=>'mysql',
		'dbprefix'=>'',
		'pconnect'=>FALSE,
		'stricton'=>FALSE,
		'db_debug'=>FALSE,
		'cache_on'=>FALSE,
		'cachedir'=>''
	),
);