<?php
/*
 * $Id: config.php 13 2006-07-07 02:57:16Z dengwei $
 *
 * global config file
 */

require_once "smarty/Smarty.class.php";
require_once "adodb/adodb.inc.php";

/*
 * global variables
 */

$NowPathArray = explode( "v3" , str_replace( "\\" , "/" , dirname(__FILE__) ) );

@define("root_path" , $NowPathArray[0] );
@define('__SITE_ROOT' , root_path . "v3/" );

/*
 * instance of Smarty
 */

$tpl = new Smarty();

$tpl->template_dir	= __SITE_ROOT . "templates/"; 
$tpl->compile_dir	= __SITE_ROOT . "templates_c/"; 
$tpl->config_dir	= __SITE_ROOT . "configs/"; 
$tpl->cache_dir		= __SITE_ROOT . "cache/";

$tpl->left_delimiter	= '<!-- {';
$tpl->right_delimiter	= '} -->'; 

$tpl->caching		= true;
$tpl->cache_lifetime	= 10;
$tpl->cache_dir		= __SITE_ROOT . "cache/";

// ·ÀÖ¹ÖØ¸´
$tpl->cache_dir		.= strtoupper(substr(md5($_SERVER['URL']),0,2));

?>
