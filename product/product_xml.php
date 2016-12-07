<?php
/**
 * 力前侩 XML 单捞磐
 */
include_once '../Libs/_php/rankup_basic.class.php';
include_once '../rankup_module/rankup_builder/attachment.class.php';
include_once './class/product.class.php';

$product = new product;
$rows = $product->get_product($_GET['no']);

$attach = new attachment('product');
$attach_contents = $attach->print_attachments('', array(
	'entry' => '{:content:}',
	'image' => '<src>'.$base_url.'{:folder:}{:name:}</src>'
));

$title = ''; //$rows['title'];
$explain1 = ''; //$rows['comment'];
$explain2 = '';

$node = '
<root>
	<containerWidth>680</containerWidth>
	<containerHeight>460</containerHeight>
	<images type="3">
		'.$attach_contents.'
	</images>
	<imageList type="2">
		<posi>
			<top>420</top>
			<left>160</left>
		</posi>
		<transparent>100</transparent>
	</imageList>
	<contents type="2">
		<top>
			<text><![CDATA['.$title.']]></text>
			<size>35</size>
			<color>0Xffffff</color>
		</top>
		<middle>
			<text><![CDATA['.$explain1.']]></text>
			<size>13</size>
			<color>0Xc9c9c9</color>
		</middle>
		<bottom>
			<text><![CDATA['.$explain2.']]></text>
			<size>11</size>
			<color>0Xc9c9c9</color>
		</bottom>
		<posi>
			<top>10</top>
			<left>0</left>
		</posi>
		<bgImage>./img/text_bg.png</bgImage>
		<transparent>0</transparent>
	</contents>
</root>';

// EUC-KR 牢内爹 贸府
if(rankup_util::check_unicode($title.$explain1.$explain2)) {
	$node = rankup_util::euckr($node);
}
xmls($node);
?>