<?php
/**
 * $Id$
 * 
 * 后台控制面板头文件
 */

define('__ADMIN__', true );

if( false === $_SESSION['admin_panel'] )
{
	// 没有进入管理员面板的权限
	die('你无权限进入管理面板。');
}

require_once( "../config.php" );

debug("管理面板头文件加载完毕");
?>