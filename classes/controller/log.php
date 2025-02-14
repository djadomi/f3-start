<?php
namespace Controller;
class Log extends \Log {
	function write($text, $level = 1) {
		$format = 'c';
		$f3 = \Base::instance();
		if ($f3->get('DEBUG') >= $level) {
			$f3->write($this->file, date($format) . (isset($_SERVER['REMOTE_ADDR']) ? (' [' . $_SERVER['REMOTE_ADDR'] . ']') : '') . ' ' . trim($text) . PHP_EOL, TRUE);
		}
	}
}
