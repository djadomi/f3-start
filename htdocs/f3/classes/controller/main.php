<?php
namespace Controller;
class Main extends \Prefab {
	function __construct() {
		$f3 = \Base::instance();
		$this->log = new Log('debug.log');
	}
	function beforeroute() {
		$f3 = \Base::instance();
	}
	function afterroute() {
		$f3 = \Base::instance();
	}
	function page() {
		$f3 = \Base::instance();
	}
	function error() {
		$f3 = \Base::instance();
		$f3->result = \json_encode([
			'error' => true,
			'code' => $f3->get('ERROR.code'),
			'status' => $f3->get('ERROR.status'),
			'text' => $f3->get('ERROR.text'),
		]);
		$this->log->write("ERROR: Main->error: " . $this->jsonlog(json_decode($f3->result)), 0);
	}
	function jsonlog($i) {
		return preg_replace('/[\s\t\n]+/', ' ', json_encode($i, JSON_PRETTY_PRINT));
	}
}
