<?php
/*
HTMLFilter 1.01 - HTML/XHTML filter
----------------------------------
against XSS(Cross Site Scripting) & CSRF(Cross-site request forgery)
Copyright (C) 2008-2009  Jacob Lee

This program is free software and open source software; you can redistribute
it and/or modify it under the terms of the GNU General Public License as
published by the Free Software Foundation; either version 2 of the License,
or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
59 Temple Place, Suite 330, Boston, MA  02111-1307  USA  or visit
http://www.gnu.org/licenses/gpl.html

*** AUTHOR INFORMATION ***

E-mail:      letsgolee at lycos dot co dot kr
*/
include_once (dirname(__FILE__).'/HTMLFilterConfig.php');

define('HTMLFILTER_TEXT', 0);
define('HTMLFILTER_OPENTAG', 1);
define('HTMLFILTER_CLOSETAG', 2);
define('HTMLFILTER_CMMT', 3);
define('HTMLFILTER_UNANALYZED', 4);

class HTMLFilter extends HTMLFilterConfig
{
	var $_rgb = false;

	var $_html = '';

	var $_userFunctions = array();

	var $_callUserFunctions = false;

	var $_userFunction_parameters = array();

	/* Public functions */

	function use_rgb()
	{
		$this->_rgb = true;
	}

	function add_block_url_syntax($syntax)
	{
		$this->block_url_syntax[] = $syntax;
	}

	function set_tag($tagname, $set=false)
	{
		$this->tags[$tagname] = $set;
	}

	function set_tag_attribute($tagname, $attrname, $set=false)
	{
		if (!array_key_exists($tagname, $this->tags)) {
			$this->tags[$tagname] = true;
		}
		$this->tag_attributes[$tagname][$attrname] = $set;
	}

	function register_function($funcname)
	{
		$this->_userFunctions[] = $funcname;
	}

