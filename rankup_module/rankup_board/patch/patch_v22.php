<?php
//�Խ��� ���׷��̵� ��ġ���� -- 2010-11-05 �Խ��� ��Ÿ�� ����
include_once "../../../Libs/_php/rankup_basic.class.php";
$rankup_control->query("alter table `rankup_board_config` change `style` `style` enum('normal', 'gallery', 'webzin', 'mantoman') not null default 'normal'");
?>