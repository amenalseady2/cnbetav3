<?php
/*
 * $Id$
 */

// 程序名：xml.php
// 用  途：根据传入内容生成 XML 文件
// 作  者：邓威
// 日  期：2006-03-04
// 修　改：2006-03-13 邓威，错误修正
// 修　改：2006-07-07 邓威，重写代码，把原先的三个 class 改写成一个
error_reporting(E_ALL);
/*
$tree = new xml;
$tree->type("xml");
$tree->tag("tree");
$tree->encoding('GB2312');
$tree->attributes( Array('aaa' => 111, 'bbb' => 222 ) );
$tree1 = $tree;
$tree1->tag("tree1");
$tree1->value('aaaa',true);

$tree2 = new xml;
$tree2->tag('tree2');
$tree2->attribute('abc','cba');

$tree->addNode($tree1);
$tree->addNode($tree1);
$tree->addNode($tree1);
$tree->addNode($tree1);
$tree->addNode($tree2);
$tree->addNode($tree1);
$tree->addNode($tree1);
$tree->addNode($tree1);

header('Content-Type: text/xml'); 
echo $tree->display();
 */

$tree11 = new xml("tagname", 'asdfasdf');
echo $tree11->display();

$tree22 = new xml('tagname1',123123, array( 'bb' => 11 ));
echo $tree22->display();

$tree33 = new xml('tagname3');
echo $tree33->display();

class xml
{
	var $NodeTag		= "";		// 当前对象的标签名
	var $NodeType		= "node";	// 共有四种类型：element, node, xml
	var $NodeNodes		= array();	// 子结点集合
	var $NodeEncoding	= "utf-8";	// 编码方式
	var $NodeAttributes	= array();	// 属性集合
	var $NodeVersion	= "1.0";	// 版本号
	var $cdata		= false;	// 是否使用 cdata 分隔符
	var $NodeValue		= "";		// 结点值


	/*
	 * 构造函数
	 */

	function xml($strTag = NULL,$mixValue = NULL, $arrAttributes = NULL, $cdata = false)
	{
		if( !$strTag ) return false;
		$this->tag($strTag);
		if( is_array( $arrAttributes ) ) $this->attributes($arrAttributes);
		if( method_exists($mixValue, "type") && ( $mixValue->type() == "node" || $mixValue->type() == "element" ))
		{
			$this->addNode($mixValue);
		}
		else
		{
			if( is_string($mixValue) ) $this->cdata = true;
			$this->value($mixValue, $cdata);
		}
		return $this;
	}

	/*
	 * 返回子节点数量
	 */

	function NodeCount()
	{
		return count($this->NodeNodes);
	}

	/*
	 * XML 文件版本号
	 * 现行的 XML 文件中只有 1.0 版本
	 */

	function version($strVersion = NULL)
	{
		if( !$strVersion ) return $this->NodeVersion;
		$this->NodeVersion = $strVersion;
		return true;
	}

	/*
	 * XML 文件编码
	 */

	function encoding($strEncoding = NULL)
	{
		if( !$strEncoding ) return $this->NodeEncoding;
		$this->NodeEncoding = $strEncoding;
		return true;
	}

	/*
	 * 节点标签名
	 */

	function tag($strTag = NULL)
	{
		if( !$strTag ) return $this->NodeTag;
		$this->NodeTag = $strTag;
		return true;
	}

	/*
	 * 设置节点属性
	 */

	function attribute($strAttributeName = NULL, $mixAttributeValue = NULL)
	{
		if( !$strAttributeName ) return false;
		// 设置属性
		if( $mixAttributeValue )
		{
			$this->NodeAttributes[$strAttributeName] = $mixAttributeValue;
		}
		else // 删除属性
		{
			unset( $this->NodeAttributes[$strAttributeName] );
		}
		return true;
	}

	/*
	 * 设置节点属性集
	 */

	function attributes($arrAttributes = NULL)
	{
		if( !$arrAttributes ) return false;
		if( !is_array($arrAttributes) ) return false;
		$this->NodeAttributes = $arrAttributes;
		return true;
	}

