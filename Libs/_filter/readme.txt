
±âº» »ç¿ë¹ı:
============

include_once('HTMLFilter.php');
$filter = new HTMLFilter();
$content = $filter->parse($content);

»ç¿ëÇÔ¼ö:
=========

1. use_rgb(): rgb(255,255,255)¿Í °°Àº Çü½ÄÀ» ±×´ë·Î »ç¿ëÇÏ°íÀÚ ÇÒ ¶§ ¾¹´Ï´Ù. 
±âº»°ªÀº Çí½ºÇü½Ä(¿¹ #FFFFFF)À¸·Î ÀüÈ¯µË´Ï´Ù.
¿¹:
$filter->use_rgb();
$content = $filter->parse($content);

2. set_tag($tagname, $set=false): ±âº»ÀûÀ¸·Î HTMLFilterConfig.php ÆÄÀÏ¿¡¼­ ¼¼ÆÃµÇ¾î ÀÖÀ¸³ª
ÀÏ½ÃÀûÀ¸·Î ¼¼ÆÃÀ» ¹Ù²Ù°íÀÚ ÇÒ°æ¿ì »ç¿ëÇÕ´Ï´Ù. È¤Àº »õ·Î¿î ÅÂ±×¸¦ Á¤ÀÇÇÏ´Â °Íµµ °¡´ÉÇÕ´Ï´Ù.
¿¹:
±âº»ÀûÀ¸·Î meta ÅÂ±×´Â false·Î ÁöÁ¤µÇ¾î ÀÖ½À´Ï´Ù. Á÷Á¢ HTMLFilterConfig.phpÆÄÀÏÀ» ¼öÁ¤ÇÏ¿©µµ µÇ³ª
set_tagÇÔ¼ö¸¦ ÀÌ¿ëÇÏ¿© ¹Ù²Ü ¼ö ÀÖ½À´Ï´Ù.
$filter->set_tag('meta', true);
$content = $filter->parse($content);

»õ·Î¿î ÅÂ±× Á¤ÀÇ
$filter->set_tag('convas', true);

3. set_tag_attribute($tagname, $attrname, $set=false): set_tag()¿Í ¸¶Âù°¡Áö·Î °¢ ÅÂ±×ÀÇ ¼Ó¼ºÀ» ¹Ù²Ü ¶§ »ç¿ëÇÕ´Ï´Ù.
¿¹:
$filter->set_tag('convas', true)
$filter->set_tag_attribute('convas', 'width', true);
$filter->set_tag_attribute('convas', 'height', true);
$content = $filter->parse($content);

4. add_block_url_syntax($syntax): ±İÁöÇÏ°í ½ÍÀº URLÀÇ Á¤±Ô½ÄÀ» Ãß°¡ÇÕ´Ï´Ù. HTMLFilterConfig.php ÆÄÀÏÀÇ $block_url_syntax¿¡ Ãß°¡ÇÏ¿©µµ µË´Ï´Ù. 
ÀÌÀü ¹öÁ¯ÀÇ add_prohibited_url_syntaxÀÇ ÀÌ¸§ÀÌ add_block_url_syntax·Î ¹Ù²î¾ú½À´Ï´Ù.
¿¹:
$filter->add_block_url_syntax('/hackers\.web\.net/i');
$content = $filter->parse($content);

5. register_function($function_name): »ç¿ëÀÚ ÇÔ¼ö¸¦ µî·ÏÇÒ ¶§ »ç¿ëÇÕ´Ï´Ù. »ç¿ëÀÚ ÇÔ¼öÀÇ µî·ÏÀº ¿©·¯°³ °¡´ÉÇÕ´Ï´Ù.
µî·ÏÇÒ ÇÔ¼öÀÇ Çü½ÄÀº ÇÔ¼öÀÌ¸§($tagname, $attrname, $attrval, $global_variable)·Î Â÷·Ê·Î ÅÂ±×ÀÌ¸§, ¼Ó¼ºÀÌ¸§, ¼Ó¼º°ª, ±Û·Î¹úº¯¼öÀÔ´Ï´Ù. ¸¸ÀÏ $tagnameÀÇ °ªÀ» false·Î ³õÀ¸¸é ±× ÅÂ±×°¡ Á¦°ÅµÇ°í $attrvalÀÇ °ªÀ» false·Î ³õÀ¸¸é ¼Ó¼ºÀÌ Á¦°ÅµË´Ï´Ù. È¤Àº ÅÂ±×ÀÇ ÀÌ¸§ÀÌ³ª ¼Ó¼ºÀÇ ÀÌ¸§À» ¹Ù²Ù´Â °Íµµ °¡´ÉÇÕ´Ï´Ù. ±Û·Î¹ú º¯¼öÀÇ °ªÀ» ¹Ù²Ù¸é °¢ ÇÔ¼ö¿¡µµ Àû¿ëµË´Ï´Ù.
¶ÇÇÑ ±Û·Î¹ú º¯¼ö´Â ¿©·¯°³¸¦ ÀÔ·ÂÇÒ ¼ö ÀÖµµ·Ï arrayÀÔ´Ï´Ù.
¿¹:
// src°ªÀÌ http://·Î ½ÃÀÛµÇÁö ¾ÊÀ¸¸é Á¦°ÅÇÏ±â
function myFunction($tagname, $attrname, $attrval, $g)
{
	if ($tagname == 'img && $attrname == 'src' && !preg_match('/^http:\/\//i', $attrval)) {
		$tagname = false;
	}
}

// br ÅÂ±×¿¡¼­ ¼Ó¼ºÀ» ¸ğµÎ Á¦°ÅÇÏ±â
// HTMLConfig.php¿¡¼­ ¼Ó¼ºÀÌ ¾ø¾îµµ µÇÁö¸¸ »ç¿ëÀÚ Á¤ÀÇ ÇÔ¼ö¸¦ ÅëÇØ¼­ ¾ø¾Ö´Â ¿¹Á¦ÀÔ´Ï´Ù.
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

HTMLFilterConfig.php ÆÄÀÏÀº ¹İµå½Ã HTMLFilter.php¿Í µ¿ÀÏÇÑ µğ·ºÅä¸®³»¿¡ ÀÖ¾î¾ß ÇÕ´Ï´Ù. 
ÀÌÀüÀÇ $prohibited_url_syntaxÀÇ ÀÌ¸§ÀÌ $block_url_syntax·Î ¹Ù²î¾ú½À´Ï´Ù.

»ç¿ë Class º¯¼ö:

1. $block_url_syntax: Â÷´Ü URLÀÇ Á¤±Ô½ÄÀ» Àû¾î¾ß ÇÕ´Ï´Ù. Á¤±Ô½Ä¿¡ ÀÍ¼÷ÇÏÁö ¸øÇÏ¸é preg_quote¸¦ ÀÌ¿ëÇÏ¼¼¿ä.
¿¹:
var $block_url_syntax = array(
	'/'.preg_quote('sir.co.kr').'/i'
	);

2. $css_syntax: Çã¿ëÇÒ css Á¤±Ô½ÄÀÔ´Ï´Ù. {width: 50px}¶ó´Â css°¡ ÀÖÀ» ¶§ 50px¿Í °°ÀÌ ¼Ó¼º°ª ºÎºĞÀ» Ã¼Å©ÇÒ ¶§ ¾²ÀÏ Á¤±Ô½ÄÀÔ´Ï´Ù. ±âº»°ªÀº '/^([a-z0-9#\!\.\,\-\*°¡-ÆR¤¡-¤¾¤¿-¤Ó\t ]+)$/i'ÀÔ´Ï´Ù. ÀÌÁ¤µµ¸é ´ëºÎºĞÀº ¹®Á¦¾øÀ»°Å¶ó »ı°¢µË´Ï´Ù.

3. $attributes_need_url_filtering: URL ÇÊÅÍ¸µÀÌ ÇÊ¿äÇÑ ¼Ó¼ºµéÀÔ´Ï´Ù. ¿¹·Î background:url('javascript:alert('XSS')·Î ÇÊÅÍ¸µÀÌ ÇÊ¿äÇÑ ¼Ó¼ºÀ» Ãß°¡ÇÏ¸é µË´Ï´Ù.

4. $script_types: URL¿¡¼­ ½ºÅ©¸³ÀÌ °¡´ÉÇÏ°Ô ÇÏ´Â °ªµéÀÔ´Ï´Ù.

5. $object_security: <object>ÅÂ±×¿¡¼­ º¸¾È»ó ¼³Á¤ÀÌ ÇÊ¿äÇÑ º¯¼öµéÀ» Àû½À´Ï´Ù.

6. $tags: »ç¿ëÇÒ ÅÂ±×µéÀ» Á¤ÇÕ´Ï´Ù. ¸ñ·Ï¿¡ ¾ø°Å³ª °ªÀÌ falseÀÎ ÅÂ±×´Â Á¦°ÅµË´Ï´Ù.

7. $tag_attributes: »ç¿ëÇÒ ÅÂ±×ÀÇ ¼Ó¼ºÀ» Á¤ÇÕ´Ï´Ù. ¸ñ·Ï¿¡ ¾ø°Å³ª °ªÀÌ falseÀÎ ¼Ó¼ºÀº Á¦°ÅµË´Ï´Ù.

8. $unanalyzed_tags: ÀÌ º¯¼ö´Â µÇµµ·ÏÀÌ¸é º¯°æÀ» ÇÏÁö ¸»±â ¹Ù¶ø´Ï´Ù. textarea³ª styleÀÌ³ª script ÅÂ±×´Â °°Àº ÅÂ±× ÀÌ¸§À¸·Î ´İÇô¾ß ÇÕ´Ï´Ù. Áï <textarea>´Â </textarea>·Î ´İ¾Æ¾ß ÇÏ¸ç Áß°£¿¡ ¾î¶² ÅÂ±×°¡ ¿À´õ¶óµµ textareaÀÇ ÅØ½ºÆ®·Î ÀÎ½ÄµË´Ï´Ù. 

9. $empty_tags: xhtml Çü½ÄÀ¸·Î ÀüÈ¯À» À§ÇØ ÇÊ¿äÇÕ´Ï´Ù. ¿¹·Î <br>Àº ´İ´Â ÅÂ±×°¡ ¾øÀ¸¹Ç·Î <br />ÀÇ Çü½ÄÀ¸·Î ÀüÈ¯µË´Ï´Ù.

10. $empty_attributes: xhmlÇü½ÄÀ» À§ÇÑ ºó ¼Ó¼º°ªÀ» °¡Áú ¼ö ÀÖ´Â ¼Ó¼ºÀÔ´Ï´Ù. ºñ¾îÀÖ´Â ¼Ó¼ºÀº ±×´ë·Î ±× °ªÀ» °®½À´Ï´Ù.  ¿¹·Î <input type="text" readonly>´Â <input type="text" readonly="readonly" />·Î ÀüÈ¯µË´Ï´Ù.

11. $css_properties: css¿¡¼­ »ç¿ëÇÒ ¼Ó¼ºÀ» Á¤ÇÕ´Ï´Ù. ¸ñ·Ï¿¡ ¾ø°Å³ª °ªÀÌ falseÀÎ ¼Ó¼ºÀº Á¦°ÅµË´Ï´Ù.



±×´©º¸µå¿¡ Ãß°¡ÇÏ±â:
====================

lib Æú´õ¿¡ htmlfilter¶ó´Â Æú´õ »ı¼ºÈÄ ¾ĞÃàµÈ ÆÄÀÏÀ» ±×°÷¿¡ Ç®¾î
±× Æú´õ ¾È¿¡ HTMLFilter.php ¹× HTMLFilterConfig.php°¡ ÀÖ°Ô ÇÏ¼¼¿ä.

±×¸®°í lib/common.lib.phpÆÄÀÏÀ» ¿­¾î conv_content()ÇÔ¼ö¸¦ Ã£¾Æ ´ÙÀ½À¸·Î ±³Ã¼ÇÕ´Ï´Ù.

function conv_content($content, $html) 
{ 
	global $config, $board; 

	if ($html) 
	{ 
		include_once("$g4[path]/lib/htmlfilter/HTMLFilter.php"); 
		$filter = new HTMLFilter(); 

		if ($html == 2) { // ÀÚµ¿ ÁÙ¹Ù²Ş 
			$content = preg_replace("/\n/", "<br/>", $content); 
		} 

		// XSS (Cross Site Script) ¸·±â 
		$content = $filter->parse($content); 
	} 
	else // text ÀÌ¸é 
	{ 
		// & Ã³¸® : &amp; &nbsp; µîÀÇ ÄÚµå¸¦ Á¤»ó Ãâ·ÂÇÔ 
		$content = html_symbol($content); 

		// °ø¹é Ã³¸® 
		//$content = preg_replace("/  /", "&nbsp; ", $content); 
		$content = str_replace("  ", "&nbsp; ", $content); 
		$content = str_replace("\n ", "\n&nbsp;", $content); 

		$content = get_text($content, 1); 
		$content = url_auto_link($content); 
	} 

	return $content; 
}


»ç¿ëÀÚ ÇÔ¼ö¸¦ ³Ö¾î º» ¿¹:

function conv_content($content, $html) {
	global $config, $board; 

	if ($html)  {
		include_once("$g4[path]/lib/htmlfilter/HTMLFilter.php");
		$filter = new HTMLFilter();

		// ÇÔ¼öÇü½Ä(ÅÂ±×ÀÌ¸§, ¼Ó¼ºÀÌ¸§, ¼Ó¼º°ª, ±Û·Î¹úº¯¼ö)
		// °ª ¿¹: <img src="http://sir.co.kr/logo.gif" width="100px"> ÀÏ°æ¿ì 
		//        'img',    'src',    'http://sir.co.kr/logo.gif'
		//        'img',    'width',  '100px' ÀÌ·¸°Ô µÎ ¹ø ½ÇÇàµË´Ï´Ù.
		function resize($tagname, $attrname, $attrval, $g)
		{
			global $board;

			if (!$g['flag']) {
				$g['flag'] = true;
				$g['change'] = false;
				$g['width'] = 0;
			}

			if ($tagname == 'img') {
				// width°¡ $board[bo_image_width]º¸´Ù Å©¸é
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
					// ¼Ó¼º°ªÀÌ false°¡ µÇ¸é ÅÂ±×¿¡¼­ ±× ¼Ó¼ºÀº Á¦°ÅµÊ
					$attrval = false;
				}
				if (($attrname == 'width' || $attrname == 'height') && intval($attrval) == 0) {
					// width³« height°¡ 0°ªÀÎ °æ¿ì ÇØÅ·½Ãµµ·Î ¿©±â°í ÅÂ±×¸¦ ¾ø¾Ú
					// $tagname À» false·Î ³õÀ¸¸é ÅÂ±×°¡ »èÁ¦µÊ.
					$tagname = false;
				}
			}
		}
		// ÇÔ¼ö µî·Ï
		// ¿©·¯°³ µî·Ïµµ °¡´ÉÇÕ´Ï´Ù.
		// ¿©·¯°³ µî·Ï½Ã ÇÔ¼ö¸¦ ¸¸µé ¶§ 4¹øÂ° º¯¼ö´Â ±Û·Î¹úÀÔ´Ï´Ù.
		$filter->register_function('resize');

		// ¿©·¯°³ µî·Ï½Ã register_function('ÇÔ¼öÀÌ¸§')À» »ç¿ë.
		// ¿¹:
		// $filter->register_function('resize');
		// $filter->register_function('check_n_del_table');
		
		if ($html == 2) {
			// ÀÚµ¿ ÁÙ¹Ù²Ş
			$content = preg_replace("/\n/", "<br/>", $content);
		}
		
		// XSS (Cross Site script) ¸·±â
		$content = $filter->parse($content);
	}
	else // text ÀÌ¸é
	{
		// & Ã³¸® : &amp; &nbsp; µîÀÇ ÄÚµå¸¦ Á¤»ó Ãâ·ÂÇÔ
		$content = html_symbol($content);
			
		// °ø¹é Ã³¸®
		//$content = preg_replace("/  /", "&nbsp; ", $content);
		$content = str_replace("  ", "&nbsp; ", $content);
		$content = str_replace("\n ", "\n&nbsp;", $content);
			
		$content = get_text($content, 1);
		$content = url_auto_link($content);
	}
		
	return $content;
}