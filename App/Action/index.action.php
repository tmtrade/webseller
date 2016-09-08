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
class IndexAction extends AppAction
{

	protected $onlineName = 'YzCoNlInE';
        public $ptype = 1;
	/**
	 * 控制器默认方法
	 * @author	void
	 * @since	2015-11-20
	 *
	 * @access	public
	 * @return	void
	 */
	public function index()
	{
		if(!$this->isLogin){
			$this->set('ptype',7);
			$this->display('login/login.index.html');
			exit;
		}
		//检测是否第一次登陆
		if(!$this->userinfo['isfirst']){
			$res = $this->load('login')->isFirst();//修改为非首次登陆
			if($res) $this->checkMsg();
		}
		//获取我的商品个各状态个数
		$sell = $this->com('redisHtml')->get('sell_count_'.UID);
		if(empty($sell)){
		    $sell['three'] = $this->load('goods')->getSaleCancelCount($this->userInfo['id'],3);//未通过
		    $sell['four'] = $this->load('goods')->getSaleCancelCount($this->userInfo['id'],1);//已失效
		    $this->com('redisHtml')->set('sell_count_'.UID, $sell, 60);
		}
                $sell['one'] = $this->load('goods')->getSaleCount($this->userInfo['id'],1);//出售中
                $sell['two'] = $this->load('goods')->getSaleCount($this->userInfo['id'],2);//审核中
		$this->set("sellCount",$sell);
		//得到最近一个月的收益情况
		$month_income = $this->com('redisHtml')->get('month_income_'.UID);
		if(empty($month_income)){
		    $month_income = $this->load('income')->getMonthIncome();
		    $this->com('redisHtml')->set('month_income_'.UID, $month_income, 600);
		}
		$this->set('month_income',$month_income);
		
		//得到最近的四条站内信
		$msg_list = $this->load('messege')->getSizeMsg(4);
		$this->set('msg_list',$msg_list);
		//得到最近的2条报价单
		$quotation = $this->load('quotation')->getQuotation(2);
		$this->set('quotation',$quotation);
		//得到最近热搜词
		$keyword_list = $this->com('redisHtml')->get('keyword_count');
		if(empty($keyword_list)){
		    $keyword_list = $this->load('keyword')->getKeywordRanking();
		    $this->com('redisHtml')->set('keyword_count', $keyword_list, 600);
		}
		$this->set('keyword_list',$keyword_list);
		$this->display();
	}
	
	//设置头像
	public function setAvatar()
	{
	   $msg = array(
            'code'  => 0,
            'msg'   => '上传失败',
            );
	    $image = $this->input('path', 'string');
	    $images = new Images("file");
	    $res = $images->thumb(".".$image,0,0);
	    $userInfoId	= $this->userInfo['id'];
	    $saveinfo	= array('photo' => $res['big']);
	    $isupdate	= $this->load('user')->setAvatar($userInfoId, $saveinfo);
	    if($isupdate){
		  $msg['code'] = 1;
		  $msg['msg'] = $res['big'];
	    }
	    $this->returnAjax($msg);
	}

	/**
	 * 渲染修改密码页面
	 */
	public function changePassword(){
		$this->pageTitle   = "我的资料-一只蝉出售者平台";
		$this->set('title',$this->pageTitle);
		$this->display();
	}

	public function getOnlineStatus()
	{
		$online = $this->com('redisQc')->get($this->onlineName);
		$this->returnAjax(array('code'=>1,'msg'=>$online));
	}
	
}
?>