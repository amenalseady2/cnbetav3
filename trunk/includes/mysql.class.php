<?php
//*-------------------------------------------------------------------------
// 修改：2004-8-7 修改扩展类的 close() 函数，使这可以注销类的自身，但是保留连接的资源号 2004-8-7
// 修改：2005-12-14 使用配置数组对类的进行配置，错误显示可以关闭，可以写入日志。
// 添加：2005-12-18 添加新的内部方法 _get_table()。
// 添加：2005-12-18 添加新的方法 update_id(),delete_id()。
// 添加：2005-12-18 添加新的内部方法 _get_table()。
// 修改：2005-12-18 insert_array() 执行没有返回结果。
// 修改：2005-12-28 return_all_array() 如果没有结果，返回false。
// 修改：2006-01-09 将选择数据库的操作放在query()中进行，而且是在数据库没为空的情况下进行。
// 修改：2006-01-26 query($sql, $fetch)　当 $fetch 为true 也就是自动解析打开的时候，可以支持缓存，同时缓存配置要打开，缓存的配置与缓存类一样。
// 修改：2006-02-04 insert_row()　进行字符串过滤的时候函数有一个bug
// 
//-------------------------------------------------------------------------*/

include_once('base.class.php');
include_once('log.class.php');
include_once('cache.class.php');

define( 'DB_MYSQL', '20060204');
define( 'DB_LOG_PATH', '/data1/logs/baselib_error/mysql.error');

class MYSQL
{
	// 配置数组
	//var $conf = array();
	var $conf = array(
		'd' => '',
		'h' => '',
		'u' => '',
		'p' => '',
		'error_report' => false,	// trur (report the error),false (slient)
		'halt_on_error' => false,	// true (halt with message),false (ignore errors quietly)
		'error_log' => DB_LOG_PATH,	// log file

		'debug' => false,			// echo the debug message
		'auto_free' => true,		// 自动清空。Set to 1 for automatic mysql_free_result()

		//--- 缓存设置
		'root' => '/data0/cache/newmanshow/', // 根目录
		'level' => 2, // 目录分级 
		'ext' => '.php', // 扩展名
		'master' => 'mysql_class', // 主 key 
		'type' => C_FILE,				// 缓存的容器， C_FILE 文件，C_MYSQL 数据库，C_BERKELEY 数据库，C_SHMOP 共享内存
		'lifetime' => 900,			// 生存时间秒，最少60秒，太低反而会加重系统负担
		'debug' => false,			// 打开调试信息
		'close' => false,			// 关闭 cache 

		'serialize' => true, // 序列化
		//'base64' => true, // base64编码
		//'gz_level' => 6, // 压缩的程度1－9。0或空：不压缩。
		//'module' => 'chache_mod_exmaple_test.php',			// 读取模块
		'force_get' => true, // 当取读数据库失败的时候是否强取缓存的内容
	);
	/*
	*/
 
	var $Seq_Table = "db_sequence"; // 指定保存数据锁定信息的表。

	// public: result array and current row number
	// 设置查询结果数组、结果数
	var $Record = array();			// 解析后的结果数组
	var $Row;						// 当前的记录数

	// public: current error number and error text
	var $Errno = 0;					// 错误号
	var $Error = "";				// 错误信息

	// public: this is an api revision, not a CVS revision.
	var $type = "mysql";			// 类的类型
	var $revision = "1.2";			// 类的版块号

	// private: link and query handles
	// 数据库连接号
	var $Link_ID  = 0; 				// MYSQL操作结果的指针
	var $Query_ID = 0; 				// 设置数据库表，为新增加

	var $anbbs_table;						// 设置 addnew 的开始标志，为新增加
	var $AddNew;					// 设置 addnew 的数组，为新增加
	var $AddArray = array();		// 设置update_talbe()中，字段与值
	var $condition;					// 设置update_talbe()中的条件
	var $ArrayCach = false;		// 缓存所有结果的二维数组
	var $array_x = array();			// 缓存指定字段的一维数组
	var $sql_fields = '';			// 操作SQL操作的字段串,字段名用英文逗号分开 ,
	var $connect_key = null;		// 2005-01-19 当前缓冲的数组连接号

	// public: constructor
	// 构造函数
	function MYSQL($query="")
	{
		if (!empty($query))
		{
			$this->query($query);
		}
	}

