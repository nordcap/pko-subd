<?php
include_once("modules/lib/lib.php");


$Query = "SELECT work.id, work.Comment ".
"FROM work ".
"WHERE ".
"work.id =".escapeshellarg($_REQUEST['id_work']);

if ( !($dbResult = mysql_query($Query, $link)) )
{
	print "Выборка $Query не удалась!\n".mysql_error();
}
else {
	while ( $row = mysql_fetch_array($dbResult, MYSQL_BOTH) )
	{
		$str = "<p class=\"f-input-help\">Документ имеет {$row['Comment']}</p>";
	}
}

  print  $str;

	?>
