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
}