	//--- 数据库的连接参数 --------------
	// 修改：将资源连接参数缓存到两个全局数组中 2004-8-7

	function connect( $Database=null)
	{
		/**
		* 修改成可以使用全局配置的变量
		赋值顺序：程序输入 > 全局变量 > 类中定义
		二○○三年十一月二十八日
		// 修改：2005-12-14 使用数组，取消参数传入
		*/

		if (is_resource($this->Link_ID))
		{
			return $this->Link_ID;
		}

		global $an_db_var;		 // 数据库连接参数数组2004-8-7
		global $an_db_conn;		 // 数据连接的资源数组2004-8-7

		//--- 接收参数的处理
		if (!is_array($Database))
		{
			$this->halt("connect 连接参数不是数组。");
			return false;
		}
		$this->conf = $Database;

		// establish connection, select database

		/*
		*/
		// 判断相同的参数是否存在,存在直接返回
		// 构造连接参数
		$var = array();
		$var['d'] = $this->conf['d'];
		$var['h'] = $this->conf['h'];
		$var['u'] = $this->conf['u'];
		$var['p'] = $this->conf['p'];

		if (!empty($an_db_var))
		{
			if ($this->conf['debug'])
			{
				echo "CONNECT: 判断缓存中是否存在连接……\n";
			}
			foreach ($an_db_var as $key => $val)
			{
				if ($val === $var)
				{
					if (is_resource($an_db_conn[$key]))
					{
						$this->Link_ID = $an_db_conn[$key];
						$this->connect_key = $key;
						if ($this->conf['debug'])
						{
							echo "CONNECT: 使用缓存的连接！\n";
						}
					}
					else
					{
						unset($an_db_conn[$key]);
						unset($an_db_var[$key]);
					}
				}
			}
		}

		// if the connection not egzist,connect and save
		if (!is_resource($this->Link_ID))
		{
			$this->Link_ID = @mysql_connect( $this->conf['h'], $this->conf['u'], $this->conf['p']);

			// 连接成功后，将参数与连接号压入数组
			if ($this->Link_ID)
			{
				$key = count($an_db_conn);
				//echo '$var';var_dump($var);
				$an_db_var[$key]  = $var;
				$an_db_conn[$key] = $this->Link_ID;
				$this->connect_key = $key;
				if ($this->conf['debug'])
				{
					echo "CONNECT: 新建连接，圧入缓存。\n";
				}
				// echo "新建连接：";
				//echo '$an_db_var';var_dump($an_db_var);
				//echo '$an_db_conn';var_dump($an_db_conn);
			}
			else
			{
				//$this->halt("connect($this->conf['h'], $this->conf['u'], \$Password) 失败。<BR><font color='#ff0000'>数据库连接出错！</font><BR>\n请检查：<BR>\n1、数据库系统是否启动<BR>\n2、连接参数是否正确！");
				$this->halt("数据库繁忙请稍后访问。");
				return false;
			}
		}
		return $this->Link_ID;
	}

	/////////////////////////////////////////////////////////////////////////////////////////

	//public 返回当前解析后的数组
	// 二○○三年十二月十七日
	// 添加：2005-08-22 自动解析
	function return_array($fetch=false)
	{
		if ($fetch)
		{
			if ($stat = $this->next_record())
			{
				return $this->Record;
			}
			else
			{
				return $stat;
			}
		}
		else
		{
			return $this->Record;
		}
	}

	//public 返回包含所有结果 2维 数组
	// 2004-3-1
	function return_all_array()
	{
/*		$r = $this->nf();
		if (empty($r))
		{
			return false;
		}
*/
		if (is_array($this->ArrayCach))
		{
			return $this->ArrayCach;
		}
		$this->ArrayCach = array();
		while ( $this->next_record())
		{
			$this->ArrayCach[] = $this->Record;
		}
		return $this->ArrayCach;
	}

	//public 返回包含所指定字段的一维数组
	// 2004-3-2
	// 测试通过，很不错！
	// 修改：2004-12-17 对缓存的数组进行的校验及容错

