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