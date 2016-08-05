<?php

class RpcServerController extends RPCController {

    function actionMember() {
        $this->rpc->rpcServer('member_1', 'rpc', 'user');
    }

    function actionSendSms() {
        $this->rpc->rpcServer('SmsManager', 'rpc');
    }

    function actionSendEmail() {
        $this->rpc->rpcServer('EmailManager', 'rpc');
    }

}
