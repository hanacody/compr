<?php
/**
 * 모듈러 클래스 V1.0
 *@author: kurokisi
 *@authDate: 2011.09.09
 */

class rankup_moduler {
	var $modules = array();

	function rankup_moduler() {
		$this->set_modules();
	}

	// 파일명으로 모듈/컴포넌트 추출
	function educe_module($page) {
		global $base_url;
		foreach($this->modules as $key1=>$value1) {
			if(isset($value1['components']['index_file'])) {
				$files = explode('|', $value1['components']['index_file']);
				$seperators = explode('|', $value1['components']['seperator']);
				foreach($files as $index=>$file) {
					if($page==$base_url.$file) {
						$module = $key1;
						$component = $seperators[$index] ? $_GET[$seperators[$index]] : 0;
						$name = $value1['name'];
						break;
					}
				}
			}
			else if(is_array($value1['components'])) {
				foreach($value1['components'] as $key2=>$value2) {
					if($value2['file']) {
						$files = explode('|', $value2['file']);
						foreach($files as $file) {
							if($page==$base_url.$file) {
								$module = $key1;
								$component = $key2;
								$name = $value2['name'];
								break;
							}
						}
						if($component) break;
					}
				}
			}
			if($component) break;
		}
		return compact('module', 'component', 'name');
	}

	// 모듈 설정
	function set_modules() {
		global $base_dir;
		include 'module_setting.inc.php';
		if(is_array($module_names)) {
			foreach($module_names as $folder) {
				$init_file = $base_dir.$folder.'/module.init.php';
				if(is_file($init_file)) {
					include $init_file;
					$this->modules = array_merge($this->modules, $modules);
				}
			}
		}
	}

	// 모듈 반환
	function print_modules($entry) {
		$datas = array();
		foreach($this->modules as $key=>$rows) {
			$name = $rows['name'];
			array_push($datas, compact('key', 'name'));
		}
		return fetch_contents($datas, $entry);
	}

	// 컴포넌트 반환
	function get_components($module) {
		global $base_dir;
		$set = $this->modules[$module]['components'];
		if(isset($set['class_name'])) {
			if(isset($GLOBALS[$set['constvar']])) {
				$constvar = $GLOBALS[$set['constvar']];
			}
			else {
				if($set['class_file']) {
					foreach(explode('|', $set['class_file']) as $class_file) {
						if($class_file && is_file($base_dir.$class_file)) include_once $base_dir.$class_file;
					}
				}
				// new class
				eval("\$constvar = new $set[class_name];");
			}
			$components = call_user_func(
				array($constvar, $set['method']),
				$set['parameters'][0],
				$set['parameters'][1]
			);
		}
		else {
			$components = $this->modules[$module]['components'];
		}
		return $components;
	}

	// 컴포넌트 반환
	function print_components($module, $entry) {
		global $base_dir;
		$datas = array();
		$components = $this->get_components($module);
		if(is_array($components)) { // 2012.01.19 fixed
			foreach($components as $key=>$rows) {
				$name = $rows['name'];
				if(is_array($rows['option'])) {
					$init = 'init';
					$html_file = $base_dir.$rows['option']['html'];
					if(is_file($html_file)) {
						ob_start();
						include $html_file;
						$html = ob_get_contents();
						ob_end_clean();
					}
					$js = $rows['option']['js'];
				}
				else {
					$init= 'non';
					$html = $js = '';
				}
				array_push($datas, compact('key', 'name', 'init', 'html', 'js'));
			}
		}
		return fetch_contents($datas, $entry);
	}

	// 모듈 페이지 반환
	function get_url($rows) {
		global $base_url;
		$components = $this->get_components($rows['module']);
		$cs_rows = $components[$rows['component']];
		if($cs_rows['file']) {
			$url = $base_url.array_shift(explode('|', $cs_rows['file']));
			$url .= '?pid='.$rows['no'];
			if($rows['options']) {
				$params = http_build_query(json_decode($rows['options']));
				if($params) $url .= '&'.$params;
			}
		}
		else {
			$url = $base_url.$cs_rows['url'];
		}
		return $url;
		//return $cs_rows['file'] ? $base_url.array_shift(explode('|', $cs_rows['file'])) : $base_url.$cs_rows['url'];
	}
}

?>