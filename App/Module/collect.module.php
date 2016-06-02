<?
/**
 * 收藏商标
 *
 * 获取商标 关注商标  删除商标
 * 
 * @package	Module
 * @author	martin
 * @since	2016/1/28
 */
class CollectModule extends AppModule
{
	/**
	 * 引用业务模型
	 */
	public $models = array(
        'collect'		=> 'collect',
		'proposer'		=> 'proposer',
		'second'		=> 'secondstatus',
		'usercollect'	=> 'usercollect',
		'sale'			=> 'sale',
		'collectstatus'	=> 'collectstatus',
		'trademark'		=> 'trademark',
		'statusnew'		=> 'statusnew',
		);
	/**
	 * 获取收藏商标
	 * @author	martin
	 * @since	2016/1/28
	 *
	 * @access	public
	 * @param	array	$param	参数
	 * @return	array	关注商标列表
	 */
	public function getPageListCollect($param , $search)
	{
		$r['eq']		= array('userId' => $param['user']);
		$r['page']		= $param['page'];
		$r['limit']		= $param['limit'];

		if(!empty($search['proposer'])){
			$r['eq']['pid'] = $search['proposer'];
		}
		if(!empty($search['class'])){
			$r['eq']['class'] = $search['class'];
		}
		if(!empty($search['status'])){
			$statuskey	= 'status'.$search['status'];
			$r['eq'][ $statuskey ] = 1;
		}
		if(!empty($search['keyword'])){
			$r['raw'] = "(name like '%".$search['keyword']."%' or trademark='".$search['keyword']."')";
		}
		$r['order']		= array('apply_date' => $search['regdate'],'tid' => 'asc');
		$r['group']		= array('trademark' => 'asc');
		$r['eq']['source']		= 2;
		$data			= $this->import('usercollect')->findAll($r);

		foreach($data['rows'] as $key => $item){
			$trademark						= $this->load('trademark')->details($item['trademark'],$item['class']);
			$data['rows'][$key]['trade']	= $trademark;
			//获取商标的出售信息
			$pricechange					= $this->load('changeprice')->getChangeInfo($item['trademark']);
			if($pricechange!= false){
				$data['rows'][$key]['sale']	= $pricechange;
			}else{
				$data['rows'][$key]['sale']	= $this->load('sale')->getSaleInfo($item['trademark']);
			}
			$data['rows'][$key]['second']	= $this->SecondStatusValue($item);
			
			$deal							= $this->load('userdeal')->getInfoOne($param['user'],$item['tid'],2);
			$data['rows'][$key]['isdeal']	= $deal == true ? 1 : 0;

			$data['rows'][$key]['butt']		= $this->button($deal, $item,$trademark);
		}
		return $data;
	}
	/**
	 * 获取收藏商标-交易
	 * @author	martin
	 * @since	2016/1/28
	 *
	 * @access	public
	 * @param	array	$param	参数
	 * @return	array	关注商标列表
	 */
	public function getPageListCollectTrade($param , $search)
	{
		// print_r($search);
		$r['eq']			= array('userId' => $param['user']);

		if(!empty($search['keyword'])){
			$r['raw'] 		= "(name like '%".$search['keyword']."%' or trademark='".$search['keyword']."')";
		}
		$r['order']			= array('apply_date' => $search['regdate'],'tid' => 'asc');
		$r['group']			= array('trademark' => 'asc');
		$r['eq']['source']	= 1;
		$r['page']			= 1;
		$r['limit']			= 10000;
		$data				= $this->import('usercollect')->findAll($r);
		if(empty($data['total']) ) { return array(); }
		$tidList			= arrayColumn($data['rows'],'tid');
		$s['in']			= array('tid' => $tidList);
		
		$s['page']			= $param['page'];
		$s['limit']			= $param['limit'];
		if(!empty($search['status'])){
			$s['eq'] 		= array('status' => $search['status'] );
		}
		
		$data				= $this->import('sale')->findAll($s);
		

		foreach($data['rows'] as &$item){
			$trademark		= $this->load('trademark')->details($item['number'],$item['class']);
			$item['trade']	= $trademark;
			//获取商标的出售信息
			$pricechange	= $this->load('changeprice')->getChangeInfo($item['number']);
			if($pricechange!= false){
				$item['sale']	= $pricechange;
			}else{
				$item['sale']	= $this->load('sale')->getSaleInfo($item['number']);
			}
			$secode				= $this->load('secondstatus')->getTwoDetails($item['number']);
			$item['second']	= $this->SecondStatusValue($secode);
		}
		return $data;
	}
	/**
	 * 获取商标总数 竞手-交易
	 * @author	Edmund
	 * @since	2016/1/28
	 *
	 * @access	public
	 * @param	array	$param	参数
	 * @return	array	销售商标详情
	 */		
	public function getSrouceCount($userId)
	{	
		$r['eq']		= array('userId' => $userId,'source' => 2);
		$r['group']		= array('trademark' => 'asc');
		$data['trade']	= $this->import('usercollect')->count($r);
		$data['trade'] 	= empty($data['trade']) ? 0 : $data['trade'];
		$data['sale']	= $this->getTradeCountagain($userId);
		$data['sale']   = empty($data['sale']) ? 0 : $data['sale'];
		return $data;
	}
	/**
	 * 获取交易商标总数
	 * @author	Edmund
	 * @since	2016/1/28
	 *
	 * @access	public
	 * @param	array	$param	参数
	 * @return	array	销售商标详情
	 */	
	public function getTradeCountagain($userId)
	{	
		$r['eq']			= array('userId' => $userId,'source' => 1);
		$r['group']			= array('trademark' => 'asc');
		$r['page']			= 1;
		$r['limit']			= 10000;
		$data				= $this->import('usercollect')->findAll($r);
		if(empty($data['total']) ) { return 0; }
		$tidList			= arrayColumn($data['rows'],'tid');
		
		$m['in']			= array('tid' => $tidList);
		$tradeCount			= $this->import('sale')->count($m);
		return $tradeCount;
	}
	
