<?php
/*
 * $Id: config.php 13 2006-07-07 02:57:16Z dengwei $
 *
 * 全局配置文件
 */
require_once("smarty/Smarty.class.php");
require_once("includes/adodb.inc.php");

// 配置信息
define('DEBUG', true);	// 开启调试状态

$smarty_cache_md5_length	=	2;		// smarty 对象 cache 目录子目录名长度
$smarty_lifetime		=	10;		// smarty 页面生存时间
$smarty_caching			=	true;		// 是否缓存页面
$smarty_left_delimiter		=	'<!-- {';	// 左分隔符
$smarty_right_delimiter		=	'} -->';	// 右分隔符

$admin_panel_path		=	'admin';

// global variables

// 设置项目目录
$project_path = "";
$project_path_array = explode("\\",__FILE__);
for($counter = 1; $counter < count($project_path_array)-1 ; $counter++ )
	$project_path .= $project_path_array[$counter] . "/";
unset($project_path_array);
@define('__SITE_ROOT' , '/' . $project_path );

// Smarty 对象实例

$tpl = new Smarty();

$tpl->template_dir	= __SITE_ROOT . "templates/"; 
$tpl->compile_dir	= __SITE_ROOT . "templates_c/"; 
$tpl->config_dir	= __SITE_ROOT . "configs/"; 
$tpl->cache_dir		= __SITE_ROOT . "cache/";

$tpl->left_delimiter	= $smarty_left_delimiter;
$tpl->right_delimiter	= $smarty_right_delimiter;

$tpl->caching		= $smarty_caching;
$tpl->cache_lifetime	= $smarty_lifetime;
$tpl->cache_dir		= __SITE_ROOT . "cache/";
// 防止重复
$tpl->cache_dir		.= strtoupper(substr(md5($_SERVER['URL']),0,$smarty_cache_md5_length));

// 开启缓存和 SESSION
ob_start();
session_start();

// 调试函数
function debug($msg)
{
	if( DEBUG ) 
	{
		echo date("Y-m-d H:i:s") . " $msg <br />";
	}
}
?>
