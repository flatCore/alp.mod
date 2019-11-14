<?php

if(!defined('FC_INC_DIR')) {
	die("No access");
}

echo '<h3>'.$mod_name.' '.$mod_version.' <small>| '.$mod['description'].'</small></h3>';

include '../modules/'.$mod_name.'.mod/install/installer.php';
include '../modules/'.$mod_name.'.mod/backend/functions.php';

$alp_prefs = get_alp_preferences();

/**
 * set filter by request set_pb_filter
 */

if(isset($_REQUEST['alp_filter'])) {
	$_SESSION['alp_filter'] = clean_varname($_REQUEST['alp_filter']);
}

if($_SESSION['alp_filter'] == '') {
	$alp_filter = '';
} else {
	$alp_filter = $_SESSION['alp_filter'];
}


if(isset($_REQUEST['setType'])) {
	
	if($_REQUEST['setType'] == 'System') {
		if($_SESSION['setType_system'] == 'system') {
			$_SESSION['setType_system'] = '';
		} else {
			$_SESSION['setType_system'] = 'system';
		}
	}
	if($_REQUEST['setType'] == 'Module') {
		if($_SESSION['setType_module'] == 'module') {
			$_SESSION['setType_module'] = '';
		} else {
			$_SESSION['setType_module'] = 'module';
		}
	}
	
}

$btn_class_system = '';
if($_SESSION['setType_system'] == 'system') {
	$btn_class_system = 'active';
}

$btn_class_module = '';
if($_SESSION['setType_module'] == 'module') {
	$btn_class_module = 'active';
}


$dbh = new PDO("sqlite:$mod_db");

/* delete */
if(is_numeric($_REQUEST['delete'])){
	$delete = (int) $_REQUEST['delete'];
	$sql = "DELETE FROM entries WHERE alp_id = $delete";
	$cnt_changes = $dbh->exec($sql);

	if($cnt_changes > 0) {
		echo '<div class="alert alert-success">Eintrag wurde gelöscht</div>';
	}
} /* eo delete */


/* save/update alp */
if(isset($_POST['save_entry'])) {

	if(!is_numeric($_POST['edit_id'])) {
		$modus = 'new';
	} else {
		$modus = 'update';
		$edit_id = (int) $_POST['edit_id'];
	}
	
	$alp_date = time();
	$alp_shorthand = clean_varname($_POST['alp_shorthand']);
	
	$sql_new = "INSERT INTO entries (
			alp_id, alp_shorthand, alp_text, alp_lang, alp_date, alp_author
			) VALUES (
			NULL, :alp_shorthand, :alp_text, :alp_lang, :alp_date, :alp_author ) ";

	$sql_update = "UPDATE entries
				SET	alp_shorthand = :alp_shorthand,
					alp_text = :alp_text,
					alp_lang = :alp_lang,
					alp_date = :alp_date,
					alp_author = :alp_author
				WHERE alp_id = :edit_id ";
				
	if($modus == "new")	{				
		if($sth = $dbh->prepare($sql_new)) {
			$sth->bindParam(':alp_shorthand', $alp_shorthand, PDO::PARAM_STR);
			$sth->bindParam(':alp_text', $_POST['alp_text'], PDO::PARAM_STR);
			$sth->bindParam(':alp_lang', $_POST['alp_lang'], PDO::PARAM_STR);
			$sth->bindParam(':alp_date', $alp_date, PDO::PARAM_STR);
			$sth->bindParam(':alp_author', $_SESSION['user_nick'], PDO::PARAM_STR);
			$edit_id = $dbh->lastInsertId();
		} else {
			print_r($dbh->errorInfo());
		}
	}
	
	if($modus == "update") {
		$sth = $dbh->prepare($sql_update);
		$sth->bindParam(':alp_shorthand', $alp_shorthand, PDO::PARAM_STR);
		$sth->bindParam(':alp_text', $_POST['alp_text'], PDO::PARAM_STR);
		$sth->bindParam(':alp_lang', $_POST['alp_lang'], PDO::PARAM_STR);
		$sth->bindParam(':alp_date', $alp_date, PDO::PARAM_STR);
		$sth->bindParam(':alp_author', $_SESSION['user_nick'], PDO::PARAM_STR);
		$sth->bindParam(':edit_id', $edit_id, PDO::PARAM_INT);	
	}

	
	$cnt_changes = $sth->execute();

	if($cnt_changes == TRUE){
		$sys_message = "{OKAY} Der Eintrag wurde gespeichert";
	} else {
		$sys_message = "{error} Der Eintrag wurde nicht gespeichert";
		echo '<hr><pre>';
		print_r($dbh->errorInfo());
		echo '</pre><hr>';
	}
	
	print_sysmsg("$sys_message");
	
}


