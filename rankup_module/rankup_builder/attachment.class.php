<?php
/**
 * Attachment Class V1.1
 *@author: kurokisi
 *@authDate: 2011.08.21
 *@updateDate: 2012.01.04
 */

class attachment {
	var $presets = array();
	var $configs = array();
	var $base_dir = '';
	var $base_url = '';
	var $NAME_SEPERATOR = '|';
	var $member_id = '';
	var $junk_clear_times = '-1 hours'; //'-30 seconds'; //'-1 hours';

	function attachment($kind='', $preset_dir='') {
		global $base_dir, $base_url, $member_info;
		$this->base_dir = $base_dir;
		$this->base_url = $base_url;
		include $preset_dir.'attachment.preset.php';
		$this->member_id = $member_info['uid'];
		if($kind) {
			$this->configs = $this->presets[$kind];
			$this->configs['save']['folder'] = $this->fetch_folder($this->configs['save']['folder']);
			if($this->configs['nonjunk']==false) $this->configs['junk']['folder'] = $this->fetch_folder($this->configs['junk']['folder']);
			$this->clear();
		}
	}

	// microtime
	function mtime() {
		list($msec, $sec) = explode(' ', microtime());
		return $sec.substr(array_pop(explode('.', $msec)), 0, 4);
	}

	// junk clear
	function clear() {
		$files = glob($this->base_dir.$this->configs['junk']['folder'].'_junk_.*');
		if(is_array($files)) {
			foreach($files as $file) {
				if(filectime($file)<=strtotime($this->junk_clear_times)) unlink($file);
			}
		}
	}

	// ���� ��ȯ @note: �ʿ信 ���� Ŀ���͸���¡ �� ��!
	function fetch_folder($folder) {
		$folder = str_replace(
			array('{no}', '{member_id}', '{fno}'),
			array($_REQUEST['no'], $this->member_id, $_REQUEST['fno']), $folder
		);
		// folder ����
		if(!is_dir($this->base_dir.$folder)) {
			if(!mkdir($this->base_dir.$folder, 0777)) {
				$xdir = substr($folder, 0, strrpos(substr($folder, 0, -1), '/'));
				if($xdir) {
					mkdir($this->base_dir.$dir, 0777);
					mkdir($this->base_dir.$folder, 0777);
				}
			}
		}
		return $folder;
	}

	// ���ϸ� ��ȯ
	function name($file, $is_junk=false) {
		$ext = $this->ext($file, is_array($file));
		if($ext===false) return false; //@terminate!
		$pattern = $is_junk ? $this->configs['junk']['name'] : $this->configs['save']['name'];
		$name = str_replace(
			array('{member_id}', '{mtime}', '{ext}'),
			array($this->member_id, $this->mtime(), $ext),
			$pattern);
		return $name;
	}

	// ����ũ ���ϸ� ��ȯ
	function unique_name($save_path, $file_name) {
		$save_file = $this->name($file_name);
		if(is_file($save_path.$save_file)) $save_file = $this->unique_name($save_path, $file_name);
		return $save_file;
	}

