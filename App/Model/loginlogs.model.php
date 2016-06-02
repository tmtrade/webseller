<?
/**
* 登录日志表
* 
* @package	Module
* @author	haydn
* @since	2016-01-18
*/
class LoginlogsModel extends AppModel
{
    /**
    * 检查账户是否存在
    * 
    * @access  public
    * @author  haydn
    * @since   2016-01-18
    * @param   string     $account      登录账户
    * @param   string     $password     登录密码
    * @param   int        $userId       用户ID
    * @param   int        $code         登录状态
    * @return  int        返回userId(0不存在、大于0存在)
    */
    public function addlog($account,$password,$userId,$code = 0)
    {  
        $data['username']       = $account;
        $data['userId']         = !empty($userId) ? $userId : 0;
        $data['isSuccess']      = $code == 1 ? 1 : 0;
        $data['failPassword']   = $password;
        $data['loginDate']      = TIME;
        $data['loginIp']        = getIp();
        return $this->create($data);
    }
}
?>