	/**
	 * 获取销售商标详情
	 * @author	Edmund
	 * @since	2016/1/28
	 *
	 * @access	public
	 * @param	array	$param	参数
	 * @return	array	销售商标详情
	 */	
	public function getTradeDetail($tid)
	{
		$rr['eq']['tid']		= $tid;
		$data			= $this->import('sale')->find($rr);
		return $data;
	}

	
	/**
	 * 判断商标的去异议、去撤三、去无效
	 * @author	martin
	 * @since	2016/4/7
	 *
	 * @access	public
	 * @param	array	$deal		是否已经提交业务
	 * @param	array	$second		商标二级信息
	 * @param	array	$trademark	商标信息
	 * @return	data	二级状态列表
	 */
	public function button($deal, $second, $trademark)
	{
		$butt['but1']		=  0;
		$butt['but2']		=  0;
		$butt['but3']		=  0;
		if($deal){
			return $butt;
		}
		//去异议
		if($second['status1'] == 1){
			$array					= array( 'tid' => $trademark['auto'], 'type' => 1 );
			$trialDate				= $this->load('noticenumber')->getNoticeType( $array );

			if($trialDate != ''){
				$trial				= strtotime('+3 month', strtotime($trialDate));
				if($trial > time()){
					$butt['but1']	= 1;
				}
			}
		}
		//去撤三
		if($second['status2'] == 1 && $second['status9'] == 0){
			$r['eq']				= array('tid' => $trademark['auto'], 'status' => '商标异议完成');
			$r['order']				= array('auto' => 'desc');
			$r['limit']				= 1;
			$statusnew				= $this->import('statusnew')->find($r);
			if($statusnew){
				$time				= $statusnew['date'];
			}elseif($trademark['reg_date'] != '0000-00-00'){
				$time				= $trademark['reg_date'];
			}else{
				$array				= array( 'tid' => $trademark['auto']);
				$raw				= 'type in (3, 12)';
				$time				= $this->load('noticenumber')->getNoticeType( $array, $raw);
			}
			if($time != '' && $time != '0000-00-00'){
				$newtime			= strtotime('+3 year', strtotime($time));
				if($newtime < time()){
					$butt['but2']	= 1;
				}
				$newtime2			= strtotime($time);
				if($newtime2 < time()){
					$butt['but3']	= 1;
				}
			}
		}
		return $butt;
	}

