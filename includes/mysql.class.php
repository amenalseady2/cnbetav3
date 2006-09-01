<?php
//*-------------------------------------------------------------------------
// �޸ģ�2004-8-7 �޸���չ��� close() ������ʹ�����ע������������Ǳ������ӵ���Դ�� 2004-8-7
// �޸ģ�2005-12-14 ʹ�������������Ľ������ã�������ʾ���Թرգ�����д����־��
// ��ӣ�2005-12-18 ����µ��ڲ����� _get_table()��
// ��ӣ�2005-12-18 ����µķ��� update_id(),delete_id()��
// ��ӣ�2005-12-18 ����µ��ڲ����� _get_table()��
// �޸ģ�2005-12-18 insert_array() ִ��û�з��ؽ����
// �޸ģ�2005-12-28 return_all_array() ���û�н��������false��
// �޸ģ�2006-01-09 ��ѡ�����ݿ�Ĳ�������query()�н��У������������ݿ�ûΪ�յ�����½��С�
// �޸ģ�2006-01-26 query($sql, $fetch)���� $fetch Ϊtrue Ҳ�����Զ������򿪵�ʱ�򣬿���֧�ֻ��棬ͬʱ��������Ҫ�򿪣�����������뻺����һ����
// �޸ģ�2006-02-04 insert_row()�������ַ������˵�ʱ������һ��bug
// 
//-------------------------------------------------------------------------*/

include_once('base.class.php');
include_once('log.class.php');
include_once('cache.class.php');

define( 'DB_MYSQL', '20060204');
define( 'DB_LOG_PATH', '/data1/logs/baselib_error/mysql.error');

class MYSQL
{
	// ��������
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
		'auto_free' => true,		// �Զ���ա�Set to 1 for automatic mysql_free_result()

		//--- ��������
		'root' => '/data0/cache/newmanshow/', // ��Ŀ¼
		'level' => 2, // Ŀ¼�ּ� 
		'ext' => '.php', // ��չ��
		'master' => 'mysql_class', // �� key 
		'type' => C_FILE,				// ����������� C_FILE �ļ���C_MYSQL ���ݿ⣬C_BERKELEY ���ݿ⣬C_SHMOP �����ڴ�
		'lifetime' => 900,			// ����ʱ���룬����60�룬̫�ͷ��������ϵͳ����
		'debug' => false,			// �򿪵�����Ϣ
		'close' => false,			// �ر� cache 

