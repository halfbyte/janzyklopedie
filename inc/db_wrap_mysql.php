<?php
#
# datenbankwrapperfunktionen fuer linkliste
#
# mysql-version auf der palmfreedom-datenbank
#

$dbname="krutisch";
$dbuser="krutischdb";
$dbpassword="funnyDB";
$dbhost="localhost";


if (!$db_wrap_included) {

function return_query($query) {
global $dbhost,$dbname,$dbpassword,$dbuser;

# datenbankwrapper für selects
	$connection = mysql_connect ($dbhost , $dbuser, $dbpassword )
        or die ("Could not connect to database");
	mysql_select_db ($dbname,$connection);
	
	$result = mysql_query($query,$connection);
	
	if ($result) {
		while($data=mysql_fetch_array($result)) {
			$list[] = $data;
		}
		
	
	} else {
		die("Query failed: ''$query''<br>".mysql_error());
	}
	mysql_close($connection);
	return $list;


}

function do_query($query) {
# datenbankwrapper für queries ohne result array
global $dbhost,$dbname,$dbpassword,$dbuser;

$retval=false;

	$connection = mysql_connect ($dbhost , $dbuser, $dbpassword )
        or die ("Could not connect to database");
	mysql_select_db ($dbname,$connection);
	
	$result = mysql_query($query,$connection);
	
	if ($result) {
		$retval= true;
	} else {
		die("Query failed: ''$query''<br>".mysql_error());
	}
	mysql_close($connection);
	return $retval;
}

function count_query($query) {
# datenbankwrapper für queries. gibt anzahl der treffer zurück
global $dbhost,$dbname,$dbpassword,$dbuser;

	$connection = mysql_connect ($dbhost , $dbuser, $dbpassword )
        or die ("Could not connect to database");
	mysql_select_db ($dbname,$connection);
	
	$result = mysql_query($query,$connection);
	
	if ($result) {
		return mysql_num_rows($result);
	} else {
		die("Query failed: ''$query''<br>".mysql_error());
	}
	
}





$db_wrap_included=true;

}




?>
