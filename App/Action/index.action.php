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
		$sell['one'] = $this->load('goods')->getSaleCount($this->userInfo['id'],1);//出售中
		$sell['two'] = $this->load('goods')->getSaleCount($this->userInfo['id'],2);//审核中
		$sell['three'] = $this->load('goods')->getSaleCancelCount($this->userInfo['id'],3);//未通过
		$sell['four'] = $this->load('goods')->getSaleCancelCount($this->userInfo['id'],1);//已失效
		$this->set("sellCount",$sell);
		//得到最近一个月的收益情况
		$month_income = $this->load('income')->getMonthIncome();
		$this->set('month_income',$month_income);
		//得到最近的四条站内信
		$msg_list = $this->load('messege')->getSizeMsg(4);
		$this->set('msg_list',$msg_list);
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
}
?>