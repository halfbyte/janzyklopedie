<?php

require_once "inc/db_wrap_mysql.php";

	function new_article() {
		$date = date("Y-m-d");
		$idx = strtolower($_POST[titel][0]);
		
		$title = $_POST[titel];
		$text = $_POST[text];
		$artikel = $_POST[artikel];
		
		do_query("INSERT INTO enz_entries (title,artikel,idx,date,text) VALUES ('$title','$artikel','$idx','$date','$text')");
	
	}
	
	function get_comments($id) {
		return return_query("SELECT * FROM enz_comments WHERE entry_ref=$id ORDER BY comment_id");
	}

	function save_comment($entry_id) {
		$username = $_POST['username'];
		$username = strip_tags($username);
		$text = $_POST['text'];
		$text = strip_tags($text,"b i a");
		$ip = $_SERVER['REMOTE_ADDR'];
		$link = $_POST['link'];
		$time = time();
		do_query("INSERT INTO enz_comments (username,text,ip,link,time,entry_ref,type) VALUES ('$username','$text','$ip','$link',$time,$entry_id,'comment')");
	}
	
	function save_article($id) {
		$date = date("Y-m-d");
		$idx = strtolower($_POST[titel][0]);
		$title = $_POST[titel];
		$text = $_POST[text];
		$artikel = $_POST[artikel];
		
		do_query("UPDATE enz_entries SET title ='$title',artikel='$artikel',idx='$idx',date='$date',text='$text' WHERE entry_id = $id");
	
	}
	
	function kill_article($id) {
		do_query("DELETE FROM enz_entries WHERE entry_id = $id");
	}

	function kill_cache($id) {
		do_query("DELETE FROM enz_cache WHERE cache_id = $id");
	}

  
	function get_article_raw($id) {
	  if (!($entry_all=return_query("SELECT * FROM enz_entries WHERE entry_id=$id")))
			return 0;
		$entry_all[0][text] =stripslashes($entry_all[0][text]);
		$entry_all[0][titel] =stripslashes($entry_all[0][titel]);
		return $entry_all[0];
 
	}
	
	function get_article($id) {
	
  if (!($entry_all=return_query("SELECT * FROM enz_entries WHERE entry_id=$id")))
		return 0;
	
  $entry = $entry_all[0];

	
	if ($cache_all=return_query("SELECT * FROM enz_cache WHERE cache_id=$id")) {
		if (($cache_all[0]['creation_date']+604800) > time()) {
			$entry[text] = $cache_all[0][cached_text];

		} else {
			$entry = update_crosslinks($entry);

			do_query("UPDATE enz_cache SET cached_text='$entry[text]',creation_date=".time()." WHERE cache_id=$id");
		}
	
	} else {
		$entry = update_crosslinks($entry);
		do_query("INSERT INTO enz_cache (cache_id,cached_text,creation_date) VALUES ($id,'$entry[text]',".time().")");

	}
	
	$entry['text'] = stripslashes($entry['text']);
	$entry['titel'] = stripslashes($entry['titel']);
	
	
	
	return $entry;
		 		
  }
	
	
	
	
	function get_latest_article() {
  	if ($entry_all=return_query("SELECT entry_id FROM enz_entries ORDER BY entry_id DESC LIMIT 0,1")) {
		  $entry = $entry_all[0];
			$entry = get_article($entry['entry_id']);
			return $entry;
    }
		return 0;
	}
	function get_latest_articles() {
	  if ($entry_all=return_query("SELECT * FROM enz_entries ORDER BY entry_id DESC LIMIT 0,10")) {
		  return $entry_all;
		}
		return 0;
	}
  
		
  function update_crosslinks($entry) {
    // do word-replacement
    if ($all_words = return_query("SELECT entry_id,title FROM enz_entries")) {
      foreach($all_words as $word) {
	      if ($word[title] != $entry[title]) {
		      $entry[text] = ereg_replace(" $word[title] "," <a href=\"index.php?action=entry&id=".$word[entry_id]."\">&gt;".$word[title]."</a> ",$entry[text]);
		    }
	    }
	  }
		// andere ersetzungen
		
		
		
    return $entry;
	}		

?>
