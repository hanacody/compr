<?php
//게시판 업그레이드 패치사항 -- 2010-11-05 게시판 스타일 변경
include_once "../../../Libs/_php/rankup_basic.class.php";
$rankup_control->query("alter table `rankup_board_config` change `style` `style` enum('normal', 'gallery', 'webzin', 'mantoman') not null default 'normal'");
?>