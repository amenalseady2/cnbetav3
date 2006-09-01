<?php
/**
 * $Id: io.class.php 10 2006-07-06 04:00:52Z dengwei $
 * $Author: dengwei $
 * $Date: 2006-07-06 12:00:52 +0800 (星期�? 06 七月 2006) $
 * $HeadURL: svn://v3.cnbeta.com/classes/io.class.php $
 * $Revision: 10 $
 *
 * ����ʵ��������
 *
 */

class tags
{
	var $arrTags = array();

	function slice($strTag_string)
	{
		if( !$strTag_string ) return false;
		$this->arrTags = array_unique(explode(" ", $strTag_string));
		return true;
	}

	function clear()
	{
		$this->arrTags = array();
	}

	function seek($strKeywords)
	{
		if( in_array( $strKeywords, $this->arrTags , true) ) return true;
		return false;
	}
	
	function tagsCount()
	{
		return count($this->arrTags);
	}

	function removeByID($intID)
	{
		return array_splice($this->arrTags, $intID-1, 1);
	}
}
?>
