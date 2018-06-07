#!/usr/bin/env php
<?php

spl_autoload_register(function($class){
	$here = dirname( __FILE__ );
	$parts = explode( '\\', $class );
	$classfiles = [
		implode( DIRECTORY_SEPARATOR, array_merge( array( $here, 'PHP-Parser', 'lib' ), $parts ) ) . '.php',
		implode( DIRECTORY_SEPARATOR, array_merge( array( $here, 'include' ), $parts ) ) . '.php',
	];
	foreach ( $classfiles as $classfile) {
		if ( file_exists($classfile) ) {
			require_once($classfile);
			return;
		}
	}	
});
