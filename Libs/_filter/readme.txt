
�⺻ ����:
============

include_once('HTMLFilter.php');
$filter = new HTMLFilter();
$content = $filter->parse($content);

����Լ�:
=========

1. use_rgb(): rgb(255,255,255)�� ���� ������ �״�� ����ϰ��� �� �� ���ϴ�. 
�⺻���� ������(�� #FFFFFF)���� ��ȯ�˴ϴ�.
��:
$filter->use_rgb();
$content = $filter->parse($content);

2. set_tag($tagname, $set=false): �⺻������ HTMLFilterConfig.php ���Ͽ��� ���õǾ� ������
�Ͻ������� ������ �ٲٰ��� �Ұ�� ����մϴ�. Ȥ�� ���ο� �±׸� �����ϴ� �͵� �����մϴ�.
��:
�⺻������ meta �±״� false�� �����Ǿ� �ֽ��ϴ�. ���� HTMLFilterConfig.php������ �����Ͽ��� �ǳ�
set_tag�Լ��� �̿��Ͽ� �ٲ� �� �ֽ��ϴ�.
$filter->set_tag('meta', true);
$content = $filter->parse($content);

���ο� �±� ����
$filter->set_tag('convas', true);

3. set_tag_attribute($tagname, $attrname, $set=false): set_tag()�� ���������� �� �±��� �Ӽ��� �ٲ� �� ����մϴ�.
��:
$filter->set_tag('convas', true)
$filter->set_tag_attribute('convas', 'width', true);
$filter->set_tag_attribute('convas', 'height', true);
$content = $filter->parse($content);

4. add_block_url_syntax($syntax): �����ϰ� ���� URL�� ���Խ��� �߰��մϴ�. HTMLFilterConfig.php ������ $block_url_syntax�� �߰��Ͽ��� �˴ϴ�. 
���� ������ add_prohibited_url_syntax�� �̸��� add_block_url_syntax�� �ٲ�����ϴ�.
��:
$filter->add_block_url_syntax('/hackers\.web\.net/i');
$content = $filter->parse($content);

5. register_function($function_name): ����� �Լ��� ����� �� ����մϴ�. ����� �Լ��� ����� ������ �����մϴ�.
����� �Լ��� ������ �Լ��̸�($tagname, $attrname, $attrval, $global_variable)�� ���ʷ� �±��̸�, �Ӽ��̸�, �Ӽ���, �۷ι������Դϴ�. ���� $tagname�� ���� false�� ������ �� �±װ� ���ŵǰ� $attrval�� ���� false�� ������ �Ӽ��� ���ŵ˴ϴ�. Ȥ�� �±��� �̸��̳� �Ӽ��� �̸��� �ٲٴ� �͵� �����մϴ�. �۷ι� ������ ���� �ٲٸ� �� �Լ����� ����˴ϴ�.
���� �۷ι� ������ �������� �Է��� �� �ֵ��� array�Դϴ�.
��:
// src���� http://�� ���۵��� ������ �����ϱ�
function myFunction($tagname, $attrname, $attrval, $g)
{
	if ($tagname == 'img && $attrname == 'src' && !preg_match('/^http:\/\//i', $attrval)) {
		$tagname = false;
	}
}

// br �±׿��� �Ӽ��� ��� �����ϱ�
// HTMLConfig.php���� �Ӽ��� ��� ������ ����� ���� �Լ��� ���ؼ� ���ִ� �����Դϴ�.
function myFunction2($tagname, $attrname, $attrval, $g)
{
	if ($tagname == 'br' && $str($attrname)) {
		$attrval = false;
	}
}
$filter->register_function('myFunction');
$filter->register_function('myFunction2');
$content = $filter->parse($content);

HTMLFilterConfig.php
====================

HTMLFilterConfig.php ������ �ݵ�� HTMLFilter.php�� ������ ���丮���� �־�� �մϴ�. 
������ $prohibited_url_syntax�� �̸��� $block_url_syntax�� �ٲ�����ϴ�.

��� Class ����:

1. $block_url_syntax: ���� URL�� ���Խ��� ����� �մϴ�. ���ԽĿ� �ͼ����� ���ϸ� preg_quote�� �̿��ϼ���.
��:
var $block_url_syntax = array(
	'/'.preg_quote('sir.co.kr').'/i'
	);

2. $css_syntax: ����� css ���Խ��Դϴ�. {width: 50px}��� css�� ���� �� 50px�� ���� �Ӽ��� �κ��� üũ�� �� ���� ���Խ��Դϴ�. �⺻���� '/^([a-z0-9#\!\.\,\-\*��-�R��-����-��\t ]+)$/i'�Դϴ�. �������� ��κ��� ���������Ŷ� �����˴ϴ�.

3. $attributes_need_url_filtering: URL ���͸��� �ʿ��� �Ӽ����Դϴ�. ���� background:url('javascript:alert('XSS')�� ���͸��� �ʿ��� �Ӽ��� �߰��ϸ� �˴ϴ�.

4. $script_types: URL���� ��ũ���� �����ϰ� �ϴ� �����Դϴ�.

5. $object_security: <object>�±׿��� ���Ȼ� ������ �ʿ��� �������� �����ϴ�.

6. $tags: ����� �±׵��� ���մϴ�. ��Ͽ� ���ų� ���� false�� �±״� ���ŵ˴ϴ�.

7. $tag_attributes: ����� �±��� �Ӽ��� ���մϴ�. ��Ͽ� ���ų� ���� false�� �Ӽ��� ���ŵ˴ϴ�.

8. $unanalyzed_tags: �� ������ �ǵ����̸� ������ ���� ���� �ٶ��ϴ�. textarea�� style�̳� script �±״� ���� �±� �̸����� ������ �մϴ�. �� <textarea>�� </textarea>�� �ݾƾ� �ϸ� �߰��� � �±װ� ������ textarea�� �ؽ�Ʈ�� �νĵ˴ϴ�. 

9. $empty_tags: xhtml �������� ��ȯ�� ���� �ʿ��մϴ�. ���� <br>�� �ݴ� �±װ� �����Ƿ� <br />�� �������� ��ȯ�˴ϴ�.

10. $empty_attributes: xhml������ ���� �� �Ӽ����� ���� �� �ִ� �Ӽ��Դϴ�. ����ִ� �Ӽ��� �״�� �� ���� �����ϴ�.  ���� <input type="text" readonly>�� <input type="text" readonly="readonly" />�� ��ȯ�˴ϴ�.

11. $css_properties: css���� ����� �Ӽ��� ���մϴ�. ��Ͽ� ���ų� ���� false�� �Ӽ��� ���ŵ˴ϴ�.



�״����忡 �߰��ϱ�:
====================

lib ������ htmlfilter��� ���� ������ ����� ������ �װ��� Ǯ��
�� ���� �ȿ� HTMLFilter.php �� HTMLFilterConfig.php�� �ְ� �ϼ���.

�׸��� lib/common.lib.php������ ���� conv_content()�Լ��� ã�� �������� ��ü�մϴ�.

function conv_content($content, $html) 
{ 
	global $config, $board; 

	if ($html) 
	{ 
		include_once("$g4[path]/lib/htmlfilter/HTMLFilter.php"); 
		$filter = new HTMLFilter(); 

		if ($html == 2) { // �ڵ� �ٹٲ� 
			$content = preg_replace("/\n/", "<br/>", $content); 
		} 

		// XSS (Cross Site Script) ���� 
		$content = $filter->parse($content); 
	} 
	else // text �̸� 
	{ 
		// & ó�� : &amp; &nbsp; ���� �ڵ带 ���� ����� 
		$content = html_symbol($content); 

		// ���� ó�� 
		//$content = preg_replace("/  /", "&nbsp; ", $content); 
		$content = str_replace("  ", "&nbsp; ", $content); 
		$content = str_replace("\n ", "\n&nbsp;", $content); 

		$content = get_text($content, 1); 
		$content = url_auto_link($content); 
	} 

	return $content; 
}


����� �Լ��� �־� �� ��:

function conv_content($content, $html) {
	global $config, $board; 

	if ($html)  {
		include_once("$g4[path]/lib/htmlfilter/HTMLFilter.php");
		$filter = new HTMLFilter();

		// �Լ�����(�±��̸�, �Ӽ��̸�, �Ӽ���, �۷ι�����)
		// �� ��: <img src="http://sir.co.kr/logo.gif" width="100px"> �ϰ�� 
		//        'img',    'src',    'http://sir.co.kr/logo.gif'
		//        'img',    'width',  '100px' �̷��� �� �� ����˴ϴ�.
		function resize($tagname, $attrname, $attrval, $g)
		{
			global $board;

			if (!$g['flag']) {
				$g['flag'] = true;
				$g['change'] = false;
				$g['width'] = 0;
			}

			if ($tagname == 'img') {
				// width�� $board[bo_image_width]���� ũ��
				if ($attrname == 'width' && intval($attrval) > $board[bo_image_width]) {
					$g['width'] = $attrval;
					$attrval = $board[bo_image_width];
					$g['change'] = true;
				}
				/*
				if ($g['change'] && $attrname == 'height') {
					$attrval = floor($attrval*500/$g['width']);
				}
				*/
				if ($g['change'] && $attrname == 'height') {
					// �Ӽ����� false�� �Ǹ� �±׿��� �� �Ӽ��� ���ŵ�
					$attrval = false;
				}
				if (($attrname == 'width' || $attrname == 'height') && intval($attrval) == 0) {
					// width�� height�� 0���� ��� ��ŷ�õ��� ����� �±׸� ����
					// $tagname �� false�� ������ �±װ� ������.
					$tagname = false;
				}
			}
		}
		// �Լ� ���
		// ������ ��ϵ� �����մϴ�.
		// ������ ��Ͻ� �Լ��� ���� �� 4��° ������ �۷ι��Դϴ�.
		$filter->register_function('resize');

		// ������ ��Ͻ� register_function('�Լ��̸�')�� ���.
		// ��:
		// $filter->register_function('resize');
		// $filter->register_function('check_n_del_table');
		
		if ($html == 2) {
			// �ڵ� �ٹٲ�
			$content = preg_replace("/\n/", "<br/>", $content);
		}
		
		// XSS (Cross Site script) ����
		$content = $filter->parse($content);
	}
	else // text �̸�
	{
		// & ó�� : &amp; &nbsp; ���� �ڵ带 ���� �����
		$content = html_symbol($content);
			
		// ���� ó��
		//$content = preg_replace("/  /", "&nbsp; ", $content);
		$content = str_replace("  ", "&nbsp; ", $content);
		$content = str_replace("\n ", "\n&nbsp;", $content);
			
		$content = get_text($content, 1);
		$content = url_auto_link($content);
	}
		
	return $content;
}