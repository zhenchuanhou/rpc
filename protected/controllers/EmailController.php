<?php

class EmailController extends Controller {
    public function  actionEmailtest(){

//        $rpc = new RPC();
//        $client = $rpc->rpcClient(Yii::app()->params['rpcEmailUrl']);
//        $id=1;
////        var_dump($client);DIE;
//        $result = $client->sendEmailAppBooking($id);
//        var_dump($result);
//        echo dirname(__FILE__);die;
//        require '../modules/rpc/server/EmailManager.php';
        set_time_limit(0);
        $model = new EmailManager();
        $result = $model->sendEmailAppBooking(1);
        var_dump($result);
    }

    public function actionSendEmailSalesOrderPaid(){
        $result = $this->client->sendSmsVerifyCode('13916681596', 654321, 10);
        $result = CJSON::decode($result);
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
