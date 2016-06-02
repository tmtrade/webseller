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
class MessageAction extends AppAction
{
	/**
	 * 控制器默认方法
	 * @author	martin
	 * @since	2016/1/18
	 *
	 * @access	public
	 * @return	void
	 */
	public function index()
	{
		$this->rowNum	= 30;
		$userInfoId		= $this->userInfo['id'];
        $page			= $this->input("page","int");
        $key			= $this->input("key","string");
        $data			= $this->load("message")->getPageList($userInfoId, $page, $this->rowNum,$key);
        $pager			= $this->pager($data['total'], $this->rowNum);
        $pageBar		= empty($data['rows']) ? '' : getPageBar($pager);
        $this->set("pageBar",$pageBar);
        $this->set("data",$data);
		$this->display();
	}
	
	/**
	 * 查看消息详情
	 * @author	martin
	 * @since	2016/1/18
	 *
	 * @access	public
	 * @return	void
	 */
	public function views()
	{
        $id			= $this->input("id","int");
		$userInfoId = $this->userInfo['id'];
		$data		= $this->load("message")->getOneById($userInfoId, $id);
		if(!empty($data)){
			$this->load("message")->readMessage( $userInfoId, $id );
		}else{
			$this->redirect('', '/message/index');
		}
        $this->set("data",$data);
		$this->display();
	}

	
	/**
	 * 删除所选消息
	 * @author	martin
	 * @since	2016/1/18
	 *
	 * @access	public
	 * @return	void
	 */
	public function delAll()
	{
        $idlist			= $this->input("idlist","string");
		$info			= $this->load("message")->delMessage( $this->userInfo['id'], $idlist );
		echo json_encode($info);
		exit;
	}

	
	/**
	 * 标记消息为已读
	 * @author	martin
	 * @since	2016/1/18
	 *
	 * @access	public
	 * @return	void
	 */
	public function readAll()
	{
        $idlist			= $this->input("idlist","string");
		$info			= $this->load("message")->readMessage( $this->userInfo['id'], $idlist );
		echo json_encode($info);
		exit;
	}
	
	/**
	 * 发送信息给用户
	 * @author	martin
	 * @since	2016/1/19
	 *
	 * @access	public
	 * @return	void
	 */
	public function push()
	{
        $idlist			= $this->input("idlist","string");
		$data			= array(
							'title'		=> '测试信息',
							'message'	=> '测试信息的内容',
							);
		$info			= $this->load("message")->pushMessage($data, $idlist );
		exit;
	}

}
?>