	/**
	 * 获取商标的二级状态的值
	 * @author	martin
	 * @since	2016/1/28
	 *
	 * @access	public
	 * @param	array	$trademark	商标信息
	 * @return	data	二级状态列表
	 */
	public function SecondStatusValue($trademark)
	{
		$newstatus	= array();
		$second		= C('SecondStatus');
		$data		= array();
		for($i = 1; $i < 29; $i ++){
			$k				= 'status' . $i;
			if($trademark[$k] == 1){
				$data[] = $second[$i];
			}
		}
		return $data;
	}
	/**
	 * 获取商标对应的的总数
	 * @author	martin
	 * @since	2016/1/28
	 *
	 * @access	public
	 * @param	array	$param	商标信息
	 * @return	data	商标总数
	 */
	public function getTradeCount($param)
	{

		$r['eq']	= $param;
		$r['group'] = array('trademark' => 'asc');
		$data		= $this->import('usercollect')->count($r);
		return $data;
	}

	
	/**
	 * 获取商标对应的的总数
	 * @author	martin
	 * @since	2016/1/28
	 *
	 * @access	public
	 * @param	array	$userId	用户ID
	 * @return	data	商标总数
	 */
	public function getTradeSale($userId)
	{

		$r['eq']	= array('userId' => $userId, 'source' => 1);
		$r['group'] = array('trademark' => 'asc');
		$r['limit'] = 10000;
		$data		= $this->import('collect')->findAll($r);
		$output		= arrayColumn($data['rows'], 'trademark');

		return $output;
	}
	/**
	 * 删除收藏商标
	 * @author	martin
	 * @since	2016/1/28
	 *
	 * @access	public
	 * @param	string	$userId	会员编号
	 * @return	object  返回业务对象
	 */
	public function deleteCollectById($param)
	{
		$r['eq']	= $param;
		$data		= $this->import('collect')->remove($r);
		return $data == true ? 1 : 0;
	}
	
	/**
	 * 获取关注的商标的所有申请人
	 * @author	martin
	 * @since	2016/2/18
	 *
	 * @access	public
	 * @param	string	$userId	会员编号
	 * @return	object  返回业务对象
	 */
	public function getProposerBuyCollect($user)
	{
		$r['eq']	= array('userId' => $user);
		$r['group'] = array('pid' => 'asc');
		$r['limit'] = 100;
		$data		= $this->import('collect')->find($r);
		$proposer	= array();
		foreach($data as $item){
			$proposer[] = $this->import('proposer')->get($item['proposer_id']);
		}
		return $proposer;
	}
	/**
	 * 获取关注的商标的销售状态
	 * @author	martin
	 * @since	2016/2/18
	 *
	 * @access	public
	 * @param	string	$userId	会员编号
	 * @return	object  返回业务对象
	 */
	public function getSaleStatusList($user)
	{
		$row = array(1=>"销售中",2=>"已下架",3=>"审核中");
		return $row;
	}
	
	/**
	 * 获取关注的商标的所有类型
	 * @author	martin
	 * @since	2016/2/18
	 *
	 * @access	public
	 * @param	string	$userId	会员编号
	 * @return	object  返回业务对象
	 */
	public function getClassBuyCollect($user)
	{
		$r['eq']	= array('userId' => $user);
		$r['group'] = array('class' => 'asc');
		$r['limit'] = 45;
		$data		= $this->import('collect')->find($r);
		$class		= array();
		foreach($data as $item){
			$str = $item['class'] < 10 ? '0' : '';
			$class[] = $str . $item['class'];
		}
		return $class;
	}
	/**
	 * 获取关注的商标的所有二级状态
	 * @author	martin
	 * @since	2016/2/18
	 *
	 * @access	public
	 * @param	string	$userId	会员编号
	 * @return	object  返回业务对象
	 */
	public function getStatusBuyCollect($user)
	{
		
		$output			= array();
		for($i = 1; $i < 29; $i++){
			$key			= 'status'.$i;
			$r['eq']		= array( 'userId' => $user, $key => 1 );
			$r['limit']		= 1;
			$data			= $this->import('usercollect')->count($r);
			if($data > 0){
				$output[]	= $key;
			}
		}
		return $output;
	}

	/**
	 * 收藏商标一年内的商标价格变动
	 * @author	martin
	 * @since	2016/2/19
	 *
	 * @access	public
	 * @param	string	$userId	会员编号
	 * @return	object  返回业务对象
	 */
	public function getPageStatusList($user, $search)
	{
		$r['eq']		= array('userId' => $user);
		$r['limit']		= $search['limit'];
		$r['page']		= $search['page'];

		if(!empty($search['proposer'])){
			$r['eq']['pid'] = $search['proposer'];
		}
		if(!empty($search['keyword'])){
			$raw[]		= "(name='".$search['keyword']."' or trademark='".$search['keyword']."')";
		}
		$raw[]			= "date>'".date('Y-m-d',strtotime("-1 year"))."' and status is not null";
		$r['raw']		= implode(' and ', $raw);
		$data			= $this->import('collectstatus')->findAll($r);
		foreach($data['rows'] as &$item){
			$trademark	= $this->load('trademark')->details($item['trademark'],$item['class']);
			$item['trade'] = $trademark;
		}
		return $data;
	}

