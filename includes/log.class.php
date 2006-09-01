<?php
/*--------------------------------------------------------------------------
// ��������union.php
// �����ܣ��û���¼��ͳһ��֤�ӿ�
// ��  �ߣ���Ȼ
// ʱ  �䣺2005-10-18(���ʱ��)
// �޸ģ�2005-03-18 ��Ȼ,
// ��ӣ�2005-10-26 ��Ȼ,���login,logout����
// ��ӣ�2005-12-09 ��Ȼ,append($f_name, $f_cont)
// �޸ģ�2005-12-03 ��Ȼ,LOG_USER_ON_LINE', '/data1/logs/uusee/

--------------------------------------------------------------------------*/
//log::log
include_once('base.class.php');

define('LOG_USER_ON_LINE', '/data1/logs/uusee/');
class log
{
	function log()
	{
	}

	/**
	* �û���¼��ʱ��
	*
	* @param string $channelno Ƶ����
	* @param string $userid �û���
	* @param string $session Ψһ�ĻỰsession
	* @return bool 
	*/
	function login($platform, $session, $userid)
	{
		// LogIn\t	�û���ʶ	ʱ��	��	��	�û�IP
		$f_name = LOG_USER_ON_LINE . date('Y-m-d') . ".log";
		$f_cont = "LogIn\t{$platform}\t{$session}\t{$channelno}\t{$userid}\t" . date('Y-m-d H:i:s') . "\t{$tmp}\t{$tmp}\t{$_SERVER['REMOTE_ADDR']}\n";
		return log::_append($f_name, $f_cont);
	}
	
	/**
	* �û��˳���¼��ʱ��
	*
	* @param string $channelno Ƶ����
	* @param string $userid �û���
	* @param string $session Ψһ�ĻỰsession
	* @return bool 
	*/
	function logout($platform, $session, $userid)
	{
		// LogIn\t	�û���ʶ	ʱ��	��	��	�û�IP
		$f_name = LOG_USER_ON_LINE . date('Y-m-d') . ".log";
		$f_cont = "LogOut\t{$platform}\t{$session}\t{$channelno}\t{$userid}\t" . date('Y-m-d H:i:s') . "\t{$tmp}\t{$tmp}\t{$_SERVER['REMOTE_ADDR']}\n";
		return log::_append($f_name, $f_cont);
	}
	
	/**
	* �û�����Ƶ����־
	*
	* @param string $channelno Ƶ����
	* @param string $userid �û���
	* @param string $session Ψһ�ĻỰsession
	* @return bool 
	*/
	function channel($platform, $session, $channelno, $userid)
	{
		$f_name = LOG_USER_ON_LINE . date('Y-m-d') . ".log";
		$f_cont = "WatchingRecStart\t{$platform}\t{$session}\t{$channelno}\t{$userid}\t" . date('Y-m-d H:i:s') . "\t{$tmp}\t{$tmp}\t{$_SERVER['REMOTE_ADDR']}\n";
		return log::_append($f_name, $f_cont);
	}
	
	function _append($f_name, $f_cont)
	{
		$f = fopen($f_name, 'a');
		if (!$f)
		{
			return false;
			//die();
		}
		fwrite($f, $f_cont);
		fclose($f);
		return true;
	}
	
	function append($f_name, $f_cont)
	{
		if ( dir::mkdir_super(dirname($f_name)))
		{
			return log::_append($f_name, $f_cont);
		}
		else
		{
			return false;
		}
	}
}

//var_dump(log::append('/a/b/c/e/abc.log', "i love you!\n"));
?>