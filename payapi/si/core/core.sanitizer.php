<?php

namespace payapi ;

final class sanitizer {

  public function sanitizeFromSchema ( $schema , $data ) {
    $diffs = array_diff_key ( $data , $schema [ '___schema___' ] ) ;
    foreach ( $diffs as $diff => $value ) {
      if ( $diff != 'numberOfInstallments' ) {
        unset ( $data [ $diff ] ) ;
      }
    }
    return $data ;
  }


}
