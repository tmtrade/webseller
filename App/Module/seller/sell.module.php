<?php
/**
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/6/24 0024
 * Time: 上午 9:46
 */
class SellModule extends AppModule{

    /**
     * 引用业务模型
     */
    public $models = array(
        'contact' => 'saleContact',
        'tm' => 'trademark',
        'second' => 'secondstatus',
        'img' => 'imgurl',
    );

    /**
     * 商标字段
     */
    protected $col = array(
        'auto as `tid`', 'id as `number`',
        'trademark as `name`', '`class`', 'pid', 'reg_date',
        'valid_end', '`goods`', '`group`',
    );

    /**
     * 检测用户是否具有提交了改商标信息
     * @param $number
     * @param $userId
     * @param string $phone
     * @return bool
     */
    public function existContact($number,$userId,$phone=''){
        //组装参数
        if ( empty($number) ) return false;
        $r['eq'] = array('number'=>$number);
        if ( $userId > 0 ){
            $r['eq']['uid'] = $userId;
        }
        if ( !empty($phone) ){
            $r['eq']['phone'] = $phone;
        }
        //查询
        $count = $this->import('contact')->count($r);
        return ($count > 0) ? true : false;
    }

    /**
     * 得到商标的信息(包含图片和二级状态)
     * @param $number
     * @return array|mixed
     */
    public function getTmInfo($number){
        //得到基础商标信息
        $r['eq']    = array('id' => $number);
        $r['col']   = $this->col;
        $r['limit'] = 100;
        $data       = $this->import('tm')->find($r);
        if(empty($data)) return array();
        //得到商标状态
        $info   = current($data);
        if(empty($info) || empty($info['tid'])) return array();
        $info['second'] = $this->getSecond($info['tid']);
        //得到商标图片
        $info['imgUrl'] = $this->getImg($number);
        return $info;
    }

    /**
     * 得到商标图片
     * @param $number
     * @return string
     */
    public function getImg($number){
        $default = '/Static/images/img1.png';
        if ( intval($number) <= 0 ) return $default;
        $r['eq']    = array('trademark_id'=>$number);
        $img        = $this->import('img')->find($r);
        return empty($img) ? $default : $img['url'];
    }

    /**
     * 商标二级状态
     * @param $tid
     * @return array
     */
    public function getSecond($tid){
        if ( intval($tid) <= 0 ) return array();
        $r['eq']    = array('tid'=>$tid);
        $second     = $this->import('second')->find($r);
        if ( empty($second) ) return array();
        $list       = array();
        $Seconds    = C("SecondStatus");
        foreach (range(1, 28) as $v) {
            $key = 'status'.$v;
            if ($second[$key] == 1){
                $list[$v] = $Seconds[$v];
            }
        }
        return $list;
    }

    /**
     * 添加商标
     * @param $params
     * @param $num int 添加数量的限制
     * @return array
     */
    public function addSell($params,$num = 20){
        if(count($params['number'])>$num) return array('code'=>1,'提交数大于'.$num);
        $mobile = $params['mobile'];
        $name = $params['name'];
        foreach($params['number'] as $k=>$v){
            $tmp = array(
                'uid'           => UID,
                'number'        => $v,
                'phone'         => $mobile,
                'contact'       => $name,
                'price'         => $params['price'][$k],
                'type'          => 1,
                'source'        => 4,
            );
            $rst = $this->importBi('sale')->addSale($tmp);
            if($rst['code']!=999){
                return array('code'=>2,'msg'=>$rst['msg']);
            }
        }
        return array('code'=>0);
    }

    /**
     * 得到模糊查询的申请人列表
     * @param $person
     * @return mixed
     */
    public function getPerson($person){
        $rst = $this->importBi('search')->aa($person);
        return $rst;
    }

    public function getPersonTm(){

    }
}