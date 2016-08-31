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
                $res['rows'][$k]['view_url'] = SITE_URL.'quotation/?id='.$v['id'].'&u='.UID;//一只蝉地址
            }
        }
        return $res;
    }
    
    /**
     * 添加页面提交的报价单内容
     * @param type $data
     * @param type $num 限制个数
     * @return 
     */
    public function insertQuotation($data,$num){
        $number_count = count($data['number']);
        if($number_count>$num) return array('code'=>1,'msg'=>'提交数大于'.$num);
        $mobile = $data['mobile'];
        $name = $data['name'];
        $tmp = array(
                'title'         => $data['title'],
                'desc'          => $data['desc'],
                'uid'           => UID,
                'phone'         => $mobile,
                'contact'       => $name,
                'qq'            => $data['qq'],
                'style'         => $data['style'],
                'avatar'        => $data['avatar'],
            );
        $quotationId = $this->addQuotation($tmp);
        if(!$quotationId) return array('code'=>1,'msg'=>'报价单添加失败');
        foreach($data['number'] as $k=>$v){
            $item = array(
                'qid'           => $quotationId,
                'number'        => $v,
                'price'         => $data['price'][$k],
                'label'         =>  $data['label'][$k],
                'sort'          =>  $number_count-$k,
            );
            $this->begin('quotationItems');
            $rst = $this->addQuotationItems($item);
            $rstImg = true;
            if(!empty($data['image'][$k])){
                $images = array(
                    'uid'           => UID,
                    'number'        => $v,
                    'image'         => $data['image'][$k],
                );
                $rstImg = $this->addUserImage($images);
            }
            
            //判断有用户是否已出售过 没有就添加到sale表
            $resSell = true;
            $isSale         = $this->load("sell")->existContact($v,0);
            if(!$isSale){
                //开始写入商标
                $tmps = array(
                    'number'        => $v,
                    'phone'         => $mobile,
                    'name'          => $name,
                    'price'         => $data['price'][$k],
                    'memo'          => "报价单添加商品",
                );
                $resSell = $this->load('sell')->documentAddSell($tmps);
                if($resSell['code']!=999){
                    $resSell = false;
                }
            }
            
                
            if($resSell && $rstImg && $rst){
                    $this->commit('quotationItems');
            }else{
                $this->rollBack('quotationItems');
                return array('code'=>2,'msg'=>"写入数据库失败");
            }
        }
        return array('code'=>0);
    }

        /**
     * 添加商品单数据
     * @param array $data
     */
    public function addQuotation($data){
        $res = $this->import('quotation')->create($data);
        return $res;
    }
    
    /**
     * 添加商品单商标数据
     * @param array $data
     */
    public function addQuotationItems($data){
        $res = $this->import('quotationItems')->create($data);
        return $res;
    }
    
    /**
     * 添加用户上传商品图片数据
     * @param array $data
     */
    public function addUserImage($data){
        $res = $this->import('userImage')->create($data);
        return $res;
    }


    /**
     * 删除商品单,当前登录用户
     * @param $id
     * @return bool
     */
    public function delete($id){
        //删除报价pdf文件
        $path = StaticDir.'png/'.UID.'/'.$id.'.png';
        if(is_file($path)){
            $rst = unlink($path);
            if(!$rst) return false;
        }
        //同时删除报价单相关表
        ///TODO///
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