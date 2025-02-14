<?php
namespace Controller;
class Util extends Main {
	function __construct() {
		parent::__construct();
	}
	public function browser() {
		$f3 = \Base::instance();
		if ($f3->get('SERVER.HTTP_USER_AGENT') == null) {
			$this->log->w($f3->get('SERVER.HTTP_USER_AGENT') ?? null, 'HTTP_USER_AGENT', 0, 0, 'WARN');
			return (object)[
				'browser' => 'no agent string',
				'browser_version' => 'no agent string',
				'device' => 'Mobile',
				'os' => 'Android',
				'os_version' => 'no agent string',
				'realos' => 'no agent string',
				'technology' => 'HTML5',
			];
		}
		$this->log->w($f3->get('SERVER.HTTP_USER_AGENT'), 'HTTP_USER_AGENT', 3, 0);
		$browser = new \foroco\BrowserDetection();
		$result = $browser->getAll($f3->get('SERVER.HTTP_USER_AGENT'));
		if (($f3->get('ALIAS') == 'home' or $f3->get('ALIAS') == 'lobby') and $f3->get('GET.tech') == '1') {
			$this->log->w($result, 'result', 2, 1);
		}
		if (!$f3->get('AJAX')) $this->log->w($result, 'result', 3, 1);
		return (object)[
			'browser' => $result['browser_name'],
			'browser_version' => $result['browser_version'],
			'device' => \ucfirst($result['device_type']),
			'os' => $result['os_name'],
			'os_version' => \ucfirst($result['os_version']),
			'realos' => $result['os_title'],
			'technology' => 'HTML5',
		];
	}
}
