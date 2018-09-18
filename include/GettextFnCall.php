<?php

use PhpParser\Node;
//use PhpParser\Node\Scalar\String_;

class GettextFnCall {

	public $files = [];

	public $message;

	public $valid;

	public $textdomain;

	/** @var Node */
	public $nodes = [];

	private $id = null;


	public function __construct( Node $node, $file ) {
		$this->add_node( $node, $file );
		$this->parse_node();
	}

	public function getId() {
		return $this->id;
	}

	public function add_node( $node, $file ) {
		$this->files[] = $file;
		$this->nodes[] = $node;
	}

	private function parse_node() {
		$this->message = [];
		$gt_fn = GettextFnCalls::gettextFunctions();
		$node = $this->nodes[0];
		$parse_rules = $gt_fn[ $node->name->getFirst() ];


		foreach ( $parse_rules as $i => $rule ) {
			if ( is_null( $rule ) ) {
				continue;
			}

			if ( $node->args[$i]->value instanceof PhpParser\Node\Scalar\String_ ) {
				$this->message[$rule] = $node->args[$i]->value->value;
			} else {
				$this->valid = false;
				return;
			}
		}
		if ( isset( $node->args[ count( $parse_rules ) ] ) ) {
			$this->textdomain = $node->args[ count( $parse_rules ) ]->value->value;
		} else {
			$this->textdomain = null;
		}
		$msg_id = isset( $this->message['singular'] ) ? $this->message['singular'] : $this->message['string'];
		if ( isset( $this->message['context'] ) ) {
			$msg_id .= ':'.$this->message['context'];
		}
		$this->id = $this->textdomain . ':' . urlencode( $msg_id );
		$this->valid = true;
	}

	private function fmt_msg($msg) {
		if ( false === strpos($msg,"\n") ) {
			return "\"{$msg}\"\n";
		}
		$msg = explode("\n",$msg);
		$msg = array_map( function($arg){
			return $arg . '\n';
		}, $msg );
		array_unshift($msg,'');
		$msg = array_map( array($this,'fmt_msg'), $msg );
		return implode('',$msg);
	}

	public function __toString() {
		$output = '';
		$comments = '';
		$body = '';
		foreach ( $this->nodes as $i => $node ) {
			$comments .= "#: {$this->files[$i]}:{$node->getStartLine()}\n";
		}

		$is_plural = false;

		foreach ( $this->message as $k => $msg ) {

			switch ( $k ) {
				case 'string':
					$body .= "msgid " . $this->fmt_msg($msg);
					break;
				case 'singular':
					$is_plural = true;
					$body .= "msgid " . $this->fmt_msg($msg);
//					$body .= "msgid \"{$msg}\"\n";
					break;
				case 'plural':
					$body .= "msgid_plural " . $this->fmt_msg($msg);
//					$body .= "msgid_plural \"{$msg}\"\n";
					$is_plural = true;
					break;
				case 'context':
					// prepend!
					$body .= "msgctxt " . $this->fmt_msg($msg);
//					$body = "msgctxt \"{$msg}\"\n" . $body;
					break;
			}
		}
		if ( $is_plural ) {
			$body .= "msgstr[0] \"\"\n";
			$body .= "msgstr[1] \"\"\n";
		} else {
			$body .= "msgstr \"\"\n";
		}
		return $comments . $body;
/*
#: woocommerce/global/quantity-input.php:45
msgctxt "Product quantity input tooltip"
msgid "Qty"
msgstr ""


*/
	}
}