	// Ȯ���� ��ȯ
	function ext($file, $is_junk=false) {
		if($is_junk===false) {
			$ext = strtolower(array_pop(explode('.', $file))); // $file : string
		}
		else { // $file : array();
			switch($this->configs['ext']['verify']) {
				case 'getimagesize':
					$_size = getimagesize($file['tmp_name']);
					$types = array(
						1 => 'gif', 2 => 'jpg', 3 => 'png', 4 => 'swf', 5 => 'psd', 6 => 'bmp',
						7 => 'tiff', // (intel byte order)
						8 => 'tiff', // (motorola byte order)
						9 => 'jpc', 10 => 'jp2', 11 => 'jpx', 12 => 'jb2', 13 => 'swc', 14 => 'iff',
						15 => 'wbmp', 16 => 'xbm'
					);
					$ext = strtolower($types[$_size[2]]);
					if($ext) break;

				case 'name': // default
					$ext = strtolower(array_pop(explode('.', $file['name'])));
					break;

				case 'mime-type': //@note: �ʿ�� /etc/mime.types ������ �����Ͽ� �߰��� ��
					preg_match('/(^[a-z].*?)\/+(.*)$/m', $file['type'], $match); // or mime_content_type($file['tmp_name']);
					list($mime_type, $prefix, $suffix) = $match;
					$types = array();
					switch($prefix) {
						case 'image':
							$types = array(
								'bmp'=>'bmp', 'gif'=>'gif', 'jpeg'=>'jpg', 'png'=>'png', 'tiff'=>'tiff', 'vnd.wap.wbmp'=>'wbmp', 'vnd.adobe.photoshop'=>'psd',
								'vnd.xiff'=>'iff', 'vnd.djvu'=>'djv', 'cgm'=>'cgm', 'ief'=>'ief',
								'x-cmu-raster'=>'ras',
								'x-portable-anymap'=>'pnm', 'x-portable-bitmap'=>'pbm', 'x-portable-graymap'=>'pgm', 'x-portable-pixmap'=>'ppm',
								'x-xwindowdump'=>'xwd', 'x-xpixmap'=>'xpm', 'x-xbitmap'=>'xbm', 'x-rgb'=>'rgb',
								'svg+xml'=>'svg',
								'pjpeg'=>'jpg', 'x-png'=>'png' // ie hack
							);
							break;

						case 'application':
							$types = array(
								'rtf'=>'rtf', 'msword'=>'doc', 'vnd.ms-excel'=>'xls', 'vnd.ms-powerpoint'=>'ppt',
								'vnd.oasis.opendocument.text'=>'odt', 'vnd.oasis.opendocument.database'=>'odb', 'vnd.oasis.opendocument.spreadsheet'=>'ods',
								'x-zip-compressed'=>'zip', 'x-tar'=>'tar', 'x-rar-compressed'=>'rar',
								'x-shockwave-flash'=>'swf', 'pdf'=>'pdf',
								'xml'=>'xml'
							);
							break;

						case 'video': $types = array('x-flv'=>'flv', 'mpeg'=>'mpg', 'msvideo'=>'avi', 'x-ms-wmv'=>'wmv', 'quicktime'=>'mov'); break;
						case 'audio': $types = array('mpeg3'=>'mp3', 'wav'=>'wav', 'aiff'=>'aif', 'x-ms-wma'=>'wma', 'x-ms-wax'=>'wax', 'x-pn-realaudio'=>'rm'); break;
						case 'text': $types = array('plain'=>'txt', 'css'=>'css'); break;
					}
					if(array_key_exists($suffix, $types)) $ext = $types[strtolower($suffix)];
					else return false; //@terminate!!
					break;
			}
			if($this->configs['ext']['allow'] && !in_array($ext, explode(',', strtolower($this->configs['ext']['allow'])))) return false;
			if($this->configs['ext']['disallow'] && in_array($ext, explode(',', strtolower($this->configs['ext']['disallow'])))) return false;
		}
		return $ext;
	}

	// ����Ʈ ��ȯ
	function toBytes($size) {
		$units = array('Byte' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4, 'PB' => 5);
		preg_match('/([0-9]{1,})([a-zA-Z]{1,})/', $size, $result);
		list($bytes, $unit) = array($result[1], $result[2]);
		for($i=0; $i<$units[$unit]; $i++) $bytes *= 1024;
		return $bytes;
	}

	// ���ε� ó��
	function post($file) { // $file : client file
		global $base_url;
		switch($file['error']) {
			case 1: return 'INI_SIZE'; // The uploaded file exceed the upload_max_filesize directive in php.ini.
			case 2: return 'FORM_SIZE'; // The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.
			case 3: return 'PARTIAL'; // The uploaded file was only partially uploaded.
			case 4: return 'NO_FILE'; // No file was uploaded.
			case 6: return 'NO_TMP_DIR'; // Missing a temporary folder. Introduced in PHP 4.3.10 and 5.0.3
			case 7: return 'CNT_WRITE'; // Failed to write file to disk. Introduced in PHP 5.1.0
			case 8: return 'EXTENSION'; // A PHP extension stopped the file upload. Introduced in PHP 5.2.0
		}
		// checking size
		if(!ceil($file['size'])) return 'NO_FILE'; //@terminate!!
		if($this->configs['limit_size']) {
			$limit_size = $this->toBytes($this->configs['limit_size']);
			if($file['size']>$limit_size) {
				unlink($file['tmp_name']);
				return 'LIMIT_SIZE'; //@terminate!!
			}
		}
		// store set
		$file_name = $this->name($file, !$this->configs['nonjunk']);
		$folder = $this->configs['nonjunk'] ? $this->configs['save']['folder'] : $this->configs['junk']['folder'];

		if($file_name===false) return 'EXTENSION'; //@terminate!!
		move_uploaded_file($file['tmp_name'], $this->base_dir.$folder.$file_name);

		// dimension - flash & image files
		if(preg_match('/flash|image\//', $file['type'])) list($width, $height) = getimagesize($this->base_dir.$folder.$file_name);

		// name
		switch($this->configs['return_name']) {
			case 'client': $name = $file['name']; break;
			case 'both': $name = $file_name.$this->NAME_SEPERATOR.$file['name']; // $name = 'remote|client'
			case 'remote': default: $name = $file_name;
		}
		return array(
			'folder' => $folder,
			'name' => $name,
			'ext' => $this->ext($name),
			'type' => $file['type'],
			'size' => $file['size'],
			'width' => $width,
			'height' => $height
		);
	}

