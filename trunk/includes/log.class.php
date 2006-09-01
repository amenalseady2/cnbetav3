<?php
/*--------------------------------------------------------------------------
// 程序名：union.php
// 功　能：用户登录的统一验证接口
// 作  者：安然
// 时  间：2005-10-18(完成时间)
// 修改：2005-03-18 安然,
// 添加：2005-10-26 安然,添加login,logout方法
// 添加：2005-12-09 安然,append($f_name, $f_cont)
// 修改：2005-12-03 安然,LOG_USER_ON_LINE', '/data1/logs/uusee/

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
	* 用户登录的时间
	*
	* @param string $channelno 频道号
	* @param string $userid 用户号
	* @param string $session 唯一的会话session
	* @return bool 
	*/
	function login($platform, $session, $userid)
	{
		// LogIn\t	用户标识	时间	空	空	用户IP
		$f_name = LOG_USER_ON_LINE . date('Y-m-d') . ".log";
		$f_cont = "LogIn\t{$platform}\t{$session}\t{$channelno}\t{$userid}\t" . date('Y-m-d H:i:s') . "\t{$tmp}\t{$tmp}\t{$_SERVER['REMOTE_ADDR']}\n";
		return log::_append($f_name, $f_cont);
	}
	
	/**
	* 用户退出登录的时间
	*
	* @param string $channelno 频道号
	* @param string $userid 用户号
	* @param string $session 唯一的会话session
	* @return bool 
	*/
	function logout($platform, $session, $userid)
	{
		// LogIn\t	用户标识	时间	空	空	用户IP
		$f_name = LOG_USER_ON_LINE . date('Y-m-d') . ".log";
		$f_cont = "LogOut\t{$platform}\t{$session}\t{$channelno}\t{$userid}\t" . date('Y-m-d H:i:s') . "\t{$tmp}\t{$tmp}\t{$_SERVER['REMOTE_ADDR']}\n";
		return log::_append($f_name, $f_cont);
	}
	
	/**
	* 用户进入频道日志
	*
	* @param string $channelno 频道号
	* @param string $userid 用户号
	* @param string $session 唯一的会话session
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