	function array_x($filed)
	{
		$this->return_all_array();
		if (empty($this->ArrayCach) && !is_array($this->ArrayCach))
		{
			return false;
		}
		//print_r( $this->ArrayCach[0]);
		if ( !@array_key_exists( $filed, $this->ArrayCach[0]))
		{
			// 如果结果数组(二维)中第一个记录没有该字段，那么返回空。
			return false;
		}
		$this->array_x = array();
		foreach ( $this->ArrayCach as $val)
		{
			$this->array_x[] = $val[$filed];
		}
		return $this->array_x;
	}

	//public 返回包含所指定字段的一维数组的合并字串
	// 2004-3-10
	// 2004-3-10 通过测试
	function string_x( $filed)
	{
		$r_array = $this->array_x( $filed);
		if ($r_array && is_array($r_array))
		{
			return implode( ',', $this->array_x);
		}
		else
		{
			return false;
		}
	}

	//public 返回新插入的ID号
	// 二○○三年四月十八日，返回新插入的ID号
	function insert_id()
	{
		return @mysql_insert_id($this->Link_ID);
	}

	// public 设置操作表
	//二○○三年六月二十八日
	function set_table( $anbbs_table)
	{
		if ( $anbbs_table)
		{
			$this->Table = $anbbs_table;
			return true;
		}
		$this->halt( " set_table() 表为空。");
		return false;
	}
	
	function _get_table($anbbs_table=null)
	{
		if (!empty($anbbs_table))
		{
			$this->Table = $anbbs_table;
		}
		if (empty($this->Table))
		{
			$this->halt( "方法 insert_array() 数据表为空。");
		}
		return $this->Table;
	}

	// public 将一条或多条记录集添加到数据库，返回插入的记录数
	//$array[n]['字段'] = '值' （二维）这样的数组插入到数据库中
	//二○○三年六月二十八日
	function insert_arrays( $array, $anbbs_table='')
	{
		$anbbs_table = $this->_get_table($anbbs_table);
		if( is_array( $array[0]))
		{
			foreach( $array as $val)
			{
				$this->insert_array( $val, $anbbs_table);
			}
			return count( $array);
		}
		else
		{
			$this->insert_array( $array,$anbbs_table );
			return 1;
		}
	}

	function insert_row($array, $anbbs_table='', $sql_fields='')
	{
		return $this->insert_array($array, $anbbs_table, $sql_fields);
	}

	// private 插入一个或多个字段与值的 "键－值对" 数组，返回是否成功
	// 将一条记录集添加到数据库
	// $array['字段'] = '值' （一维）这样的数组插入到数据库中
	//二○○三年六月二十八日
	function insert_array($array, $anbbs_table='', $sql_fields='')
	{
		$anbbs_table = $this->_get_table($anbbs_table);

		if (!is_array( $array))
		{
			$this->halt( "方法 insert_array() 第1个参数不是数组错误， 表名为 {$this->Table} ");
			return false;
		}

		if ($sql_fields)
		{
			// 添加进行SQL操作的字段字符串
			$this->set_sql_fields($sql_fields);
		}

		foreach($array as $key=>$val)
		{
			if ($this->sql_fields && $this->_check_sql_fields($key))
			{
				$values .= $val . ',';
			}
			else
			{
				$values .= "'" . mysql_escape_string($val) . "',";
			}
			$cols .= "`" . trim($key) . "`,";
		}

		$cols   = substr( $cols, 0, -1);
		$values = substr( $values, 0, -1);
		$sql = "INSERT INTO `{$this->Table}` ({$cols}) VALUES ({$values})";

		if ( $this->conf['debug'])
		{
			echo "插入数组语句：";
		}
		return $this->query($sql);
	}

	//public 更新一个包含"键－值对"的一维数组
	// 2004-8-10 指定对关键字段进行SQL语句操作
	// 修改：2004-8-12 设定表的时候不更改当前默认的表

