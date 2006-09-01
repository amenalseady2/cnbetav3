<?php
// ��ӣ�2005-11-26 ��Ȼ���µ�ob��
// ��ӣ�2006-01-10 ��Ȼ��in�������request������ȥ��3�����õ��ֺ���
// �޸ģ�2006-01-11 ��Ȼ��string��ȥ��js����������
// ��ӣ�2006-01-12 ��Ȼ��passport,x��
// �޸ģ�2006-01-16 ��Ȼ��uri����������bug

//var_dump(dir::mkdir('abc'));
//var_dump(dir::mkdir_super($_SERVER['DOCUMENT_ROOT'] . '/a/b/c/d/'));
/*
 * ������⼯�� v0.1
 */

///////////////////////////////////////////////////////////

/**
 * ͳһ��Դ�Ŵ�����
 * @package baseLib
 */

class uri
{
	function encode($type, $memberid=null, $id=null)
	{
		if (!is_array($type))
		{
			$array['type'] = $array;
			$array['memberid'] = $memberid;
			$array['id'] = $id;
		}
		else
		{
			$array = $type;
		}
		return str_pad(base_convert($array['type'], 10, 36), 2, '0', STR_PAD_LEFT)
		. str_pad(base_convert($array['memberid'], 10, 36), 6, '0', STR_PAD_LEFT)
		. str_pad(base_convert($array['id'], 10, 36), 6, '0', STR_PAD_LEFT);
	}

	function decode($str)
	{
		if (strlen($str) <> 14)
		{
			return false;
		}
		return 
		array
		(
			'type' => base_convert(substr($str, 0, 2), 36, 10),
			'memberid' => base_convert(substr($str, 2, 6), 36, 10),
			'id' => base_convert(substr($str, 8, 6), 36, 10),
		);
	}
}

/**
 * OB ������
 * @package baseLib
 */
Class ob
{
	function ob_start($var=null)
	{
		global $g_ini;
		if (!isset($g_ini['ob_start']))
		{
			if (ob_start($var=null))
			{
				$g_ini['ob_start'] = true;
			}
			else
			{
				$g_ini['ob_start'] = true;
			}
		}
		return $g_ini['ob_start'];
	}
	
}

/**
 * dir ������
 * @package baseLib
 */
Class dir
{
	function dir()
	{
		/****/
	}

	/**
	 * ����Ŀ¼
	 * @return mixed true��Ŀ¼�Ѿ��ڻ��߳ɹ�������false�Ǵ���ʧ�ܣ�Ϊ0���ļ���
	 * @param string $d_name Ŀ¼�� 
	 * @param OCT $d_mod Ȩ��
	 */
	function mkdir($d_name, $d_mod=0744)
	{
		if (is_dir($d_name)||empty($d_name))
		{
			 // best case check first
			return true;
		}
		elseif (file_exists($d_name) && !is_dir($d_name))
		{
			return 0;
		}
		return mkdir($d_name, $d_mod);
	}
	
	/**
	 * ����һϵ��Ŀ¼
	 * @return mixed true��Ŀ¼�Ѿ��ڻ��߳ɹ�������false�Ǵ���ʧ�ܣ�Ϊ0���ļ���
	 * @param string $d_name Ŀ¼�� 
	 * @param OCT $d_mod Ȩ��
	 */
	function mkdir_super($d_name, $d_mod=0744)
	{
		if (is_dir($d_name)||empty($d_name))
		{
			 // best case check first
			return true;
		}
		elseif (($d_name=='/') || (file_exists($d_name) && !is_dir($d_name)))
		{
			return 0;
		}
		$d_name = rtrim(str_replace('\\', '/', $d_name), '/');
		if (dir::mkdir_super(substr($d_name, 0, strrpos($d_name, '/'))))
		{
			//echo "$d_name\n";
			//return 1;
			return mkdir($d_name, $d_mod); // crawl back up & create dir tree
		}
		else
		{
			return false;
		}
	}
	
	function rmdir($d_name)
	{
		
	}
}

///////////////////////////////////////////////////////////

