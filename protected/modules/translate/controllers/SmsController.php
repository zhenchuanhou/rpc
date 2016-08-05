<?php

class SmsController extends RPCController {

    private $server_translate_url;

    function init() {
        parent::init();
        $this->server_translate_url = 'http://' . $_SERVER['HTTP_HOST'] . '/sms/rpc/RpcServer/';
//        echo $this->server_translate_url;die;
        //http://api.pay.com/rpc/RpcServer/Member
    }

    public function actionSendSms() {
        $url = $this->server_translate_url . 'sendSms';
        $x = $this->rpc->rpcClient($url);
        $f = $x->sendSmsVerifyCode('13916681596', 123456, 10);
        echo $f;
        exit;
    }

    public function actionTest() {
        $url = $this->server_translate_url . 'member';
        $x = $this->rpc->rpcClient($url);
        $f = $x->getData('13916681596', 123456, 10);
        var_dump($f);
        exit;
    }




}
