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

    private $num = 100000;

    /**
     * 商标字段
     */
    protected $col = array(
        'auto as `tid`', 'id as `number`',
        'trademark as `name`', '`class`', 'pid', 'reg_date',
        'valid_end', '`goods`', '`group`',
    );

    /**
     * 检测用户是否具有提交了该商标信息
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
            if(!in_array($rst['code'],array(999,109,112))){ //成功(联系人已存在也认为是成功)
                return array('code'=>2,'msg'=>$rst['msg']);
            }
        }
        return array('code'=>0);
    }
    
    /**
     * 添加导入商标
     * @param $params
     * @return array
     */
    public function documentAddSell($params){
        $tmp = array(
                'uid'           => UID,
                'number'        => $params['number'],
                'phone'         => $params['phone'],
                'contact'       => $params['name'],
                'price'         => $params['price'],
                'type'          => 1,
                'source'        => 4,
		'memo'		=> $params['memo'],
            );
            $rst = $this->importBi('sale')->addSale($tmp);
            if($rst['code']!=999){
                return false;
            }
        return true;
    }


    /**
     * 得到模糊查询的申请人列表
     * @param $person
     * @return array
     */
    public function getPerson($person){
        //redis缓存接口查询数据
        $rst = $this->com('redis')->get('tm_person'.$person);
        if(!$rst){
            $data = array(
                'num'=>$this->num,
                'keyword' => $person,
            );
            $rst = $this->importBi('proposer')->search($data);
            //处理数据
            $res = array();
            foreach($rst['rows'] as $item){
                $temp = array();
                $temp['address'] = $item['address'];
                $temp['name'] = $item['name'];
                $temp['id'] = $item['id'];
                $res[] = $temp;
            }
            if($res){
                $this->com('redis')->set('tm_person'.$person,$res,7200);
            }
            return $res;
        }
        return $rst;
    }

    /**
     * 根据申请人id得到对应的商标数据
     * @param $proposerId int
     * @param $start int
     * @return array
     */
    public function getPersonTm($proposerId,$start = 0){
        //redis缓存接口查询数据
        $rst = $this->com('redis')->get('tmproposer'.$proposerId);
        if(!$rst){
            $data = array(
                'num'=>$this->num,
                'proposerId' => $proposerId,
            );
            $rst = $this->importBi('trademark')->proposerTmsearch($data);
            if($rst['rows']){
                $this->com('redis')->set('tmproposer'.$proposerId,$rst,7200);
            }
        }
        //处理数据
        $res = array();
        $res['total'] = $rst['total'];
        foreach($rst['rows'] as $k=>$item){
            if($k<$start) continue;
            if(count($res)>=51){ //只取50条数据---count除去
                $res['now'] = $k;//保存下次改取的位置
                break;
            }
            if($item['status']=='已注册' && !($this->existContact($item['code'],UID))){ //检测商标状态及该商标是否在出售中
                $temp = array();
                $temp['number'] = $item['code'];
                $temp['name'] = $item['name'];
//                $temp['imgUrl'] = $item['imageUrl'];//暂时不可用
                $temp['imgUrl'] = $this->getImg($item['code']);
                $res[] = $temp;
            }
        }
        return $res;
    }

}