/**
 * Date������
 * @package baseLib
 */
Class Date
{
	function Date()
	{
		/****/
	}
	
	/**
	 * ��ȡ��ǰ��
	 * @return int
	 * @param boolean $all �Ƿ񷵻�4λ������� Ĭ��true 
	 */	
	
	function get_year($all = true)
	{
		return $all ? date("Y") : date("y");
	}
	
	/**
	 * ��ȡ��ǰ�·�
	 * @return int
	 */	
	
	function get_month()
	{
		return date("m");
	}
	
	/**
	 * ��ȡ��ǰӢ���·�
	 * @return string
	 * @param boolean $all �Ƿ񷵻�ȫ��Ӣ�� Ĭ��true �����򷵻ؼ�д 
	 */	
	
	function get_month_en($all = true)
	{
		return $all ? date("F") : date("M");
	}
	
	/**
	 * ��ȡ��ǰ��
	 * @return int
	 */	
	
	function get_day()
	{
		return date("d");
	}
	
	/**
	 * ��ȡ��ǰСʱ
	 * @return int
	 */	
	
	function get_hour()
	{
		return date("H");
	}
	
	/**
	 * ��ȡ��ǰ��
	 * @return int
	 */	
	
	function get_min()
	{
		return date("i");
	}
	
	/**
	 * ��ȡ��ǰ��
	 * @return int
	 */	
	
	function get_sec()
	{
		return date("s");
	}
	
	/**
	 * ��ȡ��ǰӢ������
	 * @return string
	 * @param boolean $all �Ƿ񷵻�ȫ��Ӣ�� Ĭ��true �����򷵻ؼ�д 
	 */	
	
	function get_week_en($all = true)
	{
		return $all ? date("l") : date("D");
	}
	
	/**
	 * ��ȡ��ǰ����(0-6)0����������ߵ�ǰ�ǵڼ���
	 * @return int
	 * @param boolean $all true �������ڼ� false ���ص�ǰ�ǽ���ĵڼ��� Ĭ����true
	 */	
	
	function get_week($all = true)
	{
		return $all ? date("w") : date("W");
	}
	
	/**
	 * ��ȡ����
	 * @return string
	 */	
	
	function get_date($t = false)
	{
		if ($t)
			return date("Y-m-d", $t);
		else
			return date("Y-m-d");
	}
	
	/**
	 * ��ȡʱ��
	 * @return string
	 */	
	
	function get_time()
	{
		return date("H:i:s");
	}
	
	/**
	 * ��ȡ���ں�ʱ��
	 * @return string
	 * @param int $t �����ʱ���
	 */	
	
	function get_date_time($t = false)
	{
		if ($t)
			return date("Y-m-d H:i:s", $t);
		else
			return date("Y-m-d H:i:s");
	}
	
	/**
	 * ����N���ǰ����ʱ��
	 * @return string
	 * @param int $sec  ��
	 * @param string $d  ���ص�ʱ���ʽ Ĭ�� Y-m-d H:i:s
	 */	
	 	
	function get_new_date($sec, $d = "Y-m-d H:i:s")
	{
		$sec = intval($sec);
		return date($d, mktime(date("H"), date("i"), (date("s") + $sec), date("m"), date("d"), date("Y")));
	}
	
	/**
	 * ��������ʱ��Ĳ�
	 * @return int
	 * @param string $oldTime
	 * @param string $newTime ����ʱ���������ǰ��
	 * @param string $type ������������ d �Ǽ�����,h��Сʱ,i�Ƿ���,sС��  Ĭ����d
	 */	
	function diff_date($oldTime, $newTime, $type = "d")
	{
		if (preg_match("/^(\d+)-(\d+)-(\d+)$/i", $oldTime))
		{
			$oldTime .= " 00:00:00";
		}
		if (preg_match("/^(\d+)-(\d+)-(\d+)$/i", $newTime))
		{
			$newTime .= " 00:00:00";
		}
		preg_match("/^(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)$/i", $oldTime, $o);
		preg_match("/^(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)$/i", $newTime, $n);
		$o_t = getdate(mktime($o[4], $o[5], $o[6], $o[2], $o[3], $o[1]));
		$o_n = getdate(mktime($n[4], $n[5], $n[6], $n[2], $n[3], $n[1]));
		$sec = abs($o_n[0] - $o_t[0]);
		switch ($type)
		{
			case "d":
				return floor($sec / (60*60*24));
				break;
			case "h":
				return floor($sec / (60*60));
				break;
			case "i":
				return floor($sec / (60));
				break;
			case "s":
				return $sec;
				break;
		}
	}
}


