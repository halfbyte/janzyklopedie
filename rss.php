<?php
require_once "HTML/IT.php"; 
require_once "inc/funcs.inc.php";
header("Content-type: text/xml");
if ($entries = get_latest_articles()) {
	 $tpl = new IntegratedTemplate("tpl");
	 $tpl->loadTemplatefile("rss.tpl.html", true, true);
	 foreach($entries as $entry) {
				$tpl->SetCurrentBlock("eintrag");
				$tpl->SetVariable("title",$entry[title]);
				#$tpl->SetVariable("text","no more");
				$tpl->SetVariable("text",$entry[text]);
				$tpl->SetVariable("date",$entry[date]);
				$tpl->SetVariable("link",htmlspecialchars("http://jan.krutisch.de/janzyklopedie/index.php?action=entry&id=".$entry[entry_id]));
				$tpl->ParseCurrentBlock("eintrag");
				
	}
	$tpl->show();
} else die ("Sorry, didn't work out...");

?>

