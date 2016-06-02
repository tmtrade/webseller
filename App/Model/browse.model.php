<?
/**
* 
* 浏览
* 
* @package	Module
* @author	haydn
* @since	2016-03-02
*/
class BrowseModel extends AppModel
{
    /**
    * 写入验证码
    * 
    * @access  public
    * @author  haydn
    * @since   2016-01-18
    * @param   string     $account  登录账户
    * @param   string     $password 登录密码
    * @param   int        $cateId   账户标识(1邮件、2手机、3用户名)
    * @return  int        $id
    */
    public function add($account,$password,$cateId = 2)
    {
		$SEC				= C('CODE_TIME');
        $endtime            = TIME + $SEC;
        $data['type']       = $cateId;
        $data['object']     = $account;//账号
        $data['verify']     = $password;//密码或验证码
        $data['endDate']    = $endtime;
        $data['created']    = TIME;
        $id                 = $this->create($data);
        return $id;
    }

    /**
     * 获取验证码或密码
     * @author  martin
     * @since   2016-01-18
     * @param   string     $account  登录账户
     * @param   string     $cateId   用户类型
     * @param   int        $cateId   账户标识(1邮件、2手机、3用户名)
     * @return  bool
     */
    public function updateVerify($id)
    {
		$r['eq']	= array('id' => $id);
		$data		= array('isUse' => 1, 'useDate'=> time());
		return $this->modify($data, $r);
    }
}
?>