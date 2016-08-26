<?
/**
 * 商品报价单
 * 
 * 查询、创建
 *
 * @package	Module
 * @author	Far
 * @since	2016-08-25
 */
class quotationModule extends AppModule
{
	
    /**
     * 引用业务模型
     */
    public $models = array(
        'quotation'         => 'quotation',
        'quotationItems'    => 'quotationItems',
        'userImage'         => 'userImage',
    );
    
    //获取报价商品的数据
    public function getList($params, $page, $limit=20)
    {
        //得到报价单信息
        $r = array();
        $r['page']  = $page;
        $r['limit'] = $limit;
        $r['col']   = array('id','title','created');
        $r['eq'] = array('uid'=>UID);
        if ( !empty($params['name']) ){
            $r['like']['title'] = $params['name'];
        } 
        $r['order'] = array('created'=>'desc');
        $res = $this->import('quotation')->findAll($r);
        //得到报价单内商品数量
        if($res['rows']){
            foreach($res['rows'] as $k=>$v){
                $res['rows'][$k]['count'] = $this->getQuotationNumber($v['id']);
                $res['rows'][$k]['view_url'] = SITE_URL.'quotation/?id='.$v['id'];//一只蝉地址
            }
        }
        return $res;
    }

    /**
     * 删除商品单,当前登录用户
     * @param $id
     * @return bool
     */
    public function delete($id){
        //删除报价pdf文件
        unlink(StaticDir.'pdf/'.UID.'/'.$id.'.pdf');
        return $this->import('quotation')->remove(array('eq'=>array('uid'=>UID,'id'=>$id)));
    }

    /**
     * 得到报价单内商标类商标数量
     * @param $id
     * @return int
     */
    private function getQuotationNumber($id){
        $r = array();
        $r['eq'] = array('qid'=>$id);
        return $this->import('quotationItems')->count($r);
    }

    /**
     * 得到报价单的标题
     * @param $id
     * @return string
     */
    public function getTitle($id){
        $r = array();
        $r['eq'] = array('id'=>$id);
        $r['eq'] = array('uid'=>UID);
        $r['col'] = array('title');
        $rst =  $this->import('quotation')->find($r);
        return $rst?$rst['title']:'';
    }
}
?>