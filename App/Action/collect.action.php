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
class CollectAction extends AppAction
{
	/**
	 * 我的收藏商标-竞手商标
	 * @author	Edmund
	 * @since	2016/1/28
	 *
	 * @access	public
	 * @return	void
	 */
	public function index()
	{
		$userId			= $this->userInfo['id'];

        $param['page']	= $this->input("page","int");
        $param['limit']	= $this->rowNum;
		$search			= $this->getFormData();
        $param['user']	= $userId;

        $proposerList	= $this->load("collect")->getProposerBuyCollect($userId);
        $classList		= $this->load("collect")->getClassBuyCollect($userId);
        $statusList		= $this->load("collect")->getStatusBuyCollect($userId);

		$data			= $this->load("collect")->getPageListCollect($param, $search);
        $pager			= $this->pager($data['total'], $this->rowNum);
        $pageBar		= empty($data['rows']) ? '' : getPageBar($pager);
		
		//$getSrouceCount	= $this->load("collect")->getSrouceCount($userId);

		$this->set('data',$data);
		$this->set('proposerList',$proposerList);
		$this->set('classList',$classList);
		$this->set('statusList',$statusList);
		$this->set('classnew', C('SecondStatus'));
		$this->set('classDiff',array_diff(C('CLASSNEW'), $classList));
		$this->set('pageBar',$pageBar);
		$this->set('search',$search);
		$this->set('getSrouceCount',$getSrouceCount);
		
		
		$this->display();
	}
	/**
	 * 我的收藏商标-交易商标
	 * @author	Edmund
	 * @since	2016/1/28
	 *
	 * @access	public
	 * @return	void
	 */
	public function trade()
	{
		$userId			= $this->userInfo['id'];

        $param['page']	= $this->input("page","int");
        $param['limit']	= $this->rowNum;
		$search			= $this->getFormData();
        $param['user']	= $userId;
        $saleStatusList	= $this->load("collect")->getSaleStatusList($userId);
 
		$data			= $this->load("collect")->getPageListCollectTrade($param, $search);  
		$pager			= $this->pager($data['total'], $this->rowNum);
        $pageBar		= empty($data['rows']) ? '' : getPageBar($pager);
		//$getSrouceCount	= $this->load("collect")->getSrouceCount($userId);
		$this->set('data',$data);
		
		$this->set('saleStatusList',$saleStatusList);
		$this->set('classnew', C('SecondStatus'));
		$this->set('classDiff',array_diff(C('CLASSNEW'), $classList));
		$this->set('pageBar',$pageBar);
		$this->set('search',$search);
		$this->set('getSrouceCount',$getSrouceCount);
		//print_r($data);
		$this->display();
	}

	
	/**
	 * 导出我的商标
	 * @author
	 * @since	2016/3/9
	 *
	 * @access	public
	 * @return	void
	 */
	public function excel()
	{
		$search			= $this->getFormData('collectindex');
        $param['page']	= 1;
        $param['limit']	= 1000;
        $param['user']	= $this->userInfo['id'];
        $data			= $this->load("collect")->getPageListCollect($param, $search);
		$http			= $this->load("mytrade")->exceltable($data['rows'],'我的收藏');
		echo $http; exit;
	}
	/**
	 * 删除收藏
	 * @author	martin
	 * @since	2016/1/28
	 *
	 * @access	public
	 * @return	void
	 */
	public function deleteCollect()
	{
        $param['trademark']		= $this->input("id","string");
        $param['source']		= $this->input("source","int");
        $param['userId']		= $this->userInfo['id'];
		$bool					= $this->load('collect')->deleteCollectById($param);
		echo $bool;exit;
	}

	/**
	 * 价格异动
	 * 收藏商标一年内的商标价格变动
	 * @author	martin
	 * @since	2016/1/28
	 *
	 * @access	public
	 * @return	void
	 */
	public function statusLog(){
		
		$search			= $this->getFormData();
        $search['page']	= $this->input("page","int");
        $search['limit']= $this->rowNum;
        $proposerList	= $this->load("collect")->getProposerBuyCollect($this->userInfo['id']);
        $data			= $this->load("collect")->getPageStatusList($this->userInfo['id'], $search);
		
        $pager			= $this->pager($data['total'], $this->rowNum);
        $pageBar		= empty($data['rows']) ? '' : getPageBar($pager);

		$this->set('search',$search);
		$this->set('proposerList',$proposerList);
		$this->set('data',$data);
		$this->set('pageBar',$pageBar);
		$this->display();
	}
	
