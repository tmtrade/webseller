<?
/**
 * 价格变动
 *
 * 价格变动增加，获取
 * 
 * @package	Module
 * @author	martin
 * @since	2016/3/1
 */
class ChangePriceModule extends AppModule
{
	/**
	 * 引用业务模型
	 */
	public $models = array(
		'price'		=> 'ChangePrice',
		'sale'		=> 'sale',
		
		);
	/**
	 * 添加价格变动信息
	 * @author	martin
	 * @since	2016/3/1
	 * 
	 * @access	public
	 * @param	string	$number		商标号	
	 * @param	string	$oldInfo	修改前的商标数据
	 * @param	string	$newInfo	修改后的商标数据
	 * @return	int
	 */
	public function addChange($number, $oldInfo, $newInfo)
	{	
		$keylist			= array('price', 'priceType','isOffprice','salePrice','salePriceDate', 'isSale', 'isLicense');
		$old				= array();
		$new				= array();
		foreach($keylist as $item){
			$old[ $item ]	= $oldInfo[ $item ];
			$new[ $item ]	= $newInfo[ $item ];
		}
		$diff				= array_diff_assoc($old, $new);

		if(empty($diff)){ return false; }
		$priceType			= array();
		if(isset($newInfo['isSale']) && $newInfo['isSale'] == 1){
			$priceType[]	= "出售";
		}
		if(isset($newInfo['isLicense']) && $newInfo['isLicense'] == 1){
			$priceType[]	= "许可";
		}
		$data['trademark']	= $number;
		$data['priceType']	= implode('、',$priceType);
		$data['oldInfo']	= var_export($old,true);
		$data['newInfo']	= var_export($new,true);
		$data['created']	= time();
		$bool				= $this->import('price')->create($data);
		return $bool == true ? true : false;
	}

	/**
	 * 添加价格变动信息
	 * @author	martin
	 * @since	2016/3/1
	 * 
	 * @access	public
	 * @param	string	$number		商标号	
	 * @return	int
	 */
	public function getChangeInfo($number){
		//查询商标是否是出售商标
		$s['eq']	= array('number'=>$number,'status'=>1);
		$sale		= $this->import('sale')->find($s);
		if($sale  == false)  return false;

		$r['eq']	= array('trademark' => $number);
		$r['limit']	= 1;
		$r['order']	= array('id' => 'desc');
		$data		= $this->import('price')->find($r);
		if($data == false) return false;
		$oldInfo	= eval("return (".$data['oldInfo'].");");
		$newInfo	= eval("return (".$data['newInfo'].");");
		$mess ='-';
		if($newInfo['priceType'] == 1){
			$newOffprice =$newInfo['salePrice'] >= 10000 ? (getFloatValue($newInfo['salePrice'] / 10000,2) . '万') : $newInfo['salePrice'];
			
			$oldSalePrice =$oldInfo['price'] >= 10000 ? (getFloatValue(($oldInfo['price'] * 1.1)/ 10000,2) . '万') : ($oldInfo['price'] == 0 ? '议价' : ($oldInfo['price'] * 1.1));
			$newSalePrice =$newInfo['price'] >= 10000 ? (getFloatValue(($newInfo['price']  * 1.1)/ 10000,2) . '万') : ($newInfo['price'] == 0 ? '议价' : ($newInfo['price']  * 1.1));

			if($newInfo['isOffprice'] == 1 && ($newInfo['salePriceDate'] ==0 ||$newInfo['salePriceDate'] > time())){//特价
				$mess = $newOffprice;
				if($newInfo['salePriceDate'] == 0){
					$mess .= '（不限时特价）';
				}else{
					$mess .= '（截至'.date('Y-m-d',$newInfo['salePriceDate']).'）';
				}
				$mess .= '&nbsp;<font color="#ff9d1f">'.date('Y-m-d',time()).'前出售价格为：'.$oldSalePrice .'</font>';  
			}elseif($newInfo['price'] != $oldInfo['price']) {//定价
				$mess = $newSalePrice.' &nbsp;<font color="#ff9d1f">'.date('Y-m-d',time()).'前出售价格为：'.$oldSalePrice.'</font>'; 
			}elseif($oldInfo['priceType'] == 2){
				$mess = $newSalePrice .' &nbsp;<font color="#ff9d1f">'.date('Y-m-d',time()).'前出售价格为：议价</font>'; 
			}
		}elseif($newInfo['priceType'] == 2){
			$mess = '议价 &nbsp;<font color="#ff9d1f">'.date('Y-m-d',time()).'前出售价格为：'.round($oldInfo['price']/10000,2) .'万</font>'; 
		}
		return array('price'=>$mess, 'priceType'=>$data['priceType']);

	}
}
?>