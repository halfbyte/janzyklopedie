<?php
require_once "HTML/IT.php"; 
require_once "inc/funcs.inc.php";

	session_start();
	$PASSWORD = "rock10";

	$headtpl = new IntegratedTemplate("tpl");
	$headtpl->loadTemplatefile("head.tpl.html", true, true);

	$tpl = new IntegratedTemplate("tpl");

	
	$error_message = "";


	
# post actions

if ($_POST[action]!="") {
	if ($_POST[action] == "login") {
		$headtpl->SetVariable("titel","Login");
		if ($_POST[password] == $PASSWORD) {
			$_SESSION[password] = $PASSWORD;
			$error_message = "Logged in";
		} else {
			$error_message = "Wrong password. Fool.";
		}
	}	elseif ($_POST[action] == "new") {
	
		$headtpl->SetVariable("titel","Neuen Eintrag speichern");
		if ($_SESSION[password] == $PASSWORD) {
			new_article();
			$error_message = "New Article saved";
		} else {
			$error_message = "Nicht authentifiziert. Fool.";
		}
	}	elseif ($_POST[action] == "kill") {
		if ($_SESSION[password] == $PASSWORD) {
			$headtpl->SetVariable("titel","Eintrag Löschen");
			kill_article($_POST[entry_id]);
			$error_message = "Eintrag gelöscht";
		} else {
			$error_message = "Nicht authentifiziert. Fool.";
		}
	}	elseif ($_POST[action] == "edit") {
			if ($entry = get_article_raw($_POST[entry_id])) {
				$headtpl->SetVariable("titel","Eintrag Editieren: $entry[title]");
				$tpl->loadTemplatefile("edit.tpl.html", true, true);
				$artikels = array('der','die','das');
				$tpl->SetVariable("text",$entry[text]);
				$tpl->SetVariable("titel",$entry[title]);
				$tpl->SetVariable("entry_id",$entry[entry_id]);

				foreach ($artikels as $art) {
					$tpl->SetCurrentBlock("artikelchoose");
					$tpl->SetVariable("artikel",$art);
					if ($entry[artikel] == $art) $tpl->SetVariable("selected","selected");
					$tpl->ParseCurrentBlock("artikelchoose");
				}
				
			}
	}	elseif ($_POST[action] == "cacheupdate") {
	
		$headtpl->SetVariable("titel","Cacheentry löschen und somit updaten");
		if ($_SESSION[password] == $PASSWORD) {
			kill_cache($_POST[entry_id]);
			$error_message = "Cache cleared";
			
		} else {
			$error_message = "Nicht authentifiziert. Fool.";
		}
	
	
	}	elseif ($_POST[action] == "save") {
		$headtpl->SetVariable("titel","Eintrag Speichern: $entry[title]");
		if ($_SESSION[password] == $PASSWORD) {
			save_article($_POST[entry_id]);
			kill_cache($_POST[entry_id]);
			$error_message = "Geänderter Artikel gespeichert";
		} else {
			$error_message = "Nicht authentifiziert. Fool.";
		}
	

	}	elseif ($_POST[action] == "comment") {
		if ($_POST['id']!= "") {
		    save_comment($_POST['id']);
			header("Location: index.php?action=entry&id={$_POST['id']}");
		} else {
			$error_message = "Fehlende Angaben!.";
		}
	
	
	}	else {
		$error_message = "Wrong POST action";
	}
	
}
	

# get actions
	
