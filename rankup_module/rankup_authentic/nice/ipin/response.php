<?php
/**
 * 나이스아이핀 수신정보 반환
 *@author: kurokisi
 *@authDate: 2012.01.12
 *@note: 절대 수정 금지!
 */
?>
<script type="text/javascript">
//<![CDATA[
opener.verify_infos('ipin_verify', '<?=$_POST['SendInfo']?>');
self.close();
//]]>
</script>