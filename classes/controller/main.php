<?php
namespace Controller;
class Main extends \Prefab {
	public $s, $e, $log, $v;
	function __construct() {
		$this->s = \microtime(1);
		$f3 = \Base::instance();
		$this->log = new \Controller\Log('debug.log');
		$this->v = new \Predis\Client([
			'scheme' => 'tcp',
			'host' => $f3->get('valkey.host') ?? '127.0.0.1',
			'port' => $f3->get('valkey.port') ?? 6379,
		]);
		$this->log->w($this->v->get('foo'), 'v->foo', 2, 0);
	}
	function beforeroute(\Base $f3) {
		if ($f3->get('GET.cc') == 1) \Cache::instance()->reset();
		if ($f3->get('SERVER.HTTP_HX_REQUEST')) $f3->set('htmx', true);
	}
	function afterroute(\Base $f3) {
		if ($f3->get('SERVER.HTTP_HX_REQUEST') ?? null) $f3->set('AJAX', true);
		$f3->set('hl', function($lang, $in) {
			$highlighter = new \Highlight\Highlighter();
			$highlit = $highlighter->highlight($lang, $in);
			return $highlit->value;
		});
		$outline = ($f3->get('AJAX') or $f3->get('raw')) ? 'async.html' : 'index.html';
		echo \Template::instance()->render($outline, $f3->get('contenttype') ?: 'text/html');
		$this->e = \microtime(1);
		if (!$f3->get('AJAX')) $this->log->w(\number_format($this->e - $this->s, 10), "{$f3->get('URI')} main timer", 1, 0);
	}
	function content($f3) {
		// \Flash::instance()->addMessage('lorem ipsum', 'success');
		// \Flash::instance()->addMessage('lorem ipsum', 'warning');
		// \Flash::instance()->addMessage('lorem ipsum', 'error');
		$t = $f3->get('ALIAS') . '.html';
		$f3->set('template', \file_exists('../ui/' . $t) ? $t : 'missing.html');
		$this->log->w($f3->get('template'), 'template', 2, 0);
	}
	function error($f3) {
		$f3->set('pagetitle', "{$f3->get('ERROR.code')} {$f3->get('ERROR.status')}");
		$this->log->w($f3->get('ERROR.text'), '', 0, 0, 'ERROR');
		$f3->set('template', 'error.html');
	}
}