	/**
	 * 导出我的收藏的商标动态
	 * 
	 * @author	martin
	 * @since	2016/3/10
	 *
	 * @access	public
	 * @return	void
	 */
	public function excellog(){
		
		$search			= $this->getFormData('collectstatuslog');
        $search['page']	= 1;
        $search['limit']= 1000;
        $data			= $this->load("collect")->getPageStatusList($this->userInfo['id'], $search);
		$http			= $this->load("mytrade")->exceltablelog($data['rows'],'我的收藏');
		echo $http;exit;
	}


	/**
	 * 收藏商标
	 * 
	 * @author	martin
	 * @since	2016/2/22
	 *
	 * @access	public
	 * @return	void
	 */
	public function addtrademark()
	{
		$jsoncallback	= $_GET['jsoncallback'];
		$data['number']	= $this->input('number','string');
		$data['source']	= $this->input('source','int');
		$data['ukey']	= $this->input('ukey','string');
		if(empty($data['ukey'])){
			$this->returnmess('emptyUkey',$jsoncallback);
		}elseif(empty($data['number'])){
			$this->returnmess('params',$jsoncallback);
		}
		$userId			= $this->load('sessions')->getUserIdByCookie( $data['ukey'] );
		if(empty($userId)){
			$this->returnmess('emptyUser',$jsoncallback);
		}
		$output			= $this->load('collect')->addCollectTrademark( $data, $userId );
		echo $jsoncallback."('" . json_encode( $output ) . "')"; exit;
	}
	
	/**
	 * 删除收藏商标
	 * 
	 * @author	martin
	 * @since	2016/3/14
	 *
	 * @access	public
	 * @return	void
	 */
	public function removetrademark()
	{
		$jsoncallback	= $_GET['jsoncallback'];
		$data['number']	= $this->input('number','string');
		$data['source']	= $this->input('source','int');
		$data['ukey']	= $this->input('ukey','string');
		if(empty($data['ukey'])){
			$this->returnmess('emptyUkey',$jsoncallback);
		}elseif(empty($data['number'])){
			$this->returnmess('params',$jsoncallback);
		}
		$userId			= $this->load('sessions')->getUserIdByCookie( $data['ukey'] );
		if(empty($userId)){
			$this->returnmess('emptyUser',$jsoncallback);
		}
		$output			= $this->load('collect')->removeCollectTrademark( $data, $userId );
		echo $jsoncallback."('" . json_encode( $output ) . "')"; exit;
	}
	/**
	 * 获取用户收藏的商标列表
	 * 
	 * @author	martin
	 * @since	2016/3/15
	 *
	 * @access	public
	 * @return	void
	 */
	public function userCollectList(){
		$jsoncallback	= $_GET['jsoncallback'];
		$data['num']	= $this->input('num','string');
		$data['source']	= $this->input('source','int');
		$data['ukey']	= $this->input('ukey','string');
		if(empty($data['ukey'])){
			$this->returnmess('emptyUkey',$jsoncallback);
		}elseif( empty($data['num'])){
			$this->returnmess('params',$jsoncallback);
		}
		$userId			= $this->load('sessions')->getUserIdByCookie( $data['ukey'] );
		if(empty($userId)){
			$this->returnmess('emptyUser',$jsoncallback);
		}
		$output			= $this->load('collect')->getUserCollectList( $data, $userId );
		echo $jsoncallback."('" . json_encode( $output ) . "')"; exit;
	}

	/**
	 * 收藏申请人的商标
	 * 
	 * @author	martin
	 * @since	2016/2/29
	 *
	 * @access	public
	 * @return	void
	 */
	public function addproposer()
	{
		$jsoncallback	= $this->input('jsoncallback','string');
		$data['id']		= $this->input('id','string');
		$data['source']	= $this->input('source','int');
		$data['ukey']	= $this->input('ukey','string');
		if(empty($data['ukey'])){
			$this->returnmess('emptyUkey',$jsoncallback);
		}elseif( empty($data['id']) ){
			$this->returnmess('params',$jsoncallback);
		}
		$userId			= $this->load('sessions')->getUserIdByCookie( $data['ukey'] );
		if(empty($userId)){
			$this->returnmess('emptyUser', $jsoncallback);
		}
		$output			= $this->load('collect')->addCollectProposer( $data, $userId );
		echo $jsoncallback."('" . json_encode( $output ) . "')";exit;
	}

	/**
	 * 处理报错信息
	 * @author	martin
	 * @since	2016/2/22
	 *
	 * @access	public
	 * @return	void
	 */
	public function returnmess( $type, $jsoncallback ){
		switch ( $type ) {
			case 'emptyUkey':
				$mess = '请登录';
				break;
			case 'params':
				$mess = '参数有误';
				break;
			case 'trade':
				$mess = '商标不存在';
				break;
			case 'emptyUser':
				$mess = '用户不存在';
				break;
		}
		$output	= array('typde' => 2, 'mess' => $mess);
		echo $jsoncallback . "('" . json_encode( $output ) . "')";exit;
	}
}
?>