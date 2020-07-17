<?php
namespace Qttyeah\Feiyun;

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
     * 请求数据
     * @var array
     */
    public $msgInfo = [];

    /**
     * 设置公共函数
     * Feieyun constructor.
     * @param $config
     */
    public function __construct($config)
    {
        header("Content-type: text/html; charset=utf-8");
        $this->_user = $config['user'];
        $this->_ukey = $config['ukey'];
        $time = time();
        $this->msgInfo = [
            'user' => $this->_user,
            'stime' => $time,
            'sig' => $this->signature($time)
        ];

//        $this->printerAddlist();
    }

    /**
     * 批量添加打印机
     * @param $printers
     * @return string
     */
    public function printerAddlists($printers)
    {
        $printerContent = "";
        foreach ($printers as $value) {
            if (!empty($printerContent)) {
                $printerContent = "\n";
            }
            $printerContent .= implode('#', $value);
        }
        $this->msgInfo['apiname'] = 'Open_printerAddlist';
        $this->msgInfo['printerContent'] = $printerContent;
        return $this->getResult();
    }

    /**
     * 添加一个打印机
     * @param $printers
     * @return string
     */
    public function printerAddlist($printers)
    {
        $printerContent = implode('#', $printers);
        $this->msgInfo['apiname'] = 'Open_printerAddlist';
        $this->msgInfo['printerContent'] = $printerContent;
        return $this->getResult();
    }

    /**
     * 打印订单小票
     * @param int $sn 打印机编号id
     * @param array $content 打印内容  title:标题 table：标题 con :数组 内容；remark:数组 备注；total:合计金额；
     *                                  address：地址 tel:联系电话；times:订餐时间 code:扫码地址
     * @param int $times 打印联数
     * @return string
     */
    function printMsg($content = [], $sn = 1, $times = 1)
    {
        $this->msgInfo['apiname'] = 'Open_printMsg';
        $this->msgInfo['sn'] = $sn;
        $this->msgInfo['content'] = $content;
        $this->msgInfo['times'] = $times;

        return $this->getResult();
    }

    /**
     * 打印标签
     * @param int $sn
     * @param array $content
     * @param int $times
     * @return string
     */
    function printLabelMsg($content = [], $sn = 1, $times = 1)
    {
        $this->msgInfo['apiname'] = 'Open_printLabelMsg';
        $this->msgInfo['sn'] = $sn;
        $this->msgInfo['content'] = $content;
        $this->msgInfo['times'] = $times;
        return $this->getResult();
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
     * @return string
     */
    private function getResult()
    {
        $client = new Client($this->url, $this->port);
        if (!$client->post($this->path, $this->msgInfo)) {
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
        //公共参数，请求公钥
        return sha1($this->_user . $this->_ukey . $time);
    }


}
