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
    private $class = null;
    private $isLink = 2;
    /**
     * 引用业务模型
     */
    public $models = array(
        'quotation' => 'quotation',
        'quotationItems' => 'quotationItems',
        'userImage' => 'userImage',
        'tminfo' => 'saleTminfo',
        'tm' => 'trademark',
        'class' => 'tmClass',
        'user' => 'user',
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
        $r['order'] = array('created'=>'asc');
        if ( !empty($params['name']) ){
            $r['like']['title'] = $params['name'];
        } 
        $r['order'] = array('created'=>'desc');
        $res = $this->import('quotation')->findAll($r);
        //得到报价单内商品数量
        if($res['rows']){
            foreach($res['rows'] as $k=>$v){
                $res['rows'][$k]['count'] = $this->getQuotationNumber($v['id']);
                $res['rows'][$k]['view_url'] = SELLER_URL.'p-'.$v['id'].'-'.UID.'.html';
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
    public function insertQuotation($data,$num, $quotationId=0){
        if(count($data['number'])>$num) return array('code'=>1,'msg'=>'提交数大于'.$num);
        $mobile = $data['mobile'];
        $name = $data['name'];
        if($quotationId==0){
            $tmp = array(
                'title'         => $data['title'],
                'desc'          => $data['desc'],
                'uid'           => UID,
                'phone'         => $mobile,
                'contact'       => $name,
                'qq'            => $data['qq'],
                'style'         => $data['style'],
                'avatar'        => $data['avatar'],
                'isLink'        => $data['isLink'],
            );
            $quotationId = $this->addQuotation($tmp);
            if(!$quotationId) return array('code'=>1,'msg'=>'报价单添加失败');
        }else{
            //edit
            $tmp = array(
                'title'         => $data['title'],
                'desc'          => $data['desc'],
                'phone'         => $data['mobile'],
                'contact'       => $data['name'],
                'qq'            => $data['qq'],
                'style'         => $data['style'],
                'avatar'        => $data['avatar'],
                'isLink'        => $data['isLink'],
            );
            $this->load('quotation')->editQuotation($tmp, $quotationId);
            $this->load('quotation')->delQuotationItems($quotationId);
        }
        
        foreach($data['number'] as $k=>$v){
            //图片
            $rstImg = 0;
            if(!empty($data['image'][$k])){
                $img = $this->load('quotation')->getUserImage($v);
                if(!empty($img) && $img!=$data['image'][$k]){
                    $tmp_img = array(
                        'image'         => $data['image'][$k],
                    );
                    $rstImg = $this->load('quotation')->editUserImage($tmp_img,$v);
                }else{
                    $images = array(
                        'uid'           => UID,
                        'number'        => $v,
                        'image'         => $data['image'][$k],
                    );
                    $rstImg = $this->addUserImage($images);
                }
            }
            
            //商品
            $item = array(
                'qid'           => $quotationId,
                'number'        => $v,
                'price'         => $data['price'][$k],
                'label'         =>  $data['label'][$k],
                'sort'          =>  ($k+1),
                'imgId'         =>  $rstImg,
                
            );
            $this->begin('quotationItems');
            $rst = $this->addQuotationItems($item);
            
            
            //判断有用户是否已出售过 没有就添加到sale表
            $resSell = true;
            $sale         = $this->load("sale")->getSaleInfo($v);
            if(empty($sale)){
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
            
            if($resSell && $rst){
                    $this->commit('quotationItems');
            }else{
                $this->rollBack('quotationItems');
                return array('code'=>1,'msg'=>"写入数据库失败");
            }
        }
        return array('code'=>$quotationId);
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
        $path = './Data/'.UID.'/'.$id.'.jpg';
        if(is_file($path)){
            $rst = unlink($path);
            if(!$rst) return false;
        }
        //删除报价单详情
        $this->begin('quotation');
        $rst = $this->import('quotationItems')->remove(array('eq'=>array('qid'=>$id)));
        if($rst){
            //删除报价单表
            $rst = $this->import('quotation')->remove(array('eq'=>array('uid'=>UID,'id'=>$id)));
            if($rst){
                return $this->commit('quotation');
            }
        }
        $this->rollBack('quotation');
        return false;
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
        $r['eq']['id'] = $id;
        $r['eq']['uid'] = UID;
        $r['col'] = array('title');
        $rst =  $this->import('quotation')->find($r);
        return $rst?$rst['title']:'';
    }

    /**
     * 得到指定数量的报价单
     * @param int $num
     * @return array
     */
    public function getQuotation($num = 3){
        $r['col']   = array('id','title','created');
        $r['eq'] = array('uid'=>UID);
        $r['order'] = array('created'=>'desc');
        $r['limit'] = $num;
        $rst = $this->import('quotation')->find($r);
        if($rst){
            foreach($rst as $k=>$v){
                $rst[$k]['view_url'] = SELLER_URL.'p-'.$v['id'].'-'.UID.'.html';
            }
        }
        return $rst?:array();
    }
    
     /**
     * 得到报价单的信息
     * @param $id
     * @return string
     */
    public function getQuotationInfo($id){
        $r = array();
        $r['eq']['id'] = $id;
        $r['eq']['uid'] = UID;
        $res = $this->import('quotation')->find($r);
        return $res;
    }
    
     /**
     * 得到报价单商标信息
     * @param $id
     * @return string
     */
    public function getQuotationItemByQid($qid){
        $r = array();
        $r['eq'] = array('qid'=>$qid);
        $r['limit'] = 12;
        $r['col'] = array('qid','number');
        $r['order'] = array('sort'=>'asc');
        $rst =  $this->import('quotationItems')->findAll($r);
        return $rst;
    }
    
    /**
     * 得到报价单商标信息
     * @param $id
     * @return string
     */
    public function getQuotationItemInfo($qid,$number){
        $r = array();
        $r['eq'] = array('qid'=>$qid,'number'=>$number);
        $rst =  $this->import('quotationItems')->find($r);
        return $rst;
    }
    /**
     * 得到报价单的标题
     * @param $id
     * @return string
     */
    public function getUserImage($number){
        $r = array();
        $r['eq']['number'] = $number;
        $r['eq']['uid'] = UID;
        $r['col'] = array('image');
        $rst =  $this->import('userImage')->find($r);
        return $rst?$rst['image']:'';
    }
    
    //添加子分类 edit
	public function editUserImage($data, $number)
	{
		$rc['eq'] = array('number' => $number,'uid'=>UID);
		$res      = $this->import('userImage')->modify($data, $rc);
		return $res;
	}
    //添加子分类 edit
	public function editQuotation($data, $id)
	{
		$rc['eq'] = array('id' => $id);
		$res      = $this->import('quotation')->modify($data, $rc);
		return $res;
	}

	//添加子分类 del  对应 items
	public function delQuotationItems($id)
	{
		$rc['eq'] = array('qid' => $id);
		$res      = $this->import('quotationItems')->remove($rc);
		return $res;
	}

    /**
     * 得到报价单详细信息
     * @param $id
     * @param $uid int 用户id
     * @return array
     */
    public function getDetail($id,$uid){
        $r = array();
        $r['eq']['id'] = $id;
        $r['col'] = array('desc','phone','qq','style','avatar','isLink');
        $rst = $this->import('quotation')->find($r);
        if($rst){
            //得到头像地址
            $rst['avatar_path'] = $this->getAvatar($rst['avatar'],$uid);
            //是否连接到一只蝉详情
            $this->isLink = $rst['isLink'];
            //得到报价单详细数据
            $rst['data'] = $this->handleData($id,$uid);
        }
        return $rst?:array();
    }

    /**
     * 得到报价单头像
     * @param $avatar
     * @param $uid
     * @return string
     */
    public function getAvatar($avatar,$uid){
        if($avatar==1){
            return StaticDir.'1.0/images/mr-s-head.png';//默认女
        }elseif($avatar==2){
            return StaticDir.'1.0/images/mr-m-head.png';//默认男
        }elseif($avatar==4){
            //用户头像
            return SELLER_URL.$this->import('user')->get($uid,'photo');
        }
    }

    /**
     * 得到报价单商标信息
     * @param $id
     * @param $uid
     * @return array
     */
    private function handleData($id,$uid){
        if(!$id) return array();
        //得到报价单的商标信息
        $r = array();
        $r['eq']['qid'] = $id;
        $r['limit'] = 50;
        $r['order'] = array('sort'=>'asc');
        $r['col'] = array('number','price','label');
        $rst = $this->import('quotationItems')->find($r);
        if(!$rst) return array();
        foreach($rst as $k=>$row){
            //得到图片
            $rst[$k]['img'] = $this->getImg($row['number'],$uid);
            //得到商品-分类-申请时间信息
            $tmp = $this->getTm($row['number']);
            $rst[$k] = array_merge($rst[$k],$tmp);
        }
        return $rst;
    }
    
    /**
     * 获取预览的具体数据
     * @param type $data
     * @return type
     */
    function getPreviemInfo($data){
        foreach($data['number'] as $k=>$v){
            $rst[$k]['number'] = $v;
            $rst[$k]['price'] = $data['price'][$k];
            $rst[$k]['label'] = $data['label'][$k];
            //得到图片
            if(!empty($data['image'][$k])){
                $rst[$k]['img'] = $data['image'][$k];
            }else{
                $rst[$k]['img'] = $this->getImg($v,UID);
            }
            //得到商品-分类-申请时间信息
            $tmp = $this->getTm($v);
            $rst[$k] = array_merge($rst[$k],$tmp);
        }
        return $rst;
    }

    /**
     * 得到商标的分类名数组
     * @return array|null
     */
    private function getClass(){
        if(!$this->class){
            $this->class = $this->getTmClass();
        }
        return $this->class;
    }

    /**
     * 得到商标图片
     * @param $number
     * @param $uid
     * @return string
     */
    private function getImg($number,$uid){
        //查询用户上传
        $r = array();
        $r['eq']['uid'] = $uid;
        $r['eq']['number'] = $number;
        $r['order'] = array('created'=>'desc');
        $r['col'] = array('image');
        $rst = $this->import('userImage')->find($r);
        if($rst && $rst['image']){
            return SELLER_URL.$rst['image'];
        }else{
            //查询美化商标
            $r = array();
            $r['eq']['number'] = $number;
            $r['col'] = 'embellish';
            $rst = $this->import('tminfo')->find($r);
            if($rst && $rst['embellish']){
                return  TRADE_URL.$rst['embellish'];
            }else{
                //查询商标原图
                return $this->load('trademark')->getImg($number);
            }
        }
    }

    /**
     * 得到商标的商品.申请时间.分类信息
     * @param $number
     * @return array|mixed
     */
    private function getTm($number){
        $r = array();
        $r['eq']['id'] = $number;
        $r['col'] = array('goods','apply_date','class','trademark','auto');
        $r['limit'] = 50;//多类问题
        $rst = $this->import('tm')->find($r);
        if(!$rst) return array();
        $info = current($rst);
        $class = arrayColumn($rst,'class');
        $temp = array();
        $className = $this->getClass();
        foreach($class as $v){
            $temp[$v] = $className[$v];
        }
        $info['class'] = $temp;
        //得到一只蝉详情地址
        $info['view'] = ' href="javascript:;" ';
        if($this->isLink==1){
            $_class = array_shift($class);
            $info['view'] = ' href="'.SITE_URL.'d-'.$info['auto'].'-'.$_class.'.html'.'" target="_blank"';
        }
        return $info;
    }

    /**
     * 得到商标分类
     * @return array
     */
    private function getTmClass(){
        $r['eq'] = array('parent'=>0);
        $r['limit'] = 100;
        $r['col'] = array('number','name','title');
        $res = $this->import('class')->find($r);
        if(!$res) return array();
        $tmp = array();
        foreach($res as $v){
            $tmp[$v['number']] = empty($v['title']) ? $v['name'] : $v['title'];
        }
        ksort($tmp);
        return $tmp;
    }
}
?>