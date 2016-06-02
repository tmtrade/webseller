<?
/**
* 项目登录页面
*
*
* @package	Action
* @author	void
* @since	2016-01-20
*/
class LoginAction extends AppAction
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
		/*  先不处理，等所有页面套完了。
		*   登录页还没有完全套完，存在bug
		if( $this->isLogin == true ){
			$this->redirect('', '/user/main/');
		}
		$this->display();
		*/

		$this->display('index/index.index.html');

	}
	/**
	* 用户登录
	* @author   hyand
	* @since    2016-01-20
	* @return   json    返回检查结果数据包
	*/
	public function login()
	{
		$account    = $this->input('account', 'string', '');
		$cateId    	= $this->input('cateId', 'string', '');
		$password   = $this->input('password', 'string', '');

		$callback   = $this->input('callback', 'string', '');//跨域使用
		$expire   	= $this->input('expire', 'string', '');
		if ( empty($account) || !in_array(isCheck($account),array(1,2))  ){
			$code   = 2;
		}else{
			$this->verifyAjax($callback);//跨域验证
			$expire	= !empty($expire) ? $expire : 0;
			$code   = $this->load('login')->login($account,$password,$cateId,$expire);
		}
		$msg		= $this->load('login')->getError($code);
		$result     = array('code' => $code,'msg' => $msg);
		$this->setJson($result,$callback);
	}
	/**
	* 验证用户是否注册
	* @author   hyand
	* @since    2016-01-20
	* @return   json    返回检查结果数据包
	*/
	public function verifyUser()
	{
		$account    = $this->input('account', 'string', '');
		$callback   = $this->input('callback', 'string', '');//跨域使用
		$this->verifyAjax($callback);//跨域验证

		$cateId    	= isCheck($account);
		$isexist    = $this->load('register')->exist($account,$cateId);
		if( $isexist == 0  ){
			$code	= 2;
			$msg	= '用户不存在';
		}else{
			$code	= 1;
			$msg	= '用户存在';
		}
		$result     = array('code' => $code,'msg' => $msg);
		$this->setJson($result,$callback);
	}
	/**
	* 发送密码(注册用)
	* @author   hyand
	* @since    2016-01-20
	* @return   json    返回检查结果数据包
	*/
	public function sendPassword()
	{
		!$this->isAjax() && $this->redirect('', '/');
		$account = $this->input('account', 'string', '');
		$callback= $this->input('callback', 'string', '');//跨域使用
		$cateId  = isCheck($account);
		if ( empty($account) || !in_array($cateId,array(1,2)) ){
			$code	= 3;
		}else{
			$isexist= $this->load('register')->exist($account,$cateId);
			if( $isexist == 0 ){//注册用户
				$pword = getRandChar();
				$id    = $this->load('register')->regDeal($account,$pword,$cateId);
				$code  = $id > 0 ? 1 : 0;
			}
		}
		$result = array('code' => $code);
		$this->setJson($result,$callback);
	}
	/**
	* 找回密码
	* @since    2016-01-20
	* @author   haydn
	* @return   void
	*/
	public function backPassword()
	{
		$code   	= 0;
		$account 	= $this->input('account', 'string', '');
		$callback   = $this->input('callback', 'string', '');//跨域使用
		$this->verifyAjax($callback);//跨域验证
		$cateId    	= isCheck($account);
		if ( empty($account) || !in_array($cateId,array(1,2)) ){
			$code       = 3;
		}else{
			$isexist    = $this->load('register')->exist($account,$cateId);
			if( $isexist == 1 ){
				$password 	= 'zz123456';//getRandChar();
				$code 		= $this->load('register')->updatePassword($account,$password,$cateId);
			}
		}
		$result = array('code' => $code);
		$this->setJson($result,$callback);
	}
	/**
	* 退出登录
	* @static   2016-01-21
	* @author   haydn
	* @return   void
	*/
	public function logout()
	{
		$callback  = $this->input('callback', 'string', '');
		LoginAuth::logout();
		if( !empty($callback) ){
			$this->verifyLog();
		}else{
			$this->redirect('', '/index/');
		}

	}
	/**
	* 获取登录页面
	* @static	2016-01-25
	* @author	haydn
	* @return	json
	*/
	public function getLogHtml()
	{
		$callback	= $this->input('callback', 'string', '');
		$this->verifyAjax($callback);

		$contents	= '';
		empty($callback) && $this->redirect('', '/user/main/');
		$file		= WebDir.'/Static/script/user/login.html';
		file_exists($file) == 1 && $contents = file_get_contents($file);
		$result 	= array('contents' => $contents);
		$this->setJson($result,$callback);
	}
	/**
	* 设置返回的json值
	* @static	2016-01-25
	* @author	haydn
	* @param 	array 		$array		返回数据
	* @param 	string		$callback	token值
	* @return	void
	*/
	private function setJson($array,$callback)
	{
		if(!empty($callback)){
			$data	= $this->setLogInfo();
			$array 	= count($data) > 0 ? array_merge($array,$data) : $array;
			exit($callback . "([" . json_encode($array) . "])");
		}else{
			$this->returnAjax($array);
		}
	}
	/**
	* 设置登录信息
	* @static	2016-01-26
	* @author	haydn
	* @return	array	$data登录信息
	*/
	public function setLogInfo()
	{
		$data	= array();
		$cookid = getUserKey();
		if( !empty($cookid) ){
			$array	= $this->load('login')->getTokenUser();
			$data	= array('ukey' => $cookid,'nickname' => $array['specname'],'usermobile' => $array['mobile']);
		}
		return $data;
	}
	/**
	* 验证是否登录（接口用）
	* @static	2016-01-26
	* @author	haydn
	* @return	void
	*/
	public function verifyLog()
	{
		$array		= array();
		$callback   = $this->input('callback', 'string', '');//跨域使用
		$this->verifyAjax($callback);

		$ukey		= C('PUBLIC_USER');
		$cookid 	= LoginAuth::get($ukey);
		$array['code'] = !empty($cookid) ? 1 : 0;
		$this->setJson($array,$callback);
	}
	/**
	* 发送验证码(接口调用)
	* @static	2016-01-26
	* @author	haydn
	* @return	void
	*/
	public function sendCode()
	{
		$callback= $this->input('callback', 'string', '');//跨域使用
		$this->verifyAjax($callback);

		$account = $this->input('account', 'string', '');
		$cateId  = isCheck($account);

		if ( empty($account) || !in_array($cateId,array(2)) ){
			$code	= 3;
			$msg	= '账号格式错误';
		}else{
			$id		= $this->load('verify')->sendCode($account,$cateId);
			$code	= $id > 0 ? 1 : 0;
			$msg	= $id > 0 ? '验证码发送成功' : '验证码发送失败';
		}
		$result = array('code' => $code,'msg' => $msg);
		$this->setJson($result,$callback);
	}
	/**
	* 验证code是否合法(接口调用)
	* @static	2016-01-26
	* @author	haydn
	* @return	void
	*/
	public function verifyCode()
	{
		$callback	= $this->input('callback', 'string', '');//跨域使用
		$this->verifyAjax($callback);//验证跨域域名是否正确

		$account 	= $this->input('account', 'string', '');
		$password	= $this->input('password', 'string', '');
		$mcode   	= $this->input('mcode', 'string', '');//验证码
		$cateId  	= isCheck($account);
		$array		= $this->load('verify')->getVerify($account,$password,$cateId);
		$this->setJson($array,$callback);
	}
	/**
	* 注册或重置密码(接口调用)
	* @static	2016-01-26
	* @author	haydn
	* @return	void
	*/
	public function remoteUser()
	{
		$account	= $this->input('account', 'string', '');
		$yzm		= $this->input('password', 'string', '');//验证码
		$cateId  	= $this->input('cateId', 'string', '');
		$callback	= $this->input('callback', 'string', '');//跨域使用
		$expire   	= $this->input('expire', 'string', '');
		$this->verifyAjax($callback);
		if ( empty($account) || !in_array(isCheck($account),array(2)) ){
			$code	= 3;
		}else{
			$array	= $this->load('verify')->getVerify($account,$yzm,$cateId);//验证码验证
			if( $array['code'] == 1 ){
				$expire		= !empty($expire) ? toSec($expire) : 0;
				$code   	= $this->load('login')->userCodeLog($account,$yzm,$cateId);//验证码登录
			}else{
				$code = 4;
			}
		}
		$result = array('code' => $code,'nickname' => $account);
		$this->setJson($result,$callback);
	}
	/**
	* 验证是否合法
	* @since	2016-02-19
	* @author	haydn
	* @param	string	$callback	跨域编号
	* @return	void
	*/
	private function verifyAjax($callback)
	{
		if( !empty($callback) ){
			$is = $this->checkSource();
			if(!$is){
				$callback	= $this->input('callback', 'string', '');//跨域使用
				$result 	= array('code' => '-1','msg' => 'key验证失败');
				$this->setJson($result,$callback);
			}
		}else{
			!$this->isAjax() && $this->redirect('', '/');
		}
	}
	/**
	* 网络信息入库
	* @since	2016-02-27
	* @author	haydn
	* @return	json
	*/
	public function networkLogin()
	{
		$callback	= $this->input('callback', 'string', '');//跨域使用
		$this->verifyAjax($callback);
		$account	= $this->input('tel', 'string', '');
		$cateId 	= isCheck($account);
		$code		= 0;
		$msg		= '';
		$netArray	= array();
		if( !in_array($cateId,array(1,2)) ){
			$msg 	= '账号格式错误';
		}else{
			$data	= $_GET;
			$is		= $this->load('register')->exist($account,$cateId);
			if( $is == 1 ){
				$code	= 2;
				$msg	= '账号存在,请输入密码登录';
			}else{
				$code	= 1;
				$msg	= '账号不存在';
			}
			$uid		= !empty($this->userInfo['id']) ? $this->userInfo['id'] : 0;
			$array		= $this->load('buybrand')->addBackup($account,$uid,$data);
			if( $array['code'] == 1 ){
				$netmsg 	= '网络信息写入成功';
				$netCode	= 1;
			}else{
				$netmsg		= '网络信息写入失败';
				$netCode	= 0;
			}
			$netArray	= array('netcode' => $netCode,'netmsg' => $netmsg);
		}
		$result = array('code' => $code,'msg' => $msg,'data' => $netArray);
		$this->setJson($result,$callback);
	}
}
?>