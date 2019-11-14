<?php

/**
 * alp Database-Scheme
 * install/update the table for preferences
 *
 * modus = extend || overwrite
 * 
 */

$database = "alp";
$table_name = "prefs";

$cols = array(
	"prefs_id"  => 'INTEGER NOT NULL PRIMARY KEY',
	"prefs_status"  => 'VARCHAR',
	"prefs_version" => 'VARCHAR',
	"prefs_modus" => 'VARCHAR'
  );
  
  
 
?>
