<?
/**
* 用户登录信息表
* 
* @package	Module
* @author	haydn
* @since	2016-01-18
*/
class SessionsModel extends AppModel
{
    /**
    * 添加sessions
    * 
    * @access  public
    * @author  haydn
    * @since   2016-01-18
    * @param   int  $userId     用户ID
    * @param   int  $cookid     cookid
    * @param   int  $cateId     账户标识(1邮件、2手机、3用户名)
    * @return  viod
    */
    public function add($userId,$cookid,$cateId = 2)
    {  
        $data['cookieId']   = $cookid;
        $data['userId']     = $userId;
        $data['loginDate']  = TIME;
        $data['loginIp']    = getIp();
        $data['lastDate']   = TIME;
        $data['lastIp']     = getIp();
        $data['type']     	= $cateId;
        return $this->create($data); 
    }
    /**
    * 更新sessions
    * 
    * @access  public
    * @author  haydn
    * @since   2016-01-18
    * @param   int  $userId     用户ID
    * @param   int  $cookid     cookid
    * @param   int  $cateId     账户标识(1邮件、2手机、3用户名)
    * @return  viod
    */
    public function update($userId,$cookid,$cateId = 2)
    {
        $data['cookieId']   = $cookid;
        $data['type']     	= $cateId;
        $r['eq'] = array('userId' => $userId);
        return $this->modify($data, $r);
    }
    /**
    * sessions写入或更新
    * 
    * @access  public
    * @author  haydn
    * @since   2016-01-18
    * @param   int  $userId     用户ID
    * @param   int  $cookid     cookid
    * @param   int  $cateId     账户标识(1邮件、2手机、3用户名)
    * @return  void
    */
    public function addSessions($userId,$cookid,$cateId)
    {
    	$this->add($userId,$cookid,$cateId);
    	/*
        $num = $this->getSessCount($userId);
        if( $num == 0 ){
            $this->add($userId,$cookid,$cateId);
        }else{
            $this->update($userId,$cookid,$cateId);
        }*/
        return true;
    }
    /**
    * 获取账户数量
    * 
    * @access  public
    * @author  haydn
    * @since   2016-01-18
    * @param   string     $userId   账户id
    * @return  int        $num      返回(0不存在、大于0存在)
    */
    public function getSessCount($userId)
    {
        $r['eq']    = array(
            'userId'  => $userId,
        );
        $r['col']   = array('count(`id`) as num');
        $data       = $this->find($r);
        $num        = !empty($data['num']) ? $data['num'] : 0;
        return $num;
    }
    /**
    * 根据token获取session信息
    * @author   haydn
    * @since    2016-01-19
    * @param    string $token
    * @return   array
    */
    function getByToken($token)
    {
        $r['eq'] = array('cookieId' => $token);
        return $this->find($r);
    }
}
?>