		'serialize' => true, // ���л�
		//'base64' => true, // base64����
		//'gz_level' => 6, // ѹ���ĳ̶�1��9��0��գ���ѹ����
		//'module' => 'chache_mod_exmaple_test.php',			// ��ȡģ��
		'force_get' => true, // ��ȡ�����ݿ�ʧ�ܵ�ʱ���Ƿ�ǿȡ���������
	);
	/*
	*/
 
	var $Seq_Table = "db_sequence"; // ָ����������������Ϣ�ı�

	// public: result array and current row number
	// ���ò�ѯ������顢�����
	var $Record = array();			// ������Ľ������
	var $Row;						// ��ǰ�ļ�¼��

	// public: current error number and error text
	var $Errno = 0;					// �����
	var $Error = "";				// ������Ϣ

	// public: this is an api revision, not a CVS revision.
	var $type = "mysql";			// �������
	var $revision = "1.2";			// ��İ���

	// private: link and query handles
	// ���ݿ����Ӻ�
	var $Link_ID  = 0; 				// MYSQL���������ָ��
	var $Query_ID = 0; 				// �������ݿ��Ϊ������

	var $anbbs_table;						// ���� addnew �Ŀ�ʼ��־��Ϊ������
	var $AddNew;					// ���� addnew �����飬Ϊ������
	var $AddArray = array();		// ����update_talbe()�У��ֶ���ֵ
	var $condition;					// ����update_talbe()�е�����
	var $ArrayCach = false;		// �������н���Ķ�ά����
	var $array_x = array();			// ����ָ���ֶε�һά����
	var $sql_fields = '';			// ����SQL�������ֶδ�,�ֶ�����Ӣ�Ķ��ŷֿ� ,
	var $connect_key = null;		// 2005-01-19 ��ǰ������������Ӻ�

	// public: constructor
	// ���캯��
	function MYSQL($query="")
	{
		if (!empty($query))
		{
			$this->query($query);
		}
	}

	//--- ���ݿ�����Ӳ��� --------------
	// �޸ģ�����Դ���Ӳ������浽����ȫ�������� 2004-8-7

	function connect( $Database=null)
	{
		/**
		* �޸ĳɿ���ʹ��ȫ�����õı���
		��ֵ˳�򣺳������� > ȫ�ֱ��� > ���ж���
		���������ʮһ�¶�ʮ����
		// �޸ģ�2005-12-14 ʹ�����飬ȡ����������
		*/

		if (is_resource($this->Link_ID))
		{
			return $this->Link_ID;
		}

		global $an_db_var;		 // ���ݿ����Ӳ�������2004-8-7
		global $an_db_conn;		 // �������ӵ���Դ����2004-8-7

		//--- ���ղ����Ĵ���
		if (!is_array($Database))
		{
			$this->halt("connect ���Ӳ����������顣");
			return false;
		}
		$this->conf = $Database;

		// establish connection, select database

		/*
		*/
		// �ж���ͬ�Ĳ����Ƿ����,����ֱ�ӷ���
		// �������Ӳ���
		$var = array();
		$var['d'] = $this->conf['d'];
		$var['h'] = $this->conf['h'];
		$var['u'] = $this->conf['u'];
		$var['p'] = $this->conf['p'];

		if (!empty($an_db_var))
		{
			if ($this->conf['debug'])
			{
				echo "CONNECT: �жϻ������Ƿ�������ӡ���\n";
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
							echo "CONNECT: ʹ�û�������ӣ�\n";
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

			// ���ӳɹ��󣬽����������Ӻ�ѹ������
			if ($this->Link_ID)
			{
				$key = count($an_db_conn);
				//echo '$var';var_dump($var);
				$an_db_var[$key]  = $var;
				$an_db_conn[$key] = $this->Link_ID;
				$this->connect_key = $key;
				if ($this->conf['debug'])
				{
					echo "CONNECT: �½����ӣ��R�뻺�档\n";
				}
				// echo "�½����ӣ�";
				//echo '$an_db_var';var_dump($an_db_var);
				//echo '$an_db_conn';var_dump($an_db_conn);
			}
			else
			{
				//$this->halt("connect($this->conf['h'], $this->conf['u'], \$Password) ʧ�ܡ�<BR><font color='#ff0000'>���ݿ����ӳ���</font><BR>\n���飺<BR>\n1�����ݿ�ϵͳ�Ƿ�����<BR>\n2�����Ӳ����Ƿ���ȷ��");
				$this->halt("���ݿⷱæ���Ժ���ʡ�");
				return false;
			}
		}
		return $this->Link_ID;
	}

	/////////////////////////////////////////////////////////////////////////////////////////

	//public ���ص�ǰ�����������
	// ���������ʮ����ʮ����
	// ��ӣ�2005-08-22 �Զ�����
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

	//public ���ذ������н�� 2ά ����
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

	//public ���ذ�����ָ���ֶε�һά����
	// 2004-3-2
	// ����ͨ�����ܲ���
	// �޸ģ�2004-12-17 �Ի����������е�У�鼰�ݴ�

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
			// ����������(��ά)�е�һ����¼û�и��ֶΣ���ô���ؿա�
			return false;
		}
		$this->array_x = array();
		foreach ( $this->ArrayCach as $val)
		{
			$this->array_x[] = $val[$filed];
		}
		return $this->array_x;
	}

	//public ���ذ�����ָ���ֶε�һά����ĺϲ��ִ�
	// 2004-3-10
	// 2004-3-10 ͨ������
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

	//public �����²����ID��
	// �������������ʮ���գ������²����ID��
	function insert_id()
	{
		return @mysql_insert_id($this->Link_ID);
	}

	// public ���ò�����
	//������������¶�ʮ����
	function set_table( $anbbs_table)
	{
		if ( $anbbs_table)
		{
			$this->Table = $anbbs_table;
			return true;
		}
		$this->halt( " set_table() ��Ϊ�ա�");
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
			$this->halt( "���� insert_array() ���ݱ�Ϊ�ա�");
		}
		return $this->Table;
	}

	// public ��һ���������¼����ӵ����ݿ⣬���ز���ļ�¼��
	//$array[n]['�ֶ�'] = 'ֵ' ����ά��������������뵽���ݿ���
	//������������¶�ʮ����
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

	// private ����һ�������ֶ���ֵ�� "����ֵ��" ���飬�����Ƿ�ɹ�
	// ��һ����¼����ӵ����ݿ�
	// $array['�ֶ�'] = 'ֵ' ��һά��������������뵽���ݿ���
	//������������¶�ʮ����
	function insert_array($array, $anbbs_table='', $sql_fields='')
	{
		$anbbs_table = $this->_get_table($anbbs_table);

		if (!is_array( $array))
		{
			$this->halt( "���� insert_array() ��1����������������� ����Ϊ {$this->Table} ");
			return false;
		}

		if ($sql_fields)
		{
			// ��ӽ���SQL�������ֶ��ַ���
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
			echo "����������䣺";
		}
		return $this->query($sql);
	}

	//public ����һ������"����ֵ��"��һά����
	// 2004-8-10 ָ���Թؼ��ֶν���SQL������
	// �޸ģ�2004-8-12 �趨���ʱ�򲻸��ĵ�ǰĬ�ϵı�

	function update_table( $array, $condition, $anbbs_table='', $sql_fields='')
	{
		//$array		"����ֵ��"��һά����
		//$condition	����
		//$anbbs_table=''		��
		//������������һ��������ֵ�ԡ��������ִ���������������һ������������ֵ�ԡ���һά���飨�������ʡ�ԣ�
		$anbbs_table = $this->_get_table($anbbs_table);

		if ( $condition)
		{
			$this->condition = " WHERE {$condition}";
		}
		if ( !is_array($array))
		{
			$this->halt(" update_table( \$array, $condition, $anbbs_table) ���󣺵�һ�������������飡");
		}

		if ($sql_fields)
		{
			// ��ӽ���SQL�������ֶ��ַ���
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
			echo "����������䣺";
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
			echo "ɾ�� id={$id}��";
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
			echo "���� id={$id}��";
		}
		return $this->update_table( $array, "id='{$id}'", $anbbs_table, $sql_fields);
	}
	
	//public �����¼������ִ�в����������¼����ִ�и��²���
	// bool/int insert_update( $Ҫ���������, $Ҫ���µ�����, $��='', $sql����ֶ�='');
	// 2004-5
	// ԭ����ִ�в������ʱ������ $array ����һ�������жϣ�������������ֵ���ظ�
	// ��ִ�и��µĲ���ʱ�����ϲ����ɵ�������Ϊ���µ��������и��²���
	// ����:2004-8-12 �����Ż������趨sql����ֶ�

	function insert_update( $array, $update_fild, $anbbs_table='', $sql_fields='')
	{
		if ( empty( $array) && !is_array($array))
		{
			$this->halt(" insert_update( \$array, $update_fild, $anbbs_table) ���󣺵�1�������������飡");
		}

		if ( empty( $update_fild) && !is_array($update_fild))
		{
			$this->halt("insert_update( \$array, $update_fild, $anbbs_table) ���󣺵�2�������������飡");
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
			echo "���������������䣭";
		}
		if ($this->f( 'count(*)') < 1)
		{
			// �����¼������,ִ�в������
			return $this->insert_array( $array, $anbbs_table, $sql_fields);
		}

		// ���ִ�и��²���
		return update_table( $update_fild, $s_where, $anbbs_table, $sql_fields);
	}

	//--- ���ִ��SQL������ֶ� ---------
	// 2004-8-10

	function set_sql_fields($fields='')
	{
		$this->sql_fields = ',,' . $fields . ',';
	}

	//--- ���ִ��SQL������ֶ� ---------
	// ��������ֶ����ֶ��ִ�Ϊ�գ����� 0
	// ������ڷ��� true �� ���� false
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

	// ��ӣ�2005-08-22 close( $fetch=false) �Ƿ��ͷ�������Դ
	function close($link=false)
	{
		//�ͷ���Դ
		$this->free();
		if ($link && $this->Link_ID)
		{
			global $an_db_var, $an_db_conn;		 // �������ӵ���Դ����2004-8-7

			@mysql_close($this->Link_ID);
			unset($an_db_var[$this->connect_key]);
			unset($an_db_conn[$this->connect_key]);
		}

		//�ͷ���Դ
		// ע������� 2004-8-7
		// �޸ģ�2005-01-26 ��ͬ��php�汾���ܲ�֧��
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
	// �����ѯ���
	// �޸ģ�2005-08-22 ��һЩ�������Ҳ��������
	// �޸ģ�2006-01-31 ���������Ӳ����
	function free()
	{
		if ($this->Query_ID)
		{
			@mysql_free_result($this->Query_ID);
		}

		$this->Query_ID = 0;
		// ��ջ���Ľ������ 2004-3-10
		$this->Row = 0;						// ��ǰ�ļ�¼��
		$this->ArrayCach = false;		// �������н���Ķ�ά����
		$this->array_x = array();			// ����ָ���ֶε�һά����
		$this->Record = array();			// ������Ľ������

		//$this->Table = '';						// ���� addnew �Ŀ�ʼ��־��Ϊ������
		$this->AddNew = array();					// ���� addnew �����飬Ϊ������
		$this->AddArray = array();		// ����update_talbe()�У��ֶ���ֵ
		$this->condition = '';					// ����update_talbe()�е�����
		$this->sql_fields = '';			// ����SQL�������ֶδ�,�ֶ�����Ӣ�Ķ��ŷֿ� ,
		//$this->connect_key = null;		// 2005-01-19 ��ǰ������������Ӻ�
	}

	// public: perform a query
	// ִ�в�ѯ���
	// ��ӣ�2005-08-22 �Զ�����
	// ��ӣ�2006-01-25 cache����
	function query_exe($Query_String)
	{
		if (empty($Query_String))
		{
			return 0;
		}
		if (!$this->connect())
		{
			return 0;		 //���û������
		}
		
		if (!empty($this->conf['d']))
		{
			if (!@mysql_select_db($this->conf['d'], $this->Link_ID))
			{
				$this->halt( "<font color='#ff0000'>�޷�ѡ�����ݿ�:{$this->conf['d']}</font><BR>\n���飺<BR>\n1�����ݿ��Ƿ����<BR>\n2�����Ƿ�����ز�����Ȩ�ޣ�");
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
			$this->halt( "����� SQL ���:\n<br> " . $Query_String);
			return false;
		}
		// Will return nada if it fails. That's fine.
		return $this->Query_ID;
	}

	// public: perform a query
	// ִ�в�ѯ���
	// ��ӣ�2005-08-22 �Զ�����
	// ��ӣ�2006-01-25 cache����
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

		//--- ���濪
		if (!$this->conf['close'] && $this->conf['root'])
		{
			if (strtolower(substr(ltrim($Query_String), 0, 6))=='select')
			{
				$tem_cache_open = true;
				$r = cache_storage::read($this->conf, $Query_String, $this->conf['force_get']);
				if (is_array($r) && $r['result']===true)
				{
					// ��������
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
			// ִ��ʧ�ܵ�ʱ�򣬿��Է���ǿȡ���������
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
		//����ִ�е� sql ��䵽���� $this->Record �У�����ɹ����� �棬���򷵻� ��
		//����ӽ������� MYSQL_ASSOC(������Ĭ��), MYSQL_NUM(��������), MYSQL_BOTH(����)
		//������Ҫ�����ţ������������ʮ����ʮ����
		if ( !$this->Query_ID)
		{
			$this->halt( "���� next_record ����ǰû�в�ѯ�����");
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
	// �ڽ���ж�λ
	function seek( $pos = 0)
	{
		// �ڽ�����ж�λ���ڽ�����ж�λ���ɹ������棬ʧ�ܷ��ؼٲ���ָ�붨λ�����һ����¼
		$status = @mysql_data_seek( $this->Query_ID, $pos);
		if ( $status)
		{
			$this->Row = $pos;
		}
		else
		{
			//$this->halt("seek($pos) ʧ��: ��¼���й��� " . $this->num_rows() . " ����¼��");

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
		// ִ�б������Ĳ���
		// $anbbs_table ����Ϊ���飬��� $anbbs_table Ϊ $anbbs_table['read']��������ʽ����ô������Ϊֻ��
		//                  ��� $anbbs_table Ϊ $anbbs_table[0]��������ʽ����ô������Ϊ $mode �趨��ģʽ
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
			$this->halt( "lock($anbbs_table, $mode) ʧ�ܡ�");
			return 0;
		}
		return $res;
	}

	function unlock()
	{
		// ���ȫ���ı��������ɹ������棬ʧ�ܷ��ؼٲ���ʾʧ����Ϣ
		$this->connect();

		$res = @mysql_query("unlock tables");
		if (!$res)
		{
			$this->halt("unlock() ʧ�ܡ�");
			return 0;
		}
		return $res;
	}

	// public: evaluate the result (size, width)
	function affected_rows()
	{
		// ���ر��β���Ӱ��ļ�¼��
		return @mysql_affected_rows( $this->Link_ID);
	}

	function num_rows()
	{
		// ���ز�ѯ����еļ�¼��
		return @mysql_num_rows( $this->Query_ID);
	}

	function num_fields()
	{
		// ���ؽ���е��ֶ���
		return @mysql_num_fields( $this->Query_ID);
	}

	// public: shorthand notation
	function nf()
	{
		// ���ز�ѯ����еļ�¼��
		return $this->num_rows();
	}

	function np()
	{
		echo $this->num_rows();
	}

	function f($Name)
	{
		// ���ز�ѯ����������ָ���ֶ�ֵ
		return $this->Record[$Name];
	}

	function p($Name)
	{
		echo $this->Record[$Name];
	}

	// public: return table metadata
	function metadata( $anbbs_table = '', $full = false)
	{
		// ���ر����Ϣ����
		// �����Ϊ�շ������ݿ������б����Ϣ����
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
	//����������������ʾ��ʾ��Ϣ
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
			// д����־
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
		// �������ݱ������б�����ּ��������ݿ�����
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

//--- �ر����ݿ������ -----------
// �ر�ָ�����������ӣ����δָ���������ر���������
//2004-8-6
// �޸ģ�2005-09-30 ���ô������ر�����

function an_sql_connect_close()
{
	global $an_db_var, $an_db_conn;

	// ���δָ���������ر���������
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
