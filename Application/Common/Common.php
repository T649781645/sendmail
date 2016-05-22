<?php
  function ReadExcel($file = 'test.csv') {
    $temp = file($file);
    /*for ($i=0;$i <count($temp);$i++) {
      $string = explode(",",$temp[$i]);
      //通过循环对数据作一些处理
      for($k=0;$k<11;$k++){
        //对数据转码，转为utf-8
        $string[$k] = iconv('GBK', 'utf-8', $string[$k]);
        //替换列中的一些字符（避免某些?乱码字符，这个根据你的需要替换）
        $string[$k] = str_replace('?','',$string[$k]);
      }
    }*/
    return $temp;
  }
?>