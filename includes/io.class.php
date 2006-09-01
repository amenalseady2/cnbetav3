<?php
/**
 * $Id: io.class.php 10 2006-07-06 04:00:52Z dengwei $
 * $Author: dengwei $
 * $Date: 2006-07-06 12:00:52 +0800 (星期四, 06 七月 2006) $
 * $HeadURL: svn://v3.cnbeta.com/classes/io.class.php $
 * $Revision: 10 $
 *
 */

class in
{	
	function get(&$v, $in, $def=null)
	{
		return in::input($v, $in, $def, 0);
	}

	function post(&$v, $in, $def=null)
	{
		return in::input($v, $in, $def, 1);
	}

	function both(&$v, $in, $def=null)
	{
		return in::input($v, $in, $def, 2);
	}
	
	function request(&$v, $in, $def=null)
	{
		return in::input($v, $in, $def, 3);
	}

	function input(&$v, $in, $def, $cgitype)
	{
		$val = NULL;
		switch($cgitype)
		{
			case 1:
				if (isset($_POST[$in]))
				{
					$val = $_POST[$in];
				}
				break;
			case 2:
				if (isset($_POST[$in]))
				{
					$val = $_POST[$in];
				}
				elseif (isset($_GET[$in]))
				{
					$val = $_GET[$in];
				}
				break;
			case 3:
				if (isset($_REQUEST[$in]))
				{
					$val = $_REQUEST[$in];
				}
				break;
			case 0:
			default:
				if (isset($_GET[$in]))
				{
					$val = $_GET[$in];
				}
				break;
		}
		
		if (is_null($val) or $val == '')
		{
			if (is_numeric($v))
			{
				$v = $def + 0;
			}
			else
			{
				$v = $def . '';
			}
			return false;
		}
		else
		{
			if (is_numeric($v))
			{
				if (!is_numeric($val))
				{
					$v = $def + 0;
					return false;
				}
			}
			$v = $val;
			return true;
		}
	}
}

class out
{
	function filename()
	{
		return strval(date('YmdHis'));
	}
	
	function exist($filepath)
	{
		if (file_exists($filename)) {
			return true;
		}
		else {
			return false;
		}
	}
	
	function copy( $src, $dst )
	{
		return copy($src, $dst);
	}

}

?>
