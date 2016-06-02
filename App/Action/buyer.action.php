<?
/**
* 项目首页
*
* 首页内容展示
*
* @package	Action
* @author	void
* @since	2015-11-20
*/
class BuyerAction extends AppAction
{
    //public  $debug = true;
	public $status = array(
		1 	=> '销售中',
		2	=> '已下架',
		3	=> '审核中'
	);
	/**
	* 引用业务模型
	*/
	public $models = array(
		'verify'	=> 'verify',
        'statusnew'	=> 'statusnew',
	);

	/**
	* 我的需求列表
	* @author	martin
	* @since	2016/1/26
	*
	* @access	public
	* @return	void
	*/
	public function index()
	{
		$userInfoId			= $this->userInfo['id'];
		$param				= $this->getFormData();
		$param['page']		= $this->input('page','int');
		$param['limit']		= 30;
		$param['nottype']	= 5;
		$buyinfo			= $this->load('buyer')->getInfoAll($userInfoId, 0, $param);
        $pager				= $this->pager($buyinfo['paging']['total'], 30);
        $pageBar			= empty($buyinfo['data']) ? '' : getPageBar($pager);
		$this->set('param',$param);
		$this->set('buyinfo',$buyinfo['data']);
		$this->set('crmtype', C('CRMTYPE'));
		$this->set('crmstep', C('CRMSTEP'));
		
		$this->set('pageBar', $pageBar);
		$this->display();
	}


	/**
	* 我的需求详情
	* @author	martin
	* @since	2016/1/27
	*
	* @access	public
	* @return	void
	*/
	public function views()
	{
		$id				= $this->input('id','int');
		$userInfoId		= $this->userInfo['id'];
		$param			= $this->getFormData('buyerindex');
		$param['page']	= $this->input('page','int');
		$param['limit']	= $this->rowNum;
		$buyinfo		= $this->load('buyer')->getInfoAll($userInfoId,$id, $param);
		//$brand			= $this->load('buyer')->getInfoBuyId($userInfoId,$id);
		$this->set('buyinfo',$buyinfo['data'][0]);
		$this->set('brand',$brand);
		$this->set('crmstep', C('CRMSTEP'));
		$this->display();
	}


	/**
	* 我的求购列表
	* @author	martin
	* @since	2016/2/25
	*
	* @access	public
	* @return	void
	*/
    public function myinfo()
    {
        $userInfoId		= $this->userInfo['id'];
        $param			= $this->getFormData();
        $param['page']	= $this->input('page','int');
        $param['limit']	= 30;
        $param['pttype']= '求购';
        $buyinfo		= $this->load('buyer')->getInfoAll($userInfoId, 0, $param);
        $pager			= $this->pager($buyinfo['paging']['total'], 30);
        $pageBar		= empty($buyinfo['data']) ? '' : getPageBar($pager);
        $saleList		= $this->load('sale')->getSaleListBuyer(8);
        $this->set('param',$param);
        $this->set('buyinfo',$buyinfo['data']);
        $this->set('crmstate', C('CRMSTATE'));
        $this->set('pageBar', $pageBar);
		$this->set('saleList',$saleList);
        $this->display();
    }

