<?php

$data   = json_decode( file_get_contents( "articles.json" ), JSON_OBJECT_AS_ARRAY );
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



foreach ( $data as $record => $entries ) {
    $output     = formatRecord( $entries );
    echo "$output\n<br clear=both>\n";
    //printf( "<h2>%s</h2>\n", implode( " ; ", $entries["dcTitleFull"] ) );
}




function formatRecord( $entries ) {
    global $template;
    $output = "";

    $tags    = ['isPartOfISSN', 'identifierISSN', 'acSource' ];
    foreach ($tags as $tag ) {
        if ( ! empty($entries[$tag]) ) {
            // Container at top
            $output .= sprintf( "
            <hr clear=both>
            <div class='container'>
                <a href='%s'><img src='%s.jpg' class='logo'></a>
                <div class='bottom-left'>
                    <span class='title overlayentity'>%s</span><span class='release overlayentity'>%s</span><span class='creator overlayentity'>%s</span>
                </div>
                <div class='overlay'>%s</div>
            </div>
            "
//,   implode( " ; ", $entries['hasOnlineAccess'] )
,   implode( " ; ", ( ! empty($entries['hasOnlineAccess']) ? $entries['hasOnlineAccess'] : [] ) )
,   strtolower(implode( " ; ", $entries[$tag] ))
,   implode( " ; ", $entries['dcTitleFull'] )
,   implode( " ; ", $entries['date'] )
//,   implode( " ; ", $entries['creator'] )
,   ucwords(implode( " ; ", ( ! empty($entries['creator']) ? $entries['creator'] : [] ) ),  " \t\r\n\f\v-_")
,   implode( " ; ", ( ! empty($entries['partOf']) ? $entries['partOf'] : $entries['acSource'] ) )
);
            break;
        }
    }

    foreach ( $template as $field ) {
        //var_export($field);
        
        foreach ( $field as $tag => $form ) {
            if ( ! empty($entries[$tag]) )
                $output .= sprintf( $form, implode( " ; ", $entries[$tag] ) );
        }
        
    }
    return( $output );
}
?>