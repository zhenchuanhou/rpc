<?php

class TestController extends RPCController {

    private $server_translate_url;

    function init() {
        parent::init();
        $this->server_translate_url = 'http://' . $_SERVER['HTTP_HOST'] . '/sms/rpc/RpcServer/';
//        echo $this->server_translate_url;die;
        //http://api.pay.com/rpc/RpcServer/Member
    }

    public function actionTest() {
        $url = $this->server_translate_url . 'member';
        $x = $this->rpc->rpcClient($url);
        $model = MsgSmsTemplate::model()->getById(1);
        file_put_contents('a.txt', var_export($model, true));
        $f = $x->getData($model);
        print_r($f);
        exit;
    }

}