	function update_table( $array, $condition, $anbbs_table='', $sql_fields='')
	{
		//$array		"键－值对"的一维数组
		//$condition	条件
		//$anbbs_table=''		表
		//更新条件可是一个“键－值对”（两个字串参数），或者是一个包括“键－值对”的一维数组（后面参数省略）
		$anbbs_table = $this->_get_table($anbbs_table);

		if ( $condition)
		{
			$this->condition = " WHERE {$condition}";
		}
		if ( !is_array($array))
		{
			$this->halt(" update_table( \$array, $condition, $anbbs_table) 错误：第一个参数不是数组！");
		}

		if ($sql_fields)
		{
			// 添加进行SQL操作的字段字符串
			$this->set_sql_fields($sql_fields);
		}

		foreach ( $array as $k=>$v)
		{
			if ($this->sql_fields && $this->_check_sql_fields($v))
			{
				$tem .= " `{$k}`={$v},";
			}
			else
			{
				$tem .= " `{$k}`='" . mysql_escape_string($v) . "',";
			}
		}
		$tem = rtrim($tem, ',');
		$sql = "UPDATE `{$anbbs_table}` SET {$tem} {$this->condition}";

		if ( $this->conf['debug'])
		{
			echo "更新数组语句：";
		}

		return $this->query($sql);
	}

	function del_id($id, $anbbs_table=null)
	{
		$anbbs_table = $this->_get_table($anbbs_table);

		if (empty($id))
		{
			return false;
		}

		if ( $this->conf['debug'])
		{
			echo "删除 id={$id}：";
		}

		$sql = "DELETE FROM `{$anbbs_table}` WHERE id='{$id}' LIMIT 1";
		return $this->query($sql);
	}
	
	function update_id($array, $id, $anbbs_table=null, $sql_fields='')
	{
		$anbbs_table = $this->_get_table($anbbs_table);

		if (empty($id))
		{
			return false;
		}

		if ( $this->conf['debug'])
		{
			echo "更新 id={$id}：";
		}
		return $this->update_table( $array, "id='{$id}'", $anbbs_table, $sql_fields);
	}
	
	//public 如果记录不存在执行插入操作，记录存在执行更新操作
	// bool/int insert_update( $要插入的数组, $要更新的数组, $表='', $sql语句字段='');
	// 2004-5
	// 原理：在执行插入操作时，先以 $array 生成一个条件判断，以免这个插入的值有重复
	// 在执行更新的操作时，以上步生成的条件作为更新的条件进行更新操作
	// 完善:2004-8-12 函数优化＆可设定sql语句字段

	function insert_update( $array, $update_fild, $anbbs_table='', $sql_fields='')
	{
		if ( empty( $array) && !is_array($array))
		{
			$this->halt(" insert_update( \$array, $update_fild, $anbbs_table) 错误：第1个参数不是数组！");
		}

		if ( empty( $update_fild) && !is_array($update_fild))
		{
			$this->halt("insert_update( \$array, $update_fild, $anbbs_table) 错误：第2个参数不是数组！");
		}

		$anbbs_table = $this->_get_table($anbbs_table);

		foreach ( $array as $key=>$val )
		{
			$tem .= " `{$key}`='{$val}' and";
		}
		$s_where = substr($tem, 0, -4);
		$sql = "select count(*) from `{$anbbs_table}` WHERE {$s_where} limit 1";
		$this->query($sql);
		$this->next_record();
		if ( $this->conf['debug'])
		{
			echo "插入｜更新数组语句－";
		}
		if ($this->f( 'count(*)') < 1)
		{
			// 如果记录不存在,执行插入操作
			return $this->insert_array( $array, $anbbs_table, $sql_fields);
		}

		// 最后执行更新操作
		return update_table( $update_fild, $s_where, $anbbs_table, $sql_fields);
	}

	//--- 添加执行SQL的语句字段 ---------
	// 2004-8-10

	function set_sql_fields($fields='')
	{
		$this->sql_fields = ',,' . $fields . ',';
	}

	//--- 检查执行SQL的语句字段 ---------
	// 如果检查的字段与字段字串为空，返回 0
	// 如果存在返回 true 否 返回 false
	// 2004-8-10

	function _check_sql_fields($s_field)
	{
		if ($s_field && $this->sql_fields)
		{
			if (strpos(',' . $this->sql_fields .',', ',' . $s_field . ','))
			{
				return true;
			}
			return false;
		}
		return 0;
	}

	// 添加：2005-08-22 close( $fetch=false) 是否释放连接资源
	function close($link=false)
	{
		//释放资源
		$this->free();
		if ($link && $this->Link_ID)
		{
			global $an_db_var, $an_db_conn;		 // 数据连接的资源数组2004-8-7

			@mysql_close($this->Link_ID);
			unset($an_db_var[$this->connect_key]);
			unset($an_db_conn[$this->connect_key]);
		}

		//释放资源
		// 注销这个类 2004-8-7
		// 修改：2005-01-26 不同的php版本可能不支持
		//@$this = null;
	}

