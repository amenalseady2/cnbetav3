<?php
/**
 * $Id$
 * 
 * ��̨�������ͷ�ļ�
 */

define('__ADMIN__', true );

if( false === $_SESSION['admin_panel'] )
{
	// û�н������Ա����Ȩ��
	die('����Ȩ�޽��������塣');
}

require_once( "../config.php" );

debug("�������ͷ�ļ��������");
?>