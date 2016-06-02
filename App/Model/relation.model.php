<?
/**
* 
* 用户关联信息表
* 
* @package	Module
* @author	haydn
* @since	2016-01-21
*/
class RelationModel extends AppModel
{
    /**
    * 写入用户关联
    * 
    * @access  public
    * @author  haydn
    * @since   2016-01-18
    * @param   int     $userId      用户ID
    * @param   int     $aid         网络ID
    * @param   int     $type        关联类型(0：网络信息)
    * @return  int     $id
    */
    public function add($userId,$aid,$type = 0)
    {
        $data['userId']     = $userId;
        $data['type']       = $type;
        $data['relationId'] = $aid;
        $data['created']    = TIME;
        $id                 = $this->create($data);
        return $id;
    }
    /**
    * 用户id获取信息
    * 
    * @access  public
    * @author  haydn
    * @since   2016-01-18
    * @param   int     $userId      用户ID
    * @param   int     $type        关联类型(0：网络信息)
    * @return  int     $id
    */
    public function getRelationInfo($userId)
    {
        $r['eq']    = array(
            'userId'  => $userId,
        );
        $r['order']    = array('created' => 'desc');
        $r['col']   = array('*');
        $data       = $this->find($r);
        return $data;
    }
    /**
    * 获取关联数
    * @access  public
    * @author  haydn
    * @since   2016-02-27
    * @param   int     $userId      用户ID
    * @param   int     $type      	类型
    * @return  int     $count
    */
    public function getRelationCount($userId,$type = 0)
    {
        $r['eq']    = array(
            'userId'	=> $userId,
            'type'  	=> $type,
        );
        $count      = $this->count($r);
        return $count;
    }
    /**
    * 获取关联数
    * @access  public
    * @author  haydn
    * @since   2016-02-27
    * @param   int     $userId      用户ID
    * @param   int     $aid         网络ID
    * @param   int     $type        关联类型(0：网络信息)
    * @return  int     $is
    */
    public function updateRelation($userId,$aid,$type = 0)
    {
        $r['eq']    = array(
            'userId'  	=> $userId,
            'type'  	=> $type,
        );
        $data		= array('relationId' => $aid,'updated' => TIME);
        $is			= $this->modify($data, $r);
        return $is;
    }
}
?>