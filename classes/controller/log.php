<?php
namespace Controller;
class Log extends \Log {
	function write($text, $level = 1) {
		$format = 'c';
		$f3 = \Base::instance();
		if ($f3->DEBUG >= $level) {
			$f3->write($this->file, date($format) . (isset($_SERVER['REMOTE_ADDR']) ? (' [' . $_SERVER['REMOTE_ADDR'] . ']') : '') . ' ' . trim($text) . PHP_EOL, TRUE);
		}
	}
	// this is a compact log standardising method. Call it with $this->l($var_to_log, $name_of_var_if_you_like, $debuglevel, $jsonify)
	function w(mixed $var = '', string $note = '', int $level = 1, int|bool $json = 1, string $status = 'NOTICE', int $truncate = 0) {
		if ($json) {
			$var = $this->jsonlog($var);
		} else {
			$var = var_export($var, 1);
		}
		// TODO: use $truncate to shorten (mostly error) output that is a string (not much point shortening non-string output)
		$backtrace = \debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT|DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
		$from = "{$backtrace['class']}::{$backtrace['function']}() $note ";
		$from .= $json ? '(jsonified)' : '';
		$from = preg_replace(['/\s+/', '/\s+$/'], [' ', ''], $from);
		$this->write("$status: $from: " . $var, $level);
	}
	function jsonlog($i) {
		return preg_replace('/[\s\t\n]+/', ' ', json_encode($i, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
	}
}