///////////////////////////////////////////////////////////

/**
 * �����ȡ��
 */
class in
{
	/**
	 * ��get��ʽȡcgi����
	 * @return mixed
	 * @param	string	$v
	 *			string	$in
	 *			integer	$def
	 */
	function get(&$v, $in, $def=null)
	{
		return in::input($v, $in, $def, 0);
	}
	
	/**
	 * ��post��ʽȡin����
	 * @return mixed
	 * @param	string	$v
	 *			string	$in
	 *			integer	$def
	 */
	function post(&$v, $in, $def=null)
	{
		return in::input($v, $in, $def, 1);
	}
	
	/**
	 * ��get,post��ʽȡcgi����
	 * @return mixed
	 * @param	string	$v
	 *			string	$in
	 *			integer	$def
	 */
	function both(&$v, $in, $def=null)
	{
		return in::input($v, $in, $def, 2);
	}

	/**
	 * ��get,post,cookie��ʽȡ����
	 * @return mixed
	 * @param	string	$v
	 *			string	$in
	 *			integer	$def
	 */
	function request(&$v, $in, $def=null)
	{
		return in::input($v, $in, $def, 3);
	}

	/**
	 * CGI��������
	 * @return mixed
	 * @param	string	$v
	 *			string	$in
	 *			integer	$def
	 */
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
/*				if (isset($_GET[$in]))
				{
					$val = $_GET[$in];
				}
				elseif (isset($_POST[$in]))
				{
					$val = $_POST[$in];
				}
				elseif (isset($_COOKIE[$in]))
				{
					$val = $_COOKIE[$in];
				}
*/				break;
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
				if (!is_numeric($val))	// ���Ҫ������ֵ���������Ƿ���ֵ
				{
					$v	= $def + 0;
					return false;
				}
			}
			$v	= $val;
			return true;
		}
	}
}

/**
 * ����У����
 */
class check
{
	/**
	 * ��֤Email�ĺϷ���
	 * @return boolean
	 * @param string $str Email�ַ���
	 */
	function email($str)
	{
		return preg_match('/^[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)*@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+){1,4}$/', $str) ? true : false;
	}
	
	/**
	 * ��֤��ݵĺϷ���
	 * @return boolean
	 * @param string $str ����ַ���4λ����19**-20**
	 */
	function year($str)
	{
		if (is_numeric($str))
		{
			preg_match('/^19|20[0-9]{2}$/', $str) ? true : false;
		}
		return false;
	}

	/**
	 * ��֤�·ݵĺϷ���
	 * @return boolean
	 * @param string $str �·��ַ���
	 */
	function month($str)
	{
		if (is_numeric($str) && $str > 0 && $str < 13)
		{
			return true;
		}
		return false;
	}

	/**
	 * ��֤���ڵĺϷ���
	 * @return boolean
	 * @param string $str �·��ַ���
	 */
	function day($str)
	{
		if (is_numeric($str) && $str > 0 && $str < 32)
		{
			return true;
		}
		return false;
	}
	
	/**
	 * ���URL�ĺϷ��ԣ����URLͷ�Ƿ�Ϊ http, https, ftp
	 * @return boolean
	 * @param string $str ����ַ���
	 */
	function url($str)
	{
		$allow = array('http', 'https', 'ftp');
		
		if (preg_match('!^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?!', $str, $matchs))
		{
			$scheme = $matches[2];
			if (in_array($scheme, $allow))
			{
				return true;
			}
		}
		return false;
	}