	// public: some trivial reporting
	function link_id()
	{
		return $this->Link_ID;
	}

	function query_id()
	{
		return $this->Query_ID;
	}

	// public: discard the query result
	// 清除查询结果
	// 修改：2005-08-22 将一些缓存清空也放在这里
	// 修改：2006-01-31 表、缓存连接不清空
	function free()
	{
		if ($this->Query_ID)
		{
			@mysql_free_result($this->Query_ID);
		}

		$this->Query_ID = 0;
		// 清空缓存的结果数组 2004-3-10
		$this->Row = 0;						// 当前的记录数
		$this->ArrayCach = false;		// 缓存所有结果的二维数组
		$this->array_x = array();			// 缓存指定字段的一维数组
		$this->Record = array();			// 解析后的结果数组

		//$this->Table = '';						// 设置 addnew 的开始标志，为新增加
		$this->AddNew = array();					// 设置 addnew 的数组，为新增加
		$this->AddArray = array();		// 设置update_talbe()中，字段与值
		$this->condition = '';					// 设置update_talbe()中的条件
		$this->sql_fields = '';			// 操作SQL操作的字段串,字段名用英文逗号分开 ,
		//$this->connect_key = null;		// 2005-01-19 当前缓冲的数组连接号
	}

	// public: perform a query
	// 执行查询语句
	// 添加：2005-08-22 自动解析
	// 添加：2006-01-25 cache功能
	function query_exe($Query_String)
	{
		if (empty($Query_String))
		{
			return 0;
		}
		if (!$this->connect())
		{
			return 0;		 //如果没有连接
		}
		
		if (!empty($this->conf['d']))
		{
			if (!@mysql_select_db($this->conf['d'], $this->Link_ID))
			{
				$this->halt( "<font color='#ff0000'>无法选择数据库:{$this->conf['d']}</font><BR>\n请检查：<BR>\n1、数据库是否存在<BR>\n2、您是否有相关操作的权限！");
				//return 0;
			}
		}

		// New query, discard previous result.
		if ( $this->Query_ID || is_array($this->ArrayCach))
		{
			$this->free();
		}

		if ( $this->conf['debug'])
		{
			echo ("Debug: query = {$Query_String}<br>\n");
		}

		$this->Query_ID = @mysql_query( $Query_String, $this->Link_ID);
		$this->Errno = mysql_errno();
		$this->Error = mysql_error();

		if ( !$this->Query_ID)
		{
			$this->halt( "错误的 SQL 语句:\n<br> " . $Query_String);
			return false;
		}
		// Will return nada if it fails. That's fine.
		return $this->Query_ID;
	}

	// public: perform a query
	// 执行查询语句
	// 添加：2005-08-22 自动解析
	// 添加：2006-01-25 cache功能
	function query($Query_String, $fetch=false)
	{
		$tem_cache_open = false;

		if (empty($Query_String))
		{
			return 0;
		}

		if ($fetch===false)
		{
			return $this->query_exe($Query_String);
		}

		//--- 缓存开
		if (!$this->conf['close'] && $this->conf['root'])
		{
			if (strtolower(substr(ltrim($Query_String), 0, 6))=='select')
			{
				$tem_cache_open = true;
				$r = cache_storage::read($this->conf, $Query_String, $this->conf['force_get']);
				if (is_array($r) && $r['result']===true)
				{
					// 缓存命中
					if ( $this->conf['debug'])
					{
						echo ("Debug: query_cache:read sucess = {$Query_String}<br>\n");
					}
					return $r['cont'];
				}
			}
		}

		if( !$this->query_exe($Query_String))
		{
			// 执行失败的时候，可以返回强取缓存的内容
			if (is_array($r) && $r['cont'])
			{
				if ( $this->conf['debug'])
				{
					echo ("Debug: query_cache:force_get = {$Query_String}<br>\n");
				}
				return $r['cont'];
			}
			return false;
		}

		$r_array = $this->return_all_array();
		if ($tem_cache_open)
		{
			if ( $this->conf['debug'])
			{
				echo ("Debug: query_cache:write = {$Query_String}<br>\n");
			}
			cache_storage::write($this->conf, $Query_String, $r_array);
		}
		return $r_array;
	}

