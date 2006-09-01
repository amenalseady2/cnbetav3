<?php
/*
 * $Id$
 */

// ��������xml.php
// ��  ;�����ݴ����������� XML �ļ�
// ��  �ߣ�����
// ��  �ڣ�2006-03-04
// �ޡ��ģ�2006-03-13 ��������������
// �ޡ��ģ�2006-07-07 ��������д���룬��ԭ�ȵ����� class ��д��һ��
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
	var $NodeTag		= "";		// ��ǰ����ı�ǩ��
	var $NodeType		= "node";	// �����������ͣ�element, node, xml
	var $NodeNodes		= array();	// �ӽ�㼯��
	var $NodeEncoding	= "utf-8";	// ���뷽ʽ
	var $NodeAttributes	= array();	// ���Լ���
	var $NodeVersion	= "1.0";	// �汾��
	var $cdata		= false;	// �Ƿ�ʹ�� cdata �ָ���
	var $NodeValue		= "";		// ���ֵ


	/*
	 * ���캯��
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
	 * �����ӽڵ�����
	 */

	function NodeCount()
	{
		return count($this->NodeNodes);
	}

	/*
	 * XML �ļ��汾��
	 * ���е� XML �ļ���ֻ�� 1.0 �汾
	 */

	function version($strVersion = NULL)
	{
		if( !$strVersion ) return $this->NodeVersion;
		$this->NodeVersion = $strVersion;
		return true;
	}

	/*
	 * XML �ļ�����
	 */

	function encoding($strEncoding = NULL)
	{
		if( !$strEncoding ) return $this->NodeEncoding;
		$this->NodeEncoding = $strEncoding;
		return true;
	}

	/*
	 * �ڵ��ǩ��
	 */

	function tag($strTag = NULL)
	{
		if( !$strTag ) return $this->NodeTag;
		$this->NodeTag = $strTag;
		return true;
	}

	/*
	 * ���ýڵ�����
	 */

	function attribute($strAttributeName = NULL, $mixAttributeValue = NULL)
	{
		if( !$strAttributeName ) return false;
		// ��������
		if( $mixAttributeValue )
		{
			$this->NodeAttributes[$strAttributeName] = $mixAttributeValue;
		}
		else // ɾ������
		{
			unset( $this->NodeAttributes[$strAttributeName] );
		}
		return true;
	}

	/*
	 * ���ýڵ����Լ�
	 */

	function attributes($arrAttributes = NULL)
	{
		if( !$arrAttributes ) return false;
		if( !is_array($arrAttributes) ) return false;
		$this->NodeAttributes = $arrAttributes;
		return true;
	}

	/*
	 * �ӽڵ㼯��
	 */
	function nodes($arrNodes = NULL)
	{
		if( !$arrNodes ) return $this->NodeNodes;
		if( !is_array($arrNodes) ) return false;
		$this->NodeNodes = $arrNodes;
		return true;
	}

	/*
	 * �ڵ�����
	 */

	function type($strType = NULL)
	{
		if( !$strType ) return $this->NodeType;
		if( $strType != "xml" && $strType != "node" && $strType != "element" ) return false;
		$this->NodeType = $strType;
		return true;
	}

	/*
	 * �����µĽڵ����
	 */

	function addNode($objNode = NULL)
	{
		if( !$objNode ) return false;
		// lcl �� local ����д��lcl_type ���� local_type �ֲ���������д
		$lcl_type = $objNode->type();
		if( $lcl_type != "element" && $lcl_type != "node" && $lcl_type != "xml" ) return false;
		if( $lcl_type == "xml" ) $objNode->type("node");
		// �����µĽڵ����
		$this->NodeNodes[count($this->NodeNodes)] = $objNode;
		return true;
	}

	/*
	 * ɾ��ָ���Ľڵ�
	 */

	function removeNodeByIndex($intIndex = 0)
	{
		return array_splice($this->NodeNodes, $intIndex-1, 1);
	}

	/*
	 * ɾ��ָ��������
	 */

	function removeAttributeByIndex($intIndex = 0)
	{
		return array_splice($this->NodeAttributes, $intIndex-1, 1);
	}

	/*
	 * �ڵ�ֵ
	 */

	function value($mixValue = '' , $cdata = false)
	{
		if( !$mixValue ) return $this->NodeValue;
		if( $cdata ) $this->cdata = true;
		$this->NodeValue = $mixValue;
		return true;
	}

	/*
	 * �õ���ʾ�ַ���
	 */

	function display($src_encoding = NULL, $intDepth = 0)
	{
		$strDisplay = "";
		$strAttribute = "";	

		if( !$intDepth ) $intDepth = 0;

		// ���ڵ�
		if( $this->type() == "xml" ) 
		{
			$strDisplay .= "<?xml version=\"" . $this->version() . "\" encoding=\"" . $this->encoding() . "\" ?>\n";
		}

		// ��������
		$TAB_FIX = str_repeat( "\t" , $intDepth);

		// ����
		if( ($lcl_count = count( $this->attributes() ) ) > 0 )
		{
			// ������
			foreach( $this->NodeAttributes as $SingleAttribute => $SingleValue )
			{
				$strAttribute .= ' ' . $SingleAttribute . '="' . $SingleValue . '"';
			}
		}

		// ���ɱ�ǩͷ
		$strDisplay .= $TAB_FIX . "<" . $this->NodeTag . $strAttribute;

		// �ڵ�
		if( ($lcl_counts = $this->NodeCount()) > 0 )
		{
			// �رձ�ǩͷ
			$strDisplay .= ">\n";
			// ������ӽڵ㼯
			foreach( $this->NodeNodes as $SingleNode )
			{
				$strDisplay .= $SingleNode->display($src_encoding, $intDepth+1);
			}
			$strDisplay .= "</" . $this->NodeTag . ">\n";
		}
		else
		{
			// û���ӽڵ�
			if( !$this->NodeValue )
			{
				// �ڵ�û��ֵ				
				// �رձ�ǩͷ
				$strDisplay .= " />";
			}
			else
			{							
				// �ڵ���ֵ
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
