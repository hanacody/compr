<?php
#start
$is_Demo			= rankup_basic::is_demo(); // 데모버젼여부
$is_UNICODE		= false; // 유니코드 변환
$table_setup		= false;
$table_name		= "rankup_description";
$table_schema		=
"CREATE TABLE $table_name (
  idx integer unsigned NOT NULL auto_increment,
  kind enum('F','D','T') default 'F',
  location varchar(255) NOT NULL default '',
  object varchar(255) NOT NULL default '',
  description varchar(255) default NULL,
  PRIMARY KEY (idx))";
#end
?>