	function parse($html)
	{
		$this->_html = $html;

		// disable server-side script
		// server-side script can be in the attribute value
		$html = str_replace('<?', '&lt;?', $html);
		$html = str_replace('?>', '?&gt;', $html);

		$len = strlen($html);
		$p = 0;
		$tagstack = array();
		$data = '';
		$tag = array();
		$state = 0;
		$tagtype = HTMLFILTER_TEXT;

		$tagname = '';
		$taglen = 0;
		$attrname = '';
		$attrval = '';
		$quot = '';
		$c = '';

		$soc = 0; /* start pointer location of a comment */
		$eoc = 0; /* end pointer location of a comment */

		$unget = false;

		while (1)
		{
			if ($p >= $len && !$unget) {
				// check if the tag is not finished
				// <a href=http://richarea.net target= */
				switch ($state) {
					case 0:
						$tagstack[] = array('type'=>$tagtype, 'data'=>$data);
						break;
					case 1:
						//$tagstack[] = array('type'=>$tagtype, 'data'=>$data.'&lt;');
						$tagstack[] = array('type'=>$tagtype, 'data'=>$data.'<');
						break;
					case 2:
					case 3:
						/* <strong */
						$tagstack[] = array('type'=>$tagtype, 'tag'=>$tagname);
						break;
					case 5:
					case 6:
						/* <div style */
					case 7:
						/* <div style= */
						$tag['attr'][] = array('name'=>$attrname,
								'val'=>($state == 7 ? '' : null), 'quot'=>null);
						$tag['type'] = $tagtype;
						$tag['tag'] = $tagname;
						$tagstack[] = $tag;
						break;
					case 8:
					case 9:
						/* <div style=color:red */
						/* <div style=' */
						/* <div style='color:red */
						$tag['attr'][] = array('name'=>$attrname,
								'val'=>$attrval, 'quot'=>($state == 9 ? $quot : null));
						$tag['type'] = $tagtype;
						$tag['tag'] = $tagname;
						$tagstack[] = $tag;
						break;
					case 10:
						/* <!-- comment */
						$tagstack[] = array('type'=>$tagtype, 'data'=>substr($html, $soc));
						break;
					case 12:
						/* <textarea> this is a text */
						if ($tagname == 'textarea') {
							$tagstack[] = array('type'=>$tagtype,
								'tag'=>$tagname, 'data'=>htmlspecialchars($data, ENT_NOQUOTES));
						}
						else {
							$tagstack[] = array('type'=>$tagtype, 'tag'=>$tagname, 'data'=>$data);
						}
						// closing tag should be included
						$tagstack[] = array('type'=>HTMLFILTER_CLOSETAG, 'tag'=>$tagname);
						break;
				}
				return $this->_compile($tagstack);
			}
			if ($unget) {
				$unget = false;
			}
			else {
				$c = $html{$p++};
			}
			switch ($state) {
				case 0: /* get until encounters '<' */
					if ($c == '<') {
						$state = 1;
						break;
					}
					$data .= $c;
					break;
				case 1: /* got '<', check if it is a tag opener */
					if (preg_match('/^[a-z]$/i', $c)) { /* a tagname starts */
						$state = 2;
						$tagtype = HTMLFILTER_OPENTAG;
						$tagname = $c;
						if (strlen($data)) {
							$tagstack[] = array('type'=>HTMLFILTER_TEXT, 'data'=>$data);
							$data = '';
						}
						break;
					}
					if ($c == '/') { /* close tag */
						if (preg_match('/^[a-z]$/i', $html{$p})) {
							$state = 2;
							$tagtype = HTMLFILTER_CLOSETAG;
							if (strlen($data)) {
								$tagstack[] = array('type'=>HTMLFILTER_TEXT, 'data'=>$data);
								$data = '';
							}
							break;
						}
						$data .= '</';
						$state = 0;
						break;
					}
					if ($c == '!') {
						if (substr($html, $p, 2) == '--') {
							$p += 2;
							$soc = $p;
							$state = 10;
							if (strlen($data)) {
								$tagstack[] = array('type'=>HTMLFILTER_TEXT, 'data'=>$data);
								$data = '';
							}
							$tagtype = HTMLFILTER_CMMT;
							break;
						}
						/* avoid tags like 'doctypehacked' */
						if (preg_match("/^doctype[ \t\r\n]$/i", substr($html, $p, 8))) {
							$tagname = '!doctype';
							$tagtype = HTMLFILTER_OPENTAG;
							$p += 8;
							$state = 3;
							if (strlen($data)) {
								$tagstack[] = array('type'=>HTMLFILTER_TEXT, 'data'=>$data);
								$data = '';
							}
							break;
						}
						$data .= '<!';
						$state = 0;
						break;
					}
					if ($c == '<') {
						$data .= '<';
						break;
					}
					$data .= '<'.$c;
					$state = 0;
					break;
				case 2: /* getting tag name */
					if ($this->_isSpace($c) || $c == '/') {
						$tagname = strtolower($tagname);
						// if $c is '/' then unget it so that next state it will be dealt.
						if ($c ==  '/') {
							$unget = true;
						}
						$state = 3;
						break;
					}

					if ($c == '>') { // let's close the tag.
						$tagname = strtolower($tagname);
						$unget = true;
						$state = 4;
						break;
					}
					$tagname .= $c;
					break;
				case 3: /* got $tagname, waiting any word as a attribute name or '>' or '/' */
					if ($this->_isSpace($c)) {
						/* ignore space character */
						break;
					}
					if ($c == '/') {
						/* ignore '/' anyway */
						if ($html{$p} == '>') {
							$state = 4;
							break;
						}
						break;
					}
					if ($c == '>') {
						$unget = true;
						$state = 4;
						break;
					}
					$attrname = $c; /* got any character as a attribute name starter */
					$attrval = '';
					$state = 5;
					break;
				case 4: /* close a tag */
					$tag['type'] = $tagtype;
					$tag['tag'] = $tagname;
					$tagstack[] = $tag;
					$tag = array();

					/* when $tagname is a tag that has contents that should be not analyzed */
					if ($tagtype == HTMLFILTER_OPENTAG && in_array($tagname, $this->unanalyzed_tags)) {
						$taglen = strlen($tagname);
						$state = 12;
						$attrname = $attrval = $quot = '';
						$tagtype = HTMLFILTER_UNANALYZED;
						break;
					}

					$state = 0;
					$tagname = $attrname = $attrval = $quot = '';
					$tagtype = HTMLFILTER_TEXT;
					break;
				case 5: /* getting attribute name */
					/* checking whether $attrname is allowed or not will be done after $attrval is given */
					if ($this->_isSpace($c)) {
						/* got attribut name */
						$attrname = strtolower($attrname);
						$state = 6;
						break;
					}
					if ($c == '/') {
						/* got attribute name but has no value */
						/* '/' will be ignored */
						$attrname = strtolower($attrname);
						$tag['attr'][] = array('name'=>$attrname, 'val'=>null, 'quot'=>null);
						$attrname = '';
						$state = 3;
						break;
					}
					if ($c == '>') { /* tag finisher */
						$attrname = strtolower($attrname);
						$tag['attr'][] = array('name'=>$attrname, 'val'=>null, 'quot'=>null);
						$attrname = '';
						$unget = true;
						$state = 4;
						break;
					}
					if ($c == '=') {
						$state = 7;
						break;
					}
					if ($c == '"' || $c == "'") {
						$attrname .= '?'; /* " or ' will be changed as ? */
					}
					else {
						$attrname .= $c;
					}
					break;
				case 6: /*  got $attrname waiting '=' */
						/* <a href   = "#"> */
					if ($this->_isSpace($c)) {
						break;
					}
					if ($c == '=') {
						$state = 7;
						break;
					}
					/* got any word for attribute */
					$attrname = strtolower($attrname);
					$tag['attr'][] = array('name'=>$attrname, 'val'=>null, 'quot'=>null);
					$attrname = '';
					$unget = true;
					$state = 3;
					break;
				case 7: /* got '=', waiting attribute value starter */
						/* <a href   =   aaa/bbb/ccc> */
					if ($this->_isSpace($c)) {
						break;
					}
					if ($c == '>') {
						$attrname = strtolower($attrname);
						$tag['attr'][] = array('name'=>$attrname, 'val'=>'', 'quot'=>null);
						$attrname = '';
						$unget = true;
						$state = 4;
						break;
					}
					if ($c == '"' || $c == "'") {
						$quot = $c;
						$state = 9;
						break;
					}
					$attrval = $c;
					$state = 8;
					break;
				case 8: /* getting attribute value */
					/* <a href   =   aaa/bbb/ccc> */
					if ($this->_isSpace($c) || $c == '>') {
						$attrname = strtolower($attrname);
						$tag['attr'][] = array('name'=>$attrname, 'val'=>$attrval, 'quot'=>null);
						$attrname = $attrval = '';
						if ($c == '>') {
							$unget = true;
							$state = 4;
						}
						else {
							$state = 3;
						}
						break;
					}
					$attrval .= $c;
					break;
				case 9: /* got attribute quote value, waiting any character or the quote type value */
					if ($c == $quot) {
						$attrname = strtolower($attrname);
						$tag['attr'][] = array('name'=>$attrname, 'val'=>$attrval, 'quot'=>$quot);
						$attrname = $attrval = $quot = '';
						$state = 3;
						break;
					}
					$attrval .= $c;
					break;
				case 10: /* comment */
					if ($c == '-' && substr($html, $p, 2) == '->') {
						$eoc = $p - 1;
						$p += 2;
						$tagstack[] = array('type'=>$tagtype, 'data'=>substr($html, $soc, $eoc-$soc));
						$eoc = $soc = 0;
						$tagtype = HTMLFILTER_TEXT;
						$state = 0;
						break;
					}
					break;
				case 11: /* ! tag start */
					/* get rid of it */
					if ($c == '>') {
						$state = 0;
					}
					break;
				case 12: /* don't analyze until meet a close tag of textarea or style or script */
					if ($c == '<' && $html{$p} == '/' && strtolower(substr($html, $p+1, $taglen)) == $tagname) {
						$tagstack[] = array('type'=>$tagtype, 'tag'=>$tagname, 'data'=>$data);
						$data = '';
						$unget = true;
						$state = 0;
						$tagname = '';
						$taglen = 0;
						$tagtype = HTMLFILTER_TEXT;
						break;
					}
					$data .= $c;
					break;
			}
		}
	}

