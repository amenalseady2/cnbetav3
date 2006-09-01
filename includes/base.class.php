<?php
// 添加：2005-11-26 安然，新的ob类
// 添加：2006-01-10 安然，in类添加新request方法，去除3个无用的字函数
// 修改：2006-01-11 安然，string类去除js函数处理部分
// 添加：2006-01-12 安然，passport,x类
// 修改：2006-01-16 安然，uri变量交换的bug

//var_dump(dir::mkdir('abc'));
//var_dump(dir::mkdir_super($_SERVER['DOCUMENT_ROOT'] . '/a/b/c/d/'));
/*
 * 基本类库集合 v0.1
 */

///////////////////////////////////////////////////////////

/**
 * 统一资源号处理类
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
 * OB 处理类
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
 * dir 处理类
 * @package baseLib
 */
Class dir
{
	function dir()
	{
		/****/
	}

	/**
	 * 生成目录
	 * @return mixed true是目录已经在或者成功建立，false是创建失败，为0非文件，
	 * @param string $d_name 目录名 
	 * @param OCT $d_mod 权限
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
	 * 生成一系列目录
	 * @return mixed true是目录已经在或者成功建立，false是创建失败，为0非文件，
	 * @param string $d_name 目录名 
	 * @param OCT $d_mod 权限
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
 * Date处理类
 * @package baseLib
 */
Class Date
{
	function Date()
	{
		/****/
	}
	
	/**
	 * 获取当前年
	 * @return int
	 * @param boolean $all 是否返回4位数的年份 默认true 
	 */	
	
	function get_year($all = true)
	{
		return $all ? date("Y") : date("y");
	}
	
	/**
	 * 获取当前月份
	 * @return int
	 */	
	
	function get_month()
	{
		return date("m");
	}
	
	/**
	 * 获取当前英文月份
	 * @return string
	 * @param boolean $all 是否返回全部英文 默认true 否则则返回简写 
	 */	
	
	function get_month_en($all = true)
	{
		return $all ? date("F") : date("M");
	}
	
	/**
	 * 获取当前日
	 * @return int
	 */	
	
	function get_day()
	{
		return date("d");
	}
	
	/**
	 * 获取当前小时
	 * @return int
	 */	
	
	function get_hour()
	{
		return date("H");
	}
	
	/**
	 * 获取当前分
	 * @return int
	 */	
	
	function get_min()
	{
		return date("i");
	}
	
	/**
	 * 获取当前秒
	 * @return int
	 */	
	
	function get_sec()
	{
		return date("s");
	}
	
	/**
	 * 获取当前英文星期
	 * @return string
	 * @param boolean $all 是否返回全部英文 默认true 否则则返回简写 
	 */	
	
	function get_week_en($all = true)
	{
		return $all ? date("l") : date("D");
	}
	
	/**
	 * 获取当前星期(0-6)0是星期天或者当前是第几周
	 * @return int
	 * @param boolean $all true 返回星期几 false 返回当前是今年的第几周 默认是true
	 */	
	
	function get_week($all = true)
	{
		return $all ? date("w") : date("W");
	}
	
	/**
	 * 获取日期
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
	 * 获取时间
	 * @return string
	 */	
	
	function get_time()
	{
		return date("H:i:s");
	}
	
	/**
	 * 获取日期和时间
	 * @return string
	 * @param int $t 输入的时间戳
	 */	
	
	function get_date_time($t = false)
	{
		if ($t)
			return date("Y-m-d H:i:s", $t);
		else
			return date("Y-m-d H:i:s");
	}
	
	/**
	 * 计算N秒后（前）的时间
	 * @return string
	 * @param int $sec  秒
	 * @param string $d  返回的时间格式 默认 Y-m-d H:i:s
	 */	
	 	
	function get_new_date($sec, $d = "Y-m-d H:i:s")
	{
		$sec = intval($sec);
		return date($d, mktime(date("H"), date("i"), (date("s") + $sec), date("m"), date("d"), date("Y")));
	}
	
	/**
	 * 计算两个时间的差
	 * @return int
	 * @param string $oldTime
	 * @param string $newTime 两个时间参数不分前后
	 * @param string $type 计算相差的类型 d 是计算天,h是小时,i是分钟,s小秒  默认是d
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
 * 输入获取类
 */
class in
{
	/**
	 * 以get方式取cgi变量
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
	 * 以post方式取in变量
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
	 * 以get,post方式取cgi变量
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
	 * 以get,post,cookie方式取变量
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
	 * CGI变量接收
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
				if (!is_numeric($val))	// 如果要求是数值，而传入是非数值
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
 * 输入校验类
 */
class check
{
	/**
	 * 验证Email的合法性
	 * @return boolean
	 * @param string $str Email字符串
	 */
	function email($str)
	{
		return preg_match('/^[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)*@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+){1,4}$/', $str) ? true : false;
	}
	
	/**
	 * 验证年份的合法性
	 * @return boolean
	 * @param string $str 年份字符串4位数字19**-20**
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
	 * 验证月份的合法性
	 * @return boolean
	 * @param string $str 月份字符串
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
	 * 验证日期的合法性
	 * @return boolean
	 * @param string $str 月份字符串
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
	 * 检查URL的合法性，检测URL头是否为 http, https, ftp
	 * @return boolean
	 * @param string $str 年份字符串
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

	//-- 判断字符串是否含有非法字符 -------
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

	//-- 判断字符串是否含有非法词汇 -------
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
 * 文件相关LIB包
 * History: 2005-6-22  创建类
 */

class file
{
	/**
	 * 读取文件返回文件内容
	 * @return mixed
	 * @param string $fname 文件名
	 */
	function read($fname)
	{
		return file_get_contents($fname);
	}
	
	/**
	 * 以覆盖的方式写数据到文件
	 * @return bool
	 * @param string $fname 文件名
	 * @param string $content 需要写入的内容
	 */
	function write($fname, $content)
	{
		return file::_write_file($fname, $content, 'w');
	}
	
	/**
	 * 以追加的方式写数据到文件
	 * @return bool
	 * @param string $fname		文件名
	 * @param string $content	需要写入的内容	
	 */
	function append($fname, $content)
	{
		return file::_write_file($fname, $content, 'a');
	}

	/**
	 * 写数据到文件
	 * @return bool
	 * @param string $fname		文件名
	 * @param string $content	需要写入的内容
	 * @param string $mod		写入文件的方式
	 */	
	function _write_file($fname, $content, $mod)
	{
		$fp = fopen($fname, $mod);
		if ($fp)
		{
			if (flock($fp, LOCK_EX))
			{
				fwrite($fp, $content);
				// 释放锁定
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
 * STRING处理类
 * @package baseLib
 */

Class string
{
	function string()
	{
		/****/
	}
	
	/**
	 * 处理截取中文字符串的操作
     * @return string
     * @param string $str 要处理的字符
	 * @param string $start 开始位置
	 * @param string $offset 偏移量
	 * @param string $t_str 字符结果尾部增加的字符串，默认为空
	 * @param boolen $ignore $start位置上如果是中文的某个字后半部分是否忽略该字符，默认true
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
				if ($i == $start)  //检测头一个字符的时候，是否需要忽略半个中文
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
	 * 判断字符某个位置是中文字符的左半部分还是右半部分，或不是中文
	 * 返回 1 是左边 0 不是中文 -1是右边
     * @return int
	 * @param string $str 开始位置
	 * @param int $location 位置
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
	 * 判断字符是否全是中文字符组成
	 * 2 全是 1部分是 0没有中文
     * @return boolean
	 * @param string $str 要判断的字符串
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
	 * 过滤字符串中的特殊字符
	 * @return string
	 * @param string $str 需要过滤的字符
	 * @param string $filtStr 需要过滤字符的数组（下标为需要过滤的字符，值为过滤后的字符）
	 * @param boolen $regexp 是否进行正则表达试进行替换，默认false
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
	 * 过滤字符串中的HTML标记 < >
	 * @return string
	 * @param string $str 需要过滤的字符
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
	 * 过滤字符串的特殊字符，防止注入攻击
	 */
	function esc_mysql($str)
	{
		return mysql_escape_string($str);
	}

	/**
	 * 过滤字符串的特殊字符，以便把数据输出到页面做编辑显示
	 * @return string
	 * @param string $str 需要过滤的字符
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
	 * 过滤字符串的特殊字符，以便把数据输出到页面做输出显示
	 * @return string
	 * @param string $str 需要过滤的字符
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