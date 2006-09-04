<?php
/*
 * $Id: config.php 13 2006-07-07 02:57:16Z dengwei $
 *
 * ȫ�������ļ�
 */

require_once "smarty/Smarty.class.php";
require_once "includes/adodb.inc.php";
/**
 * ������Ϣ
 */
$smarty_cache_md5_length	=	2;		// smarty ���� cache Ŀ¼��Ŀ¼������
$smarty_lifetime		=	10;		// smarty ҳ������ʱ��
$smarty_caching			=	true;		// �Ƿ񻺴�ҳ��
$smarty_left_delimiter		=	'<!-- {';	// ��ָ���
$smarty_right_delimiter		=	'} -->';	// �ҷָ���
/**
 * global variables
 */
// ������ĿĿ¼
$project_path = "";
$project_path_array = explode("\\",__FILE__);
for($counter = 1; $counter < count($project_path_array)-1 ; $counter++ )
	$project_path .= $project_path_array[$counter] . "/";
unset($project_path_array);
@define('__SITE_ROOT' , '/' . $project_path );

/**
 * Smarty ����ʵ��
 */

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
// ��ֹ�ظ�
$tpl->cache_dir		.= strtoupper(substr(md5($_SERVER['URL']),0,$smarty_cache_md5_length));
?>
