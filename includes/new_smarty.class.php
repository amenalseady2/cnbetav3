<?
/**
 * smarty类扩展，由smarty继承
 * 扩展：
 * 		1、smarty缓存目录的动态设置，兼容目前使用的cache.class.php中cache类使用的缓存配置，读取相同配置文件，即可在smarty类中，实现相同的缓存。
 */

include_once("smarty/Smarty.class.php");
include_once("base.class.php");

error_reporting(0);

class new_smarty extends Smarty
{
	var $conf;
	
	/**
	 * 设置缓存
	 *
	 * @conf 配置数组,和cache类的文件格式相同，但只用到其中root,level,lifetime三项
	 * @key  键
	 */
	function set_cache($conf=array(), $key=null)
	{
		// 得到配置文件
		if(!is_array($conf))
		{
			error_log('cache配置数组错误',0);
			return;
		}
		else
		{
			$this->conf = $conf;
		}
		
		// 得到生成目录使用的KEY 数组
		if (is_array($key))
		{
			$serialize_key = serialize($key);
		}
		else
		{
			$serialize_key = "";
		}
		
		// 根据KEY 数组序列化后的MD5值以及level，拼出缓存目录
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
			error_log("创建缓存目录失败".$cache_dir, 0);
			return;
		}
		
		// 给smarty对象的cache_dir,cache_lifetime 赋值,以及声明模板caching打开(1)
		$this->cache_dir = $cache_dir;
		$this->cache_lifetime = $this->conf['lifetime'];
		$this->caching = 1;
	}
}
?>