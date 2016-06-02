<?
/**
 * 申请人
 * 
 * @package	Module
 * @author	martin
 * @since	2016/1/28
 */
class MytradeModule extends AppModule
{
	public $models = array(
        'mytrademark'   => 'mytrademark',
        'mytradestatus' => 'mytradestatus',
		'proposer'		=> 'proposer',
		'second'		=> 'secondstatus',
		'sale'			=> 'sale',
		'newtrade'		=> 'newtrade',
		'trademark'		=> 'trademark',
		'userdeal'		=> 'userdeal',
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
	public function getPageListTrade($param , $search)
	{
		$r['eq']		= array('userId' => $param['user']);
		$r['page']		= $param['page'];
		$r['limit']		= $param['limit'];
		$raw[]			= "tid is not null and proposer_id !=0 ";

		
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
			$raw[]		= "(trademark_id='".$search['keyword']."' or trademark like '%".$search['keyword']."%')";
		}
		if(!empty($search['raw'])){
			$raw[]		= $search['raw'];
		}
		$r['raw']		= "(".implode(' and ', $raw).")";
		$r['order']		= array('sortDate' => 'desc', 'tid'=>'asc');
		$data			= $this->import('mytrademark')->findAll($r);
		
		$saleList		= $this->load('salecontact')->getSellBrandIds($param['user']);
		foreach($data['rows'] as $key => $item){
			$trademark						= $this->load('trademark')->details($item['trademark_id'],$item['class']);
			$data['rows'][$key]['trade']	= $trademark;
			$data['rows'][$key]['sale']		= in_array($item['trademark_id'],$saleList) == false ? 0 : 1;
			//$data['rows'][$key]['sale']		= $this->load('sale')->getSaleInfo($item['trademark_id']);
			$data['rows'][$key]['second']	= $this->SecondStatusValue($item);
			$data['rows'][$key]['isNew']	= $this->load('newtrade')->findOne($param['user'], $item['tid']);
		}
		return $data;
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
	 * 获取关注的商标的所有类型
	 * @author	martin
	 * @since	2016/2/18
	 *
	 * @access	public
	 * @param	string	$userId	会员编号
	 * @return	object  返回业务对象
	 */
	public function getClassList($user)
	{
		$r['eq']	= array('userId' => $user);
		$r['group'] = array('class' => 'asc');
		$r['raw']	= "tid is not null and  class !=0";
		$r['limit'] = 45;
		$data		= $this->import('mytrademark')->find($r);
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
	public function getStatusList($user)
	{
		$output			= array();
		for($i = 1; $i < 29; $i++){
			$key			= 'status'.$i;
			$r['eq']		= array( 'userId' => $user, $key => 1 );
			$data			= $this->import('mytrademark')->count($r);
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
		$data			= $this->import('mytradestatus')->findAll($r);
		foreach($data['rows'] as &$item){
			$trademark	= $this->load('trademark')->details($item['trademark'],$item['class']);
			$item['trade'] = $trademark;
		}
		return $data;
	}

	
	/**
	 * 我的商标总数
	 * @author	martin
	 * @since	2016/3/7
	 *
	 * @access	public
	 * @param	string	$userId	会员编号
	 * @return	object  返回业务对象
	 */
	public function getMytradeCount( $param, $raw = '')
	{
		$r['eq']		= $param;
		if($raw!=''){
			$r['raw']	= $raw;
		}
		$data			= $this->import('mytrademark')->count($r);
		return $data;
	}

	/**
	 * 导出excel
	 * @author	martin
	 * @since	2016/3/9
	 *
	 * @access	public
	 * @param	string	$data	数据包
	 * @return	null  
	 */
	public function exceltable($data,$title)
	{
		$objEndDate	= $this->load('noticenumber')->getNewDate();
		$count = count($data);
		$content	= array();
		foreach( $data as $k => $v ){
			
			$applyDate	= $v['trade']['apply_date']	== '0000-00-00' ? '-' : $v['trade']['apply_date'];
			$validStart	= $v['trade']['reg_date']	== '0000-00-00' ? '-' : $v['trade']['reg_date'];
			$validEnd	= $v['trade']['valid_end']	== '0000-00-00' ? '-' : $v['trade']['valid_end'];
			$trialDate	= $v['trade']['trial_date']	== '0000-00-00' ? '-' : $v['trade']['trial_date'];

			$content[] = array(
						($k + 1),
						$v['trade']['id'],
						$v['trade']['class'],
						$v['trade']['trademark'],
						$v['trade']['proposerName'],
						$applyDate,
						$trialDate,
						$validStart,
						$validEnd,
						$v['trade']['goods'],
						$v['trade']['newstatus'],
			);
		}
		$array['content']		= $content;
		$array['filename'] 		= $title.'-所有商标';
		$array['templateid']	= '2';
		$array['specify']		= array('C2' => '数据截止时间：' . $objEndDate . '  导出时间：' . date('Y/m/d', time()),'A3' => "【".$title."】——商标总表（共计" .$count  ."件）");
		return $this->htmliframe($array);
	}

	 
	/**
	 * 生成html页面内容
	 * @author	martin
	 * @since	2016/3/10
	 *
	 * @access	public
	 * @param	string	$http	数据包
	 * @return	string	页面输出内容  
	 */
	public function htmliframe($array)
	{
		if(empty($array['content'] )) { $array['content'][0] = array();}
		$http	= serialize($array);
		excelForm($http);
	}
	 
	/**
	 * 导出excel
	 * @author	martin
	 * @since	2016/3/9
	 *
	 * @access	public
	 * @param	string	$data	数据包
	 * @return	null  
	 */
	public function exceltablelog($data,$title)
	{
		$objEndDate	= $this->load('noticenumber')->getNewDate();
		$count = count($data);
		$content	= array();
		foreach( $data as $k => $v ){
			$statueDate	= $v['date'] == '0000-00-00' ? '-' : $v['date'];

			$content[] = array(
						($k + 1),
						$v['trade']['id'],
						$v['trade']['class'],
						$v['trade']['trademark'],
						$v['trade']['proposerName'],
						$statueDate,
						$v['status'],
						$v['trade']['goods'],
			);
		}
		$array['content']		= $content;
		$array['filename'] 		= $title.'-最新动态';
		$array['templateid']	= '3';
		$array['specify']		= array('C2' => '数据截止时间：' . $objEndDate . '  导出时间：' . date('Y/m/d', time()),'A3' => "【".$title."】——最新动态（共计" .$count  ."件）");
		return $this->htmliframe($array);
	}

	/**
	 * 插入办理业务到分配系统
	 * 同时插入记录表
	 * @author	martin
	 * @since	2016/4/6
	 *
	 * @access	public
	 * @param	string	$userId	用户ID
	 * @param	string	$search	数据包
	 * @return	null  
	 */
	public function addNetwork($userId, $search)
	{	
		$search['tid']		= explode(',',rtrim($search['tid'],","));
		$r['in']			= array('auto' => $search['tid']);
		$r['limit']			= 1000;
		$trademark			= $this->import('trademark')->findAll($r);
		$tradeinfo			= array();
		if( !empty($search['name']) ){
			$tradeinfo[]	= '联系人：' . $search['name'];
		}
		if( !empty($search['phone']) ){
			$tradeinfo[]	= '联系电话：' . $search['phone'];
		}
		if( !empty($search['usertext']) ){
			$tradeinfo[]	= '办理商标' . $search['usertext'].'业务';
		}else{
			$tradeinfo[]	= '办理商标业务';
		}
		foreach($trademark['rows'] as $item){
			$tradeinfo[]	= '商标名：'.$item['trademark'].';商标号：'.$item['id'].";第".$item['class']."类";
		}
		$input		= array(
					'type'		=> 1,
					'referer'	=> '',
					'source'	=> 0,
					'name'		=> $search['name'],
					'pttype'	=> '',
					'tel'		=> $search['phone'],
					'remarks'	=> implode("；", $tradeinfo),
		);
		$output				= $this->load('network')->networkJoin($input);
		if($output['code'] == 1 && !empty($output['data']['id'])){
			$bool = $this->load('relation')->addRelation($userId,$output['data']['id'] );


			foreach($trademark['rows'] as $item){
				$deal = array(
					'userId'	=> $userId,
					'tid'		=> $item['auto'],
					'number'	=> $item['id'],
					'class'		=> $item['class'],
					'created'	=> time(),
					'source'	=> $search['source'],
					);
				$this->load('userdeal')->add($deal);
			}
		}else{
			$bool = false;
		}
		return $bool;
	}

	/**
	 * 获取是否办理业务列表
	 * 
	 * @author	martin
	 * @since	2016/4/6
	 *
	 * @access	public
	 * @param	string	$data	数据包
	 * @return	null  
	 */
	public function getDealList($data,$userId)
	{
		foreach($data as &$item){
			$bool			= $this->load('userdeal')->getInfoOne($userId,$item['trade']['auto'],1);
			$item['isdeal'] = $bool == true ? 1 : 0;
		}
		return $data;
	}
	/**
	 * 获取已处理的商标案件总数
	 * 
	 * @author	martin
	 * @since	2016/4/7
	 *
	 * @access	public
	 * @param	string	$userId 用户ID
	 * @param	string	$userId	数据包
	 * @return	null  
	 */
	public function getDealCount($userId)
	{
		$r['eq']		= array('userId'=> $userId, 'source'=>1);
		$r['group']		= array('tid'=> 'desc');
		$r['limit']		= 100000;
		$data			= $this->import('userdeal')->findAll($r);
		if($data['total'] > 0) {
			$list		= arrayColumn($data['rows'],'tid');
			$q['in']	= array('tid'=>$list);
			$q['eq']	= array('userId'=>$userId);
			$total		= $this->import('mytrademark')->count($q);
			return $total;
		}else{
			return 0;
		}
	}

}
?>