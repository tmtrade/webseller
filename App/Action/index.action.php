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
		      $this->redirect('', '/login/index/');
		}
		
		//获取我的商品个各状态个数
		$sell = $this->com('redisHtml')->get('sell_count');
		if(empty($sell)){
		    $sell['one'] = $this->load('goods')->getSaleCount($this->userInfo['id'],1);//出售中
		    $sell['two'] = $this->load('goods')->getSaleCount($this->userInfo['id'],2);//审核中
		    $sell['three'] = $this->load('goods')->getSaleCancelCount($this->userInfo['id'],3);//未通过
		    $sell['four'] = $this->load('goods')->getSaleCancelCount($this->userInfo['id'],1);//已失效
		    $this->com('redisHtml')->set('sell_count', $sell, 600);
		}
		$this->set("sellCount",$sell);
		//得到最近一个月的收益情况
		$month_income = $this->load('income')->getMonthIncome();
		$this->set('month_income',$month_income);
		//得到最近的四条站内信
		$msg_list = $this->load('messege')->getSizeMsg(4);
		$this->set('msg_list',$msg_list);
		
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
	   
	    $image = $this->input('path', 'string');
	    $images = new Images("file");
	    $res = $images->thumb(".".$image,false,1);
	    $userInfoId	= $this->userInfo['id'];
	    $saveinfo	= array('photo' => $res['big']);
	    $isupdate	= $this->load('user')->setAvatar($userInfoId, $saveinfo);
	    if($isupdate){
		 $this->redirect('', '/index');
	    }
	}

	/**
	 * 渲染修改密码页面
	 */
	public function changePassword(){
		$this->display();
	}
}
?>