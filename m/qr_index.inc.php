<?php
/**
 * QR CODE VIEW
 */
$mobile->print_header();
?>
<body style="background:#f1f1f1">
<style type="text/css"> @import url(./design/top/frame.css?<?=time()?>); </style>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td>
		<ul class="nav">
			<li class="lbtn"></li>
			<li>
				<?php
				// �ΰ����
				include_once $base_dir.'rankup_module/rankup_builder/attachment.class.php';
				$attach = new attachment('mobile_logo', $mobile->m_dir.'builder/');
				echo $attach->preview($mobile->settings['logo'], array(
					'image' => '<img src="{:folder:}{:name:}" vspace="5" />'
				));
				?>
			</li>
			<li class="rbtn"></li>
		</ul>
		<nav><ul id="gnb" style="border-bottom:0"></ul></nav>
	</td>
</tr>
<tr>
	<td>
		<div style="width:300px;margin:20px auto;background:#fff;padding:20px 0;">
			<center><img src="./design/site/qrcode.png" style="border:1px #333 solid" /></center>
			<div style="background:#f1f1f1;border-top:1px #f1f1f1 solid;margin:20px;margin-bottom:0">
				<div style="display:inline-block;margin:10px;font-size:11px;color:#555">
					���� <b style="font-weight:bold;color:#0099cc"><?=$config_info['site_name']?></b>�� ã���ּż� �����մϴ�.<br /><br />
					Internet Explorer ������������ ����� ���������� �̿��Ͻ� �� �����ϴ�.<br /><br />
					<u>����Ʈ������ QR �ڵ带 �����ø� ����� ���������� ����</u>�Ͻ� �� �ֽ��ϴ�.
				</div>
			</div>
		</div>
	</td>
</tr>
<tr>
	<td>
		<div class="footer" style="padding:10px 0">
			<div style="font-size:10px"><?=$mobile->configs['copyright']?></div>
		</div>
	</td>
</tr>
</table>
</body>
</html>