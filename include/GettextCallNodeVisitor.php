<?php

use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeDumper;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;




class GettextCallNodeVisitor extends NodeVisitorAbstract {

	private $func_calls = [];
	private $current_fn_filter = [];

	public function leaveNode(Node $node) {
		if ($node instanceof Node\Expr\FuncCall) {
			$this->func_calls[] = $node;
		}
		return null;//new Node\Expr\FuncCall( $node->value );
	}

	public function get_func_calls( $filter = [] ) {

		if ( empty( $filter ) ) {
			return $this->func_calls;
		}
		$this->current_fn_filter = $filter;
		return array_filter( $this->func_calls, function($node){
			return in_array( $node->name->getFirst(), $this->current_fn_filter );
		} );

	}
}