	// public: walk result set
	function next_record( $type=MYSQL_ASSOC)
	{
		//解析执行的 sql 语句到数组 $this->Record 中，如果成功返回 真，否则返回 假
		//新添加解释类型 MYSQL_ASSOC(关联－默认), MYSQL_NUM(数字索引), MYSQL_BOTH(两者)
		//常量不要用引号！！二○○三年十二月十七日
		if ( !$this->Query_ID)
		{
			$this->halt( "方法 next_record 调用前没有查询结果。");
			return 0;
		}

		$this->Record = @mysql_fetch_array( $this->Query_ID, $type);
		$this->Row += 1;
		$this->Errno = mysql_errno();
		$this->Error = mysql_error();

		$stat = is_array( $this->Record);
		if ( !$stat && $this->conf['auto_free'])
		{
			@mysql_free_result($this->Query_ID);
		}
		return $stat;
	}

	// public: position in result set
	// 在结果中定位
	function seek( $pos = 0)
	{
		// 在结果集中定位，在结果集中定位，成功返回真，失败返回假并将指针定位到最后一个记录
		$status = @mysql_data_seek( $this->Query_ID, $pos);
		if ( $status)
		{
			$this->Row = $pos;
		}
		else
		{
			//$this->halt("seek($pos) 失败: 记录集中共有 " . $this->num_rows() . " 个记录。");

			/**
			 * half assed attempt to save the day, 
			 * but do not consider this documented or even
			 * desireable behaviour.
			 */
			@mysql_data_seek($this->Query_ID, $this->num_rows());
			$this->Row = $this->num_rows;
			return 0;
		}

		return 1;
	}

	// public: table locking
	function lock( $anbbs_table, $mode="write")
	{
		// 执行表锁定的操作
		// $anbbs_table 可以为数组，如果 $anbbs_table 为 $anbbs_table['read']……　形式，那么锁定表为只读
		//                  如果 $anbbs_table 为 $anbbs_table[0]……　形式，那么锁定表为 $mode 设定的模式
		$this->connect();

		$query = "lock tables ";
		if (is_array($anbbs_table))
		{
			while (list($key, $value) = each($anbbs_table))
			{
				if ($key == "read" && $key != 0)
				{
					$query .= "$value read, ";
				}
				else
				{
					$query .= "$value $mode, ";
				}
			}
			$query = substr( $query, 0, -2);
		}
		else
		{
			$query .= "$anbbs_table $mode";
		}
		$res = @mysql_query( $query, $this->Link_ID);
		if ( !$res)
		{
			$this->halt( "lock($anbbs_table, $mode) 失败。");
			return 0;
		}
		return $res;
	}

	function unlock()
	{
		// 解除全部的表锁定，成功返回真，失败返回假并显示失败信息
		$this->connect();

		$res = @mysql_query("unlock tables");
		if (!$res)
		{
			$this->halt("unlock() 失败。");
			return 0;
		}
		return $res;
	}

	// public: evaluate the result (size, width)
	function affected_rows()
	{
		// 返回本次操作影响的记录数
		return @mysql_affected_rows( $this->Link_ID);
	}

	function num_rows()
	{
		// 返回查询结果中的记录数
		return @mysql_num_rows( $this->Query_ID);
	}

	function num_fields()
	{
		// 返回结果中的字段数
		return @mysql_num_fields( $this->Query_ID);
	}

	// public: shorthand notation
	function nf()
	{
		// 返回查询结果中的记录数
		return $this->num_rows();
	}

	function np()
	{
		echo $this->num_rows();
	}

	function f($Name)
	{
		// 返回查询结果解析后的指定字段值
		return $this->Record[$Name];
	}

	function p($Name)
	{
		echo $this->Record[$Name];
	}

