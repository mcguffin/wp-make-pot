<?php

use PhpParser\Node;

class GettextFnCalls {

	private static $instance = null;

	/*
Vars:
year
owner
version (=theme version)
POT-Creation-Date
X-Generator
	*/

	private $pot_header = '# Copyright (C) 2018 Jörn Lund
# This file is distributed under the GNU General Public License v2 or later.
#, fuzzy
msgid ""
msgstr ""
"Project-Id-Version: _bs v0.0.1\n"
"Report-Msgid-Bugs-To: \n"
"POT-Creation-Date: 2016-05-06 15:49+0100\n"
"PO-Revision-Date: 2015-12-31 12:00+0100\n"
"Language-Team: \n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"Plural-Forms: nplurals=2; plural=n != 1;\n"
"X-Generator: Poedit 1.8.1\n"
"X-Poedit-SourceCharset: UTF-8\n"
"X-Poedit-KeywordsList: __;_e;__ngettext:1,2;_n:1,2;__ngettext_noop:1,2;"
"_n_noop:1,2;_c;_nc:4c,1,2;_x:1,2c;_nx:4c,1,2;_nx_noop:4c,1,2;_ex:1,2c;"
"esc_attr__;esc_attr_e;esc_attr_x:1,2c;esc_html__;esc_html_e;"
"esc_html_x:1,2c\n"
"X-Textdomain-Support: yes\n"
"X-Poedit-Basepath: ./\n"
"Last-Translator: \n"
"X-Poedit-SearchPath-0: ..\n"
"X-Poedit-SearchPathExcluded-0: node_modules\n"';

	private static $gettext_functions = array(
		'_'					=> array('string'),
		'__'				=> array('string'),
		'_e'				=> array('string'),
		'_c'				=> array('string'),
		'esc_attr__'		=> array('string'),
		'esc_html__'		=> array('string'),
		'esc_attr_e'		=> array('string'),
		'esc_html_e'		=> array('string'),

		'_n_noop'			=> array('singular', 'plural' ),
		'__ngettext_noop'	=> array('singular', 'plural'),
		'_n_js'				=> array('singular', 'plural'),

		'_x'				=> array('string', 'context'),
		'_ex'				=> array('string', 'context'),
		'esc_attr_x'		=> array('string', 'context'),
		'esc_html_x'		=> array('string', 'context'),

		'_n'				=> array('singular', 'plural', null ),
		'_nc'				=> array('singular', 'plural', null ),
		'__ngettext'		=> array('singular', 'plural', null),

		'_nx'				=> array('singular', 'plural', null, 'context'),

		'_nx_noop'			=> array('singular', 'plural', 'context'),
		'_nx_js'			=> array('singular', 'plural', 'context'),
	);

	public $textdomain = null;

	public static function gettextFunctions() {
		return self::$gettext_functions;
	}

	public static function instance() {
		if ( is_null(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	private $fn_calls = [];

	public function add( Node $node, $file ) {
		$call = new GettextFnCall( $node, $file );
		if ( ! $call->valid ) {
			return;
		}
		$id = $call->getId();
		if ( ! isset( $this->fn_calls[$id] ) ) {
			$this->fn_calls[$id] = $call;
		} else {
			$this->fn_calls[$id]->add_node( $node, $file );
		}
		return $this->fn_calls[$id];
	}

	public function __toString() {
		$out = '';
		$out .= $this->pot_header . "\n";
		$out .= "\n";
		foreach ( $this->fn_calls as $fn_call ) {
			if ( $this->textdomain === $fn_call->textdomain ) {
				$out .= $fn_call;
				$out .= "\n";
			}
		}
		return $out;
	}

}
