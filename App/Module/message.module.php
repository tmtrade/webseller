<?
/**
 * 应用业务组件基类
 *
 * 存放业务组件公共方法
 * 
 * @package	Model
 * @author	void
 * @since	2015-11-20
 */
class MessageModule extends AppModule
{
	/**
     * 引用业务模型
     */
    public $models = array(
        'message'		=> 'message',
        'user'			=> 'user',
        'template'		=> 'template',
    );

	/**
	 * 分页获取消息
	 * @author	martin
	 * @since	2016/1/18
	 *
	 * @access	public
	 * @param	int		$page		页码	
	 * @param	int		$rowNum		每页条数
	 * @return	array  返回业务对象
	 */
	public function getPageList($userInfoId,$page, $rowNum,$key='',$isRead = true)
	{
		if(!empty($key)){
			$r['raw'] = "(title like '%".$key."%')";
		}
		if( $isRead == false ){
			array('isRead'=>0);
		}
		$eqArray	= $isRead == false ? array('userId'=>$userInfoId,'isRead'=>0) : array('userId'=>$userInfoId);
		$r['limit']	= $rowNum;
		$r['page']	= $page;
		$r['eq']	= $eqArray;
		$r['order']	= array('isRead' => 'asc', 'created' => 'desc');
		$data		= $this->import("message")->findAll($r);
		foreach($data['rows'] as &$item){
			if($item['tplId'] != 0){
				$template			= $this->import('template')->get($item['tplId']);
				$item['title']		= $template['title'];
				$item['message']	= $template['tpl'];
			}
		}
		return $data;

	}

	/**
	 * 根据消息编号得到商标详情
	 * @author	martin
	 * @since	2016/1/18
	 *
	 * @access	public
	 * @param	int		$id		消息ID	
	 * @return	array	消息详情
	 */
	public function getOneById($userInfoId, $id)
	{
		if(empty($userInfoId) || empty($id) ){
			return array();
		}
		$r['eq']	= array('userId'=>$userInfoId, 'id' => $id);
		$data		= $this->import("message")->find($r);
		return $data;
	}

	/**
	 * 批量删除消息
	 * @author	martin
	 * @since	2016/1/18
	 *
	 * @access	public
	 * @param	string	$list		消息id
	 * @return	string  返回业务对象
	 */
	public function delMessage( $userInfoId, $list )
	{
		if(empty($userInfoId) || empty($list)){
			return '操作失败！';
		}
		$arr		= explode(",",$list);
		$arr		= array_filter($arr);
		if(empty($arr)){
			return '你未勾选信息！';
		}

		$r['eq']	= array('userId'=>$userInfoId);
		$r['in']	= array('id' => $arr);
		$bool		= $this->import('message')->remove( $r );
		if( $bool ){
			return '操作成功！';
		}else{
			return '操作失败！';
		}
	}

	/**
	 * 批量标记为已读消息
	 * @author	martin
	 * @since	2016/1/18
	 *
	 * @access	public
	 * @param	string	$list		消息id
	 * @return	string  返回业务对象
	 */
	public function readMessage( $userInfoId, $list )
	{
		if(empty($userInfoId) || empty($list)){
			return '操作失败！';
		}
		$arr		= explode(",", $list);
		$arr		= array_filter($arr);
		if(empty($arr)){
			return '你未勾选信息！';
		}

		$r['eq']	= array('userId'=>$userInfoId);
		$r['in']	= array('id' => $arr);
		$data		= array("isRead" => 1);

		$bool		= $this->import('message')->modify( $data, $r );
		if( $bool ){
			return '操作成功！';
		}else{
			return '操作失败！';
		}
	}

	/**
	 * 批量发送信息
	 * @author	martin
	 * @since	2016/1/19
	 *
	 * @access	public
	 * @param	string	$mess		消息内容
	 * @param	string	$list		用户ID
	 * @return	string  返回业务对象
	 */
	public function pushMessage( $mess, $list)
	{
		if(empty($list)){
			return false;
		}
		$arr					= explode(",", $list);
		$arr					= array_filter($arr);
		$mess['created']		= time();
		$mess['isRead']			= 0;
		if(empty($arr)){
			$r['limit']			= 10000000000;
			$user				= $this->import('user')->findAll($r);
			$arr				= $user['rows'];
			foreach($user['rows'] as $item){
				$mess['userId']	= $item['id'];
				$this->import('message')->create( $mess );
			}
		} else {
			foreach($arr as $item){
				$mess['userId']	= $item;
				$this->import('message')->create( $mess );
			}
		}
		return true;
	}
}
?>