	// public: return table metadata
	function metadata( $anbbs_table = '', $full = false)
	{
		// 返回表的信息数组
		// 如果表为空返回数据库中所有表的信息数组
		$count = 0;
		$id    = 0;
		$res   = array();

		/**
		 * Due to compatibility problems with Table we changed the behavior
		 * of metadata();
		 * depending on $full, metadata returns the following values:
		 * 
		 * - full is false (default):
		 * $result[]:
		 * [0]["table"]table name
		 * [0]["name"] field name
		 * [0]["type"] field type
		 * [0]["len"]field length
		 * [0]["flags"]field flags
		 * 
		 * - full is true
		 * $result[]:
		 * ["num_fields"] number of metadata records
		 * [0]["table"]table name
		 * [0]["name"] field name
		 * [0]["type"] field type
		 * [0]["len"]field length
		 * [0]["flags"]field flags
		 * ["meta"][field name]index of field named "field name"
		 * The last one is used, if you have a field name, but no index.
		 * Test:if (isset($result['meta']['myfield'])) { ...
		 */ 
		// if no $anbbs_table specified, assume that we are working with a query
		// result
		if ( $anbbs_table) {
			$this->connect();
			$id = @mysql_list_fields( $this->conf['d'], $anbbs_table);
			if ( !$id)
			$this->halt( "Metadata query failed.");
		}
		else {
			$id = $this->Query_ID;
			if ( !$id)
			$this->halt("No query specified.");
		}

		$count = @mysql_num_fields($id);
		// made this IF due to performance (one if is faster than $count if's)
		if ( !$full){
			for ($i = 0; $i < $count; $i++) {
				$res[$i]["table"] = @mysql_field_table ($id, $i);
				$res[$i]["name"] = @mysql_field_name($id, $i);
				$res[$i]["type"] = @mysql_field_type($id, $i);
				$res[$i]["len"] = @mysql_field_len ($id, $i);
				$res[$i]["flags"] = @mysql_field_flags ($id, $i);
			}
		}
		else{ // full
			$res["num_fields"] = $count;
	
			for ($i = 0; $i < $count; $i++) {
				$res[$i]["table"] = @mysql_field_table ($id, $i);
				$res[$i]["name"] = @mysql_field_name($id, $i);
				$res[$i]["type"] = @mysql_field_type($id, $i);
				$res[$i]["len"] = @mysql_field_len ($id, $i);
				$res[$i]["flags"] = @mysql_field_flags ($id, $i);
				$res["meta"][$res[$i]["name"]] = $i;
			}
		}
		// free the result only if we were called on a table
		if ($anbbs_table) @mysql_free_result($id);
		return $res;
	}

	// private: error handling
	//公共函数，用于显示提示信息
	function halt( $msg)
	{
		$this->Error = @mysql_error($this->Link_ID);
		$this->Errno = @mysql_errno($this->Link_ID);

		if ( $this->conf['error_report'])
		{
			$this->haltmsg($msg);
		}
		if ( $this->conf['error_log'])
		{
			// 写入日志
			@log::append($this->conf['error_log'], date::get_date_time() . "\t{$_SERVER['PATH_TRANSLATED']}\t{$this->Errno}\t{$this->Error}\n");
		}
		if ( $this->conf['halt_on_error'])
		{
			die("Session halted.");
		}
	}

	function haltmsg($msg)
	{
		printf("<b>Database error:</b> %s<br>\n", $msg);
		printf("<b>MySQL Error</b>: %s (%s)<br>\n",	$this->Errno,$this->Error);
	}

	function table_names()
	{
		// 返回数据表中所有表的名字及所属数据库数组
		$this->query("SHOW TABLES");
		$i = 0;
		while ($info = mysql_fetch_row( $this->Query_ID))
		{
			$return[$i]["table_name"]       = $info[0];
			$return[$i]["tablespace_name"]  = $this->conf['d'];
			$return[$i]["database"]         = $this->conf['d'];
			$i++;
		}
		return $return;
	}
}

//--- 关闭数据库的连接 -----------
// 关闭指定参数的连接；如果未指定参数，关闭所有连接
//2004-8-6
// 修改：2005-09-30 不用传参数关闭所有

function an_sql_connect_close()
{
	global $an_db_var, $an_db_conn;

	// 如果未指定参数，关闭所有连接
	if(!is_array($an_db_var))
	{
		return 0;
	}

	foreach($an_db_conn as $v)
	{
		if (is_resource($v))
		{
			mysql_close($v);
		}
	}

	$an_db_var = null;
	$an_db_conn = null;

	return true;
}

register_shutdown_function('an_sql_connect_close');
?>