	/* Private functions */

	function _isSpace($c)
	{
		return preg_match("/^[ \r\n\t]$/", $c);
	}

	function _isSet($val)
	{
		return isset($val) && $val;
	}

	function _compile($tagstack)
	{
		if (count($this->_userFunctions)) {
			$this->_callUserFunctions = true;
			// let's do not check if function exists
			// for then we cannot see any errors even when there is any
/*
			$functions = array();
			foreach ($this->_userFunctions as $function) {
				if (function_exists($function)) {
					$functions[] = $function;
				}
			}
			$this->_userFunctions = $functions;

*/
			$this->_userFunctions = array_map('strtolower', $this->_userFunctions);
		}
		$taglist = array();
		$html = '';
		foreach ($tagstack as $tag)
		{
			$data = '';
			$f_param_url = false;
			$f_obj_data = false;
			$f_meta = false;
			$http_equiv_type = '';

			switch ($tag['type']) {
				case HTMLFILTER_OPENTAG:
					if ($this->_isSet($this->tags[$tag['tag']])) {
						// because of user functions, let's change the position of it
						//$data .= '<'.$tag['tag'];
						if ($tag['tag'] == 'object') {
							$tag['attr'] = $this->_secureObject($tag);
						}
						if ($tag['tag'] == 'meta') {
							$f_meta = true;
						}
						if (isset($tag['attr']) && count($tag['attr'])) {
							foreach ($tag['attr'] as $attr) {
								if ($this->_isSet($this->tag_attributes[$tag['tag']][$attr['name']])) {
									if (!$this->_rgb) {
										$attr['val'] = $this->_RGB2HEX($attr['val']);
									}
									switch ($attr['name']) {
										/* <param name="url" value="malicious-url" /> */
										case 'name':
											if ($tag['tag'] == 'param' &&
												$this->_checkExpress($attr['val'], array('url', 'movie'), true)
											) {
												$f_param_url = true;
											}
											break;
										case 'value':
											if ($f_param_url) {
												$attr['val'] = $this->_filterURL($attr['val']);
												/* $f_param_url = false; */
												/* what if <param name="url" value="1st-url" value="2nd-url" /> */
											}
											break;
										/* <OBJECT TYPE="text/x-scriptlet" DATA="http://ha.ckers.org/scriptlet.html"></OBJECT> in Opera 9.02 */
										case 'type':
											if ($tag['tag'] == 'object' &&
												$this->_checkExpress($attr['val'], 'text/x-scriptlet', true)
											) {
												$f_obj_data = true;
											}
											break;
										case 'data':
											if ($f_obj_data) {
												$attr['val'] = $this->_filterURL($attr['val']);
												/* $f_obj_data = false; */
												/* what if <object type="text/x-scriptlet" data="malicious-url_1" data="malicious-url_2"> */
											}
											break;
										case 'classid':
										case 'archive':
										case 'code':
										case 'codebase':
											if ($tag['tag'] == 'applet' || $tag['tag'] == 'object') {
												$attr['val'] = $this->_filterURL($attr['val']);
											}
											break;
										case 'style':
											$attr['val'] = $this->_filterStyle($attr['val']);
											break;
										case 'href':
										case 'lowsrc':
										case 'dynsrc':
										/* <TABLE BACKGROUND="javascript:alert('XSS')"> */
										/* <TD BACKGROUND="javascript:alert('XSS')"> */
										case 'background':
											$attr['val'] = $this->_filterURL($attr['val']);
											break;
										case 'src':
											if ($tag['tag'] == 'embed' &&
												$this->_checkExpress($attr['val'], 'data:', false)
											) {
												/* <EMBED SRC="data:image/svg+xml;base64,PHN2ZyB4bWxuczpzdmc9Imh0dH A6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcv MjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hs aW5rIiB2ZXJzaW9uPSIxLjAiIHg9IjAiIHk9IjAiIHdpZHRoPSIxOTQiIGhlaWdodD0iMjAw IiBpZD0ieHNzIj48c2NyaXB0IHR5cGU9InRleHQvZWNtYXNjcmlwdCI+YWxlcnQoIlh TUyIpOzwvc2NyaXB0Pjwvc3ZnPg==" type="image/svg+xml" AllowScriptAccess="always"></EMBED> */
												/* decoded code:
												<svg xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" x="0" y="0" width="194" height="200" id="xss"><script type="text/ecmascript">alert("XSS");</script></svg> */
												/* we don't know what is inside the data in many cases... so remove it!!! */
												$attr['val'] = false;
											}
											else {
												$attr['val'] = $this->_filterURL($attr['val']);
											}
											break;
										case 'http-equiv':
											if ($f_meta) {
												$http_equiv_type = $attr['val'];
											}
											break;
										case 'content':
											if ($f_meta) {
												$attr['val'] = $this->_filterMETA($attr['val'], $http_equiv_type);
											}
											break;
									}
									// user functions
									if ($this->_callUserFunctions) {
										foreach ($this->_userFunctions as $function) {
											$function(&$tag['tag'], &$attr['name'], &$attr['val'], &$this->_userFunction_parameters);
										}
									}
									if ($attr['val'] !== false) {
										$data .= ' '.$attr['name'].'="'.$this->_escapeQuote($attr['val'], '"').'"';
									}
								}
							}
						}
						// the value of $tag['tag'] can be changed because of user function
						// check if $tag['tag'] is false
						if ($this->_callUserFunctions && $tag['tag'] === false) {
							$data = '';
							break;
						}
						$data = '<'.$tag['tag'].$data;
						//$data .= (in_array($tag['tag'], $this->empty_tags) ? ' />' : '>');
						if (in_array($tag['tag'], $this->empty_tags)) {
							$data .= ' />';
						}
						else {
							$data .= '>';
							//$taglist[] = $tag['tag'];
							array_push($taglist, $tag['tag']);
						}
					}
					break;
				case HTMLFILTER_CLOSETAG:
					if ($this->_isSet($this->tags[$tag['tag']])) {
						if (count($taglist)) {
							$t = array_pop($taglist);
							if ($t != $tag['tag']) {
								if (in_array($tag['tag'], $taglist)) {
									while($t != $tag['tag']) {
										$data .= '</'.$t.'>';
										$t = array_pop($taglist);
									}
								}
								else {
									break;
								}
							}
							$data .= '</'.$tag['tag'].'>';
						}
					}
					break;
				case HTMLFILTER_CMMT:
					// we don't need comment
					// $data = $tag['data'];
					break;
				case HTMLFILTER_UNANALYZED:
					if ($this->tags[$tag['tag']]) {
						//$data = ($tag['tag'] == 'textarea' ? htmlspecialchars($tag['data'], ENT_NOQUOTES) : $tag['data']);
						$data = ($tag['tag'] == 'textarea' ? $this->_filterText($tag['data']) : $tag['data']);
					}
					break;
				//case HTMLFILTER_TEXT:
				default:
					//$data .= preg_replace('/&amp;#(x)?([0-9A-F]{2,7});/i',"&#$1$2;" ,htmlspecialchars($tag['data'], ENT_NOQUOTES));
					$data .= $this->_filterText($tag['data']);
					break;
			}
			$html .= $data;
		}
		while(count($taglist)) {
			$t = array_pop($taglist);
			$html .= '</'.$t.'>';
		}
		return $html;
	}