	//-- �ж��ַ����Ƿ��зǷ��ַ� -------
	function check_badchar($str, $allowSpace=false)
	{
		if ($allowSpace)
		{
			return preg_match ("/[><,.\][{}?\/+=|\\\'\":;~!@#*$%^&()`\t\r\n-]/i", $str) == 0 ? true : false;
		}
		else
		{
			return preg_match ("/[><,.\][{}?\/+=|\\\'\":;~!@#*$%^&()` \t\r\n-]/i", $str) == 0 ? true : false;
		}
	}

	//-- �ж��ַ����Ƿ��зǷ��ʻ� -------
	function check_badword($str, $bad_words)
	{
		if (!is_array($bad_words))
		{
			$bad_words = split(',', $bad_words);
		}

		foreach ($bad_words as $v)
		{
			if (stristr($str, $v))
			{
				return $v;
			}
		}
		return '';
	}
}


///////////////////////////////////////////////////////////

/**
 * �ļ����LIB��
 * History: 2005-6-22  ������
 */

class file
{
	/**
	 * ��ȡ�ļ������ļ�����
	 * @return mixed
	 * @param string $fname �ļ���
	 */
	function read($fname)
	{
		return file_get_contents($fname);
	}
	
	/**
	 * �Ը��ǵķ�ʽд���ݵ��ļ�
	 * @return bool
	 * @param string $fname �ļ���
	 * @param string $content ��Ҫд�������
	 */
	function write($fname, $content)
	{
		return file::_write_file($fname, $content, 'w');
	}
	
	/**
	 * ��׷�ӵķ�ʽд���ݵ��ļ�
	 * @return bool
	 * @param string $fname		�ļ���
	 * @param string $content	��Ҫд�������	
	 */
	function append($fname, $content)
	{
		return file::_write_file($fname, $content, 'a');
	}

	/**
	 * д���ݵ��ļ�
	 * @return bool
	 * @param string $fname		�ļ���
	 * @param string $content	��Ҫд�������
	 * @param string $mod		д���ļ��ķ�ʽ
	 */	
	function _write_file($fname, $content, $mod)
	{
		$fp = fopen($fname, $mod);
		if ($fp)
		{
			if (flock($fp, LOCK_EX))
			{
				fwrite($fp, $content);
				// �ͷ�����
				flock($fp, LOCK_UN);
				return true;
			}
			flock($fp, LOCK_UN);
		}
		return false;
	}
}	

///////////////////////////////////////////////////////////

/**
 * STRING������
 * @package baseLib
 */