$filter = '';
if($alp_filter != '') {
	$filter .= "WHERE (alp_shorthand IS NOT NULL) AND (alp_shorthand LIKE :alp_filter)";
}

$sql = "SELECT * FROM entries $filter ORDER BY alp_shorthand ASC";
$sth = $dbh->prepare($sql);
if($alp_filter != '') {
	$sth->bindValue(':alp_filter', "%$alp_filter%", PDO::PARAM_STR);
}
$sth->execute();
$entries = $sth->fetchAll();
$dbh = null;

$all_languages = get_system_languages();

$lang_select = '<select class="form-control custom-select" name="alp_lang">';
for($i=0;$i<count($all_languages);$i++) {
	$lang_folder = $all_languages[$i]['lang_folder'];
	$lang_select .= "<option value='$lang_folder'>$lang_folder</option>";
}

$lang_select .= '</select>';

if(!is_numeric($_REQUEST['edit_id'])) {
	$btn_value = $lang['save'];
	$alp_text = '';
	$alp_shorthand = '';
	$modus = 'new';
} else {
	$lang_select = '<select class="form-control custom-select" name="alp_lang">';
	$btn_value = $lang['update'];
	$edit_id = (int) $_REQUEST['edit_id'];
	$modus = 'Update';
	
	$dbh = new PDO("sqlite:$mod_db");
	$sql = "SELECT * FROM entries WHERE alp_id = $edit_id";
	$get_alp = $dbh->query($sql);
	$get_alp = $get_alp->fetch(PDO::FETCH_ASSOC);
	$dbh = null;
	
	foreach($get_alp as $k => $v) {
	   $$k = stripslashes($v);
	}
	
	$alp_date = date("Y-m-d",$alp_date);
	
	for($i=0;$i<count($all_languages);$i++) {
	
		$lang_folder = $all_languages[$i]['lang_folder'];
		
		if(strpos("$alp_lang", "$lang_folder") !== false) {
			$sel_lang = "selected";
		} else {
			$sel_lang = "";
		}
		
		if($alp_lang == "" AND $lang_folder == "$_SESSION[lang]") {
			$sel_lang = "checked";
		}
		
		$lang_select .= "<option value='$lang_folder' $sel_lang>$lang_folder</option>";
	
	} // eo $i
	$lang_select .= '</select>';



}


/* form */

if(!isset($alp_date)) {
	$alp_date = date("Y-m-d");
}

if($_GET['set_shorthand'] != '') {
	$alp_shorthand = $_GET['set_shorthand'];
	
	if(isset($_GET['l'])) {
		$l = basename($_GET['l']);
		if(is_file('../lib/lang/'.$l.'/dict-frontend.php')) {
			ob_start();
			include '../lib/lang/'.$l.'/dict-frontend.php';
			$alp_text = $lang[$alp_shorthand];
			ob_end_clean();
		}
	}
	
	
}

$tplform = file_get_contents("../modules/alp.mod/templates/acp_form.tpl");
$tplform = str_replace('{form_action}', "acp.php?tn=moduls&sub=alp.mod&a=start", $tplform);
$tplform = str_replace('{btn_value}', $btn_value, $tplform);
$tplform = str_replace('{alp_text}', $alp_text, $tplform);
$tplform = str_replace('{alp_langs}', $lang_select, $tplform);
$tplform = str_replace('{alp_shorthand}', $alp_shorthand, $tplform);
$tplform = str_replace('{alp_date}', $alp_date, $tplform);
$tplform = str_replace('{edit_id}', $edit_id, $tplform);
$tplform = str_replace('{label_shorthand}', $mod_lang['label_shorthand'], $tplform);
$tplform = str_replace('{label_language}', $mod_lang['label_language'], $tplform);
$tplform = str_replace('{label_text}', $mod_lang['label_text'], $tplform);
$tplform = str_replace('{token}', $_SESSION['token'], $tplform);

echo $tplform;



/* add system entries to array $entries */

foreach($all_languages as $entry) {
	foreach($entry['lang_contents'] as $k => $v) {
		$x++;
		$system_entries[$x]['alp_shorthand'] = $k;
		$system_entries[$x]['alp_text'] = $v;
		$system_entries[$x]['alp_lang'] = $entry['lang_folder'];
		$system_entries[$x]['alp_origin'] = 'system';
	}
}

$show_entries = array();
$sys_entries = array();
$mod_entries = array();

if($_SESSION['setType_system'] == 'system') {
	$sys_entries = $system_entries;
}

if($_SESSION['setType_module'] == 'module') {
	$mod_entries = $entries;
}

$show_entries = array_merge($sys_entries,$mod_entries);

foreach ($show_entries as $key => $row) {
  $string[$key] = $row['alp_shorthand'];
}




array_multisort($string, SORT_ASC, SORT_STRING, $show_entries);


