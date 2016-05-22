//发送邮件
  public function sendmail() {
    $array = file('./Upload/' .I('file'));//读取csv文件数据
    $list = array();
    for($i =0 ;$i < count($array);$i++) {
      $a = explode(",",iconv('GBK', 'utf-8', $array[$i]));//把每一行都转换成数组
      $list[$i] = $a;//组成二维数组
    }
    $title = $list[0];
    unset($title[2]);//删除邮箱标题列
    $this->assign('title',$title);//表格标题行
    array_shift($list);
    import('@.ORG.SmtpMail');//引入smtp邮件类
    $mail = new SmtpMail();
    $config = C('mail_config');
    foreach($list as $key => $value) {
      $temp_value = $value;//临时保留邮箱列供下面写日志使用
      $config['to'] = $value[2];//收件人
      $config['subject'] = '4月份薪资明细-'  . $value[1] . '*机密';
      unset($value[2]);//删除邮箱数据列
      $this->assign('list',$value);//把当前人员数据赋值给模版
      $this->assign('username',$value[1]);//当前用户名
      $config['body'] = $this->fetch();//获取模版内容邮件内容
      $mail->setServer("smtp.exmail.qq.com");
      $mail->setMailInfo($config);
      if(!$mail->sendMail()) {
        echo $mail->error() . '<br>';
        echo $value[1] . ':' . $temp_value[2] ."发送失败!<br>";
        //写日志
      }else {
        echo $value[1] . ':' . $temp_value[2] ."发送成功!<br>";
        //写日志
      }
    }
    echo "<a href='".U('Index/index')."'>返回</a>";
  }