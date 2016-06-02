<?
/**
 * 项目注册页面
 *
 *
 * @package	Action
 * @author	void
 * @since	2016-01-20
 */
class RegisterAction extends AppAction
{
    
	/**
	 * 控制器默认方法
	 * @author	void
	 * @since	2016-02-19
	 *
	 * @access	public
	 * @return	void
	 */
	public function index()
	{  
        $this->display();
	}
    /**
    * 用户注册
    * @author   hyand
    * @since    2016-01-20
    * @return   json    返回检查结果数据包
    */
    public function regUser()
    {
    	!$this->isAjax() && $this->redirect('', '/');
        $account    = $this->input('account', 'string', '');
        $password   = $this->input('password', 'string', '');
        $cateId    	= isCheck($account);
        $code		= 0;
        if ( empty($account) || !in_array($cateId,array(1,2))  ){
            $code   = 2;
        }else{
        	$is		= $this->load('register')->exist($account,$cateId);
        	if( $is == 1 ){
				$code	= 3;
        	}else{
                $id		= $this->load('register')->regDeal($account,$password,$cateId);
                if( $id > 0 ){
					$code  = $this->load('login')->login($account,$password,$cateId);
                }
        	}
        }
        $msg		= $this->getRegMsg($code);
        $result     = array('code' => $code,'msg' => $msg);
        $this->returnAjax($result);
    }
    /**
    * 验证用户是否注册
    * @author   hyand
    * @since    2016-01-20
    * @return   json    返回检查结果数据包
    */
    public function verifyUser()
    {
        !$this->isAjax() && $this->redirect('', '/');
        $account    = $this->input('account', 'string', '');
        $cateId    	= $this->input('cateId', 'string', '');
        $isexist    = $this->load('register')->exist($account,$cateId);
        $code		= $isexist == 0 ? 2 : 1;
        $result     = array('code' => $code);
        exit(json_encode($result));
    }
    public function getRegMsg($code)
    {
		$msg = '';
		switch($code){
			case 0;
				$msg = '账号注册失败';
			break;
			case 1;
				$msg = '注册成功';
			break;
			case 2;
				$msg = '账号格式错误';
			break;
			case 3;
				$msg = '账号存在注册失败';
			break;
			default;
				$msg = '系统繁忙请稍后再试';
		}
		return $msg;
    }
}
?>