if($alp_prefs['prefs_modus'] == 'overwrite' && isset($_POST['save_entry'])) {
	echo '<pre>';
	build_advanced_lf($entries);
	echo '</pre>';
}

/* list entries */

$cnt_entries = count($show_entries);

if($cnt_entries < 1) {
	echo '<p class="alert alert-info">Keine Einträge vorhanden.</p>';
}

echo '<div class="well well-sm">';

echo '<div class="row">';
echo '<div class="col-md-4">';

echo '<div class="btn-group">';

echo '<a class="btn btn-fc '.$btn_class_system.'" href="?tn=moduls&sub=alp.mod&a=start&setType=System">System</a>';
echo '<a class="btn btn-fc '.$btn_class_module.'" href="?tn=moduls&sub=alp.mod&a=start&setType=Module">alp.mod</a>';
echo '</div>';

echo '</div>';
echo '<div class="col-md-8">';

echo '<form class="form-inline float-right" action="?tn=moduls&sub=alp.mod&a=start" method="POST">';
echo '<input type="text" class="form-control" name="alp_filter" value="'.$alp_filter.'"> ';
echo '<input type="submit" name="set_alp_filter" value="Filter" class="btn btn-fc">';
echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</form>';
echo '</div>';

echo '</div>';
echo '</div>';


$tplfile = file_get_contents("../modules/alp.mod/templates/acp_list.tpl");
echo '<div class="well well-sm">';
echo '<table class="table table-sm table-striped">';

echo '<thead><tr>';
echo '<th style="width:200px;">Shorthand</th><th>Type</th><th style="width:120px;">Sprache</th><th>Text</th><th style="width:200px;"></th>';
echo '</tr></thead>';

for($i=0;$i<$cnt_entries;$i++) {

	$tpl = $tplfile;
	
	$entry_id = $show_entries[$i]['alp_id'];
	$entry_date = date("Y-m-d",$show_entries[$i]['alp_date']);
	$entry_text = stripslashes($show_entries[$i]['alp_text']);
	$entry_shorthand = $show_entries[$i]['alp_shorthand'];
	$entry_lang = $show_entries[$i]['alp_lang'];
	$entry_flag = '<img src="../lib/lang/'.$entry_lang.'/flag.png" width="22">';
	$entry_origin = $show_entries[$i]['alp_origin'];
	$tr_class = ' ';
	
	$link_edit = '<a class="btn btn-fc btn-sm" href="?tn=moduls&sub=alp.mod&a=start&edit_id='.$entry_id.'">'.$lang['edit'].'</a>';
	$link_delete = '<a class="btn btn-fc text-danger btn-sm" href="?tn=moduls&sub=alp.mod&a=start&delete='.$entry_id.'" onclick="return confirm(\''.$lang['confirm_delete_data'].'\')">'.$lang['delete'].'</a>';
	
	if($entry_origin == 'system') {
		$link_edit = '<a class="btn btn-fc btn-sm" href="?tn=moduls&sub=alp.mod&a=start&set_shorthand='.$entry_shorthand.'&l='.$entry_lang.'">'.$lang['duplicate'].'</a>';
		$link_delete = '';
		$tr_class .= 'system-row ';
		$tpl = str_replace("{item_type}", $icon['cogs'], $tpl);
	} else {
		$tpl = str_replace("{item_type}", 'alp', $tpl);
	}
	
	
	

	if($entry_shorthand != $entries[$i-1]['alp_shorthand']) {
		if($tr_set == 'even') {
			$tr_set = 'odd';
		} else {
			$tr_set = 'even';
		}
		$tr_class .= 'new-row ';		
	} else {
		$tr_class .= 'items-row ';
	}
	
	$tr_class .= $tr_set;
	
	$tpl = str_replace("{tr_class}", $tr_class, $tpl);
	$tpl = str_replace("{item_shorthand}", $entry_shorthand, $tpl);
	$tpl = str_replace("{item_text}", $entry_text, $tpl);
	$tpl = str_replace("{item_lang}", $entry_lang, $tpl);
	$tpl = str_replace("{item_flag}", $entry_flag, $tpl);
	$tpl = str_replace("{btn_edit}", "$link_edit", $tpl);
	$tpl = str_replace("{btn_delete}", "$link_delete", $tpl);
	
	echo $tpl;

}

echo '</table>';
echo '</div>';





function clean_varname($str) {
	$str = strtolower($str);
	$a = array('ä','ö','ü','ß',' - ',' + ',' / ','/'); 
	$b = array('ae','oe','ue','ss','_','_','_','_');
	$str = str_replace($a, $b, $str);
	$str = preg_replace('/\s/s', '_', $str);  // replace blanks -> '_'
	//$str = preg_replace('/[^a-z_]/isU', '', $str); // only a-z
	$str = trim($str); 
	return $str; 
}  


?>