	/**
	* 我的求购详情
	* @author	martin
	* @since	2016/2/25
	*
	* @access	public
	* @return	void
	*/
	public function myview()
	{
		$id				= $this->input('id','int');
		$userInfoId		= $this->userInfo['id'];
		$param			= $this->getFormData('buyermyinfo');
		$param['page']	= 1;
		$param['limit']	= 1;
		$buyinfo		= $this->load('buyer')->getInfoAll($userInfoId,$id, $param);

        $tm = $buyinfo['data'][0]['tm'];
        foreach($tm as  &$v){
            $tminfo		= $this->load('statusnew')->getInfoAll($v['trademark'],$v['class']);
            $v['tminfo'] = $tminfo;
        }

        $state = arrayColumn($buyinfo['data'][0]['step'],"state");
        $dates = arrayColumn($buyinfo['data'][0]['step'],"date");

        //print_r($dates);
		$this->set('buyinfo',$buyinfo['data'][0]);
        $this->set('state',$state);
        $this->set('dates',$dates);
        $this->set('tm',$tm);
		$this->set('brand',$brand);
		$this->display();
	}
	/**
	* 我的出售
	* @author	haydn
	* @since	2016/3/01
	*
	* @access	public
	* @return	void
	*/
	pubLic function mysell()
	{
		$pagesize	= $this->rowNum;
		$userInfoId	= $this->userInfo['id'];
		$page		= $this->input("page","int");
		$search		= $this->getFormData();
		$array		= $this->load('sale')->getSellList($userInfoId,$search,$page,$pagesize);
		$data['rows']	= $array['rows'];
		$data['total'] 	= $array['total'];
		$pager   		= $this->pager($data['total'], $pagesize);
        $pageBar 		= empty($data['rows']) ? '' : getPageBar($pager);
        $this->set('data',$data);
        $this->set('pager',$pager);
        $this->set('pageBar',$pageBar);
		//去掉已下架
		$statusNew = array(
			1 	=> '已审核',
			2	=> '未审核'
		);
        $this->set('status',$statusNew);
        $this->set('search',$search);
		$this->display();
	}
	/**
	* 我的出售详细
	* @author	hyand
	* @since	2016-03-02
	* @return	void 
	*/
	public function mysellContent()
	{
		$saleType	= array('1' => '出售','2' => '许可','3' => '出售+许可' );
		$number 	= $this->input('number','int');
		$class		= $this->input('class','string');
		$userInfoId = $this->userInfo['id'];
		$search		= array('number' => $number,'class' => $class);
		$storeNum	= $this->load('collect')->censusBrand($userInfoId,$number);//收藏数
		$lookNum	= $this->load('browse')->browseCount($number,$class);//浏览数
		$data		= $this->load('sale')->getSellList($userInfoId,$search);
		$list		= $this->load('buyer')->getMysellContent($userInfoId,$search);
		$this->set('data',$data);
		$this->set('status',$this->status);
		$this->set('storeNum',$storeNum);
		$this->set('lookNum',$lookNum);
		$this->set('saleType',$saleType);
		$this->set('list',$list);
		$this->display();
	}
	/**
	 * 已买到商标
	 * @author	martin
	 * @since	2016/3/1
	 *
	 * @access	public
	 * @return	void
	 */
	public function tradeList()
	{
		$userId			= $this->userInfo['id'];
		$param			= $this->getFormData();
		$param['page']	= $this->input('page','int');
		$param['limit']	= $this->rowNum;
		$buyinfo		= $this->load('buyer')->getClinchBrandInfo($userId,$param);
        $pager			= $this->pager($buyinfo['total'], $this->rowNum);
        $pageBar		= empty($buyinfo['rows']) ? '' : getPageBar($pager);
		$this->set('param',$param);
		$this->set('data',$buyinfo);
		$this->set('search',$param);
		$this->set('pageBar',$pageBar);
		$this->set('crmstate', C('CRMSTATE'));
		$this->display();
	}
	/**
	 * 导出我的出售
	 * @author	haydn
	 * @since	2016/3/10
	 * @access	public
	 * @return	void
	 */
	public function excel()
	{
		$search			= $this->getFormData('buyermysell');
		$userId			= $this->userInfo['id'];
		$data			= array();
		for( $i = 1; $i < 100; $i++ ){
			$array		= $this->load('sale')->getSellList($userId,$search,$i,5);
			if( count($array['rows']) > 0 ){
				$data	= array_merge($data,$array['rows']);
			}else{
				break;
			}
		}
		$http			= $this->load('sale')->exceltable($data);
		excelForm($http);
	}

    /**
     * 删除我的出售
     * @author	Alexey
     * @since	2016/4/1
     * @access	public
     * @return	void
     */
    function delMysell(){

		$data = array(
			'uid'           => $this->userInfo['id'],
			'number'        => $this->input('number','text'),
		);
        $res = $this->load('buyer')->delMysell($data);
        echo $res;

    }

	 /**
     * 修改出售价格
     * @author	martin
     * @since	2016/4/5
     * @access	public
     * @return	void
     */
    public function editPrice()
	{
		$params['price']		= $this->input("price","int");
		$params['type']			= $this->input("type","int");
		$params['saleid']		= $this->input("saleid","int");
		$data					= $this->load('salecontact')->editSalePrice($params);
		echo json_encode($data);exit;
    }

	/**
	 * 个人删除商标
	 * @author	martin
	 * @since	2016/4/5
	 * @access	public
	 * @return	void
	 */
	public function history()
	{
		
		$userId				= $this->userInfo['id'];
		$search				= $this->getFormData('buyermysell');
		$search['page']		= $this->input("page","int");
		$search['pagesize']	= $this->rowNum;
		$data				= $this->load('salehistory')->getHistoryPage($userId,$search);
		$pager   			= $this->pager($data['total'], $this->rowNum);
        $pageBar 			= empty($data['rows']) ? '' : getPageBar($pager);
        $this->set('data',$data);
        $this->set('pager',$pager);
        $this->set('pageBar',$pageBar);
        $this->set('status',$this->status);
        $this->set('search',$search);
		$this->display();

	}
    /**
     * 我的求购
     * @author    alexey
     * @since     2016/4/8
     * @access    public
     * @return    void
     */
    public function addBuy()
    {
		$userId				= $this->userInfo['id'];
        $array['name']		= $this->input("name", "text");
        $array['tel']		= $this->input("tel", "text");
        $array['remarks']	= $this->input("remarks", "text");
        $data				= $this->load('buyer')->addBuy($userId, $array);
        echo $data;exit;
    }
}
?>