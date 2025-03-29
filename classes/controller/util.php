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
	/**
	 * Serves concatenated and minified CSS and JavaScript assets.
	 *
	 * This method handles the serving of CSS and JavaScript assets. It concatenates
	 * multiple files, minifies them, and sets the appropriate content type header.
	 * It supports both CSS (including SCSS compilation) and JavaScript files.
	 *
	 * @param \Base $f3 The F3 base instance.
	 */
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
						'inc/microtip.css',
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
						'inc/alpine-collapse.min.js',
						'inc/alpine-persist.min.js',
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
