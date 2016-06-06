<?php
class IndexAction extends Action {
  //读取csv数据
  public function index() {
    $array = file('./Upload/'.I('file'));
    $list = array();
    for($i =0 ;$i < count($array);$i++) {
      $a = explode(",",iconv('GBK', 'utf-8', $array[$i]));//把每一行都转换成数组
      $list[$i] = $a;//组成二维数组
    }
    $title = $list[0];
    $this->assign('title',$title);//表格标题行
    unset($list[0]);//删除表格标题行
    $this->assign('list',$list);//表格标题行
    $this->assign('config',session('?email')?1:0);
    $this->display();
  }

  public function upload() {
    import('ORG.Net.UploadFile');
    $upload = new UploadFile();// 实例化上传类
    $upload->uploadReplace = true;//覆盖同名文件
    $upload->saveRule = 'time';
    $upload->allowExts  = array('csv');// 设置附件上传类型
    $upload->savePath =  './Upload/';// 设置附件上传目录
    if(!$upload->upload()) {// 上传失败
      $this->error($upload->getErrorMsg());//提示错误信息
    } else{// 上传成功
      $info =  $upload->getUploadFileInfo();//获取上传文件信息
      //$this->success('上传成功!',U('Index/index',array('file'=>$info['savename'])));
      redirect(U('Index/index').'?file='.$info[0]['savename']);
    }
  }

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
    $config = session('email');//session('email');//C('mail_config');
    foreach($list as $key => $value) {
      $temp_value = $value;//临时保留邮箱列供下面写日志使用
      $config['to'] = $value[2];//收件人
      //$config['subject'] = str_replace('{$username}',$value[1],$config['subject']);//'4月份薪资明细-'  . $value[1] . '*机密'
      $config['subject'] = str_replace('{$username}',$value[1],session('email.subject'));//'4月份薪资明细-'  . $value[1] . '*机密'
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


  //模板下载
  public function template() {
    $file = realpath("template.csv");  //获取绝对路径
    if(is_file($file)) {
      header("Content-Type: application/force-download");
      header("Accept-Length:" . filesize($file));
      header("Content-Disposition: attachment; filename=".basename($file));
      readfile($file);
      exit;
    }else {
      header("HTTP/1.1 404 Not Found");
      exit("文件不存在！");
    }
  }

  //参数配置
  public function config() {
    if(IS_POST OR IS_AJAX) {
      $config = array('from'=>I('user'),'username'=>I('user'),'password'=>I('pass'),'subject'=>I('subject'),'body'=>$_POST['body'],'isHTML'=>TRUE);
      session('email',$config);
      if(file_put_contents(TMPL_PATH.'Index_sendmail.html',session('email.body'))) {
        /*if(IS_POST) {
          $this->redirect(U('Index/index'),'保存成功!',3);
        }*/
        if(IS_AJAX) {
          $this->success('保存成功,关闭浏览器配置失效!');
        }
        $t = session('email');
        dump($t);
      }
    }else {
      $this->subject = session('?email')?session('email.subject'):file_get_contents(APP_PATH.'/Conf/static_config/subject.txt');
      $this->body = session('?email')?session('email.body'):file_get_contents(APP_PATH.'/Conf/static_config/template.txt');
      $this->display();
    }
  }

  public function test(){
    dump(session('email'));
  }

}