Class string
{
	function string()
	{
		/****/
	}
	
	/**
	 * �����ȡ�����ַ����Ĳ���
     * @return string
     * @param string $str Ҫ������ַ�
	 * @param string $start ��ʼλ��
	 * @param string $offset ƫ����
	 * @param string $t_str �ַ����β�����ӵ��ַ�����Ĭ��Ϊ��
	 * @param boolen $ignore $startλ������������ĵ�ĳ���ֺ�벿���Ƿ���Ը��ַ���Ĭ��true
	 */
	function substr_cn($str, $start, $offset, $t_str='', $ignore=true)
	{
	 	$length  = strlen($str);
		if ($length <=  $offset && $start == 0)
		{
			return $str;
		}
		if ($start > $length)
		{
			return $str;
		}
		$r_str     = "";
		for ($i = $start; $i < ($start + $offset); $i++)
		{ 
			if (ord($str{$i}) > 127)
			{
				if ($i == $start)  //���ͷһ���ַ���ʱ���Ƿ���Ҫ���԰������
				{
					if (string::is_cn_str($str, $i) == 1)
					{
						if ($ignore)
						{
							continue;
						}
						else
						{
							$r_str .= $str{($i - 1)}.$str{$i};
						}
					}
					else
					{
						$r_str .= $str{$i}.$str{++$i};
					}
				}
				else
				{
					$r_str .= $str{$i}.$str{++$i};
				}
			}
			else
			{
				$r_str .= $str{$i};
				continue;
			}
		}
		return $r_str . $t_str;
	}
	
	
	/**
	 * �ж��ַ�ĳ��λ���������ַ�����벿�ֻ����Ұ벿�֣���������
	 * ���� 1 ����� 0 �������� -1���ұ�
     * @return int
	 * @param string $str ��ʼλ��
	 * @param int $location λ��
	 */
	 
	function is_cn_str($str, $location)
	{ 
		$result	= 1;
		$i		= $location;
		while(ord($str{$i}) > 127 && $i >= 0)
		{ 
			$result *= -1; 
			$i --; 
		} 
		
		if($i == $location)
		{ 
			$result = 0; 
		} 
		return $result; 
	} 
	
	/**
	 * �ж��ַ��Ƿ�ȫ�������ַ����
	 * 2 ȫ�� 1������ 0û������
     * @return boolean
	 * @param string $str Ҫ�жϵ��ַ���
	 */
	 
	function chk_cn_str($str)
	{ 
		$result = 0;
		$len = strlen($str);
		for ($i = 0; $i < $len; $i++)
		{
			if (ord($str{$i}) > 127)
			{
				$result ++;
				$i ++;
			}
			elseif ($result)
			{
				$result = 1;
				break;
			}
		}
		if ($result > 1)
		{
			$result = 2;
		}
		return $result;
	}
	
	/**
	 * �����ַ����е������ַ�
	 * @return string
	 * @param string $str ��Ҫ���˵��ַ�
	 * @param string $filtStr ��Ҫ�����ַ������飨�±�Ϊ��Ҫ���˵��ַ���ֵΪ���˺���ַ���
	 * @param boolen $regexp �Ƿ�����������Խ����滻��Ĭ��false
	 */
	
	function filt_string($str, $filtStr, $regexp=false)
	{
		if (!is_array($filtStr))
		{
			return $str;
		}
		$search		= array_keys($filtStr);
		$replace	= array_values($filtStr);
				
		if ($regexp)
		{
			return preg_replace($search, $replace, $str);
		}
		else
		{
			return str_replace($search, $replace, $str);
		}
	}
	
	/**
	 * �����ַ����е�HTML��� < >
	 * @return string
	 * @param string $str ��Ҫ���˵��ַ�
	 */
	
	function un_html($str)
	{
		$s	= array(
			"&"     => "&amp;",
			"<"	=> "&lt;",
			">"	=> "&gt;",
			"\n"	=> "<br>",
			"\t"	=> "&nbsp;&nbsp;&nbsp;&nbsp;",
			"\r"	=> "",
			" "	=> "&nbsp;",
			"\""	=> "&quot;",
			"'"	=> "&#039;",
		);

		return string::filt_string($str, $s);
	}
	
	/**
	 * �����ַ����������ַ�����ֹע�빥��
	 */
	function esc_mysql($str)
	{
		return mysql_escape_string($str);
	}

	/**
	 * �����ַ����������ַ����Ա�����������ҳ�����༭��ʾ
	 * @return string
	 * @param string $str ��Ҫ���˵��ַ�
	 */
	function esc_edit_html($str)
	{
		$s	= array(
			"&"     => "&amp;",
			"<"		=> "&lt;",
			">"		=> "&gt;",
			"\""	=> "&quot;",
			"'"		=> "&#039;",
		);
		return strtr($str, $s);
	}

	/**
	 * �����ַ����������ַ����Ա�����������ҳ���������ʾ
	 * @return string
	 * @param string $str ��Ҫ���˵��ַ�
	 */
	function esc_show_html($str)
	{
		$s	= array(
			"&"     => "&amp;",
			"<"		=> "&lt;",
			">"		=> "&gt;",
			"\n"	=> "<br>",
			"\t"	=> "&nbsp;&nbsp;&nbsp;&nbsp;",
			"\r"	=> "",
			" "		=> "&nbsp;",
			"\""	=> "&quot;",
			"'"		=> "&#039;",
		);
		return strtr($str, $s);
	}
}
?>