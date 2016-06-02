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
class MytradeAction extends AppAction
{
	
	/**
	 * 获取每个页面的总数量
	 * @author	martin
	 * @since	2016/3/28
	 *
	 * @access	public
	 * @return	void
	 */
	public function getNumPage()
	{
        $param['page']		= $this->input("page","int");
        $param['limit']		= $this->rowNum;
        $param['user']		= $this->userInfo['id'];
        $trade				= $this->load("mytrade")->getPageListTrade($param, array());
		$total['trade']		= $trade['total'];
        $search['page']		= $this->input("page","int");
        $search['limit']	= $this->rowNum;
        $mytrade			= $this->load("mytrade")->getPageStatusList($this->userInfo['id'], $search);
		$total['mytrade']	= $mytrade['total'];
		$deal['raw']	= '(status4 = 1 or status8 = 1 or status9 = 1 or status10 = 1 or  status14 = 1 or status28 = 1 or status15 = 1)';
        $deal				= $this->load("mytrade")->getPageListTrade($param, $deal);
		$total['deal']		= $deal['total'];

		$this->set('total',$total);
	}
	/**
	 * 我的商标[二级状态表]
	 * @author	martin
	 * @since	2016/1/28
	 *
	 * @access	public
	 * @return	void
	 */
	public function index()
	{
        $param['page']	= $this->input("page","int");
        $param['limit']	= $this->rowNum;
		$search			= $this->getFormData();
        $param['user']	= $this->userInfo['id'];
        $proposerList	= $this->load("userproposer")->getMyProposerOnly($this->userInfo['id']);
        $classList		= $this->load("mytrade")->getClassList($this->userInfo['id']);
        $statusList		= $this->load("mytrade")->getStatusList($this->userInfo['id']);
        $data			= $this->load("mytrade")->getPageListTrade($param, $search);
        $pager			= $this->pager($data['total'], $this->rowNum);
        $pageBar		= empty($data['rows']) ? '' : getPageBar($pager);
		$this->getNumPage();
		$this->set('data',$data);
		$this->set('proposerList',$proposerList);
		$this->set('classList',$classList);
		$this->set('statusList',$statusList);
		$this->set('classnew', C('SecondStatus'));
		$this->set('classDiff',array_diff(C('CLASSNEW'), $classList));
		$this->set('pageBar',$pageBar);
		$this->set('search',$search);
		$this->display();
	}

	/**
	 * 导出我的商标
	 * @author	martin
	 * @since	2016/3/9
	 *
	 * @access	public
	 * @return	void
	 */
	public function excel()
	{
		$search			= $this->getFormData('mytradeindex');
        $param['page']	= 1;
        $param['limit']	= 1000;
        $param['user']	= $this->userInfo['id'];
        $data			= $this->load("mytrade")->getPageListTrade($param, $search);
		$http			= $this->load("mytrade")->exceltable($data['rows'],'我的商标');
	}
	
	/**
	 * 我的商标状态异动[三级状态]
	 * 
	 * @author	martin
	 * @since	2016/2/25
	 *
	 * @access	public
	 * @return	void
	 */
	public function statusLog(){
		$search			= $this->getFormData();
        $search['page']	= $this->input("page","int");
        $search['limit']= $this->rowNum;
        $proposerList	= $this->load("userproposer")->getMyProposerOnly($this->userInfo['id']);
        $data			= $this->load("mytrade")->getPageStatusList($this->userInfo['id'], $search);
		
        $pager			= $this->pager($data['total'], $this->rowNum);
        $pageBar		= empty($data['rows']) ? '' : getPageBar($pager);

		$this->getNumPage();
		$this->set('search',$search);
		$this->set('proposerList',$proposerList);
		$this->set('data',$data);
		$this->set('pageBar',$pageBar);
		$this->display();
	}
	
	/**
	 * 导出我的商标的商标动态
	 * 
	 * @author	martin
	 * @since	2016/3/10
	 *
	 * @access	public
	 * @return	void
	 */
	public function excellog(){
		$search			= $this->getFormData('mytradestatuslog');
        $search['page']	= 1;
        $search['limit']= 1000;
        $data			= $this->load("mytrade")->getPageStatusList($this->userInfo['id'], $search);
		$http			= $this->load("mytrade")->exceltablelog($data['rows'],'我的商标');
		echo $http;exit;
	}
	/**
	 * 出售/许可商标
	 * 
	 * @author	martin
	 * @since	2016/3/2
	 *
	 * @access	public
	 * @return	void
	 */
	public function addsale(){
		$search			= $this->getFormData();
        $userId			= $this->userInfo['id'];
		$data			= $this->load('sale')->addSaleInfo($userId, $search);
		echo json_encode($data);
		exit;
	}
	/**
	 * 删除最新商标的状态[点击查看商标详情时执行]
	 * 
	 * @author	martin
	 * @since	2016/3/2
	 *
	 * @access	public
	 * @return	void
	 */
	public function showtm()
	{
        $tid			= $this->input("tid","int");
        $user			= $this->userInfo['id'];
		$this->load('newtrade')->deleteInfo($user, $tid);
		exit;
	}
	/**
	 * 业务待办
	 * 
	 * @author	martin
	 * @since	2016/4/6
	 *
	 * @access	public
	 * @return	void
	 */
	public function business()
	{
		$search			= $this->getFormData('mytradeindex');
		$search['raw']	= '(status4 = 1 or status8 = 1 or status9 = 1 or status10 = 1 or  status14 = 1 or status28 = 1 or status15 = 1)';
        $param['page']	= $this->input("page","int");
        $param['limit']	= $this->rowNum;
        $param['user']	= $this->userInfo['id'];

		$this->getNumPage();
        $proposerList	= $this->load("userproposer")->getMyProposerOnly($this->userInfo['id']);
        $classList		= $this->load("mytrade")->getClassList($this->userInfo['id']);
        $statusList		= $this->load("mytrade")->getStatusList($this->userInfo['id']);
        $data			= $this->load("mytrade")->getPageListTrade($param, $search);
        $data['rows']	= $this->load("mytrade")->getDealList($data['rows'], $this->userInfo['id']);
        $pager			= $this->pager($data['total'], $this->rowNum);
        $pageBar		= empty($data['rows']) ? '' : getPageBar($pager);

		$this->set('data',$data);
		$this->set('proposerList',$proposerList);
		$this->set('classList',$classList);
		$this->set('statusList',$statusList);
		$this->set('classnew', C('SecondStatus'));
		$this->set('classDiff',array_diff(C('CLASSNEW'), $classList));
		$this->set('pageBar',$pageBar);
		$this->set('search',$search);
		$this->display();
	}
	/**
	 * 提交业务办理
	 * 
	 * @author	martin
	 * @since	2016/4/6
	 *
	 * @access	public
	 * @return	void
	 */
	public function businessajax()
	{
        $search['tid']		= $this->input("tid","string");
        $search['phone']	= $this->input("phone","string");
        $search['name']		= $this->input("name","string");
        $search['usertext']	= $this->input("usertext","string");
        $search['source']	= $this->input("source","int");
		$bool				= $this->load('mytrade')->addNetwork( $this->userInfo['id'], $search );
		echo $bool == true ? 1 : 0; exit;
	}
}
?>