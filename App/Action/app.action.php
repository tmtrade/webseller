<?
/**
* 应用控制器基类
*
* 存放控制器公共方法
*
* @package	Action
* @author	void
* @since	2015-11-20
*/
abstract class AppAction extends Action
{
	/**
	* 每页显示多少行
	*/
	public $rowNum      = 15;

	public $userInfo    = array();

	public $isLogin     = false;

	public $token;

	/**
	* 前置操作(框架自动调用)
	* @author	void
	* @since	2015-11-20
	*
	* @access	public
	* @return	void
	*/
	public function before()
	{
		//不验证登录的url
		$mods = array(
			'login'			=> '*',
		);
		//验证用户是否登录
		$allow  = false;
		$mod	= $this->mod;
		if ( isset($mods[$mod]) ) {
			if ( is_array($mods[$mod]) ) {
				$allow = in_array($this->action, $mods[$mod]) ? true : false;
			} else {
				$allow = $mods[$mod] == '*' ? true : false;
			}
		}
		$isLogin = $this->setLoginUser();
		if ( !$allow && !$isLogin) {
			$this->redirect('', '/login/index');
			exit;
		}
		//得到站内信的数量
		$msg_num = $this->load('messege')->getMsgNum();
		$this->set('msg_num',$msg_num);
		//客服qq
		$this->set('qq_num',C('qq_num'));
		//静态文件版本号>>控制js,css缓存
		$this->set('static_version', 9980);
		$this->set('current_url', '/'.$this->mod .'/' . $this->action.'/');
	}

	/**
	* 后置操作(框架自动调用)
	* @author	void
	* @since	2015-11-20
	*
	* @access	public
	* @return	void
	*/
	public function after()
	{
		//自定义业务逻辑
	}

	/**
	* 输出json数据
	*
	* @author	Xuni
	* @since	2015-11-06
	*
	* @access	public
	* @return	void
	*/
	protected function returnAjax($data=array())
	{
		$jsonStr = json_encode($data);
		exit($jsonStr);
	}

	/**
	* 设置用户信息数据
	*
	* @author  haydn
	* @since   2016-01-20
	* @access  public
	* @return  void
	*/
	protected final function setLoginUser()
	{
		$uKey 			= C('PUBLIC_USER');
		$this->token 	= LoginAuth::get($uKey);
		if(!empty($this->token)) {
			$session = $this->import('sessions', 2)->getByToken($this->token);
			if( isset($session['userId']) && $session['cookieId'] == $this->token ) {
				$userInfo   = $this->import('user', 2)->get($session['userId']);
				$mbinfo     = array(
					'id'        => $session['userId'],
					'mobile'    => $userInfo['mobile'],
					'email'    	=> $userInfo['email'],
					'cfwId'     => $userInfo['cfwId'],
					'cateId'	=> $session['type']
				);
				$this->userInfo = $mbinfo;
				$this->isLogin  = true;
				$this->setUserView();
				if(!defined('UID')) define('UID',$session['userId']);//定义用户id常量UID
				//取回公共发信的站内信
				$this->load('messege')->createSelfMsg($session['userId']);
				return true;
			}
		}
		return false;
	}

	/**
	* 设置用户信息数据到页面
	* @author  haydn
	* @since   2016-01-20
	* @access  public
	* @return  void
	*/
	protected final function setUserView()
	{
		$this->userinfo					= $this->load('user')->getInfoById( $this->userInfo['id'] );
		$this->userInfo['specname'] 	= isset($this->userinfo['specname'])?$this->userinfo['specname']:'';
		$this->userInfo['mobile_hide'] 	=  isset($this->userinfo['mobile_hide'])?$this->userinfo['mobile_hide']:'';
		$this->userInfo['email_hide'] 	= isset($this->userinfo['email_hide'])?$this->userinfo['email_hide']:'';
		$this->set('nickname', $this->userinfo['firstname']);//名称
		$this->set('userMobile', $this->userInfo['mobile_hide']);//手机
		$this->set('userEmail', $this->userInfo['email_hide']);//邮箱
		$this->set('photo', $this->userinfo['photo']);//头像
		$this->set('userInfo', $this->userinfo);//用户数组
		$this->set('cfwId', $this->userInfo['cfwId']);//超凡网id
		$this->set('isLogin', $this->isLogin);//是否登录
//		$this->myStaff();
	}

	/**
	* 删除用户信息数据
	* @author  haydn
	* @since   2016-01-20
	* @access  public
	* @return  void
	*/
	protected final function removeUser()
	{
		$this->nickname     = '';
		$this->userMobile   = '';
		$this->cfwId        = '';
		$this->userInfo     = '';
		$this->isLogin      = false;
		$this->setUserView();
	}

	/**
	* 验证js跨域
	* @author   haydn
	* @since    2016-01-27
	* @return   bool		验证来源的合法性（true:合法 false:非法）
	*/
	protected final function checkSource()
	{
		$this->recordLook();
		$is			= false;
		$timestamp	= $_GET['timestamp'];
		if( time() - $timestamp < 1000 ){
			$nonceStr	= $_GET['nonceStr'];
			$signature	= $_GET['signature'];
			$surl		= $_GET['surl'];
			$referer 	= $_SERVER["HTTP_REFERER"];
			$jsapiToken = 'chaofnwang';
			$key		= sha1("jsapi_ticket={$jsapiToken}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$referer}");
			if( $key == $signature ){
				$is = true;
			}
		}
		return $is;
	}

	/**
	 * 检测当前url地址(操作)是否发送站内信
	 * @param $uid int|string 站内信的发送对象(群发以逗号隔开)
	 * @param $sendtype int 站内信的发送方式,默认对一,2对多,3全体
	 */
	protected function checkMsg($uid = null,$sendtype=1){
		//设置发送的对象
		if(!$uid){
			$uid = UID;
		}
		//设置发送的类型
		if(!in_array($sendtype,array(1,2,3))){
			$sendtype = 1;
		}
		//得到当前url地址
		$url = 'http://'.$_SERVER['HTTP_HOST'].'/'.$this->mod.'/'.$this->action;
		//得到监控触发的信息
		$monitor = $this->load('messege')->getMonitor();
		if($monitor){
			//判断当前url是否发送信息
			foreach($monitor as $item){
				if(strpos($item['url'],$url)!==false){
					$params = array();
					$params['title'] = $item['title'];
					$params['type'] = $item['type'];
					$params['sendtype'] = $sendtype;
					$params['content'] = $item['content'];
					$params['uids'] = $uid;//当前用户
					$this->load('messege')->createMsg($params);
					break;
				}
			}
		}
	}
	
    //图片上传
    public function ajaxUploadPic()
    {
    	$kb = $this->input('size', 'int', 0);
        $msg = array(
            'code'  => 0,
            'msg'   => '',
            'img'   => '',
            );
        if ( empty($_FILES) || empty($_FILES['fileName']) ) {
            $msg['msg'] = '请上传图片';
            $this->returnAjax($msg);
        }
        if ( $kb > 0 && ($kb*1024 < $_FILES['fileName']['size']) ){
        	$msg['msg'] = "文件大小超过 $kb KB限制";
        	$this->returnAjax($msg);
        }
        $obj = $this->load('upload')->upload('fileName', 'img');
        if ( $obj->_imgUrl_ ){
            $msg['code']    = 1;
            $msg['img']     = $obj->_imgUrl_;
        }else{
            $msg['msg']     = $obj->msg;
        }
        $this->returnAjax($msg);
    }
}
?>