	// ���� ����
	function save($junk_files) {
		$saves = array();
		$junk_dir = $this->base_dir.$this->configs['junk']['folder'];
		$save_dir = $this->base_dir.$this->configs['save']['folder'];
		foreach(explode(',', $junk_files) as $junk_file) {
			$save_file = $this->unique_name($save_dir, $junk_file);
			rename($junk_dir.$junk_file, $save_dir.$save_file);
			array_push($saves, $save_file);
		}
		return implode(',', $saves);
	}

	// ���� ���� - 2012.02.01 fixed
	function del($file) {
		$save_path = $this->base_dir.$this->configs['save']['folder'];
		// save folder
		if(is_file($save_path.$file)) unlink($save_path.$file);
		else if(isset($this->configs['junk'])) { // junk folder
			$junk_path = $this->base_dir.$this->configs['junk']['folder'];
			if(is_file($junk_path.$file)) unlink($junk_path.$file);
		}
		return true;
	}

	// error �޽��� ó��
	function error_msg($code) {
		switch($code) {
			case 'INI_SIZE': $msg = '���ε��� ������ php.ini upload_max_filesize ���þ�� Ů�ϴ�.'; break;
			case 'FORM_SIZE': $msg = '���ε��� ������ HTML ������ ������ MAX_FILE_SIZE ���þ�� Ů�ϴ�.'; break;
			case 'LIMIT_SIZE': $msg = sprintf('���ε��� ������ ����ũ�⺸�� Ů�ϴ�. (����ũ��:%s)', $this->configs['limit_size']); break;
			case 'UPLOAD_ERR_PARTIAL': $msg = '������ �Ϻθ� ���۵Ǿ����ϴ�.'; break;
			case 'NO_FILE': $msg = '������ ���۵��� �ʾҽ��ϴ�.'; break;
			case 'NO_TMP_DIR': $msg = '������ �ӽ� ������ �����ϴ�.'; break;
			case 'CNT_WRITE': $msg = '������ ���� ���⸦ �����߽��ϴ�.'; break;
			//case 'EXTENSION': $msg = 'Ȯ�忡 ���� ���� ���ε尡 �����Ǿ����ϴ�.'; break;
			case 'EXTENSION': $msg = sprintf('���ε��� �� ���� �������� �Դϴ�. (��������:%s)', $this->configs['ext']['allow']); break;
		}
		return $msg;
	}

	// ÷������ ��� ��ȯ
	function print_attachments($prefix='', $entry) {
		$datas = array();
		$folder = $this->configs['save']['folder'];
		$files = glob($this->base_dir.$folder.$prefix.'*');
		if(is_array($files)) {
			$count = 0;
			if($entry['random']==true) shuffle($files);
			foreach($files as $file) {
				$mime = mime_content_type($file);
				$name = basename($file);

				if(preg_match('/image\//', $mime)) $skin = $entry['image'];
				else if(preg_match('/flash/', $mime)) $skin = $entry['flash'];
				else $skin = $entry['etc'];
				$content = fetch_skin(compact('folder', 'name'), $skin);

				$selected = ($name==$entry['value']) ? $entry['selected'] : '';
				array_push($datas, compact('name', 'folder', 'selected', 'content'));
				if($entry['limits'] && ++$count>=$entry['limits']) break;
			}
		}
		return fetch_contents($datas, $entry);
	}

	// ��������Ʈ ��ȯ
	function optimal_size($dimension, $resize, $fixed=false) {
		list($width, $height) = $dimension;
		if($fixed==false) {
			if($resize[0]!=null && $width>$resize[0]) {
				$height = round($height / ($width/$resize[0]));
				$width = $resize[0];
			}
			if($resize[1]!=null && $height>$resize[1]) {
				$width = round($width / ($height/$resize[1]));
				$height = $resize[1];
			}
		}
		else {
			if($resize[0]) $width = $resize[0];
			if($resize[1]) $height = $resize[1];
		}
		return array($width, $height);
	}

	// ÷������ ������
	function preview($name, $entry, $resize=array()) {
		if(!$name) return '';
		$file = $this->base_dir.$this->configs['save']['folder'].$name;
		if(is_file($file)) {
			$folder = $this->base_url.$this->configs['save']['folder'];
			$mime = mime_content_type($file);
			list($width, $height, $type) = getimagesize($file);
			if(count($resize)==2) list($width, $height) = $this->optimal_size(array($width, $height), $resize);
			if(preg_match('/image\//', $mime)) {
				/// IE 6.x png pix
				if(rankup_util::ie_version()=='6.0' && $type==3) {
					$entry['image'] = str_replace('<img ', '<img class="png24" ', $entry['image']);
				}
				$skin = $entry['image'];
			}
			else if(preg_match('/flash/', $mime)) $skin = $entry['flash'];
			else $skin = $entry['etc'];
			$content = fetch_skin(compact('folder', 'name', 'width', 'height'), $skin);
		}
		return $content;
	}
}

?>