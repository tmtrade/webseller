<?
/*
 * 数据分析模块
 *
 *autor     Xuni
 *date      2016-11-02
 */
class AnalysisModule extends AppModule
{
    public $models = array(
        'analy'         => 'saleAnalysis',
        'analyItems'    => 'saleAnalysisItems',
        'sale'          => 'sale',
        'class'         => 'tmClass',
    );

    /**
     * genuine月份获取页面数据
     * @param type $month
     * @return type
     */
    public function getSaleAnalysisData($month)
    {
        $analy = $this->getSaleAnalyByMonth($month);
        
        var_dump($month);exit;
        if ( empty($analy) ) return array();

        $items = $this->getSaleAnalyItems($analy['id']);
        if ( empty($analy) ) return array();

        $_items= [];
        foreach ($items as $v){
            if ( in_array($v['type'], array(2,5,6)) ){
                $_items[$v['type']][$v['data1']] = $v;
            }else{
                $_items[$v['type']][] = $v;
            }
        }
        return $_items;
    }

    /**
     * 获取选择的月份ID
     * @param type $month
     * @return type
     */
    public function getSaleAnalyByMonth($month)
    {
        $r['eq']    = array('month'=>month);
        $r['limit'] = 1;
        return $this->import('analy')->find($r);
    }

    /**
     * 获取详细信息
     * @param type $analyId
     * @return type
     */
    public function getSaleAnalyItems($analyId)
    {
        $r['eq']    = array('analyId'=>$analyId);
        $r['order'] = array('data2'=>'desc');
        $r['limit'] = 200;
        return $this->import('analyItems')->find($r);
    }

   /**
     * 获取商标分类与群组相关标题
     *
     * 获取商标分类与群组相关标题
     * 
     * @author  Xuni
     * @since   2016-03-08
     *
     * @return  array
     */
    public function getClassGroup($class=0, $group=1)
    {
        if ( $class == 0 && $group != 1 ){
            $r['eq'] = array('parent'=>0);
        }elseif ( $class != 0 && $group == 1 ){
            //$r['eq'] = array('parent'=>$class);
            $r['raw'] = " (`parent` = '$class' OR `number` = '$class') ";
        }elseif ( $class != 0 ){
            $r['eq'] = array('number'=>$class);
        }
        //$r['order'] = array('parent'=>'asc','number'=>'asc');
        $r['limit'] = 1000;

        $_class = $_group = array();
        $res    = $this->import('class')->find($r);
        if ( empty($res) ) return array();

        foreach ($res as $k => $v) {
            if ( $v['parent'] == '0' ){
                $_class[$v['number']] = empty($v['title']) ? $v['name'] : $v['title'];
            }elseif ( $v['parent'] != 0 ){
                $_group[$v['parent']][$v['number']] = empty($v['title']) ? $v['name'] : $v['title'];
            }
        }
        ksort($_class);
        return array($_class, $_group);
    }
}
?>