	/**
	 * 收藏商标【根据商标单个收藏】
	 * @author	martin
	 * @since	2016/2/22
	 *
	 * @access	public
	 * @param	string	$data	页面提交的商标信息
	 * @param	string	$userId	会员编号
	 * @return	object  返回业务对象
	 */
	public function addCollectTrademark($data,$userId)
	{
		$trademark				= $this->load('trademark')->moreInfo($data['number']);
		if(empty($trademark)){
			return  array('type' => 2, 'mess' => '商标不存在');
		}
		$count					= $this->censusBrand($userId, '', $data['data']);
		if($count > 1999){
			return  array('type' => 2, 'mess' => '收藏失败，您收藏商标数量超过限制');
		}
		$bool					= $this->load( 'collect' )->addCollect($userId, $trademark['rows'], $data['source']);	
		if($bool > 0){
			return  array('type' => 1, 'mess' => '收藏成功');
		}else{
			return  array('type' => 2, 'mess' => '收藏失败');
		}
	}

	/**
	 * 删除收藏商标【根据商标单个收藏】
	 * @author	martin
	 * @since	2016/3/14
	 *
	 * @access	public
	 * @param	string	$data	页面提交的商标信息
	 * @param	string	$userId	会员编号
	 * @return	object  返回业务对象
	 */
	public function removeCollectTrademark($data,$userId)
	{
		$trademark				= $this->load('trademark')->moreInfo($data['number']);
		if(empty($trademark)){
			return  array('type' => 2, 'mess' => '商标不存在');
		}
		$i = 0;
		foreach($trademark['rows'] as $item){
			$r['eq'] = array(
				'userId'	=> $userId,
				'trademark'	=> $item['id'],
				'class'		=> $item['class'],
				'source'	=> $item['source'] == 2 ? 2 : 1,
				);
			$bool	= $this->import( 'collect' )->remove($r);
			$bool == true ? $i++ : '';
		}
		return $i;
	}
	/**
	 * 收藏申请人的商标【根据申请人批量收藏】
	 * @author	martin
	 * @since	2016/2/29
	 *
	 * @access	public
	 * @param	array	$data	页面提交的商标信息
	 * @param	int		$userId	会员编号
	 * @return	object  返回业务对象
	 */
	public function addCollectProposer( $data, $userId )
	{
		$proposer		= $this->import('proposer')->get($data['id']);
		if(empty($proposer)){
			return  array('typde' => 2, 'mess' => '申请人不存在');
		}
		$r['eq']		= array('proposer_id'=>$data['id']);
		$r['limit']		= 10000;
		$trademarkList	= $this->import('trademark')->findAll($r);
		
		$count			= $this->censusBrand($userId,'', $data['source']);
		$ProposerCount	= $this->getProposerCountByUserId($userId, $data['id'], $data['source']);

		if($trademarkList['total']  == $ProposerCount['total']){
			return  array('typde' => 1, 'mess' => '已经收藏');
		}
		if(($count + $trademarkList['total'] - $ProposerCount['total']) > 1999){
			return  array('type' => 2, 'mess' => '收藏失败，您收藏商标数量超过限制');
		}
		if($ProposerCount['total'] != 0){
			$collect	= arrayColumn($ProposerCount['rows'], 'tid');
			foreach($trademarkList['rows'] as $k => $item){
				if(in_array($item['auto'], $collect)){
					unset($trademarkList['rows'][$k]);
				}
			}
		}
		
		$info		= $this->load('collect')->addCollect($userId, $trademarkList['rows'], $data['source']);

		if($info > 0){
			return  array('typde' => 1, 'mess' => '成功收藏商标'.$info.'个');
		}else{
			return  array('typde' => 1, 'mess' => '已经收藏');
		}
	}

