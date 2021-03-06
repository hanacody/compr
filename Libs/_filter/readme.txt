
기본 사용법:
============

include_once('HTMLFilter.php');
$filter = new HTMLFilter();
$content = $filter->parse($content);

사용함수:
=========

1. use_rgb(): rgb(255,255,255)와 같은 형식을 그대로 사용하고자 할 때 씁니다. 
기본값은 헥스형식(예 #FFFFFF)으로 전환됩니다.
예:
$filter->use_rgb();
$content = $filter->parse($content);

2. set_tag($tagname, $set=false): 기본적으로 HTMLFilterConfig.php 파일에서 세팅되어 있으나
일시적으로 세팅을 바꾸고자 할경우 사용합니다. 혹은 새로운 태그를 정의하는 것도 가능합니다.
예:
기본적으로 meta 태그는 false로 지정되어 있습니다. 직접 HTMLFilterConfig.php파일을 수정하여도 되나
set_tag함수를 이용하여 바꿀 수 있습니다.
$filter->set_tag('meta', true);
$content = $filter->parse($content);

새로운 태그 정의
$filter->set_tag('convas', true);

3. set_tag_attribute($tagname, $attrname, $set=false): set_tag()와 마찬가지로 각 태그의 속성을 바꿀 때 사용합니다.
예:
$filter->set_tag('convas', true)
$filter->set_tag_attribute('convas', 'width', true);
$filter->set_tag_attribute('convas', 'height', true);
$content = $filter->parse($content);

4. add_block_url_syntax($syntax): 금지하고 싶은 URL의 정규식을 추가합니다. HTMLFilterConfig.php 파일의 $block_url_syntax에 추가하여도 됩니다. 
이전 버젼의 add_prohibited_url_syntax의 이름이 add_block_url_syntax로 바뀌었습니다.
예:
$filter->add_block_url_syntax('/hackers\.web\.net/i');
$content = $filter->parse($content);

5. register_function($function_name): 사용자 함수를 등록할 때 사용합니다. 사용자 함수의 등록은 여러개 가능합니다.
등록할 함수의 형식은 함수이름($tagname, $attrname, $attrval, $global_variable)로 차례로 태그이름, 속성이름, 속성값, 글로벌변수입니다. 만일 $tagname의 값을 false로 놓으면 그 태그가 제거되고 $attrval의 값을 false로 놓으면 속성이 제거됩니다. 혹은 태그의 이름이나 속성의 이름을 바꾸는 것도 가능합니다. 글로벌 변수의 값을 바꾸면 각 함수에도 적용됩니다.
또한 글로벌 변수는 여러개를 입력할 수 있도록 array입니다.
예:
// src값이 http://로 시작되지 않으면 제거하기
function myFunction($tagname, $attrname, $attrval, $g)
{
	if ($tagname == 'img && $attrname == 'src' && !preg_match('/^http:\/\//i', $attrval)) {
		$tagname = false;
	}
}

// br 태그에서 속성을 모두 제거하기
// HTMLConfig.php에서 속성이 없어도 되지만 사용자 정의 함수를 통해서 없애는 예제입니다.
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

HTMLFilterConfig.php 파일은 반드시 HTMLFilter.php와 동일한 디렉토리내에 있어야 합니다. 
이전의 $prohibited_url_syntax의 이름이 $block_url_syntax로 바뀌었습니다.

사용 Class 변수:

1. $block_url_syntax: 차단 URL의 정규식을 적어야 합니다. 정규식에 익숙하지 못하면 preg_quote를 이용하세요.
예:
var $block_url_syntax = array(
	'/'.preg_quote('sir.co.kr').'/i'
	);

2. $css_syntax: 허용할 css 정규식입니다. {width: 50px}라는 css가 있을 때 50px와 같이 속성값 부분을 체크할 때 쓰일 정규식입니다. 기본값은 '/^([a-z0-9#\!\.\,\-\*가-힣ㄱ-ㅎㅏ-ㅣ\t ]+)$/i'입니다. 이정도면 대부분은 문제없을거라 생각됩니다.

3. $attributes_need_url_filtering: URL 필터링이 필요한 속성들입니다. 예로 background:url('javascript:alert('XSS')로 필터링이 필요한 속성을 추가하면 됩니다.

4. $script_types: URL에서 스크립이 가능하게 하는 값들입니다.

5. $object_security: <object>태그에서 보안상 설정이 필요한 변수들을 적습니다.

6. $tags: 사용할 태그들을 정합니다. 목록에 없거나 값이 false인 태그는 제거됩니다.

7. $tag_attributes: 사용할 태그의 속성을 정합니다. 목록에 없거나 값이 false인 속성은 제거됩니다.

8. $unanalyzed_tags: 이 변수는 되도록이면 변경을 하지 말기 바랍니다. textarea나 style이나 script 태그는 같은 태그 이름으로 닫혀야 합니다. 즉 <textarea>는 </textarea>로 닫아야 하며 중간에 어떤 태그가 오더라도 textarea의 텍스트로 인식됩니다. 

9. $empty_tags: xhtml 형식으로 전환을 위해 필요합니다. 예로 <br>은 닫는 태그가 없으므로 <br />의 형식으로 전환됩니다.

10. $empty_attributes: xhml형식을 위한 빈 속성값을 가질 수 있는 속성입니다. 비어있는 속성은 그대로 그 값을 갖습니다.  예로 <input type="text" readonly>는 <input type="text" readonly="readonly" />로 전환됩니다.

11. $css_properties: css에서 사용할 속성을 정합니다. 목록에 없거나 값이 false인 속성은 제거됩니다.



그누보드에 추가하기:
====================

lib 폴더에 htmlfilter라는 폴더 생성후 압축된 파일을 그곳에 풀어
그 폴더 안에 HTMLFilter.php 및 HTMLFilterConfig.php가 있게 하세요.

그리고 lib/common.lib.php파일을 열어 conv_content()함수를 찾아 다음으로 교체합니다.

function conv_content($content, $html) 
{ 
	global $config, $board; 

	if ($html) 
	{ 
		include_once("$g4[path]/lib/htmlfilter/HTMLFilter.php"); 
		$filter = new HTMLFilter(); 

		if ($html == 2) { // 자동 줄바꿈 
			$content = preg_replace("/\n/", "<br/>", $content); 
		} 

		// XSS (Cross Site Script) 막기 
		$content = $filter->parse($content); 
	} 
	else // text 이면 
	{ 
		// & 처리 : &amp; &nbsp; 등의 코드를 정상 출력함 
		$content = html_symbol($content); 

		// 공백 처리 
		//$content = preg_replace("/  /", "&nbsp; ", $content); 
		$content = str_replace("  ", "&nbsp; ", $content); 
		$content = str_replace("\n ", "\n&nbsp;", $content); 

		$content = get_text($content, 1); 
		$content = url_auto_link($content); 
	} 

	return $content; 
}


사용자 함수를 넣어 본 예:

function conv_content($content, $html) {
	global $config, $board; 

	if ($html)  {
		include_once("$g4[path]/lib/htmlfilter/HTMLFilter.php");
		$filter = new HTMLFilter();

		// 함수형식(태그이름, 속성이름, 속성값, 글로벌변수)
		// 값 예: <img src="http://sir.co.kr/logo.gif" width="100px"> 일경우 
		//        'img',    'src',    'http://sir.co.kr/logo.gif'
		//        'img',    'width',  '100px' 이렇게 두 번 실행됩니다.
		function resize($tagname, $attrname, $attrval, $g)
		{
			global $board;

			if (!$g['flag']) {
				$g['flag'] = true;
				$g['change'] = false;
				$g['width'] = 0;
			}

			if ($tagname == 'img') {
				// width가 $board[bo_image_width]보다 크면
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
					// 속성값이 false가 되면 태그에서 그 속성은 제거됨
					$attrval = false;
				}
				if (($attrname == 'width' || $attrname == 'height') && intval($attrval) == 0) {
					// width낙 height가 0값인 경우 해킹시도로 여기고 태그를 없앰
					// $tagname 을 false로 놓으면 태그가 삭제됨.
					$tagname = false;
				}
			}
		}
		// 함수 등록
		// 여러개 등록도 가능합니다.
		// 여러개 등록시 함수를 만들 때 4번째 변수는 글로벌입니다.
		$filter->register_function('resize');

		// 여러개 등록시 register_function('함수이름')을 사용.
		// 예:
		// $filter->register_function('resize');
		// $filter->register_function('check_n_del_table');
		
		if ($html == 2) {
			// 자동 줄바꿈
			$content = preg_replace("/\n/", "<br/>", $content);
		}
		
		// XSS (Cross Site script) 막기
		$content = $filter->parse($content);
	}
	else // text 이면
	{
		// & 처리 : &amp; &nbsp; 등의 코드를 정상 출력함
		$content = html_symbol($content);
			
		// 공백 처리
		//$content = preg_replace("/  /", "&nbsp; ", $content);
		$content = str_replace("  ", "&nbsp; ", $content);
		$content = str_replace("\n ", "\n&nbsp;", $content);
			
		$content = get_text($content, 1);
		$content = url_auto_link($content);
	}
		
	return $content;
}