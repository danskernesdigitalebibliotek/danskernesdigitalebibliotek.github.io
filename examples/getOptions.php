<?php
/**
 *  @file  getOptions.php
 *  @brief Gets options from the command line argument list or $_REQUeST
 *  
 *  @details   Combining short options and long options for both CLI and WEB
 *      I.e. Options are listed as a long option, where first uppercase letter
 *      equals the short option and value is description for help.
 *  
 *  $optionlist = [
 *      //Option                Description
 *      "Help"              => "Help/usage",            //  Flag
 *      "Debug::"           => "Debugging",             //:: Optional value
 *      "verBose"           => "VerBosing",             //  Flag
 *      "null"              => "Nothing - really",      //  Flag
 *      "SomeThing:"        => "Something is Rotten",   //: Mandatory value
 *      "Foo:"              => "Some array",            //: Mandatory value
 *  ];
 *  
 *  Here "verBose" takes no arguments (no ':') 
 *      short option    'b'
 *      Long option     'verbose'
 *  "Debug" can hold an optional value with 'd' or 'debug'
 *  "SomeThing:" must have a mandatory value with 's' and 'something'
 *  
 *  A value can be a string or an array
 *      -d="String"
 *  or 
 *      -d="string1" -d="string2"
 *  
 *  @copyright  http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 *  @author     Erik Bachmann <ErikBachmann@ClicketyClick.dk>
 *  @since      2020-04-04T00:44:44
 *  @version    2020-04-22T19:29:57
 */

include_once __DIR__ . "/debug.php";

/**
 *  @fn         getOptions
 *  @brief      Parse options from command line
 *  
 *  @details    Enhancement of getopt()
 *      optionlist is an array of longopts with descriptions.
 *      First uppercase character in long option is used as short option
 *      ie "verBose" will have 'b' as short option - and 'verbose' as long option
 *  
 *  
 *  @param [in] $optionlist   Array of options and descriptions
 *  @param [in] $help         Usage description build from optionlist
 *  @return     Array of long option and values
 *  
 *  @example    
 *  
 *  $optionlist = [
 *      //Option                Description     
 *      "Debug::"           => "Debugging",             //:: Optional value
 *      "verBose"           => "VerBosing",             //  Flag
 *      "null"              => "Nothing - really",      //  Flag
 *      "SomeThing:"        => "Something is Rotten",   //: Mandatory value
 *      "Foo:"              => "Some array",            //: Mandatory value
 *  ];
 *  $usage   = "\n";
 *  
 *  $opt = getOptions( $optionlist, $usage );
 *  
 *  if ( isset( $opt['help'] ) ) {
 *       echo "Usage: ";
 *   var_export( $usage );
 *  }
 *  var_export( $opt );
 *  
 *  
 *  // https://www.php.net/manual/en/features.commandline.php
 *  // parse command line arguments into the $_GET variable
 *  parse_str(implode('&', array_slice($argv, 1)), $_GET);
 *  
 *  
 *  @todo       
 *  @bug        
 *  @warning    
 *  
 *  @see        
 *  @since      2020-04-01T18:07:11
 */
function getOptions( $optionlist, &$help = false) {
    $shortopts = $longopts  = $map = [];
    $options    = [];
    $list       = [];

    if ( $help ) 
        $help    .= "\n";

    // Parse options build match lists
    foreach ( $optionlist as $optkey => $optval) {
        $long   = strtolower( $optkey );    // Long list
        $desc   = $optionlist[$optkey];
        $short  = $long[0];

        // Upper case action key from long var
        preg_match_all('/[A-Z]/', $optkey, $matches);   // https://stackoverflow.com/a/10137517
        
        // Build shortlist
        if ( isset( $matches[0][0] ) ) {
            $short  = strtolower( $matches[0][0] );
            $short  .= str_repeat ( ':', substr_count( $optkey, ':') );
        }

        debug( "\n\tshort [$short]\tlong [$long]\tdesc [$desc]" );
        
        // Map short to long
        $map[$short[0]]    = trim($long, ':');
        
        // Build help from option list
        $optionlist[$long]  = $desc;
        
        if ( $help ) {
            $help   .= sprintf( "\t%s\t%-20.20s %s" . PHP_EOL, $short, $long, $desc);
        }
        
        // Add to option lists
        unset( $optionlist[$optkey] );
        array_push( $longopts, $long );
        array_push( $shortopts, $short );
    }
    if ( $help ) {
        $help   .= PHP_EOL;
        $help   .= "\t:\tMandatory value\t\t-x=Y" . PHP_EOL;
        $help   .= "\t::\tOptionally value\t-x OR -x=Y" . PHP_EOL;
    }
    debug(
        sprintf( ">>>\nShortopts: %s\nLongopts: %s\nMap: %s\n<<< DEBUG <<<\n"
        ,    var_export($shortopts, TRUE)
        ,    var_export($longopts, TRUE)
        ,    var_export($map, TRUE)
        )
    );

    // Detect api type (if not set)
    if ( ! isset($GLOBALS['_CLI']) ) 
        $GLOBALS['_CLI'] = php_sapi_name();
    
    if ( "cli" == $GLOBALS['_CLI']  || empty($_SERVER['REMOTE_ADDR']) ) { // In cli-mode
        debug( "cli");
        // Parse $GLOBALS['argv'] since $args cannot be updated on the fly
        $_REQUEST = argv2request( $GLOBALS['argv'] );
    } else {  // Not in cli-mode
        debug( "Not cli");
    }

    debug( "_GET = " .var_export( $_GET, TRUE ) );
    debug( "_REQUEST = " .var_export( $_REQUEST, TRUE ) );
    debug( "short = " .var_export( $shortopts, TRUE ) );
    debug( "long  = " .var_export( $longopts, TRUE ) );

    // Merge options to one list with count of ':' as value
    // Add Short options -b
    for ( $i = 0 ; $i < count($shortopts) ; $i++ ) {
        $count  = substr_count( $shortopts[$i], ':');
        $key    = trim( $shortopts[$i], ':');
        $list[$key] = $count;
    }
    // Add long options --verbose
    for ( $i = 0 ; $i < count($longopts) ; $i++ ) {
        $count  = substr_count( $longopts[$i], ':');
        $key    = trim( $longopts[$i], ':');
        $list[$key] = $count;
    }
    
    debug("List: " . var_export( $list, TRUE ));
    if ( ! isset($_SESSION) )
        $_SESSION =[];

    debug( "Arguments: " . var_export( array_merge( $_ENV, $_SESSION, $_REQUEST ), TRUE ) );
/*
    $t=array_merge( $_ENV, $_SESSION, $_REQUEST );
    foreach ( $t as $option => $value) {
        if ("vendors" == $option) {
        echo "$option\t";
            var_export($value);
        }
    }
    var_export($t['vendors']);exit;
*/
    // $_REQUEST - An associative array that by default contains the contents of $_GET, $_POST and $_COOKIE
    foreach ( array_merge( $_ENV, $_SESSION, $_REQUEST ) as $option => $value) {
        if ( in_array( $option, array_keys($list) ) ) {
            debug( "Opt[$option][" . var_export($value, TRUE) . "]");
            if ( $list[$option] ) {  // If mandatory or optional - add value
                if ( is_array($value) ) {
                    // Handle array
                    $options[$option]   = implode( ",",$value);
                } else
                    $options[$option]   = $value;
            } else {    // If flag - just set
                $options[$option]   = TRUE; // TRUE or not detectable
            }
        }
    }

    // Map shortopts to longopts - unless longopt exists
    foreach ( $map as $key => $value) {
        if ( isset($options[$key]) && ! isset($options[$value]) ) {
            $options[$value]    = $options[$key];
            unset( $options[$key] );
        }
    }

    return( $options );
}   // getOptions()


