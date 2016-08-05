<?php

class SmsManager {
    const VENDOR_JIANZHOU = 'jianzhou';
    const VENDOR_YUNTONGXUN = 'yuntongxun';
    const VENDOR_ACTIVE = 'jianzhou';
    const TYPE_INFORM = 1; //通知
    const TYPE_MARKETING = 2; //营销
    const IS_AUTO_YES = 1;
    const IS_AUTO_NO = 0;
    const ACTIVATE_NO = 0; //未激活
    const ACTIVATE_OK = 1; //已激活
    const SEND_INTERVAL_SECOND = 60; //短信发送间隔 60秒
    const VERIFYCODE_EXPIRY_MINUTE = 10; //验证码有效期10分钟
    const BOOKING_RELATION_DAY = 1; //预约联系工作日

    /**
     * 发起HTTPS请求
     */
    public function curlRequest($url, $data, $post = 1) {
        //初始化curl
        $ch = curl_init();
        //参数设置
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT,10);
        curl_setopt($ch, CURLOPT_POST, $post);
        if ($post){
            $post_data = http_build_query($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        //连接失败
        if ($result == FALSE) {
            Yii::log('sms:' . var_export($data, true), CLogger::LEVEL_ERROR, __METHOD__);
            $result = "{\"statusCode\":\"1\",\"statusMsg\":\"timeout\"}";
        }

        curl_close($ch);
        return $result;
    }

    public function sendSmsTemplateViaJianZhou($to, $content, $type = self::TYPE_INFORM) {
        $config = ConfigSms::model()->getByAttributes(array('sms_name' => self::VENDOR_JIANZHOU, 'type' => $type));
        $post_data = array(
            'account' => $config->sms_id,
            'password' => $config->key,
            'destmobile' => $to, //"13020267570;17717394560"
            'msgText' => $content . '【名医主刀】',
        );

        $result = $this->curlRequest($config->url, $post_data);

        if ($result > 0) {
            $is_success = 1;
            $data['status'] = 0;
        } else {
            $is_success = 0;
            $data['status'] = -1;
        }
        $mobiles = explode(';', $to);
        foreach ($mobiles as $mobile) {
            $msgSmsLog = new MsgSmsLog();
            $msgSmsLog->vendor_name = self::VENDOR_JIANZHOU;
            $msgSmsLog->mobile = $mobile;
            $msgSmsLog->content = $content;
            $msgSmsLog->type = $type;
            $msgSmsLog->user_host_ip = Yii::app()->request->getUserHostAddress();
            $msgSmsLog->is_success = $is_success;
            $msgSmsLog->save();
            $data['smsId'][] = $msgSmsLog->getId();
        }
        return CJSON::encode($data);
    }


    // Send verifing sms to user's mobile when user registers.
    /**
     *
     * @param type $to mobile number.
     * @param type $code  verify code.
     * @param type $expiry  expiry duration, eg 10 minutes.
     * @return array of errors or empty array if it is success.
     */
    /*
      public function sendVerifyUserRegisterSms($to, $code, $expiry) {
      $templateId = '25322';  //template id, from 云通讯.
      $values = array($code, $expiry);
      return $this->sendSmsTemplateViaYunTongXun($to, $values, $templateId);
      }
     */
    protected function sendSmsTemplateViaYunTongXun($to, $values = null, $templateId) {
        require_once("./protected/sdk/yuntongxun/yuntongxun.config.php");
        //require_once("./protected/sdk/yuntongxun/yuntongxun.test.php");
        require_once("./protected/sdk/yuntongxun/CCPRestSmsSDK.php");
        //$ytxConfig from yuntongxun.config.php.

        $rest = new REST($ytxConfig['serverIP'], $ytxConfig['serverPort'], $ytxConfig['softVersion']);
        $rest->setAccount($ytxConfig['accountSid'], $ytxConfig['accountToken']);
        $rest->setAppId($ytxConfig['appId']);
        $ret = $rest->sendTemplateSMS($to, $values, $templateId);   //returns a SimpleXMLElement object.
        $errors = array();
        if (is_null($ret)) {
            // Null return.
            $errors[] = 'No response from vendor.';
        } else if ($ret->statusCode != 0) {
            // Error.           
            $msg = strval($ret->statusMsg);
            $code = strval($ret->statusCode);
            $errors[$code] = $msg;
        } else {
            // Success.            
        }
        return $errors;
    }

    /**
     * 是否可以发送 满足时间间隔
     * @param $mobile
     * @return bool
     */
    public function isSend($mobile){
        $msgSmsLog = new MsgSmsLog();
        $last = $msgSmsLog->getLastByMobile($mobile);
        if($last){
            if(time()-strtotime($last->date_created) < self::SEND_INTERVAL_SECOND){
                return false;
            }
        }
        return true;
    }

    // Send verifying sms to user's mobile number.
    // 发送验证码
    /**
     *
     * @param type $to mobile number.
     * @param type $code  verify code.
     * @param type $expiry  expiry duration, eg 10 minutes.
     * @return array of errors or empty array if it is success.
     */
    public function sendSmsVerifyCode($to, $code, $vendor = self::VENDOR_ACTIVE) {
        if(!$this->isSend($to)){
            return CJSON::encode(array('status'=>-1));
        }
        if ($vendor == self::VENDOR_YUNTONGXUN) {
            $templateId = '25322';  //template id, from 云通讯.
            $values = array($code, self::VERIFYCODE_EXPIRY_MINUTE);
            return $this->sendSmsTemplateViaYunTongXun($to, $values, $templateId);
        } elseif ($vendor == self::VENDOR_JIANZHOU) {
            $model = MsgSmsTemplate::model()->getByAttributes(array('code' => 'verifyCode', 'vendor_name' => $vendor));
            $values = array($code, self::VERIFYCODE_EXPIRY_MINUTE);
            $content = str_replace(array('{verifyCode}', '{minute}'), $values, $model->content);
            return $this->sendSmsTemplateViaJianZhou($to, $content, $model->type);
        }
    }

    /**
     * 领奖成功发送提示短信
     * @param type $to
     * @param type $data
     * @param type $vendor
     * @return type
     */
    public function sendSmsCoupon($to, $data = null, $vendor = self::VENDOR_ACTIVE) {
        if ($vendor == self::VENDOR_YUNTONGXUN) {
            $templateId = '61004';  //template id, from 云通讯.
            $error = $this->sendSmsTemplateViaYunTongXun($to, $data, $templateId);
        } elseif ($vendor == self::VENDOR_JIANZHOU) {
            $model = MsgSmsTemplate::model()->getByAttributes(array('code' => 'wx.coupon', 'vendor_name' => $vendor));
            $content = $model->content;
            return $this->sendSmsTemplateViaJianZhou($to, $content, $model->type);
        }
    }

    /**
     * 当用户提交预约后，发送短信给用户。
     * @param type $to 手机号
     * @param type $data 参数顺序: 1.专家, 2.refno 预约号
     * @param type $vendor 短信提供商
     * @return type
     */
    public function sendSmsBookingSubmit($to, $data, $vendor = self::VENDOR_ACTIVE) {
        if ($vendor == self::VENDOR_YUNTONGXUN) {
            //$templateId = '49856';  //template id, from 云通讯.
            $templateId = '50220';
            $values = array($data->refno, $data->expertBooked, self::BOOKING_RELATION_DAY);
            return $this->sendSmsTemplateViaYunTongXun($to, $values, $templateId);
        } elseif ($vendor == self::VENDOR_JIANZHOU) {
            $model = MsgSmsTemplate::model()->getByAttributes(array('code' => 'booking.create.user', 'vendor_name' => $vendor));
            $values = array($data->refno, $data->expertBooked, self::BOOKING_RELATION_DAY);
            $content = str_replace(array('{bkRefNo}', '{doctor}', '{day}'), $values, $model->content);

            return $this->sendSmsTemplateViaJianZhou($to, $content, $model->type);
        }
    }

    /**
     * 当客服给预约关联了专家后，发送短信给专家。
     * @param type $to 手机号
     * @param type $data 参数顺序： 1. refno 预约号, 2. booking id.
     * @param type $vendor 短信提供商
     * @return type
     */
    public function sendSmsBookingAssignDoctor($to, $data, $vendor = self::VENDOR_ACTIVE) {
        if ($vendor == self::VENDOR_YUNTONGXUN) {
            //$templateId = '49996';  //template id, from 云通讯.
            //$values = array($data->refno, $data->bookingId);
            $templateId = '50231';
            $url = 'http://md.mingyizhudao.com/mobiledoctor/patientbooking/doctorPatientBooking?id=' . $data->id;
            $values = array($data->refno, $url);
            return $this->sendSmsTemplateViaYunTongXun($to, $values, $templateId);
        } elseif ($vendor == self::VENDOR_JIANZHOU) {
            $model = MsgSmsTemplate::model()->getByAttributes(array('code' => 'booking.received.expert', 'vendor_name' => $vendor));
            $url = 'http://md.mingyizhudao.com/mobiledoctor/patientbooking/doctorPatientBooking?id=' . $data->id;
            $values = array($data->refno, $url);
            $content = str_replace(array('{bkRefNo}', '{url}'), $values, $model->content);
            return $this->sendSmsTemplateViaJianZhou($to, $content, $model->type);
        }
    }

    /**
     * 支付后通知短信
     * @param type $to
     * @param type $data 参数顺序 1. amount 金额, 2. refno 预约号.
     * @param type $vendor
     * @return type
     */
    public function sendSmsBookingDepositPaid($to, $data, $vendor = self::VENDOR_ACTIVE) {
        if ($vendor == self::VENDOR_YUNTONGXUN) {
            $templateId = '49857';  //template id, from 云通讯.
            $values = array($data->amount, $data->refno);
            return $this->sendSmsTemplateViaYunTongXun($to, $values, $templateId);
        } elseif ($vendor == self::VENDOR_JIANZHOU) {
            $model = MsgSmsTemplate::model()->getByAttributes(array('code' => 'booking.pay', 'vendor_name' => $vendor));
            $values = array($data->amount, $data->refno);
            $content = str_replace(array('{amount}', '{refNo}'), $values, $model->content);
            return $this->sendSmsTemplateViaJianZhou($to, $content, $model->type);
        }
    }

    /**
     * 发送自定义短信
     * @param $to
     * @param $content
     * @param string $vendor
     * @return mixed|string
     */
    public function sendSmsCustomize($to, $content, $vendor = self::VENDOR_ACTIVE) {
        if ($vendor == self::VENDOR_JIANZHOU) {
            $content = strip_tags($content);
            return $this->sendSmsTemplateViaJianZhou($to, $content, self::TYPE_MARKETING);
        }
    }

    /**
     * 创建新用户后通知短信
     * @param type $to
     * @param type $data 参数顺序 1. name 患者名, 2. password 密码.
     * @param type $vendor
     * @return type
     */
    public function sendSmsNewUser($to, $data, $vendor = self::VENDOR_ACTIVE) {
        if ($vendor == self::VENDOR_YUNTONGXUN) {

        } elseif ($vendor == self::VENDOR_JIANZHOU) {
            $model = MsgSmsTemplate::model()->getByAttributes(array('code' => 'newUser', 'vendor_name' => $vendor));
            $mobile = substr($to, -4);
            $url = 'http://www.mingyizhudao.com/user/forgetPassword';
            $values = array($data->name, $mobile, $data->password, $url);
            $content = str_replace(array('{name}', '{mobile}', '{password}', '{url}'), $values, $model->content);
            return $this->sendSmsTemplateViaJianZhou($to, $content, $model->type);
        }
    }

    /**
     * 电话无人接听或者挂断
     * @param type $to
     * @param type $data
     * @param type $vendor
     * @return type
     */
    public function sendSmsServicePhoneNot($to, $data, $vendor = self::VENDOR_ACTIVE) {
        if ($vendor == self::VENDOR_YUNTONGXUN) {

        } elseif ($vendor == self::VENDOR_JIANZHOU) {
            $model = MsgSmsTemplate::model()->getByAttributes(array('code' => 'service.phone.not', 'vendor_name' => $vendor));
            $values = array($data->disease, $data->hospital, $data->expert);
            $content = str_replace(array('{disease}', '{hospital}', '{expert}'), $values, $model->content);
            return $this->sendSmsTemplateViaJianZhou($to, $content, $model->type);
        }
    }

    /**
     * 对平台不信任
     * @param type $to
     * @param type $vendor
     * @return type
     */
    public function sendSmsServiceDistrust($to, $vendor = self::VENDOR_ACTIVE) {
        if ($vendor == self::VENDOR_YUNTONGXUN) {

        } elseif ($vendor == self::VENDOR_JIANZHOU) {
            $model = MsgSmsTemplate::model()->getByAttributes(array('code' => 'service.distrust', 'vendor_name' => $vendor));
            $content = $model->content;
            return $this->sendSmsTemplateViaJianZhou($to, $content, $model->type);
        }
    }

    /**
     * 不接受平台服务
     * @param type $to
     * @param type $vendor
     * @return type
     */
    public function sendSmsServiceReject($to, $vendor = self::VENDOR_ACTIVE) {
        if ($vendor == self::VENDOR_YUNTONGXUN) {

        } elseif ($vendor == self::VENDOR_JIANZHOU) {
            $model = MsgSmsTemplate::model()->getByAttributes(array('code' => 'service.reject', 'vendor_name' => $vendor));
            $content = $model->content;
            return $this->sendSmsTemplateViaJianZhou($to, $content, $model->type);
        }
    }

    /**
     * 添加微信
     * @param type $to
     * @param type $data
     * @param type $vendor
     * @return type
     */
    public function sendSmsWeixinAdd($to, $data, $vendor = self::VENDOR_ACTIVE) {
        if ($vendor == self::VENDOR_YUNTONGXUN) {

        } elseif ($vendor == self::VENDOR_JIANZHOU) {
            $model = MsgSmsTemplate::model()->getByAttributes(array('code' => 'wx.add', 'vendor_name' => $vendor));
            $values = array($data->weixin);
            $content = str_replace(array('{weixin}'), $values, $model->content);
            return $this->sendSmsTemplateViaJianZhou($to, $content, $model->type);
        }
    }

    /**
     * 汇款方式支付宝
     * @param type $to
     * @param type $data
     * @param type $vendor
     * @return type
     */
    public function sendSmsPayAlipay($to, $data, $vendor = self::VENDOR_ACTIVE) {
        if ($vendor == self::VENDOR_YUNTONGXUN) {

        } elseif ($vendor == self::VENDOR_JIANZHOU) {
            $model = MsgSmsTemplate::model()->getByAttributes(array('code' => 'pay.alipay', 'vendor_name' => $vendor));
            $values = array($data->money);
            $content = str_replace(array('{money}'), $values, $model->content);
            return $this->sendSmsTemplateViaJianZhou($to, $content, $model->type);
        }
    }

    /**
     * 汇款方式银行汇款
     * @param type $to
     * @param type $data
     * @param type $vendor
     * @return type
     */
    public function sendSmsPayBank($to, $data, $vendor = self::VENDOR_ACTIVE) {
        if ($vendor == self::VENDOR_YUNTONGXUN) {

        } elseif ($vendor == self::VENDOR_JIANZHOU) {
            $model = MsgSmsTemplate::model()->getByAttributes(array('code' => 'pay.bank', 'vendor_name' => $vendor));
            $values = array($data->money);
            $content = str_replace(array('{money}'), $values, $model->content);
            return $this->sendSmsTemplateViaJianZhou($to, $content, $model->type);
        }
    }

}