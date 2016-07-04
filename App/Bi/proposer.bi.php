<?
/**
 * 申请人接口
 *
 * 查询申请人
 *
 * @package	Bi
 * @author	void
 * @since	2016-02-18
 */
class ProposerBi extends Bi
{
	/**
	 * 接口标识
	 */
	public $apiId = 10;


	/**
	 * 申请人查询
	 * @author	void
	 * @since	2016-02-27
	 *
	 * @access	public
	 * @param	string	$keyword		申请人名称
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
			'name'        => isset($keyword)     ? $keyword     : '',
			'address'     => isset($address)     ? $address     : '',
			'timeType'    => isset($timeType)    ? $timeType    : 0,
			'startTime'   => isset($startTime)   ? $startTime   : 0,
			'endTime'     => isset($endTime)     ? $endTime     : 0,
			'startNumber' => isset($startNumber) ? $startNumber : 0,
			'endNumber'   => isset($endNumber)   ? $endNumber   : 0,
			'num'         => isset($num)         ? $num         : 20,
			'page'        => isset($page)        ? $page        : 1,
			);
		$data = $this->invoke("proposer/search/", $param);
		return array(
			'total' => isset($data['total']) ? $data['total'] : 0,
			'rows'  => isset($data['rows'])  ? $data['rows']  : array(),
			);
	}
}
?>