	function _filterText($data)
	{
		return preg_replace(array('/</', '/>/', '/"/'), array('&lt;', '&gt;', '&quot;'), $data);
	}

	function _getHostByDword($protocol, $dword)
	{
		return $protocol.long2ip($dword);
	}

	function _filterURL($path)
	{
		$path = trim($path);

		$p = $this->_sanitize($path);

		/* <a href=\"http://2826829833/hack/\"> */
		$p = preg_replace('/((ht|f)tp(s)?\:\/\/)([0-9]{10})/ei', "\$this->_getHostByDword('\\1','\\4')", $p);

		foreach ($this->block_url_syntax as $syntax) {
			if (preg_match($syntax, $p)) {
				return false;
			}
		}
		if ($this->_hasScript($p)) {
			return false;
		}

		// file name can be broken if $p is returned.
		// ex)
		//http://mfiles.naver.net/9e4dab71653453e1cb9f/data25/2008/9/6/95/%C2%DF_%C2%DE%BF%ED-rarra777.mp3
		// is changed to
		//http://mfiles.naver.net/9e4dab71653453e1cb9f/data25/2008/9/6/95/??_???-rarra777.mp3

		return $path;
	}

/* <img src="http://badguy.com/a.jpg"> could be dangerous when apache .htaccess is:
Redirect 302 /a.jpg http://victimsite.com/admin.asp&deleteuser */