	/*
	 * 子节点集合
	 */
	function nodes($arrNodes = NULL)
	{
		if( !$arrNodes ) return $this->NodeNodes;
		if( !is_array($arrNodes) ) return false;
		$this->NodeNodes = $arrNodes;
		return true;
	}

	/*
	 * 节点类型
	 */

	function type($strType = NULL)
	{
		if( !$strType ) return $this->NodeType;
		if( $strType != "xml" && $strType != "node" && $strType != "element" ) return false;
		$this->NodeType = $strType;
		return true;
	}

	/*
	 * 加入新的节点对象
	 */

	function addNode($objNode = NULL)
	{
		if( !$objNode ) return false;
		// lcl 是 local 的缩写，lcl_type 即是 local_type 局部变量的缩写
		$lcl_type = $objNode->type();
		if( $lcl_type != "element" && $lcl_type != "node" && $lcl_type != "xml" ) return false;
		if( $lcl_type == "xml" ) $objNode->type("node");
		// 加入新的节点对象
		$this->NodeNodes[count($this->NodeNodes)] = $objNode;
		return true;
	}

	/*
	 * 删除指定的节点
	 */

	function removeNodeByIndex($intIndex = 0)
	{
		return array_splice($this->NodeNodes, $intIndex-1, 1);
	}

	/*
	 * 删除指定的属性
	 */

	function removeAttributeByIndex($intIndex = 0)
	{
		return array_splice($this->NodeAttributes, $intIndex-1, 1);
	}

	/*
	 * 节点值
	 */

	function value($mixValue = '' , $cdata = false)
	{
		if( !$mixValue ) return $this->NodeValue;
		if( $cdata ) $this->cdata = true;
		$this->NodeValue = $mixValue;
		return true;
	}

	/*
	 * 得到显示字符串
	 */

	function display($src_encoding = NULL, $intDepth = 0)
	{
		$strDisplay = "";
		$strAttribute = "";	

		if( !$intDepth ) $intDepth = 0;

		// 根节点
		if( $this->type() == "xml" ) 
		{
			$strDisplay .= "<?xml version=\"" . $this->version() . "\" encoding=\"" . $this->encoding() . "\" ?>\n";
		}

		// 缩近修正
		$TAB_FIX = str_repeat( "\t" , $intDepth);

		// 属性
		if( ($lcl_count = count( $this->attributes() ) ) > 0 )
		{
			// 有属性
			foreach( $this->NodeAttributes as $SingleAttribute => $SingleValue )
			{
				$strAttribute .= ' ' . $SingleAttribute . '="' . $SingleValue . '"';
			}
		}

		// 生成标签头
		$strDisplay .= $TAB_FIX . "<" . $this->NodeTag . $strAttribute;

		// 节点
		if( ($lcl_counts = $this->NodeCount()) > 0 )
		{
			// 关闭标签头
			$strDisplay .= ">\n";
			// 如果有子节点集
			foreach( $this->NodeNodes as $SingleNode )
			{
				$strDisplay .= $SingleNode->display($src_encoding, $intDepth+1);
			}
			$strDisplay .= "</" . $this->NodeTag . ">\n";
		}
		else
		{
			// 没有子节点
			if( !$this->NodeValue )
			{
				// 节点没有值				
				// 关闭标签头
				$strDisplay .= " />";
			}
			else
			{							
				// 节点有值
				$strDisplay .= ">\n";
				$strDisplay .= $TAB_FIX;

				if( $this->cdata )
				{
					$strDisplay .= "<![CDATA[" . ($src_encoding?iconv($src_encoding, $this->encoding() , $this->NodeValue):$this->NodeValue) . "]]>\n";
				}
				else
				{
					$strDisplay .= ($src_encoding?iconv($src_encoding, $this->encoding() , $this->NodeValue):$this->NodeValue) . "\n";
				}

				$strDisplay .= $TAB_FIX . "</" . $this->NodeTag . ">\n";
			}

		}		
		return $strDisplay;
	}
}

?>
