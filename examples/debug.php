<?php
/**
 *  @file  debug.php
 *  @brief Debug, tracing and verbose
 *  
 *  @details   Debugging, trace logging and verbose 
 *  activated by environment variables 
 *  - or directly by globald variables in PHP
 *  - or as a part of URL
 *  
 *  @example
 *  define("_DEBUG_", 1 );
 *  include_once "debug.php";
 *  
 *  debug("Hello" );
 *  debug( 0 );
 *  debug( TRUE );
 *  debug( __FILE__ ."[". __LINE__."]" );
 *  
 *  Or environment variable "_DEBUG_"
 *  export _DEBUG_=1
 *  
 *  Output:
 *   !DEBUG /src/file.php[290]{debug}(string): 'This is a note'
 *  
 *  Reads as: "This is a note" from file.php line 290 function debug()
 *
 *  @copyright  http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 *  @author     Erik Bachmann <ErikBachmann@ClicketyClick.dk>
 *  @since      2020-03-20T16:02:16
 *  @version    2020-05-25T20:36:44
 */

 // https://stackoverflow.com/a/28898174
if(!defined('STDIN'))  define('STDIN',  fopen('php://stdin',  'r'));
if(!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'w'));
if(!defined('STDERR')) define('STDERR', fopen('php://stderr', 'w'));

if ( ! isset( $GLOBALS['config'] ) ) {
    //$GLOBALS['config']  = [];
    $config = [];
}

// Check if _DEBUG_ is defined or in environment
if ( ! isset( $GLOBALS['config']['_DEBUG_'] ) ) {
    if ( getenv('_DEBUG_') ) {
        $GLOBALS['config']['_DEBUG_'] = getenv('_DEBUG_');
    } else {
        if ( isset( $_GET['debug'] ) ) {
            $GLOBALS['config']['_DEBUG_'] = $_GET['debug'];
        } else {
            $GLOBALS['config']['_DEBUG_'] = FALSE; // No debugging
        }
    }
}

// Check if _VERBOSE_ is defined or in environment
if ( ! isset( $GLOBALS['config']['_VERBOSE_'] ) ) {
    if ( getenv('_DEBUG_') ) {
        $GLOBALS['config']['_VERBOSE_'] = getenv('_VERBOSE_');
    } else {
        if ( isset( $_GET['verbose'] ) ) {
            $GLOBALS['config']['_VERBOSE_'] = $_GET['verbose'];
        } else {
            $GLOBALS['config']['_VERBOSE_'] = FALSE; // No debugging
        }
    }
}


//echo "[{$GLOBALS['config']['_VERBOSE_']}]";

if ( $GLOBALS['config']['_DEBUG_'] ) {
    echo "!DEBUG on" . PHP_EOL;
    function debug( $msg ) {
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        $type   = gettype( $msg );
        if ('cli' === php_sapi_name() ) {
            fprintf(STDERR, "!DEBUG %s[%s]{%s}(%s): %s\n", 
                $caller['file'], $caller['line'], $bt[0]['function'], $type, var_export( $msg, TRUE ) );
        } else {
            //fprintf(STDERR, "!DEBUG <pre>%s[%s](%s): %s</pre>\n", 
            printf( "<pre>!DEBUG %s[%s]{%s}(%s): %s</pre>\n", 
                $caller['file'], $caller['line'], $bt[0]['function'], $type, var_export( $msg, TRUE ) );
        }
    }

    function trace( $msg ) {
        $bt = debug_backtrace();
        $caller = array_shift($bt);
        $type   = gettype( $msg );
        if ('cli' === php_sapi_name() ) {
            fprintf(STDERR, "!TRACE %s[%s](%s): %s\n", 
                $caller['file'], $caller['line'], $type, var_export( $msg, TRUE ) );
        } else {
            //fprintf(STDERR, "!DEBUG <pre>%s[%s](%s): %s</pre>\n", 
            printf( "<pre>!TRACE %s[%s](%s): %s</pre>\n", 
                $caller['file'], $type, $caller['line'], var_export( $msg, TRUE ) );
        }
    }
} else {
    function debug( $msg ) { /* No debugging */ }
    function trace( $msg ) { /* No debugging */ }
}

if ( $GLOBALS['config']['_VERBOSE_'] ) {
    debug( "!VERBOSE on" );
    function verbose( $msg ) {
        if ('cli' === php_sapi_name() ) {
            $bt = debug_backtrace();
            $caller = array_shift($bt);
            $type   = gettype( $msg );
            fprintf(STDERR, "%s\n", trim( var_export( $msg, TRUE ), "'") );
        } else {
            printf("<pre>%s</pre>\n", trim( var_export( $msg, TRUE ), "'") );
        }
    }   // verbose()
} else {
    function verbose( $msg ) { /* Quiert mode */ }
}

?>
