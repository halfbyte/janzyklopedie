<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
# one stop adding

require_once "inc/funcs.inc.php";

if (stristr($HTTP_USER_AGENT,'Gecko')) {
	$target = "_content";
} else {
	$target = "_blank";
}

$PASSWORD = "rock10";

if ($_POST[action]) {
if ($_POST[action]=="new") {
	if ($_POST[password] == $PASSWORD) {
		new_article();
		$message = "Article saved";
	} else {
		$message = "Wrong password. fool.";
	}
} else {
		$message = "Wrong POST action";
}
}


?>
<html>
<head>
<meta http-equiv='refresh' content='600'>
<title>janzyklopedie</title>
<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
<b>janzyklopedie v1.0</b><br />
sidebar<br />

<br />
<?php if ($message) echo $message."<br><br>" ?>

<form action = "sidebar.php" method = "post">
<select name = "artikel" size="1">
	<option value = "der">der</option>
	<option value = "die">die</option>
	<option value = "das">das</option>
</select><br />

<input type = "text" name = "titel"><br>
<textarea rows = "10" cols = "18" name = "text"></textarea><br>
<input type = "password" name = "password"><br />

<input type = "submit" value="save">
<input type = "hidden" name = "action" value = "new">
</form>
<hr>
<font size="1">
<?php
	if ($entries = get_latest_articles()) {
		foreach($entries as $entry) {
			?>
			<a href="index.php?action=entry&id=<?php echo $entry['entry_id'] ?>" target="<?php echo $target ?>">
			<?php echo $entry['title'] ?></a> (<?php echo $entry['date'] ?>)
			<hr>
			<?php
		}
	}
?>
</font>
</body>
</html>
