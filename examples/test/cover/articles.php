<?php
/**
 *  @file       articles.php
 *  @brief      Setting default cover on articles
 *  
 *  @details    Default cover build using meta data from JSON record list and simple logo covers,
 *  Requires meta data in articles.json and styling in mystyle.css 
 *  @copyright  http://www.gnu.org/licenses/lgpl.txt LGPL version 3
 *  @author     Erik Bachmann <ErikBachmann@ClicketyClick.dk>
 *  @since      2021-02-10T16:30:03
 *  @version    2021-02-10T16:34:07
 */

// Reading meta data
$data   = json_decode( file_get_contents( "articles.json" ), JSON_OBJECT_AS_ARRAY );
// Template for formatting meta data
$template   = [
    [ 'typeBibDKType'   => '[%s]<br>' . PHP_EOL ],
    [ 'dcTitleFull'   => '<span class="title">%s</span>' . PHP_EOL ],
    [ 'date'   => ' (%s) ' . PHP_EOL ],
    [ 'creator'   => '<br><span class="creator">%s</span>' . PHP_EOL ],
    [ 'abstract'   => '<dir class="abstract">%s</dir>' . PHP_EOL ],
    [ 'subject'   => 'Emneord: <dir class="subject">%s</dir>' . PHP_EOL ],
    [ 'dcTitleFull'   => '<dl>' . PHP_EOL ],
    [ 'dcLanguage'   => '<dt>Sprog<dd>%s</dd>' . PHP_EOL ],
    [ 'subjectGenre'   => '<dt>Genre<dd>%s</dd>' . PHP_EOL ],
    [ 'publisher'   => '<dt>Forlag<dd>%s</dd>' . PHP_EOL ],
    [ 'audience'   => '<dt>Målgruppe<dd>%s</dd>' . PHP_EOL ],
    [ 'partOf'   => '<dt>Findes i<dd>%s</dd>' . PHP_EOL ],
    [ 'dcTitleFull'   => '</dl>' . PHP_EOL ],
];

$data = $data['data'];

// File header
echo '<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="author" content="MarkdownViewer++" />
        <title>articles.md</title>
        <link rel="stylesheet" href="mystyle.css">
    </head>
<body>

';

// Build each record block
foreach ( $data as $record => $entries ) {
    $output     = formatRecord( $entries );
    echo "$output\n<br clear=both>\n";
}

//----------------------------------------------------------------------

function formatRecord( $entries ) {
    global $template;
    $output = "";

    //Source ID for cover if not found on Cover Service
    $tags    = ['isPartOfISSN', 'identifierISSN', 'acSource' ];
    foreach ($tags as $tag ) {
        if ( ! empty($entries[$tag]) ) {
            // Container at top
            $output .= sprintf( "
            <hr clear=both>
            <div class='container'>
                <a href='%s'><img src='logo/%s.jpg' class='logo'></a>
                <div class='bottom-left'>
                    <span class='title overlayentity'>%s</span><span class='release overlayentity'>%s</span><span class='creator overlayentity'>%s</span>
                </div>
                <div class='overlay'>%s [%s]</div>
            </div>
            "
,   implode( " ; ", ( ! empty($entries['hasOnlineAccess']) ? $entries['hasOnlineAccess'] : [] ) )
,   strtolower(implode( " ; ", $entries[$tag] ))
,   implode( " ; ", $entries['dcTitleFull'] )
,   implode( " ; ", $entries['date'] )
,   ucwords(implode( " ; ", ( ! empty($entries['creator']) ? $entries['creator'] : [] ) ),  " \t\r\n\f\v-_")
,   implode( " ; ", ( ! empty($entries['partOf']) ? $entries['partOf'] : $entries['acSource'] ) )
,   $tag
);
            break;
        }
    }

    foreach ( $template as $field ) {
        foreach ( $field as $tag => $form ) {
            if ( ! empty($entries[$tag]) )
                $output .= sprintf( $form, implode( " ; ", $entries[$tag] ) );
        }
        
    }
    return( $output );
}   // formatRecord

?>