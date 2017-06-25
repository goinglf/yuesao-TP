<?php
include_once 'CCPRestSDK.php';

class SendSMS
{
    //主帐号
    private $accountSid='8a216da85c62c9ad015c86eacf120e28';

    //主帐号Token
    private $accountToken='caac86ee77c24359bb89cedc9c1aa42a';

    //应用Id
    private $appId='8a216da85c62c9ad015c86ead06d0e2e';

    //请求地址，格式如下，不需要写https://
    private $serverIP='sandboxapp.cloopen.com';

    //请求端口
    private $serverPort='8883';

    //REST版本号
    private $softVersion='2013-12-26';

    /**
    * 发送模板短信
    * @param to 手机号码集合,用英文逗号分开
    * @param datas 内容数据 格式为数组 例如：array('Marry','Alon')，如不需替换请填 null
    * @param $tempId 模板Id
    */
    public function sendTemplateSMS($to,$datas,$tempId)
    {

        // 初始化REST SDK
        $rest = new CCPRestSDK($this->serverIP,$this->serverPort,$this->softVersion);

        $rest->setAccount($this->accountSid,$this->accountToken);
        $rest->setAppId($this->appId);

        // 发送模板短信
//          echo "Sending TemplateSMS to $to <br/>";

        $result = $rest->sendTemplateSMS($to,$datas,$tempId);
        if($result == NULL ) {
            m3_result(3,'result error!');
        }
        if($result->statusCode != 0) {
            m3_result($result->statusCode,$result->statusMsg);

        }else{
            $data['status'] = 0;
            $data['message'] = '发送成功';
            return $data;
        }
    }
}

//sendTemplateSMS("18576437523", array(1234, 5), 1);
