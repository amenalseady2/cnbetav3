<?php
/**
 * $Id: io.class.php 10 2006-07-06 04:00:52Z dengwei $
 * $Author: dengwei $
 * $Date: 2006-07-06 12:00:52 +0800 (鏄熸湡鍥? 06 涓冩湀 2006) $
 * $HeadURL: svn://v3.cnbeta.com/classes/io.class.php $
 * $Revision: 10 $
 *
 * 必须实例化运行
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
