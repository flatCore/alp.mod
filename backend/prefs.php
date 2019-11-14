<?php

if(!defined('FC_INC_DIR')) {
	die("No access");
}


echo '<h3>'.$mod_name.' '.$mod_version.' <small>| Einstellungen</small></h3>';
include '../modules/'.$mod_name.'.mod/backend/functions.php';
$checked_extend = 'checked';



if(isset($_POST['del_lang_files'])) {
	delete_advanced_lf();
}

/* WRITE THE DATA */
if(isset($_POST['save'])) {
	
	$dbh = new PDO("sqlite:$mod_db");
	
	$sql = "UPDATE prefs
					SET prefs_modus = :prefs_modus
					WHERE prefs_status = 'active' ";
					
	$sth = $dbh->prepare($sql);
	$sth->bindParam(':prefs_modus', $_POST['prefs_modus'], PDO::PARAM_STR);
	
	$cnt_changes = $sth->execute();
	
	if($cnt_changes == true){
		$sys_message = "{OKAY} $lang[db_changed]";
	} else {
		$sys_message = "{ERROR} $lang[db_not_changed]";
	}
	
	$dbh = null;
}


//print message
if($sys_message != ""){
	print_sysmsg("$sys_message");
}


$prefs = get_alp_preferences();


foreach($prefs as $k => $v) {
   $$k = stripslashes($v);
}

if($prefs_modus == 'overwrite') {
	$checked_overwrite = 'checked';
	$checked_extend = '';
} else {
	$checked_overwrite = '';
}

echo '<div class="row">';
echo '<div class="col-md-6">';

echo '<div class="well">';

echo'<form action="acp.php?tn=moduls&sub='.$mod_name.'.mod&a=prefs" class="form-horizontal" method="POST">';

echo'<fieldset>';

echo'<legend>' . $mod_lang['label_modus'] . '</legend>';


echo '<div class="form-check">';

echo '<input class="form-check-input" type="radio" name="prefs_modus" value="extend" '.$checked_extend.'>';
echo '<label class="form-check-label">';
echo '<strong>'.$mod_lang['label_modus_extend'].'</strong><br>'.$mod_lang['modus_extend_tip'];
echo '</label>';
echo '</div>';

echo '<div class="form-check">';
echo '<input class="form-check-input" type="radio" name="prefs_modus" value="overwrite" '.$checked_overwrite.'>';
echo '<label class="form-check-label">';
echo '<strong>'.$mod_lang['label_modus_overwrite'].'</strong><br>'.$mod_lang['modus_overwrite_tip'];
echo '</label>';
echo '</div>';

echo '</fieldset>';

echo '<input class="btn btn-save" type="submit" name="save" value="'.$lang['save'].'">';
echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
echo '</form>';

echo '</div>'; // well

echo '</div>'; // col
echo '<div class="col-md-6">';

echo '<div class="well">';

echo'<form action="acp.php?tn=moduls&sub='.$mod_name.'.mod&a=prefs" class="form-horizontal" method="POST">';
echo '<p>'.$mod_lang['delete_lang_files'].'</p>';

$alf = get_advanced_lf();
if((is_array($alf)) && (count($alf)>0)) {
	foreach($alf as $f) {
		echo '' . $f.'<br>';
	}
	echo '<input class="btn btn-dark text-danger" type="submit" name="del_lang_files" value="'.$lang['delete'].'">';
} else {
	echo '<p class="alert-info p-2">'.$mod_lang['no_alp_files'].'</p>';
}

echo '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';

echo '</form>';

echo '</div>'; // well

echo '</div>'; // col
echo '</div>'; // row

echo '<div class="well">';
echo '<code>README.md</code>';
$rmf = file_get_contents(__DIR__ .'/../README.md');
echo '<pre>'.$rmf.'</pre>';
echo '</div>';

?>