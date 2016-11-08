<?
/**
 * 关键字、搜索地址
 * 
 * 查询、创建
 *
 * @package	Module
 * @author	Far
 * @since	2016-06-28
 */
class KeywordModule extends AppModule
{
	
    /**
     * 引用业务模型
     */
    public $models = array(
        'kwcount'   => 'keywordCount',
    );
	
    /**
     * 获取关键字ID
     * 
     * @author  Far
     * @since   2016-06-28
     */
    public function getKeywordRanking()
    {
	$t = time()-2592000;//获取最近一月的数据
        $r['eq']    = array('type'=>1);
	$r['raw'] = ' date>='.$t;
        $r['col']   = array('keyword','count(1) as c');
        $r['group'] = array('keyword' => 'asc');
	$r['order'] = array('c'=>'desc');
	$r['limit'] = 10;
        $res = $this->import('kwcount')->find($r);
	return $res;
    }

}
?>