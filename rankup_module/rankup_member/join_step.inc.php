<?php
/**
 * ȸ������ �ܰ�
 */
switch(basename($_SERVER['PHP_SELF'])) {
	case 'join_intro.html': $_key = 1; break;
	case 'join_policy.html': $_key = 2; break;
	case 'join_pin.html': $_key = 3; break;
	case 'join_form.html': $_key = ($auth->pin_settings['use_pin']=='yes') ? 4 : 3; break;
	case 'join_outro.html': $_key = ($auth->pin_settings['use_pin']=='yes') ? 5 : 4; break;
}
$_on[$_key] = '_on';
?>

<br />
<center>

	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td><img src="./img/guide_01<?=$_on[1]?>.gif" alt="���Ծȳ�"></td>
		<td><img src="./img/guide_02<?=$_on[2]?>.gif" alt="�������"></td>
<?php
if($auth->pin_settings['use_pin']=='yes') {
?>
		<td><img src="./img/guide_03<?=$_on[3]?>.gif" alt="�Ǹ�����"></td>
		<td><img src="./img/guide_04<?=$_on[4]?>.gif" alt="�����Է�"></td>
		<td><img src="./img/guide_05<?=$_on[5]?>.gif" alt="���ԿϷ�"></td>
<?php
}
else {
?>
		<td><img src="./img/guide_031<?=$_on[3]?>.gif" alt="�����Է�"></td>
		<td><img src="./img/guide_041<?=$_on[4]?>.gif" alt="���ԿϷ�"></td>
<?php
}
?>
	</tr>
	</table>

</center>
<br />
<br />
