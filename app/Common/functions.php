<?php
  function md6( $str ){
    return md5(md5( md5($str) ));
  }