	function _filterMETA($content, $type)
	{
		$type = strtolower($this->_sanitize($type));

		if ($type == 'set-cookie') {
			/* <META HTTP-EQUIV="Set-Cookie" Content="USERID=&lt;SCRIPT&gt;alert('XSS')&lt;/SCRIPT&gt;"> */
			$filter = new HTMLFilter();

			$content = $filter->parse(html_entity_decode($content));
			return $content;
		}

		if ($type ==  'link') {
			/* <META HTTP-EQUIV="Link" Content="<http://ha.ckers.org/xss.css>; REL=stylesheet"> */
			if (!$this->_filterURL($content)) {
				return false;
			}
			else {
				return $content;
			}
		}

		if ($type == 'refresh') {
			/* <META HTTP-EQUIV="refresh" CONTENT="0;url=javascript:alert('XSS');"> */
			$content_arr = explode(';', $content);
			$content = array();

			$f_data = false;
			foreach ($content_arr as $cnt) {
				if ($f_data) {
					/* <META HTTP-EQUIV="refresh" CONTENT="0;url=data:text/html;base64,PHNjcmlwdD5hbGVydCgnWFNTJyk8L3NjcmlwdD4K"> */
					$f_data = false;
					continue;
				}
				if (preg_match('/url\s*=(.*)/i', $cnt, $m)) {
					if ($this->_checkExpress($m[1], 'data:', false)) {
						/* we don't know what is the data in many cases, get rid of it!!! */
						$f_data = true;
						continue;
					}
					if (!$this->_filterURL($m[1])) {
						continue;
					}
				}
				$content[] = $cnt;
			}
			if (!count($content)) {
				return false;
			}
			return implode(';', $content);
		}

		return $content;
	}

