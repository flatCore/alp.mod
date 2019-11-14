<?php


if(!defined('FC_INC_DIR')) {
	die("No access");
}


/**
 * get the installed language files from ../lib/lang/
 */
 
function get_system_languages($d='../lib/lang') {

	$cntLangs = 0;
	$scanned_directory = array_diff(scandir($d), array('..', '.','.DS_Store'));
	
	foreach($scanned_directory as $lang_folder) {
		if(is_file("$d/$lang_folder/index.php")) {
			include("$d/$lang_folder/index.php");
			include("$d/$lang_folder/dict-frontend.php");
			$arr_lang[$cntLangs]['lang_sign'] = $lang_sign;
			$arr_lang[$cntLangs]['lang_desc'] = $lang_desc;
			$arr_lang[$cntLangs]['lang_folder'] = $lang_folder;
			$arr_lang[$cntLangs]['lang_contents'] = $lang;
			$cntLangs++;
		}
	}
	
	return($arr_lang);
}

/**
 * get preferences
 */

function get_alp_preferences() {
	
	global $mod_db;
	
	$dbh = new PDO("sqlite:$mod_db");
	$sql = "SELECT * FROM prefs WHERE prefs_status = 'active' ";
	$prefs = $dbh->query($sql);
	$prefs = $prefs->fetch(PDO::FETCH_ASSOC);
	$dbh = null;
	
	return ($prefs);
}

/**
 * build language files
 * store them into the plugin directory
 */

function build_advanced_lf($array) {
	
	$scanned_directory = array_diff(scandir('../lib/lang'), array('..', '.','.DS_Store','index.php'));
	$lugin_header = file_get_contents('../modules/alp.mod/templates/plugin_header.tpl');
	
	$lang_str = '';
	foreach($scanned_directory as $lang_folder) {
		$lang_str = "<?php\r\n";
		$lang_str .= "$lugin_header\r\n\r\n";
		$lang_str .= "if(FC_SOURCE == 'frontend') {\r\n";
		foreach ($array as $entry) {
			if($entry['alp_lang'] == $lang_folder) {
				$lang_str .= "\$lang['".$entry['alp_shorthand']."'] = '".addslashes(str_replace("\"","&quot;",$entry['alp_text']))."';\r\n";
			}
			
		}
		$lang_str .= "}\r\n";
		$lang_str .= "?>";
		
		$file = '../content/plugins/lang_'.$lang_folder.'.php';
		if(file_put_contents($file, $lang_str, LOCK_EX)) {
			echo 'Stored file <code>'.$file.'</code><br>';
		}
		chmod("$file", 0777);
		
	}
}

/**
 * get language files from the plugin directory
 */

function get_advanced_lf() {
	$list = glob("../content/plugins/lang_*.php"); 
	return $list;
}

/**
 * delete language files from the plugin directory
 */
 
function delete_advanced_lf() {
	$list = get_advanced_lf();
	if(is_array($list)) {
		foreach($list as $f) {
			unlink($f);
		}
	}
}

?>