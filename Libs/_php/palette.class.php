<?php
/**
 * ÆÈ·¹Æ®
 *@author: kurokisi
 *@authDate: 2011.11.02
 */
class palette {
	var $color = '';
	var $stops = array();
	function palette($color, $interval=10) {
		$this->color = $color;
		$this->stops = array(
			'-2' => $this->stop($color, -($interval*2)), // Á»´õ¾îµÓ°Ô
			'-1' => $this->stop($color, -$interval), // ¾îµÓ°Ô
			'+1' => $this->stop($color, $interval), // ¹à°Ô
			'+2' => $this->stop($color, $interval*2) // Á»´õ¹à°Ô
		);
	}
	function hex2dec($hex) {
		$digits = array();
		$dec = hexdec(preg_replace('/[^0-9A-Fa-f]/', '', $hex));
		$digits['R'] = 0xFF & ($dec >> 0x10);
		$digits['G'] = 0xFF & ($dec >> 0x8);
		$digits['B'] = 0xFF & $dec;
		return $digits;
	}
	function stop($hex, $interval=10) {
		$digits = array();
		foreach($this->hex2dec($hex) as $dec) {
			$num = $dec + $interval;
			if($num<0) $num = 0;
			else if($num>255) $num = 255;
			array_push($digits, sprintf('%02s', dechex($num)));
		}
		return '#'.implode('', $digits);
	}
}
?>