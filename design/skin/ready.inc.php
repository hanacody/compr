<?php
/**
 * �غ��� ������ ��Ų
 */
include_once $base_dir.'rankup_module/rankup_builder/rankup_design.class.php';
$design = new rankup_design;

echo $design->get_settings('ready_content');

?>