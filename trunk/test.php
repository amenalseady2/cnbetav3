<?php
/**
 * $Id$
 * 
 * ²âÊÔÎÄ¼þ
 */

require_once("config.php");
$db = NewADOConnection('mysql');
$db->Connect("localhost", "web", "web", "dvv");
$result = $db->Execute("SELECT * FROM files");
/**
 * $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
 */
if ($result === false) die("failed");  
while ( false && !$result->EOF) {
	for ($i=0, $max=$result->FieldCount(); $i < $max; $i++)
		print $result->fields[$i].' ';
	$result->MoveNext();
	print "<br>n";
}
var_dump($result->GetArray());
?>