	/**
	 * 返回用户关注的商标信息[传入商标号，返回商标号对应关注信息]
	 * @author	martin
	 * @since	2016/2/29
	 *
	 * @access	public
	 * @param	int		$userId	会员编号
	 * @param	string	$trademark	商标编号
	 * @param	int		$class	商标类别
	 * @param	int		$source	收入来源
	 * @return	object  返回商标数据
	 */
	public function getUserCollect($userId, $trademark, $source)	
	{
		$source		= $source == 1 ? 1 : 2;
		$r['eq']	= array(
					'userId'	=> $userId,
					'source'	=> $source,
					);
		if( !is_array($trademark) ){
			$trademark	= array($trademark);
		}
		$r['group']	= array('trademark' => 'desc');
		$r['id']	= array('id' => 'desc');
		$r['in']	= array('trademark' => $trademark);
		$r['limit']	= 10000;
		$data		= $this->import('collect')->findAll( $r );
		if($data['total'] == 0){ return array();}
		$exits		= arrayColumn($data['rows'], 'trademark');

		$output		= array();
		foreach($trademark as $item){
			$output[$item]	= in_array($item, $exits) ? 1 : 0;
		}
		return $output;
	}
	
	/**
	 * 插入商标收藏信息
	 * @author	martin
	 * @since	2016/2/29
	 *
	 * @access	public
	 * @param	int		$userId	会员编号
	 * @param	array	$trademark	商标编号
	 * @param	array	$class	商标类别
	 * @return	object  返回商标数据
	 */
	public function addCollect($userId, $trademark, $source)
	{
		$i = 0;
		foreach( $trademark as $item ){
			$r['eq']				= array(
									'userId'	=> $userId,
									'source'	=> $source,
									'trademark'	=> $item['id'],
									'class'		=> $item['class'],
				);
			$info					= $this->import('collect')->count($r);
			if( $info > 0 ){ $i++; continue ; }
			$input	= array(
					'userId'		=> $userId,
					'tid'			=> $item['auto'],
					'trademark'		=> $item['id'],
					'class'			=> $item['class'],
					'name'			=> $item['trademark'],
					'proposer_id'	=> $item['proposer_id'],
					'pid'			=> $item['pid'],
					'apply_date'	=> $item['apply_date'],
					'source'		=> $source == 1 ? 1 : 2,
					'created'		=> time(),
					
					);
			$bool					= $this->import( 'collect' )->create( $input );
			if( $bool ) $i++;
		}

		return $i;
	}

	/**
	 * 获取用户关注是商标列表
	 * @author	martin
	 * @since	2016/2/29
	 *
	 * @access	public
	 * @param	int		$userId	会员编号
	 * @param	array	$data	页面参数
	 * @return	object  返回商标信息列表
	 */
	public function getUserCollectList($data, $userId)
	{
		$r['eq']		= array('userId' => $userId ,'source' => $data['source']);
		if( isset($data['num']) && intval($data['num']) > 1){
			$r['limit']	= $data['num'];
		}else{
			$r['limit']	= 1;
		}
		$r['group']		= array('trademark' => 'desc');
		$r['id']		= array('id' => 'desc');
		$data			= $this->import('collect')->findAll($r);
		$output			= array();
		foreach($data['rows'] as $item){
			$details	= $this->load('trademark')->details($item['trademark'], $item['class']);
			$output[]	= array(
						'tid'		=> $details['auto'],
						'number'	=> $item['trademark'],
						'class'		=> $item['class'],
						'name'		=> $details['trademark'],
						'group'		=> $details['group'],
						'goods'		=> $details['goods'],
						);
		}
		return $output;
	}

	/**
	 * 统计商标统计数
	 * @author	haydn
	 * @since	2016/3/02
	 *
	 * @access	public
	 * @param	int		$userId		会员编号
	 * @param	array	$trademark	商标编号
	 * @return	int  	$count		返回商标统计数
	 */
	public function censusBrand($userId,$trademark='',$source=0)
	{
		$r['eq']					= array('userId' => $userId);
		if($trademark!=''){
			$r['eq']['trademark']	= $trademark;
		}
		if($source != 0){
			$r['eq']['source']	= $source;
		}
		$r['order']					= array('trademark' => 'desc');
		$r['group']					= array('trademark' => 'desc');
		$count						= $this->import( 'collect' )->count( $r );
		return $count;
	}
	
	/**
	 * 获取申请人的统计个数【按申请人】
	 * @author	martin
	 * @since	2016/4/11
	 *
	 * @access	public
	 * @param	int		$userId		用户ID
	 * @param	array	$proposer	申请人ID
	 * @return	int  	$source		信息来源
	 */
	public function getProposerCountByUserId($userId, $proposer, $source)
	{
		$r['eq']	= array('userId'=>$userId, 'proposer_id'=> $proposer, 'source' => $source);
		$r['limit']	= 20000;
		$count		= $this->import('usercollect')->findAll($r);
		return $count;		
	}
}
?>