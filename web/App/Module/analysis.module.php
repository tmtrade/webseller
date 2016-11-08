<?
/**
 * 数据分享
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/11/7 0021
 * Time: 下午 14:10
 */
class AnalysisModule extends AppModule
{
    public $models = array(
        'analy'         => 'saleAnalysis',
        'analyItems'    => 'saleAnalysisItems',
        'class'         => 'tmClass',
    );

    /**
     * 得到最新的分享报告
     */
    public function getLast(){
        $r = array(
            'order'=>array('month'=>'desc'),
        );
        return $this->getDetail($r);
    }

    /**
     * 根据id得到分享信息
     * @param $id
     * @return array|bool
     */
    public function getDate($id){
        if($id){
            $r = array(
                'eq'=>array('id'=>$id),
            );
            return $this->getDetail($r);
        }else{ //无id取最新数据
            return $this->getLast();
        }
    }

    /**
     * 根据查询条件,得到分享数据详情
     * @param $r array 查询条件
     * @return array|bool
     */
    private function getDetail($r){
        $analy = $this->import('analy')->find($r);
        if($analy){
            //解析中文年月
            $analy['month_cn'] = preg_replace('/(\d{4})/','$1年',$analy['month']).'月';
            //得到具体项目
            $r = array(
                'eq'=>array('analyId'=>$analy['id']),
                'order'=>array('data2'=>'desc'),
                'limit'=>1000,
                'col'=>array('type','data1','data2'),
            );
            $items = $this->import('analyItems')->find($r);
            //处理结果
            $analy['item'] = array();
            foreach($items as $item){
                switch($item['type']){
                    case '1':
                        $analy['item'][1][] = $item;break;
                    case '2':
                        $analy['item'][2][] = $item;break;
                    case '3':
                        $analy['item'][3][] = $item;break;
                    case '4':
                        $analy['item'][4][] = $item;break;
                    case '5':
                        $analy['item'][5][] = $item;break;
                    case '6':
                        $analy['item'][6][] = $item;break;
                }
            }
        }
        return $analy;
    }

    /**
     * 得到分类名
     * @return array
     */
    public function getClassName()
    {
        $r['eq'] = array('parent'=>0);
        $r['limit'] = 1000;
        $res    = $this->import('class')->find($r);
        if ( empty($res) ) return array();
        $_class = array();
        foreach ($res as $k => $v) {
            $_class[$v['number']] = empty($v['title']) ? $v['name'] : $v['title'];
        }
        ksort($_class);
        return $_class;
    }

    /**
     * 得到的月份选择下拉框
     * @return array
     */
    public function select(){
        $r['col'] = array('id','month');
        $r['limit'] = 10000;
        $r['order'] = array('month'=>'desc');
        $data = $this->import('analy')->find($r);
        //处理数据
        if(!$data) return array();
        foreach($data as &$item){
            //解析中文年月
            $item['month_cn'] = preg_replace('/(\d{4})/','$1年',$item['month']).'月';
        }
        unset($item);
        return $data;
    }

}
?>