else if ($_GET[action] != "") {
	if ($_GET[action] == "list") {
		$headtpl->SetVariable("titel","Liste");
		$tpl->loadTemplatefile("list.tpl.html", true, true);
		for ($s=ord('a');$s<=ord('z');$s++) {
		  $c = chr($s);
			if($entries = return_query("SELECT entry_id,title,date FROM enz_entries WHERE idx='$c' ORDER BY title")) {
			  foreach($entries as $entry) {
					$tpl->SetCurrentBlock("eintrag");
						$tpl->SetVariable("titel",$entry[title]);
						$tpl->SetVariable("entry_id",$entry[entry_id]);
						$tpl->SetVariable("date",$entry[date]);
					$tpl->ParseCurrentBlock("eintrag");
				}
			}
			$tpl->SetCurrentBlock("buchstabe");
			$tpl->SetVariable("buchstabe",strtoupper($c));
			$tpl->ParseCurrentBlock("buchstabe");
		}
	} elseif ($_GET[action] == "entry") {
		if ($entry = get_article($_GET[id])) {
			# head- titel setzen
			$headtpl->SetVariable("titel",$entry[title]);
			# main-template laden
			$tpl->loadTemplatefile("entry.tpl.html", true, true);
			$tpl->SetVariable("titel",$entry[title]);
			$tpl->SetVariable("entry_id",$entry[entry_id]);
			$tpl->SetVariable("artikel",$entry[artikel]);
			$tpl->SetVariable("date",$entry[date]);
			$tpl->SetVariable("text",nl2br($entry[text]));
			
			$comments = get_comments($_GET['id']);
			if ($comments) {
			    
			
				foreach ($comments as $comment) {
					$link = $comment['link'];
					if (strpos($link,"@") != false) {
					    if (strpos($link,"mailto:") == false) {
					        $link = "mailto:".$link;
					    } else {
							if (strpos($link,"mailto:") != 0) {
							    $link = "#";
							}
						}
					} elseif (strpos($link,"http://") != 0) {
						$link = "#";
					}
					$tpl->SetCurrentBlock("comment");
						$tpl->SetVariable("comment_link",$link);
						$tpl->SetVariable("comment_username",$comment['username']);
						$tpl->SetVariable("comment_text",nl2br($comment['text']));
						$tpl->SetVariable("comment_time",date("Y-m-d H:i:s",$comment['time']));
					$tpl->ParseCurrentBlock("comment");
				}
			}
			
			
		} else {
			$headtpl->SetVariable("titel","Data not found");
			$error_message = "No Entry found. Damnski";
		}
	} elseif ($_GET[action] == "latest") {
		if ($entries = get_latest_articles()) {
			$headtpl->SetVariable("titel","Neueste 10 Einträge");
			$tpl->loadTemplatefile("latest.tpl.html", true, true);
	  	foreach($entries as $entry) {
				$tpl->SetCurrentBlock("eintrag");
				$tpl->SetVariable("titel",$entry[title]);
				$tpl->SetVariable("date",$entry[date]);
				$tpl->SetVariable("entry_id",$entry[entry_id]);
				$tpl->ParseCurrentBlock("eintrag");
			}
		} else {
			$headtpl->SetVariable("titel","Data not found");
			$error_message = "No Data found. Mistmistmist";		
		}
	}	elseif ($_GET[action] == "tools") {
	
		if ($_SESSION[password] == $PASSWORD) {
	
		  $headtpl->SetVariable("titel","Tools");
		  $tpl->loadTemplatefile("tools.tpl.html", true, true);
		  if($entries = return_query("SELECT entry_id,title FROM enz_entries ORDER BY entry_id")) {
			 foreach($entries as $entry) {
				$tpl->SetCurrentBlock("eintrag");
					$tpl->SetVariable("titel",$entry[title]);
					$tpl->SetVariable("entry_id",$entry[entry_id]);
				$tpl->ParseCurrentBlock("eintrag");
			 }
		  }
		} else {
			$headtpl->SetVariable("titel","Login");
		  $tpl->loadTemplatefile("login.tpl.html", true, false);
			
		}
	}	elseif ($_GET[action] == "logout") {
		$headtpl->SetVariable("titel","Logging out");
		unset($_SESSION[password]);
		unset($_SESSION);
		session_destroy();
		$error_message = "Logged out";
	
	} else {
		$headtpl->SetVariable("titel","Get Action wrong");
		$error_message = "Wrong GET action";
	
	}


# no action, must be index-style
} else {
  $entry = get_latest_article();
	if ($entry) {
		# head- titel setzen
		$headtpl->SetVariable("titel",$entry[title]);
		# main-template laden
		$tpl->loadTemplatefile("index.tpl.html", true, true);
		$tpl->SetVariable("titel",$entry[title]);
		$tpl->SetVariable("entry_id",$entry[entry_id]);
		$number_comments = get_number_of_comments($entry[entry_id]);
		$tpl->SetVariable("anz_kommentare",$number_comments[0]['COUNT(*)']);
		
		$tpl->SetVariable("artikel",$entry[artikel]);
		$tpl->SetVariable("date",$entry[date]);
		$tpl->SetVariable("text",nl2br($entry[text]));
	} else {
		$headtpl->SetVariable("titel","Data not found");
		
		$error_message = "Data is not found. Bad.";
	}
}
	
$headtpl->show();
if ($error_message != "") { 
	echo $error_message;
} else {
	$tpl->show();
}
require "tpl/foot.tpl.html";

?>