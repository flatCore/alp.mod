<?php

/**
 * module: alp.mod
 * global injection file
 */

if($languagePack == '') {
	$languagePack = 'de';
}

$alp_db = 'content/SQLite/alp.sqlite3';
$dbh = new PDO("sqlite:$alp_db");
$sql = "SELECT alp_shorthand, alp_text FROM entries WHERE alp_lang = '$languagePack' ";
$entries = $dbh->query($sql);
$entries = $entries->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_COLUMN);

$sql_prefs = "SELECT * FROM prefs WHERE prefs_status = 'active' ";
$alp_prefs = $dbh->query($sql_prefs);
$alp_prefs = $alp_prefs->fetch(PDO::FETCH_ASSOC);
	
$dbh = null;


/**
	* expand array $lang
	* $lang['alp_shorthand'] = alp_text;
	*
	* in overwrite mode we use advanced language files from the plugin directoy
	* the language files will be build by this module
	*/
if($alp_prefs['prefs_modus'] != 'overwrite') {
	foreach($entries as $k => $v) {
		$lang[$k] = $v[0];
	}

}

?>