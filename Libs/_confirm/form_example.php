<?php
include_once "../_php/rankup_basic.class.php";
?>
<form action="" method="post">
<p>Enter text shown below:</p>
<p><?=$rankup_control->print_confirm_image()?></p>
<p><input type="text" name="keystring"></p>
<p><input type="submit" value="Check"></p>
</form>
<?php
if(count($_POST)>0) {
	if($rankup_control->check_confirm_code($_POST['keystring'])) echo "Correct";
	else echo "Wrong";
}
?>