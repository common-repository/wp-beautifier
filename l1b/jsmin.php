<?php

/**
 * This is a for the WP-Beautifier simplified version of the PHP adaptation of
 * JSMin written by David Holmes and Gaetano Giunta. The original JSMin was
 * published by Douglas Crockford as jsmin.c.
 *
 * Permission is hereby granted to use this PHP version under the same conditions
 * as jsmin.c, which has the following notice:
 *
 * ----------------------------------------------------------------------------
 *
 * Copyright (c) 2002 Douglas Crockford  (www.crockford.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * The Software shall be used for Good, not Evil.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @package WP-Beautifier
 * @author Till Krüss <me@tillkruess.com>
 *
 */

define('EOF', FALSE);
define('ORD_NL', ord("\n"));
define('ORD_space', ord(' '));
define('ORD_cA', ord('A'));
define('ORD_cZ', ord('Z'));
define('ORD_a', ord('a'));
define('ORD_z', ord('z'));
define('ORD_0', ord('0'));
define('ORD_9', ord('9'));

define('JSMIN_ACT_FULL', 1);
define('JSMIN_ACT_BUF', 2);
define('JSMIN_ACT_IMM', 3);

class JSMin {

	var $in;
	var $out;
	var $theA;
	var $theB;
	var $inLength = 0;
	var $inPos = 0;
	var $isString = false;

	function JSMin($string) {
		$this->in = $string;
		$this->out = '';
		$this->inLength = strlen($string);
		$this->inPos = 0;
		$this->isString = true;
	}

	function isAlphaNum($c) {
		$a = ord($c);
		return ($a >= ORD_a && $a <= ORD_z) || ($a >= ORD_0 && $a <= ORD_9) || ($a >= ORD_cA && $a <= ORD_cZ) || $c === '_' || $c === '$' || $c === '\\' || $a > 126;
	}

	function get() {
		if ($this->inPos < $this->inLength) {
			$c = $this->in[$this->inPos];
			++$this->inPos;
		} else {
			return EOF;
		}
		if ($c === "\n" || $c === EOF || ord($c) >= ORD_space) {
			return $c;
		}
		if ($c === "\r") {
			return "\n";
		}
		return ' ';
	}

	function peek() {
		return $this->inPos < $this->inLength ? $this->in[$this->inPos] : EOF;
	}

	function put($c) {
		$this->out .= $c;
	}

	function next() {
		$c = $this->get();
		if ($c == '/') {
			switch ($this->peek()) {
				case '/' :
					while (true) {
						$c = $this->get();
						if (ord($c) <= ORD_NL) {
							return $c;
						}
					}
				case '*' :
					while (true) {
						$c = $this->get();
						if ($c == '*') {
							if ($this->peek() == '/') {
								$this->get();
								return ' ';
							}
						} else if ($c === EOF) {
							// trigger_error('UnterminatedComment', E_USER_ERROR);
						}
					}
				default :
					return $c;
			}
		}
		return $c;
	}

	function action($action) {
		switch ($action) {
			case JSMIN_ACT_FULL :
				$this->put($this->theA);
			case JSMIN_ACT_BUF :
				$tmpA = $this->theA = $this->theB;
				if ($tmpA == '\'' || $tmpA == '"') {
					while (true) {
						$this->put($tmpA);
						$tmpA = $this->theA = $this->get();
						if ($tmpA == $this->theB) {
							break;
						}
						if (ord($tmpA) <= ORD_NL) {
							// trigger_error('UnterminatedStringLiteral', E_USER_ERROR);
						}
						if ($tmpA == '\\') {
							$this->put($tmpA);
							$tmpA = $this->theA = $this->get();
						}
					}
				}
			case JSMIN_ACT_IMM :
				$this->theB = $this->next();
				$tmpA = $this->theA;
				if ($this->theB == '/' && ($tmpA == '(' || $tmpA == ',' || $tmpA == '=')) {
					$this->put($tmpA);
					$this->put($this->theB);
					while (true) {
						$tmpA = $this->theA = $this->get();
						if ($tmpA == '/') {
							break;
						}
						if ($tmpA == '\\') {
							$this->put($tmpA);
							$tmpA = $this->theA = $this->get();
						}
						else if (ord($tmpA) <= ORD_NL) {
							// trigger_error('UnterminatedRegExpLiteral', E_USER_ERROR);
						}
						$this->put($tmpA);
					}
					$this->theB = $this->next();
				}
				break;
			default :
				// trigger_error('Expected a JSMin::ACT_* constant in action()', E_USER_ERROR);
		}
	}

	function minify() {
		$this->theA = "\n";
		$this->action(JSMIN_ACT_IMM);
		while ($this->theA !== EOF) {
			switch ($this->theA) {
				case ' ' :
					if (JSMin::isAlphaNum($this->theB)) {
						$this->action(JSMIN_ACT_FULL);
					} else {
						$this->action(JSMIN_ACT_BUF);
					}
					break;
				case "\n" :
					switch ($this->theB) {
						case '{' : case '[' : case '(' :
						case '+' : case '-' :
							$this->action(JSMIN_ACT_FULL);
							break;
						case ' ' :
							$this->action(JSMIN_ACT_IMM);
							break;
						default :
							if (JSMin::isAlphaNum($this->theB)) {
								$this->action(JSMIN_ACT_FULL);
							} else {
								$this->action(JSMIN_ACT_BUF);
							}
							break;
					}
					break;
				default :
					switch ($this->theB) {
						case ' ' :
							if (JSMin::isAlphaNum($this->theA)) {
								$this->action(JSMIN_ACT_FULL);
								break;
							}
							$this->action(JSMIN_ACT_IMM);
							break;
						case "\n" :
							switch ($this->theA) {
								case '}' : case ']' : case ')' : case '+' :
								case '-' : case '"' : case '\'' :
									$this->action(JSMIN_ACT_FULL);
									break;
								default :
									if (JSMin::isAlphaNum($this->theA)) {
										$this->action(JSMIN_ACT_FULL);
									} else {
										$this->action(JSMIN_ACT_IMM);
									}
									break;
							}
							break;
						default :
							$this->action(JSMIN_ACT_FULL);
							break;
					}
					break;
			}
		}
		return preg_replace('~\n~ms', '', $this->out);
	}

}
?>