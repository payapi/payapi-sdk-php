<?php

namespace payapi ;

final class archival extends helper {
// router :: archivalTransaction ( $key )
  private
    $dirArchival           =   false ,
    $types                 = array (
          "transaction" ,
          "callback" ,
          "curld" ,
          "fatal"
    );

  protected function auto () {
    $this -> dirArchival = router :: dirArchival () ;
  }

  public function getArchiveData ( $key , $type ) {
    $archive = $this -> archivalFile ( $key , $type ) ;
    if ( is_file ( $archive ) === true ) {
      return file_get_contents ( $file ) ;
    }
    return false ;
  }

  public function setArchiveData ( $key , $ype , $data ) {
    //-> $data should come encoded ($data,false,true)
    $archive = $this -> archivalFile ( $key , $type ) ;
    return $this -> saveArchive ( $archive , $data ) ;
  }

  private function saveArchive ( $file , $data ) {
    router :: checkDir ( basename ( $file ) ) ;
    return file_put_contents ( $file , $data ) ;
  }

  private function archivalFile ( $key , $type ) {
    if ( $this -> checkType ( $type ) === true ) {
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
