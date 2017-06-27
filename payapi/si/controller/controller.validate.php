<?php

namespace payapi ;

final class controller_validate extends controller {

  public function run () {
    if ( $this -> arguments () !== false && $this -> arguments () !== false ) {
      if ( $this -> model -> validSchema ( $this -> arguments () , $this -> arguments ( 1 ) ) !== false ) {
        return $this -> response ( 200 ) ;
      } else {
        return $this -> response ( $this -> error -> errorAppSchemaNoValid () ) ;
      }
    } else {
      return $this -> response ( $this -> error -> errorValidatorNoDataFound () ) ;
    }
  }


}
