#/bin/php
<?php
if ( ftell ( STDIN ) !== 0 ) {
    fwrite ( STDERR , "Pipe error\n" ) ;
    exit ( 1 ) ;
}
$stream = '' ;
while ( true ) {
    $stream = trim ( fread ( STDIN , 10240 ) ) ;
    if ( feof ( STDIN ) ) break ;
    if ( $stream === false || strlen ( $stream ) === 0 ) {
        continue ;
    }
    $stream = preg_replace ( '/<br\s*\/>/' , "\n\t" , $stream ) ;
    fwrite ( STDOUT , $stream . "\n" ) ;
}
