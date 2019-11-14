<?php

/**
 * alp Database-Scheme
 * install/update the table for entries
 */

$database = "alp";
$table_name = "entries";

$cols = array(
	"alp_id"  => 'INTEGER NOT NULL PRIMARY KEY',
	"alp_shorthand" => 'VARCHAR',
	"alp_text" => 'VARCHAR',
	"alp_lang" => 'VARCHAR',
	"alp_date" => 'VARCHAR',
	"alp_author" => 'VARCHAR'
  );
  


 
?>