	function _needURLFiltering($name)
	{
		return in_array($name, $this->attributes_need_url_filtering);
	}

	function _filterStyle($style)
	{
		// style="color:#FFFFFF; font-size:12px"
		$retval = '';

		// style="width: expr/*XSS*/ession(alert('XSS'))"
		$style = preg_replace('/\/\*.*\*\//Us', '', $style);

		$css_arr = explode(';', $style);
		$style = array();
		foreach ($css_arr as $css) {
			list($name, $val) = explode(':', $css);
			$name = strtolower(trim($name));
			$val = trim($val);
			if (!$this->_isSet($this->css_properties[$name])) {
				continue;
			}
			if ($this->_needURLFiltering($name) &&
				preg_match("/^url\s*\((.*)\)/i", $val, $m)
			) {
				if (!$this->_filterURL($m[1])) {
					continue;
				}
			}
			else {
				if (!preg_match($this->css_syntax, $val)) {
					continue;
				}
				if ($this->_checkExpress($val, 'expression(', false)) {
					continue;
				}
			}
			$style[] = $name.':'.$val;
		}
		if (!count($style)) {
			return false;
		}
		return implode(';', $style);
	}

	function _secureObject($tag)
	{
		$attr_arr = array();

		if (is_array($tag['attr'])) {
			foreach ($tag['attr'] as $attr) {
				if (array_key_exists($attr['name'], $this->object_security)) {
					continue;
				}
				$attr_arr[] = $attr;
			}
		}
		foreach ($this->object_security as $name=>$default) {
			$attr_arr[] = array('name'=>$name, 'val'=>$default);
		}
/*
		<object style="LEFT: 15px; WIDTH: 294px; TOP: 10px; HEIGHT: 45px" src=http://domain.com/sample.wma width=294 height=45 type=octet-stream invokeURLs="false" autostart="false" allowScriptAccess="never" allowNetworking="internal" EnableContextMenu="false">
*/
		return $attr_arr;
	}

