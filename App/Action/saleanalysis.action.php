<?
header("Content-type: text/html; charset=utf-8");

/**
 * 广告管理
 *
 * @package    Action
 * @author     Far
 * @since      2016年6月7日10:29:46
 */
class SaleAnalysisAction extends AppAction
{
    private $_num = 10;
    /**
     * 出售数据分析
     */
    public function index()
    {
        $id = $this->input('id', 'int', 201610);
        if ( $id <= 0 ) {
            exit('参数错误');
        }

        $analy  = $this->load('analysis')->getSaleAnalysisData($id);
        $CLASS  = $this->load('analysis')->getClassGroup(0,0);
        var_dump($analy);exit;
        $this->set('info', $analy);
        $this->set('CLASS', $CLASS[0]);//分类
        $this->set('_NUMBER', C('SBNUMBER'));//商标字数
        $this->set('_TYPE', C('TYPES'));//组合类型
        $this->display();
    }

       
}

?>