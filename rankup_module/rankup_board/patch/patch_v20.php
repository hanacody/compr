<?php
include_once "../../../Libs/_php/rankup_basic.class.php";

// 1. 기본텍스트 패치
$rankup_control->query("alter table rankup_board_config add scontent text default null after sgallery");

// 2. 사용여부 패치
$queries = array(
	"ALTER TABLE `rankup_board_config` ADD `uval` ENUM( 'yes', 'no' ) NOT NULL DEFAULT 'yes' AFTER `anum`",
	"ALTER TABLE `rankup_board_config` ADD INDEX ( `uval` )"
);
foreach($queries as $query) $rankup_control->query($query);

// 3. 투표(추천/반대) 기능 패치
$queries = array(
	"ALTER TABLE `rankup_board_{:id:}` CHANGE `vnum` `gnum` INT( 11 ) UNSIGNED NOT NULL DEFAULT '0'",
	"ALTER TABLE `rankup_board_{:id:}` ADD `bnum` INT( 10 ) UNSIGNED NOT NULL AFTER `gnum`",
	"ALTER TABLE `rankup_board_{:id:}` DROP INDEX `vnum`, ADD INDEX `gnum` ( `gnum` ) , ADD INDEX `bnum` ( `bnum` )"
);
$datas = $rankup_control->query("select id from rankup_board_config");
while($rows = mysql_fetch_assoc($datas)) {
	foreach($queries as $query) $rankup_control->query(str_replace('{:id:}', $rows['id'], $query));
}

// 4. 웹진형 추가
$rankup_control->query("alter table rankup_board_config modify style enum('normal', 'gallery', 'webzin') not null default 'normal'");

?>