	function _hasScript($str)
	{
		return $this->_checkExpress($str, $this->script_types, false, false);
	}

	function _checkExpress($str, $express, $exact_match=false, $remove_encoding=true)
	{
		// $express can be a array
		if (is_array($express)) {
			$express = implode('|', array_map('preg_quote', $express, array_fill(0, count($express), '/')));
		}
		else {
			$express = preg_quote($express, '/');
		}
		$str = preg_replace("/[\r\n\t ]/", '', $str);
		if ($remove_encoding) {
			$str = $this->_sanitize($str);
		}
		$str = preg_replace('/[^a-z0-9_:;&\/\(\)\!#\.\,\-\*°¡-ÆR]/i', '', $str);
		$str = trim($str);

		return $exact_match ? preg_match("/^($express)$/i", $str) : preg_match("/($express)/i", $str);
	}

	function _sanitize($str)
	{
		$str = html_entity_decode($str);
		$str = preg_replace('/\0+/', '', $str);
		$str = preg_replace("/\\\\(00)([a-z0-9]{2}(\.[0-9]{4})?)/ie", 'chr(hexdec("\\2"))', $str);
		$str = preg_replace('/(\\\\0)+/', '', $str);
		$str = preg_replace('/%u0([a-z0-9]{3})/ei', 'chr(hexdec("\\1"))', $str);
		$str = preg_replace('/%([a-z0-9]{2,3})/ei', 'chr(hexdec("\\1"))', $str);
		$str = preg_replace('/&#x([0-9a-f]+)(;)?/ei', 'chr(hexdec("\\1"))', $str);
		$str = preg_replace('/&#([0-9]{7})/e', 'chr("\\1")', $str);
		$str = preg_replace('/&#([0-9]{2,3})(;)?/e', 'chr("\\1")', $str);

		return trim($str);
	}

	function _escapeQuote($str, $quot)
	{
		if (!$quot) {
			$quot = '"';
		}
		if ($quot == '"') {
			return str_replace('"', '\"', $str);
		}
		return str_replace("'", "\\'", $str);
	}

	function _RGB2HEX($s)
	{
		return preg_replace('/rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)/eiUs', "\$this->_toHEX(array('\\0', '\\1', '\\2', '\\3'));", $s);
	}

	function _toHEX($color)
	{
		$hex = '#';
		for ($i = 1; $i < count($color); $i++) {
			$color[$i] = intval($color[$i]);
			if ($color[$i] < 0 || $color[$i] > 255) {
				return $color[0];
			}
			$hex .= strtoupper(str_pad(dechex($color[$i]), 2, 0, STR_PAD_LEFT));
		}
		return $hex;
	}
}

?>