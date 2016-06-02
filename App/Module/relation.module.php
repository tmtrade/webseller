<?
/**
* 用户关联信息
* 
* @package	Model
* @author	void
* @since	2015-11-20
*/
class RelationModule extends AppModule
{
	/**
	* 引用业务模型
	*/
	public $models = array(
		'relation'		=> 'relation',
	);

	/**
	* 获取用户关联信息
	* @author  haydn
	* @since   2016-01-21
	* @param   int         $userId   账户id
	* @return  array       $array
	*/
	public function getRelationinfo( $userId )
	{
		$array = $this->import('relation')->getRelationInfo($userId);
		return $array;
	}
	/**
	* 获取顾问信息
	* @author  haydn
	* @since   2016-01-21
	* @param   int         $aid        网络id
	* @return  array       $staffInfo
	*/
	public function getStaffInfo( $aid )
	{
		$staffInfo = $this->importBi('crm')->getStaffInfo($aid);
		return $staffInfo;
	}
	
	/**
	* 获取用户的网路Id列表
	* @author  martin
	* @since   2016/3/2
	* @param   int         $aid        网络id
	* @return  array       $staffInfo
	*/
	public function getRelationList( $userId ,$relationId = array() )
	{
		$r['eq']	= array('userId'=>$userId);
		if( !empty($relationId) ){
			$r['in'] = array( 'relationId' => explode(',',$relationId) );
		}
		$r['limit'] = 10000;
		$info		= $this->import('relation')->findAll($r);
		$output		= array();
		foreach($info['rows'] as $item){
			$output [] = $item['relationId'];
		}
		return $output;
	}

	/**
	* 获取用户的网路Id列表
	* @author  martin
	* @since   2016/3/2
	* @param   int         $aid        网络id
	* @return  array       $staffInfo
	*/
	public function addRelation( $userId ,$relationId )
	{
		if(empty($userId) || empty($relationId) ) return false;

		$r			= array('userId'=>$userId, 'relationId'=>$relationId);
		$bool		= $this->import('relation')->create($r);
		return $bool;
	}
}
?>