/**
 *  @fn         argv2request
 *  @brief      Convert arguments from $argv to array in $_GET
 *  
 *  @details    Parse $argv and build an array to insert in $_GET
 *  
 *  Command line arguments: 
 *      -d=cli -b= -c --something=wild_thing --æøå=X --foo=1 --foo=2 --foo=3 
 *  turns into an array:
 *  array (
 *    'd' => 'cli',
 *    'b' => '',
 *    'c' => NULL,
 *    'something' => 'wild_thing',
 *    'æøå' => 'X',
 *    'foo' =>
 *    array (
 *      0 => '1',
 *      1 => '2',
 *      2 => '3',
 *    ),
 *  )
 *  
 *  
 *  @param [in] $argv   Description for $argv
 *  @return Return description
 *  
 *  @example    $_GET = argv2request( $argv );
 *  
 *  @todo       
 *  @bug        
 *  @warning    
 *  
 *  @see        
 *  @since      2020-04-01T23:48:18
 */
function argv2request( $argv ) {
    // $argv arguments to $str
    $str  = implode('&', array_slice($argv, 1));
    // trim hyphens after '&' and at beginning of line
    $str    = ltrim( preg_replace( '/\&-{1,2}/', '&', $str), '-' );
    // $str to array - including doublets!
    $_GET = proper_parse_str( $str );
    return($_GET);
}   // argv2request()


/**
 *  @fn         proper_parse_str
 *  @brief      parse_str replacement for duplicate fields
 *  
 *  @details    The parse_str builtin does NOT process a query string
 *  in the CGI standard way, when it comes to duplicate fields.  
 *  If multiple fields of the same name exist in a query string, 
 *  every other web processing language would read them into an array, 
 *  but PHP silently overwrites them
 *  
 *  @param [in] $str   URL options
 *  @return     Array with arguments (including doublet)
 *  
 *  @example        
 *      // $argv arguments to $str
 *      $str  = implode('&', array_slice($argv, 1));
 *      // trim hyphens after '&' and at beginning of line
 *      $str    = ltrim( preg_replace( '/\&-{1,2}/', '&', $str), '-' );
 *      // $str to array
 *      $_GET = proper_parse_str( $str );
 *  
 *  @todo       
 *  @bug        
 *  @warning    
 *  
 *  @see        https://www.php.net/manual/en/function.parse-str.php#76792
 *  @since      2020-04-19T11:49:17
 */
function proper_parse_str($str) {
  # result array
  $arr = array();

  # split on outer delimiter
  $pairs = explode('&', $str);

  # loop through each pair
  foreach ($pairs as $i) {
    //echo "[$i][".strpos($i, '=')."]\n";
    // Pad flags "x" with '=' to "x="
    if ( !strpos($i, '=') ) $i .= '=';
    # split into name and value
    list($name,$value) = explode('=', $i, 2);
   
    # if name already exists
    if( isset($arr[$name]) ) {
      # stick multiple values into an array
      if( is_array($arr[$name]) ) {
        $arr[$name][] = $value;
      }
      else {
        $arr[$name] = array($arr[$name], $value);
      }
    }
    # otherwise, simply stick it in a scalar
    else {
      $arr[$name] = $value;
    }
  }

  # return result array
  return $arr;
}   // proper_parse_str()


?>
