<?
/**
 * 商标查询接口
 *
 * 商标近似查询、商标详情
 * 商标名称查询（状态查询）、商标号查询（状态查询）、获取申请人商标列表（状态查询）
 *
 * @package	Bi
 * @author	void
 * @since	2016-02-19
 */
class TrademarkBi extends Bi
{
	/**
	 * 接口标识
	 */
	public $apiId = 10;

	/**
	 * 商标近似查询
	 * 
	 * @author	void
	 * @since	2016-02-23
	 *
	 * @access	public
	 * @param	string	$keyword		商标名
	 * @param	string	$classId		类别（多个用逗号分隔）
	 * @param	string	$groupCode		群组（多个用逗号分隔）
	 * @param	int		$timeType		时间类型（0未知、1申请日期、2注册日期、3有效日期）
	 * @param	int		$startTime		开始时间（时间戳）
	 * @param	int		$endTime		结束时间（时间戳）
	 * @param	int		$startNumber	开始公告期号
	 * @param	int		$endNumber		结束公告期号
	 * @param	int		$num			每页多少条	
	 * @param	int		$page			当前页码
	 * @return	array
	 */
	public function search($reqParam)
	{
		extract($reqParam);
		$param = array(
			'keyword'     => isset($keyword)     ? $keyword     : '',
			'classId'     => isset($classId)     ? $classId     : 0,
			'groupCode'   => isset($groupCode)   ? $groupCode   : '',
			'timeType'    => isset($timeType)    ? $timeType    : 0,
			'startTime'   => isset($startTime)   ? $startTime   : 0,
			'endTime'     => isset($endTime)     ? $endTime     : 0,
			'startNumber' => isset($startNumber) ? $startNumber : 0,
			'endNumber'   => isset($endNumber)   ? $endNumber   : 0,
			'num'         => isset($num)         ? $num         : 20,
			'page'        => isset($page)        ? $page        : 1,
			);
		
		$data = $this->invoke("Trademark/search/", $param);
		return array(
			'classCount'  => isset($data['classCount'])  ? $data['classCount']  : array(),
			'weightCount' => isset($data['weightCount']) ? $data['weightCount'] : array(),
			'total'       => isset($data['total'])       ? $data['total']       : 0,
			'rows'        => isset($data['rows'])        ? $data['rows']        : array(),
			);
	}

	/**
	 * 商标详情
	 * 
	 * @author	void
	 * @since	2016-02-19
	 *
	 * @access	public
	 * @param	int		$id		商标id
	 * @return	array
	 */
	public function getDetail($id)
	{
		return $this->invoke("Trademark/getDetail/", array('id' => $id));
	}

	/**
	 * 商标名称查询（状态查询）
	 * @author	void
	 * @since	2016-02-19
	 *
	 * @access	public
	 * @param	string	$keyword		商标名
	 * @param	string	$classId		类别（多个用逗号分隔）
	 * @param	int		$timeType		时间类型（0未知、1申请日期、2注册日期、3有效日期）
	 * @param	int		$startTime		开始时间（时间戳）
	 * @param	int		$endTime		结束时间（时间戳）
	 * @param	int		$startNumber	开始公告期号
	 * @param	int		$endNumber		结束公告期号
	 * @param	int		$statusId		状态id（1-28）
	 * @param	int		$order			排序（1为申请日期升序、2为降序）
	 * @param	int		$num			每页多少条	
	 * @param	int		$page			当前页码
	 * @return	array
	 */
	public function nameSearch($reqParam)
	{
		extract($reqParam);
		$param = array(
			'name'        => isset($keyword)     ? $keyword     : '',
			'classId'     => isset($classId)     ? $classId     : 0,
			'timeType'    => isset($timeType)    ? $timeType    : 0,
			'startTime'   => isset($startTime)   ? $startTime   : 0,
			'endTime'     => isset($endTime)     ? $endTime     : 0,
			'startNumber' => isset($startNumber) ? $startNumber : 0,
			'endNumber'   => isset($endNumber)   ? $endNumber   : 0,
			'statusId'    => isset($statusId)    ? $statusId    : 0,
			'order'       => isset($order)       ? $order       : 1,
			'num'         => isset($num)         ? $num         : 20,
			'page'        => isset($page)        ? $page        : 1,
			);
		
		$data = $this->invoke("StatusQuery/nameSearch/", $param);

		return array(
			'classCount'  => isset($data['classCount'])  ? $data['classCount']  : array(),
			'statusCount' => isset($data['statusCount']) ? $data['statusCount'] : array(),
			'total'       => isset($data['total'])       ? $data['total']       : 0,
			'rows'        => isset($data['rows'])        ? $data['rows']        : array(),
			);
	}

	/**
	 * 申请人商标查询（状态查询，按申请人ID查询）
	 * @author	void
	 * @since	2016-03-03
	 *
	 * @access	public
	 * @param	int		$proposerId	申请人id
	 * @param	int		$classId	商标类别id
	 * @param	int		$statusId	状态id
	 * @param	int		$order		排序
	 * @param	int		$num		返回条数
	 * @param	int		$page		页码
	 * @return	array
	 */
	public function proposerTmsearch($reqParam)
	{
		extract($reqParam);
		$param = array(
			'proposerId' => $proposerId,
			'classId'    => $classId,
			'statusId'   => $statusId,
			'order'      => $order,
			'num'        => $num,
			'page'       => $page,
			);
		
		$data = $this->invoke("StatusQuery/getProposerTrademarkList/", $param);

		return array(
			'classCount'  => isset($data['classCount'])  ? $data['classCount']  : array(),
			'statusCount' => isset($data['statusCount']) ? $data['statusCount'] : array(),
			'total'       => isset($data['total'])       ? $data['total']       : 0,
			'rows'        => isset($data['rows'])        ? $data['rows']        : array(),
			);
	}

	/**
	 * 商标号查询（状态查询）
	 * @author	void
	 * @since	2016-03-03
	 *
	 * @access	public
	 * @param	string	$keyword	商标号
	 * @param	int		$num		返回条数
	 * @param	int		$page		页码
	 * @return	array
	 */
	public function codeSearch($reqParam)
	{
		extract($reqParam);
		$param = array(
			'code' => $keyword,
			'num'  => $num,
			'page' => $page,
			);
		
		$data = $this->invoke("StatusQuery/codeSearch/", $param);
		
		return array(
			'classCount'  => isset($data['classCount'])  ? $data['classCount']  : array(),
			'statusCount' => isset($data['statusCount']) ? $data['statusCount'] : array(),
			'total'       => isset($data['total'])       ? $data['total']       : 0,
			'rows'        => isset($data['rows'])        ? $data['rows']        : array(),
			);
	}


	/**
	 * 商标号查询（状态查询）
	 * @author	void
	 * @since	2016-03-03
	 *
	 * @access	public
	 * @param	string	$keyword	商标号
	 * @param	int		$num		返回条数
	 * @param	int		$page		页码
	 * @return	array
	 */
	public function getTid($trademarkId, $class)
	{
		$param = array(
			'trademarkId'	=> $trademarkId,
			'class'			=> $class,
			);
		$data = $this->invoke("Trademark/getTidByCode/", $param);
		return $data;
	}
}
?>