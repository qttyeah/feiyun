<?php

class Feieyun
{

    private $_user = 'your user';

    private $_ukey = 'your ukey';

    private $url = 'api.feieyun.cn';

    private $port = 80;

    private $path = '/Api/Open/';

    /**
     * 打印机1
     * @var array
     */
    public $_printer = [
        '1' => [
            'sn' => 'sn',
            'key' => 'key',
            'remark' => 'remark',
            'phone' => '17788888888',
        ]
    ];

    /**
     * 设置公共函数
     * Feieyun constructor.
     */
    public function __construct()
    {
        header("Content-type: text/html; charset=utf-8");
		echo 133;die;
//        $this->printerAddlist();
    }

    /**
     * 批量添加打印机
     */
    public function printerAddlist()
    {
        $printerContent = "";
        foreach ($this->_printer as $value) {
            if (!empty($printerContent)) {
                $printerContent = "\n";
            }
            $printerContent .= implode('#', $value);
        }
        $time = time();         //请求时间
        $msgInfo = [
            'user' => $this->_user,
            'stime' => $time,
            'sig' => $this->signature($time),
            'apiname' => 'Open_printerAddlist',
            'printerContent' => $printerContent
        ];
        return $this->getResult($msgInfo);
    }

    /**
     * 打印订单小票
     * @param int $sn 打印机编号id
     * @param array $params 打印内容  title:标题 table：标题 con :数组 内容；remark:数组 备注；total:合计金额；
     *                                  address：地址 tel:联系电话；times:订餐时间 code:扫码地址
     * @param int $times 打印联数
     * @return string
     */
    function printMsg($params = [], $sn = 1, $times = 1)
    {
        if (empty($params)) $this->returnErr();
        $content = "";
        if (empty($params['title'])) $this->returnErr();
        $content .= "<CB>" . $params['title'] . "</CB><BR>";
        if (empty($params['table']) || empty($params['con'])) $this->returnErr();
        $content .= $params['table'] . "<BR>";
        $content .= "--------------------------------<BR>";
        foreach ($params['con'] as $v) {
            $content .= $v . "<BR>";
        }
        $content .= "--------------------------------<BR>";
        if (!empty($params['remark'])) {
            foreach ($params['remark'] as $v) {
                $content .= "备注：" . $v . "<BR>";
            }
        }
        if (!empty($params['total'])) {
            $content .= "合计：" . $params['total'] . "<BR>";
        }
        if (!empty($params['address'])) {
            $content .= "地址：" . $params['address'] . "<BR>";
        }
        if (!empty($params['tel'])) {
            $content .= "联系电话：" . $params['tel'] . "<BR>";
        }
        if (!empty($params['times'])) {
            $content .= "营业电话：" . $params['times'] . "<BR>";
        }
        if (!empty($params['code'])) {
            //二维码
            $content .= "<QR" . $params['code'] . "</QR>";
        }

        $time = time();         //请求时间
        $msgInfo = array(
            'user' => $this->_user,
            'stime' => $time,
            'sig' => $this->signature($time),
            'apiname' => 'Open_printMsg',
            'sn' => $this->_printer[$sn]['sn'],
            'content' => $content,
            'times' => $times
        );
        return $this->getResult($msgInfo);
    }

    /**
     * 打印标签
     * @param int $sn
     * @param array $params num:序号 number：桌号 con:名称 name:备注人 phone:联系方式
     * @param int $times
     * @return string
     */
    function printLabelMsg($params = [], $sn = 1, $times = 1)
    {
        if (empty($params) || !isset($params['num'])) return $this->returnErr();
        if (!isset($params['number']) || !isset($params['con'])) return $this->returnErr();

        //设定打印时出纸和打印字体的方向，n 0 或 1，每次设备重启后都会初始化为 0 值设置，1：正向出纸，0：反向出纸，
        $content = "<DIRECTION>1</DIRECTION>";

        $content .= "<TEXT x='9' y='10' font='12' w='1' h='2' r='0'>" . $params['num'] . "       " . $params['number'] . "</TEXT>";
        $content .= "<TEXT x='80' y='80' font='12' w='2' h='2' r='0'>" . $params['con'] . " </TEXT>";
        $content .= "<TEXT x='9' y='180' font='12' w='1' h='1' r='0'>" . $params['name'] . "       " . $params['phone'] . "</TEXT>";


        $time = time();
        $msgInfo = array(
            'user' => $this->_user,
            'stime' => $time,
            'sig' => $this->signature($time),
            'apiname' => 'Open_printLabelMsg',
            'sn' => $this->_printer[$sn]['sn'],
            'content' => $content,
            'times' => $times
        );
        return $this->getResult($msgInfo);
    }

    /**
     * 错误返回
     * @param string $msg
     * @return array
     */
    public function returnErr($msg = '参数错误')
    {
        return ['code' => 0, 'msg' => $msg];
    }


    /**
     * 获取结果
     * @param $msgInfo
     * @return string
     */
    private function getResult($msgInfo)
    {
        $client = new HttpClient($this->url, $this->port);
        if (!$client->post($this->path, $msgInfo)) {
            return ['code' => 0, 'msg' => $client->getError()];
        } else {
            return ['code' => 1, 'msg' => $client->getContent()];
        }
    }


    /**
     * [signature 生成签名]
     * @param  [string] $time [当前UNIX时间戳，10位，精确到秒]
     * @return string       [接口返回值]
     */
    private function signature($time)
    {
        return sha1($this->_user . $this->_ukey . $time);//公共参数，请求公钥
    }

}
