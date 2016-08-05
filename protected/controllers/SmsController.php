<?php

class SmsController extends Controller {
    public function actionSendSmsVerifyCode(){
        $rpc = new RPC();
        $obj = $rpc->rpcClient(Yii::app()->params['rpcSmsUrl']);
        $result = $obj->sendSmsVerifyCode('13916681596', 654321);
        $result = CJSON::decode($result);
        var_dump($result);
        echo $result['status'];
    }
    
    public function actionDbtest1(){
        $weixinpub_id = 'myzdtest';
        $wechatAccount = new WechatAccount();
        $result = $wechatAccount->getByPubId($weixinpub_id);
        $appId = $result->getAppId();
        echo "$appId";
    }
    
    /**
     * rpc demo
     */

}
