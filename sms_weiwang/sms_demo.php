<?php
include_once('sms.php');

$target = "http://cf.51welink.com/submitdata/Service.asmx/g_Submit";
//替换成自己的测试账号,参数顺序和wenservice对应
$post_data = "sname=DL-wanglu&spwd=82TcXOBS&scorpid=&sprdid=1012888&sdst=13916681596&smsg=".rawurlencode("您的验证码是：954163。请不要把验证码泄露给其他人。【微网通联】");
//$binarydata = pack("A", $post_data);
echo $gets = Post($post_data, $target);
//请自己解析$gets字符串并实现自己的逻辑
//<State>0</State>表示成功,其它的参考文档
?>