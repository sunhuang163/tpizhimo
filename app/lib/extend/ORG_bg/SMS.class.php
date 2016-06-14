<?php
/***
*
* @Author  ZhangYe
* @Date    10-11-2015
* @@短信发送接口

***/
class SMS {
    const API_UT8_URL = 'http://utf8.sms.webchinese.cn/?';
    const API_GBK_URL = 'http://gbk.sms.webchinese.cn/?';
    const API_SMS_COUNT = 'http://sms.webchinese.cn/web_api/SMS/?Action=SMS_Num&';//查看股票剩余数量

    private $m_uid = false;
    private $m_key = false;
    private $m_sign = false;

    public function __construct( $uid = false, $key = false , $sign =  false )
    {
        if( $uid )
            $this->m_uid = $uid;
        if( $key )
            $this->m_key = $key;
        if( $sign )
            $this->m_sign = $sign;

        //从数据库中加载配置
        if( !$this->m_key )
        {
            $Mwebinfo = M('Webinfo');
            $rsweb = $Mwebinfo->limit(1)->find();
            $dweb = array();
            $dweb= $rsweb;
            $dweb['extdata'] =  $rsweb['extdata'] ? unserialize( $rsweb['extdata']) : NULL;
            if( $dweb && $dweb['extdata'] && isset( $dweb['extdata']['sms']) )
            {
                if( !$this->m_uid )
                    $this->m_uid = $dweb['extdata']['sms']['uid'];
                if( !$this->m_key )
                    $this->m_key = $dweb['extdata']['sms']['key'];
                if( !$this->m_sign )
                    $this->m_sign = $dweb['extdata']['sms']['sign'] ? $dweb['extdata']['sms']['sign'] :'织墨文学' ;

                $this->m_sign = '【'.$this->m_sign.'】';
            }
        }
    }

    //发送结果
    public function sent( $txt , $phones , $charset = 'utf8')
    {
        if( !$this->m_uid || !$this->m_key || !$txt  || !$phones )
            return false;
        $ret =  false;
        $sendCnt = $txt;

        if( $charset != 'utf8')
        {
            //
        }
        $params = array('Uid'=>'','smsText'=>'','smsMob'=>'','Key'=>'');
        $params['Uid'] =  $this->m_uid ;
        $params['Key'] =  $this->m_key ;
        $params['smsText'] =  $txt ;
        $params['smsMob'] =  $phones ;
        $url = self::API_UT8_URL;
        $url  .= http_build_query( $params );
        $re  = $this->http_get( $url  );
        $re .='  '.$url;
        /* 错误代码
            -1  没有该用户账户
            -2  接口密钥不正确 [查看密钥]
            不是账户登陆密码
            -21 MD5接口密钥加密不正确
            -3  短信数量不足
            -11 该用户被禁用
            -14 短信内容出现非法字符
            -4  手机号格式不正确
            -41 手机号码为空
            -42 短信内容为空
            -51 短信签名格式不正确
            接口签名格式为：【签名内容】
            -6  IP限制
            大于0 短信发送数量
        */
        if( intval( $re ) > 0  )
        {
            $ret = true;
        }
        return $ret;
    }

    private function http_get( $url )
    {
        if(function_exists('file_get_contents'))
        {
            $file_contents = file_get_contents($url);
        }
        else
        {
            $ch = curl_init();
            $timeout = 30;
            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file_contents = curl_exec($ch);
            curl_close($ch);
        }
        return $file_contents;
    }

}
