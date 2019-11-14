<?php
/**
 * Advanced Language Packs | flatCore Modul
 * Configuration File
 */

if(FC_SOURCE == 'backend') {
	$mod_root = '../modules/alp.mod/';
} else {
	$mod_root = 'modules/alp.mod/';
}

include $mod_root.'lang/en.php';

if(is_file($mod_root.'lang/'.$languagePack.'.php')) {
	include $mod_root.'lang/'.$languagePack.'.php';
}


$mod['name'] 					= "alp";
$mod['version'] 			= "0.3";
$mod['author']				= "flatCore DevTeam";
$mod['description']		= "Advanced Language Pack - Expand or overwrite contents from default language packs";
$mod['database']			= "content/SQLite/alp.sqlite3";

$modnav[] = array('link' => $mod_lang['nav_preferences'], 'title' => '', 'file' => "prefs");


?>