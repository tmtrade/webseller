<?
/**
* 用户注册
*
* 用户注册
* 
* @package	Module
* @author	haydn
* @since	2016-01-18
*/
class UserModel extends AppModel
{
    /**
    * 检查账户是否存在
    * 
    * @access  public
    * @author  haydn
    * @since   2016-01-18
    * @param   string     $account        登录账户
    * @param   int        $cateId         账户标识(1邮件、2手机、3用户名)
    * @return  int        返回userId(0不存在、大于0存在)
    */
    public function exist($account,$cateId = 2)
    {  
        $field = $this->getUserType($cateId);
        $r['eq']    = array(
            $field  => $account,
        );
        $r['col']   = array('count(`id`) as num');
        $data       = $this->find($r);
        $num        = !empty($data['num']) ? $data['num'] : 0;
        return $num;
    }
    /**
    * 检查超凡id是否存在
    * 
    * @access  public
    * @author  haydn
    * @since   2016-01-18
    * @param   int        $cfwid         超凡id
    * @return  int        返回userId(0不存在、大于0存在)
    */
    public function existChofnId($cfwid)
    {
        $r['eq']    = array(
            'cfwId'  => $cfwid,
        );
        $r['col']   = array('count(`id`) as num');
        $data       = $this->find($r);
        $num        = !empty($data['num']) ? $data['num'] : 0;
        return $num;
    }
    /**
    * 账号注册
    * @author   haydn
    * @since    2015-12-16
    * @access   public
    * @param    string      $account    登录账户
    * @param    string      $password   登录密码
    * @param    int         $cateId     账户标识(1邮件、2手机、3用户名)
    * @return   int         $id         数据id
    */
    public function add($account,$password,$cateId = 2)
    {
        $rand               = getRandChar();
        $field              = $this->getUserType($cateId);
        $data[$field]       = $account;
        $data['password']   = getPasswordMd5($password,$rand);
        $data['salt']       = $rand;
        $data['regDate']    = TIME;
        $data['regIp']      = getIp();
        return $this->create($data);
    }
    /**
    * 账号注册(超凡网模式)
    * @author   haydn
    * @since    2015-12-16
    * @access   public
    * @param    array       $chofnInfo  超凡数据包
    * @return   int         $id         数据id
    */
    public function addChofn($chofnInfo)
    {        
        $data['cfwId']      = $chofnInfo['id'];
        $data['username']   = $chofnInfo['username'];
        $data['nickname']   = $chofnInfo['nickname'];
        $data['email']      = $chofnInfo['email'];
        $data['mobile']     = $chofnInfo['mobile'];
        $data['password']   = $chofnInfo['password'];
        $data['salt']       = $chofnInfo['salt'];
        $data['regIp']      = $chofnInfo['regIp'];
        $data['isEmail']    = $chofnInfo['isEmail'];
        $data['isMobile']   = $chofnInfo['isMobile'];
        $data['lastIp']     = $chofnInfo['lastIp'];
        $data['loginNum']   = $chofnInfo['loginNum'];
        $data['updated']    = $chofnInfo['updated'];
        $data['photo']      = $chofnInfo['photo'];
        $data['level']      = $chofnInfo['level'];
        $data['regDate']    = $chofnInfo['created'];
        return $this->create($data);
    }
    /**
    * 更新最新的超凡网账号
    * @author  haydn
    * @since   2015-12-16
    * @param   array    $chofnInfo  超凡数据包
    * @param   int	    $id  		uc用户id(大于0：用uc账号id修改 0：用超凡编号id修改)
    * @return  int      $field
    */
    public function updateChofn($chofnInfo,$id = 0)
    {
        $data['nickname']   = $chofnInfo['nickname'];//昵称
        $data['username']   = $chofnInfo['username'];//用户名
        $data['email']      = $chofnInfo['email'];//邮箱
        $data['mobile']     = $chofnInfo['mobile'];//手机
        $data['cfwId']   	= $chofnInfo['id'];
        $data['password']   = $chofnInfo['password'];//密码
        $data['salt']       = $chofnInfo['salt'];//密码签名
        if( $id == 0 ){
			$r['eq'] = array('cfwId' => $chofnInfo['id']);
        }else{
			$r['eq'] = array('id' => $id);
        }
        return $this->modify($data, $r);
    }
    /**
    * 获取用户类型字段
    * @author  haydn
    * @since   2015-12-16
    * @param   int     $cateId     账户标识(1邮件、2手机、3用户名)
    * @return  string  $field
    */
    public function getUserType($cateId)
    {
        $array  = array(1 => 'email', 2 => 'mobile', 3 => 'username',4=>'id' );
        $field  = array_key_exists($cateId,$array) ? $array[$cateId] : $array[2];
        return $field;
    }
    /**
    * 用账号获取信息
    * 
    * @access  public
    * @author  haydn
    * @since   2016-01-18
    * @param   string     $account        登录账户
    * @param   int        $cateId         账户标识(1邮件、2手机、3用户名)
    * @return  int        返回userId(0不存在、大于0存在)
    */
    public function getUserInfo($account,$cateId = 2)
    {
        $field = $this->getUserType($cateId);
        $r['eq']    = array(
            $field  => $account,
        );
        $r['col']   = array('`id`,`mobile`,`email`,`cfwId`,`password`,`salt`,`isEmail`,`isMobile`');
        $data       = $this->find($r);
        return $data;
    }
    /**
    * 更新账号信息
    * @author  haydn
    * @since   2015-12-16
    * @param   string     $account        登录账户
    * @param   int        $cateId         账户标识(1邮件、2手机、3用户名)
    * @return  void
    */
    public function updateUser($account,$cateId = 2)
    {
        $field              = $this->getUserType($cateId);
        $userInfo           = $this->getUserInfo($account,$cateId);
        $data['loginNum']   = $userInfo['loginNum'] + 1;
        $data['lastDate']   = TIME;
        $data['lastIp']     = getIp();
        $r['eq']            = array($field => $account);
        return $this->modify($data, $r);
    }
    /**
     * 检查是否登陆
     * @author  haydn
     * @since   2016-01-18
     * @return  bool
     */
    public function check()
    {
        $token = LoginAuth::get('cookid');
        if ( empty($token) ) {
            return false;
        }
        $user = $this->get($token);
        return empty($user) ? false : true;
    }
    /**
    * 更新账号密码
    * @author  haydn
    * @since   2015-12-16
    * @param   string     $account      登录账户
    * @param   string     $password     登录密码
    * @param   int        $cateId       账户标识(1邮件、2手机、3用户名)
    * @return  void
    */
    public function updatePassword($account,$password,$cateId = 2)
    {
        $field              = $this->getUserType($cateId);
        $data['password']   = $password;
        $r['eq']            = array($field => $account);
        return $this->modify($data, $r);
    }
    /**
    * 更新超凡密码
    * @author  haydn
    * @since   2016-03-30
    * @param   int     		$id      	超凡id
    * @param   string     	$password	新密码
    * @param   string       $salt		密钥
    * @return  void
    */
    public function resetCfPwd($id,$password,$salt)
    {
        $data	= array('password' => $password,'salt' => $salt);
	    $r['eq']= array('cfwId' => $id);
        return $this->modify($data, $r);
    }
    /**
    * 登录后修改登录信息
    * @author  haydn
    * @since   2016-03-01
    * @param   int        $uid	用户id
    * @return  void
    */
    public function updateLogin($uid)
    {
    	$info				= $this->get($uid);
        $data['loginNum']   = $info['loginNum'] + 1;
        $data['lastDate']	= TIME;
        $data['lastIp']		= getIp();
        $r['eq']            = array('id' => $uid);
        return $this->modify($data, $r);
    }
    /**
    * 更新手机
    * @author  haydn
    * @since   2016-04-12
    * @param   string     $mobile	手机号
    * @param   int        $id		数据id
    * @param   int        $type		修改类型（0：超凡网 1：用户中心）
    * @return  void
    */
    public function changeMobile($mobile,$id,$type = 0)
    {
    	$field				= $type == 0 ? 'cfwId' : 'id';
        $data['mobile']		= $mobile;
        $data['isMobile']	= 1;
        $r['eq']            = array($field => $id);
        return $this->modify($data, $r);
    }
}
?>