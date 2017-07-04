<?php

namespace payapi ;

final class archival extends helper {
// router :: archivalTransaction ( $key )
  private
    $expiration            =       1 , // days for refreshing files
    $dirArchival           =   false ,
    $types                 = array (
          "transaction" => false ,
          "settings"    =>  true ,
          "callback"    => false ,
          "curl"        => false ,
          "fatal"       => false
    );

  protected function auto () {
    $this -> dirArchival = router :: dirArchival () ;
    $this -> warning ( $this -> dirArchival , 'arch' ) ;
  }

  public function getArchiveData ( $key , $type ) {
    $archive = $this -> archivalFile ( $key , $type ) ;
    if ( is_file ( $archive ) === true ) {
      return file_get_contents ( $file ) ;
    }
    return false ;
  }

  public function setArchiveData ( $key , $data , $type ) {
    //-> @NOTE $data should come encoded ($data,false,true)
    $archive = $this -> archivalFile ( $key , $type ) ;
    return $this -> saveArchive ( $archive , $data ) ;
  }

  private function saveArchive ( $file , $data ) {
    $this -> debug ( 'archival file : ' . $file ) ;
    return true ;

    router :: checkDir ( basename ( $file ) ) ;
    if ( file_put_contents ( $file , $data ) === true ) {
      $this -> debug ( '[archival] saved' ) ;
      return true ;
    }
    $this -> warning ( 'saved' , 'archival' ) ;
    return false ;
  }

  private function archivalFile ( $key , $type ) {
    if ( $this -> checkArchivalType ( $type ) === true ) {
      return $this -> dirArchival . $type . DIRECTORY_SEPARATOR . 'archive' . '.' . $type . '.' . $key . '.' . 'log' ;
    }
    return false ;
  }

  private function checkArchivalType ( $type ) {
    if ( in_array ( $type , $this -> types ) === true ) {
      return true ;
    }
    $this -> warning ( 'no valid' , 'type' ) ;
    return false ;
  }

  public function __toString () {
    return $this -> dirArchival ;
  }


}
