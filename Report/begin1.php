<?php
/*
+-----------------------------------------------------------------------+
| PHP версия 5.2.1
+-----------------------------------------------------------------------+
| Copyright (c) 2008 The SAPR Group
+-----------------------------------------------------------------------+
Данный модуль является заголовком любой страницы
+-----------------------------------------------------------------------+
| Авторы: Aleksey Budaev <baa1@azot.kuzbass.net>,<budaevaa@mail.ru>
+-----------------------------------------------------------------------+
*/
print "<!DOCTYPE HTML>\n";
print "<html>\n";
print "<head>\n";
print "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; CHARSET=utf-8\"> \n";
print "<title>Автоматизированная система учета трудозатрат</title>\n";
print "<LINK  rel=\"stylesheet\" type=\"text/css\" href=\"style.css\">\n";
print "<link href=\"../favicon.ico\" rel=\"icon\" type=\"image/x-icon\" />";
print "<!--[if IE]><link rel=\"stylesheet\" href=\"style_ie.css\" type=\"text/css\"/><![endif]-->\n"; 
print "<script language=\"JavaScript\" src=\"../js/jquery.js\" type=\"text/javascript\"></script>\n";
print "<script language=\"JavaScript\" src=\"../js/jquery.tipTip.js\" type=\"text/javascript\"></script>\n";
print "<script language=\"JavaScript\" src=\"../js/jquery.date_input.js\" type=\"text/javascript\"></script>\n";
print "<script type=\"text/javascript\">\$(\$.date_input.initialize);</script>\n";
print "</head>\n";
print "<body>\n";
?>
<script language="JavaScript" type="text/javascript">
$(function()
{
	$(".help").tipTip({activation: "click", maxWidth: "auto", edgeOffset: 10, defaultPosition: "top", contenr: true});
}
);
</script>