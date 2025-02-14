<?php
namespace Controller;
class Main extends \Prefab {
	public $s, $e, $log;
	function __construct() {
		$this->s = \microtime(1);
		$f3 = \Base::instance();
		$this->log = new \Controller\Log('debug.log');
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
	function assets() {
		$f3 = \Base::instance();
		$this->log->w($f3->get('PARAMS.type'), 'type', 3);
		$f3->set('raw', 1);
		$f3->set('template', 'raw.html');
		$f3->set('result', '');
		switch ($f3->get('PARAMS.type')) {
			case 'css': {
				$f3->set('contenttype', 'text/css');
				$assets = [
					'min' => [
					],
					'css' => [
					],
					'scss' => [
						'main.scss',
					],
				];
				if ($f3->get('DEBUG') > 0) {
					$assets['scss'][] = 'atom-one-dark.css';
				}
				foreach ($assets['min'] as $i) {
					$prefix = preg_match('/^http/', $i) ? '' : '../styles/';
					$this->log->w("$prefix$i", 'adding min.css', 3, 0);
					$f3->result .= \file_get_contents("$prefix$i") . "\n";
				}
				foreach ($assets['css'] as $i) {
					$prefix = preg_match('/^http/', $i) ? '' : '../styles/';
					$this->log->w("$prefix$i", 'adding css', 3, 0);
					$f3->result .= \Web::instance()->minify("$prefix$i") . "\n";
				}
				if (count($assets['scss']) > 0) {
					$scssphp = new \ScssPhp\ScssPhp\Compiler;
					$scssphp->setImportPaths('../styles/');
					$scssphp->setOutputStyle(\ScssPhp\ScssPhp\OutputStyle::COMPRESSED);
					foreach ($assets['scss'] as $i) {
						$prefix = preg_match('/^http/', $i) ? '' : "../styles/";
						$this->log->w("$prefix$i", 'adding scss', 3, 0);
						// $file = \file_get_contents("$prefix$i");
						$f3->result .= $scssphp->compileString(\file_get_contents("$prefix$i"))->getCss();
					}
				}
				break;
			}
			case 'js': {
				$assets = [
					'min' => [
						'inc/htmx.min.js',
						'inc/alpine.min.js',
						'inc/css-scope-inline.js',
					],
					'list' => 'main.js',
				];
				foreach ($assets['min'] as $i) {
					$prefix = preg_match('/^https/', $i) ? '' : "../scripts/";
					$this->log->w("$prefix$i", 'adding minified js', 3, 0);
					$f3->result .= "/* $prefix$i */\n" . \file_get_contents("$prefix$i") . ";\n";
				}
				$f3->result .= "/* {$assets['list']} */\n" . \Web::instance()->minify($assets['list'], 'application/x-javascript', 1, "{$f3->get('root')}/scripts/");
				break;
			}
		}
	}
}
