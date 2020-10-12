<?php

// Example for getOptions
include_once "getOptions.php";

$optionlist = [
  //Option                Description     
  "Help"              => "Help/Usage",             //  Flag
  "Debug::"           => "Debugging",             //:: Optional value
  "verBose"           => "VerBosing",             //  Flag
  "null"              => "Nothing - really",      //  Flag
  "Token:"            => "Token is a must",   //: Mandatory value
//  "SomeThing:"        => "Something is Rotten",   //: Mandatory value
  "Foo:"              => "Some array",            //: Mandatory value
];
$usage   = "\n";

$opt = getOptions( $optionlist, $usage );

if ( isset( $opt['help'] ) ) {
    echo "Usage: ";
    var_export( $usage );
    var_export( $opt );
	exit;
}

//var_export( $opt ); exit;
$ref	= json_decode( file_get_contents( "examples.json" ), TRUE, 512, JSON_OBJECT_AS_ARRAY |JSON_THROW_ON_ERROR );
$PID	= FALSE;

echo "<h1>Eksempelposter</h1>\n";
//var_export( $ref ); echo "\n---\n";
foreach( $ref as $heading => $topic ) {
	echo "<h2>$heading</h2>\n";
	echo "<p>${topic['issue']}</p>\n<table border=1>\n";
	foreach( $topic['examples'] as $id => $desc ) {
		echo "<tr><td style='width: 10vw;min-width: 10vw'>$id</td>"
		.	"<td><a target='_blank' href='https://opensearch.addi.dk/b3.5_5.2/?action=search&agency=100200&profile=test&start=1&stepValue=5&query=rec.id=${id}'>Addi</a></td>"
		.	"<td><a target='_blank' href='https://openplatform.dbc.dk/v3/work?access_token=${opt['token']}&pretty=true&timings=true&pids=${id},'>Open Platform /Work</a></td>"
		.	"<td><a target='_blank' href='https://upgrade-fbs.ddbcms.dk/search/ting/${id}'>Upgrade-fbs</a></td>"
		.	"<td style='width: 20vw;min-width: 20vw'>${desc['title']}</td>"
		.	"<td style='width: 90%'>${desc['issue']}</td>"
		.	"</tr>\n"
		;
		//echo "\ttitle []\n\tissue  [] \n";
	}
	echo "</table>\n";
}

?>