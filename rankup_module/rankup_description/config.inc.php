<?php
#start
$is_Demo			= rankup_basic::is_demo(); // �����������
$is_UNICODE		= false; // �����ڵ� ��ȯ
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
