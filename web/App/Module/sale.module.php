<?php
/**
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/3/3 0003
 * Time: 下午 15:55
 */
class SaleModule extends AppModule{
    public $models = array(
        'saleTminfo'	=> 'SaleTminfo',
        'sale'		=> 'Sale',
	'contact'	=> 'saleContact',
    );

    /**
     * 根据商标得到tminfo
     * @author dower
     * @param $number
     * @param string $field
     * @return array|bool
     */
    public function getSaleTmByNumber($number){
        $r['eq'] = array('number'=>$number);
        $r['col']   = array('embellish');
        $info = $this->import('saleTminfo')->find($r);
        if($info==false){
            return false;
        }
        //返回包装数据
        if($info['embellish']){
             return true;
        }else{
             return false;
        }
    }

    /**
     * 根据商标号得到商标的包装信息(无美化图,获得商标图)
     * @param $number
     * @return array|bool
     * @throws SpringException
     */
    public function getSaltTminfoByNumber($number){
        $r['eq'] = array('number'=>$number);
        $info = $this->import('saleTminfo')->find($r);
        //无销售数据
        if($info==false){
            $info = array();
            $info['alt1'] = '';
            $info['embellish'] = $this->load('trademark')->getImg($number);
            return $info;
        }
        //返回包装数据
        if($info['embellish']){
            $info['embellish'] = TRADE_URL.$info['embellish'];
        }else{
            $info['embellish'] = $this->load('trademark')->getImg($number);
        }

        return $info;
    }

    /**
     * 判断商标是否销售中
     * @author dower
     * @param $number
     * @return bool
     */
    public function isSale($number){
        $r['eq'] = array('number'=>$number);
        $r['col'] = array('status');
        $result = $this->import('sale')->find($r);
        if($result && $result['status']==1){
            return true;
        }
        return false;
    }
    
    //获取商品信息
    public function getSaleInfo($number)
    {
        $arr['eq'] = array(
            'number' => $number,
            );
        $info = $this->import('sale')->find($arr);
        if ( empty($info) ) return array();
        return $info;
    }
    
    //获取商品number和UId获取商标下的联系人信息
    public function getSaleContactByUid($number, $uid)
    {
		$r['eq'] = array(
			'number' => $number,
			'uid' => $uid,
			);
		$r['limit'] = 1;
        return $this->import('contact')->find($r);
    }
}