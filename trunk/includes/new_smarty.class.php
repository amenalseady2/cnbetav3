<?
/**
 * smarty����չ����smarty�̳�
 * ��չ��
 * 		1��smarty����Ŀ¼�Ķ�̬���ã�����Ŀǰʹ�õ�cache.class.php��cache��ʹ�õĻ������ã���ȡ��ͬ�����ļ���������smarty���У�ʵ����ͬ�Ļ��档
 */

include_once("smarty/Smarty.class.php");
include_once("base.class.php");

error_reporting(0);

class new_smarty extends Smarty
{
	var $conf;
	
	/**
	 * ���û���
	 *
	 * @conf ��������,��cache����ļ���ʽ��ͬ����ֻ�õ�����root,level,lifetime����
	 * @key  ��
	 */
	function set_cache($conf=array(), $key=null)
	{
		// �õ������ļ�
		if(!is_array($conf))
		{
			error_log('cache�����������',0);
			return;
		}
		else
		{
			$this->conf = $conf;
		}
		
		// �õ�����Ŀ¼ʹ�õ�KEY ����
		if (is_array($key))
		{
			$serialize_key = serialize($key);
		}
		else
		{
			$serialize_key = "";
		}
		
		// ����KEY �������л����MD5ֵ�Լ�level��ƴ������Ŀ¼
		$tmp = md5($serialize_key);
		
		if ($this->conf['level'])
		{
			$s = 0;
			$tmp_path = '';
			$tmp_len = strlen($tmp)/2;
			for ($i=0; $i<$this->conf['level'] && $i<$tmp_len; $i++)
			{
				$tmp_path .= $tmp{$s++} . $tmp{$s++} . '/';
			}
		}
		
		$cache_dir = $conf['root'].$tmp_path;
		if (!dir::mkdir_super($cache_dir))
		{
			error_log("��������Ŀ¼ʧ��".$cache_dir, 0);
			return;
		}
		
		// ��smarty�����cache_dir,cache_lifetime ��ֵ,�Լ�����ģ��caching��(1)
		$this->cache_dir = $cache_dir;
		$this->cache_lifetime = $this->conf['lifetime'];
		$this->caching = 1;
	}
}
?>