<?php
/**
 * 我的收益业务模块
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/6/21 0021
 * Time: 下午 14:23
 */
class IncomeModule extends AppModule{

    public static $token = '';//万象云图片的token

    public $models = array(
        'income' => 'income',
        'img'=> 'imgurl',
        'tminfo' => 'saleTminfo',
        'ptinfo' => 'patentInfo',
        'ptlist' => 'patentList',
    );

    /**
     * 得到我的收益分页数据
     * @param $type int 商品类型 0混合,1商标,2专利
     * @param $start int 时间筛选的开始
     * @param $end int 时间筛选的结束
     * @param $page int 页码
     * @param $size int 分页数据大小
     * @return array
     */
    public function getPageList($type,$start,$end,$page,$size){
        $r = array();
        //处理时间问题
        if($start && !$end){
            $end = time();
        }
        if($end && !$start){
            $start = 1000;
        }
        if($start && $end){
            //保证end大于start
            if($start > $end){
                $start = $start ^ $end;
                $end = $start ^ $end;
                $start = $start ^ $end;
            }
            $start += 28800;//加8小时 时区问题
            $end += 115200;//加32小时 定位到当天24点
            $r['scope'] = array('date'=>array($start,$end));
        }
        //其他条件
        if($type!=2){
            $r['eq']['type'] = $type;
        }
        $r['eq']['uid'] = UID;//指定用户
        $r['page']  = $page;
        $r['limit'] = $size;
        $r['order'] = array('date'=>'desc');
        $rst = $this->import('income')->findAll($r);
        if($rst['total']==0){
            return $rst;
        }else{
            //得到商品对应的图片
            foreach($rst['rows'] as &$v){
                $v['img'] = $this->getGoodsImg($v['number'],$v['type']);
            }
            unset($v);
            return $rst;
        }
    }

    /**
     * 得到对应商品类型的收益总额及商品出售数
     * @param $type int 商品类型 0混合,1商标,2专利
     * @param $start int
     * @param $end int
     * @return int
     */
    public function getIncome($type,$start,$end){
        $r = array();
        //处理时间问题
        if($start && !$end){
            $end = time();
        }
        if($end && !$start){
            $start = 1000;
        }
        if($start && $end){
            //保证end大于start
            if($start > $end){
                $start = $start ^ $end;
                $end = $start ^ $end;
                $start = $start ^ $end;
            }
            $start += 28800;//加8小时 时区问题
            $end += 115200;//加32小时 定位到当天24点
            $r['scope'] = array('date'=>array($start,$end));
        }
        //其他条件
        if($type!=2){
            $r['eq']['type'] = $type;
        }
        $r['eq']['uid'] = UID;//指定用户
        $r['limit'] = 100000;
        $r['col'] = array('price');
        $rst = $this->import('income')->find($r);
        if($rst){
            $rst = arrayColumn($rst,'price');
            return array('count'=>count($rst),'price'=>array_sum($rst));
        }
        return  array('count'=>0,'price'=>0);
    }

    /**
     * 得到类变量--图片token(所有图片可以共用token)
     * @return mixed|string
     */
    private function getToken(){
        //初始化类变量
        if(self::$token==''){
            self::$token = $this->requests('http://wanxiang.chaofan.wang/?t=accessToken','GET',array(),false);
        }
        return self::$token;
    }

    /**
     * 根据商品的id得到商品的图片
     * @param $number
     * @param int $type 0为商标, 1为专利
     * @return string
     */
    public function getGoodsImg($number,$type=0){
        $default = '/Static/1.0/images/img1.png';
        if ( !$number ) return $default;
        if($type==0){ //商标
            //从原始表获得数据
            $r = array();
            $r['col']   = array('url');
            $r['eq']    = array('trademark_id'=>$number);
            $rst        = $this->import('img')->find($r);
            $url = empty($rst)?$default:$rst['url'];
        }else{ //专利
            //从万象云获得图片信息
            $rst = $this->getWXYImg($number);
            $url = empty($rst)?$default:$rst;
        }
        return $url;
    }

    /**
     * 从万象云得到原始专利图片
     * @param $number
     * @return string
     */
    private function getWXYImg($number){
        //从list表中获取数据
        $eq = (strpos($number, '.') !== false) ? array('number'=>$number) : array('code'=>$number);
        $r['eq']    = $eq;
        $r['limit'] = 1;
        $info = $this->import('ptlist')->find($r);
        if ( !empty($info['data']) ){
            $data = unserialize($info['data']);
        }else{
            //从万象云获得数据并保存在list表中
            $code   = (strpos($number, '.') !== false) ? strstr($number, '.', true) : $number;
            $url    = 'http://wanxiang.chaofan.wang/detail.php?id=%s&t=json';
            $url    = sprintf($url, $code);
            $data   = $this->requests($url);
            //保存到list表中
            if ( !empty($data) && !empty($data['id']) ){
                $_data = array(
                    'code'      => $code,
                    'number'    => $number,
                    'data'      => serialize($data),
                );
                $this->import('ptlist')->create($_data);
            }
        }
        //得到图片信息
        if(empty($data['figures'][0])){
            return '';
        }else{
            $token = $this->getToken();
            return 'https://user.wanxiangyun.net/client/figure/'.$data['figures'][0].'?access_token='.$token;
        }
    }

    /**
     * curl获得数据
     * @param $url
     * @param string $type
     * @param array $params
     * @param boolean $flag 默认反序列化得到数组, false时不做处理
     * @return mixed
     */
    private function requests( $url, $type='GET', $params=array(),$flag=true )
    {
        $_type = ($type == 'POST') ? 'POST' : 'GET';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $_type);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt(
            $ch, CURLOPT_POSTFIELDS, $params
        );
        $result = curl_exec($ch);
        if($result === false) {
            $result = curl_error($ch);
        }
        curl_close($ch);
        if($flag){
            $result =  json_decode(trim($result,chr(239).chr(187).chr(191)),true);
        }
        return $result;
    }
}