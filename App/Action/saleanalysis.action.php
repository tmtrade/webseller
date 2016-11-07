<?
/**
 * 数据分享
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/11/07 0021
 * Time: 下午 14:10
 */
class SaleAnalysisAction extends AppAction
{
    /**
     * 数据分享页
     */
    public function index()
    {
        $id = $this->input('id', 'int');
        //得到分享详情
        $data = $this->load('analysis')->getDate($id);
        $this->set('data',$data);
        //得到分享下拉选择
        $select = $this->load('analysis')->select();
        //得到分类信息
        $class  = $this->load('analysis')->getClassName();
        $this->set('data', $data);
        $this->set('select', $select);
        $this->set('class', $class);//分类
        $this->set('_NUMBER', C('SBNUMBER'));//商标字数
        $this->set('_TYPE', C('TYPES'));//组合类型
        $this->display();
    }

    /**
     * 下载报告
     * @param $id
     */
    public function down($id){

    }
}