<?php
namespace Service;
class Dictionary extends \Magic {
	protected $lex = [], $prev_key, $f3;
	function __construct(array $lexicon, $prev_key = null) {
		$this->lex = $lexicon;
		$this->prev_key = $prev_key;
		$this->f3 = \Base::instance();
	}
	function exists($key) {
		$val = $this->f3->ref($key, false, $this->lex);
		return isset($val);
	}
	function set($key, $val) {
		$ref = &$this->f3->ref($key, true, $this->lex);
		$ref = $val;
		return $ref;
	}
	function &get($key) {
		$val = $this->f3->ref($key, false, $this->lex);
		$current_key = ($this->prev_key ? $this->prev_key . '.' : '') . $key;
		if (is_array($val)) {
			$val = new self($val, $current_key);
		} else {
			if (!$val) {
				// do something when language key is missing
				// var_dump('missing language key: '.$current_key);
				return $current_key;
			} else {
				// var_dump('language key used: '.$current_key);
			}
		}
		return $val;
	}
	function clear($key) {
	}
}
