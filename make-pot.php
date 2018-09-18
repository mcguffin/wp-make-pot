#!/usr/bin/env php
<?php

require_once('autoload.php');
require_once('include/functions.php');


use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\Error;

define( 'ROOT', getcwd() );
define( 'DS', DIRECTORY_SEPARATOR );

function abspath( $name ) {
	return ROOT . DS . $name;
}

function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.'/'.basename($pattern), $flags));
    }
    return $files;
}


/* plugin or theme! */


$type = null;

if ( file_exists( abspath( 'functions.php' ) ) && file_exists( abspath( 'style.css' ) ) ) {
	$type = 'theme';
	$project_meta = get_file_data( abspath( 'style.css' ), array(
		'project'		=> 'Theme Name',
		'project_version'	=> 'version',
	));
} else {
	$type = 'plugin';
	foreach ( glob( abspath('*.*') ) as $test_file ) {

		$project_meta = get_file_data( $test_file, array(
			'project'		=> 'Plugin Name',
			'project_version'	=> 'Version',
		));

		if ( ! empty($project_meta['project_version'])) {
			break;
		}
	}
}
$php_files = rglob(abspath('*.php'));


$fn_calls = GettextFnCalls::instance();
$fn_calls->pot_headers = array(
	'year'				=> date('Y'),
	'author'			=> exec('whoami'),
	'creation_date'		=> date('c'),
	'revision_date'		=> date('c'),
	'generator'			=> 'WP Make Pot',
) + $project_meta;


if ( isset( $argv[1] ) ) {
	$fn_calls->textdomain = $argv[1];
	$pot_file_name = $argv[1];
} else {
	$pot_file_name = 'default';
}

foreach ( $php_files as $php_file ) {
	$parser = (new ParserFactory)->create( ParserFactory::PREFER_PHP7 );
	$code = file_get_contents( $php_file );
	try {
		$all_ast = $parser->parse($code);
		$visitor = new GettextCallNodeVisitor();
		$traverser = new NodeTraverser;
		$traverser->addVisitor($visitor);
		$traverser->traverse($all_ast);
		$ast = $visitor->get_func_calls( array_keys( GettextFnCalls::gettextFunctions() ) );
	} catch (Error $error) {
		echo "Parse error: {$error->getMessage()}\n";
		echo $error->getTraceAsString();
		echo $php_file;
		echo $code;
		exit();
	}

	foreach ( $ast as $node ) {
		$fn_calls->add( $node, str_replace( ROOT . DS, '', $php_file ) );
	}

	//break;	// $tokens = token_get_all($php_file,TOKEN_PARSE);
	// var_dump($tokens);
	echo $php_file."\n";
}

file_put_contents( "languages/{$pot_file_name}.pot", $fn_calls );
