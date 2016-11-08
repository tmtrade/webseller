<?php
define("SR", realpath(WebDir.'/Data/'));
#$ex = new Excel('\test.xlsx');

//$ex->insert('[A$]', array('F1'=>1,'F2'=>2,'F3'=>4,'F4'=>999, 'F5'=>10101));
//$ex->query("UPDATE [A$] SET [F1]=69");
//$x = $ex->first_array('select * from [网络来源-老客户$]');
//$x = $ex->getlist('[A$]','id',5,'','ORDER BY id ASC');

#$sheetName  = iconv('UTF-8','GBK','网络来源-老客户-小组');

//$x=$ex->fetch_array('select * from [网络来源-老客户$]',45);

#$x = $ex->getlist('['.$sheetName.'$]','','','','');

//$x = $ex->fetch_array('select * from ['.$sheetName.'$]',5);

#print_r($x);die;
/*
unset($x[0]);
//print_r($x);
foreach($x as $k=>$v){
    $value  = '';
    foreach($v as $key=>&$val){
        if($val!=''){
            $value  = $val;
        }
        if($val==''){
            //$val    = $value;
            $x[$k][$key]    = $value;
        }
    }
}
//print_r($x);
foreach($x[2] as $k=>$v){
    $name[$k]   = $x[1][$k].'_'.$x[2][$k].'_'.$x[3][$k];
}
$str    = '<table><tr>';
foreach( $name as $n){
    $str    .='<td>'.$n.'</td>';
}
$str    .= '</tr></table>';
echo $str;
*/
class Excel
{
	var $databasepath;//Excel文件路径
	protected $link;//链接
	function Excel($databasepath)
	{
		$this->databasepath	= $databasepath;
		$this->connect();
	}

	function connect()
	{
		$dbq = SR.'\\'.$this->databasepath;
		//debug($dbq);
		//$dbq = str_replace("\\", '/', $dbq);
		if(!file_exists($dbq)){
			exit('文件不存在');
		}
		$path = pathinfo($dbq);
		$defaultdir = $path['dirname'];
		$constr = "Driver={Microsoft Excel Driver (*.xls, *.xlsx, *.xlsm, *.xlsb)};Dbq={$dbq};Readonly=False;DefaultDir={$defaultdir};";
		//$constr = "Driver={Microsoft Excel Driver (*.xls)};DriverId=790;Dbq={$dbq};Readonly=False;DefaultDir={$defaultdir};";
		//debug($constr);
		$this->link = odbc_connect($constr, '', '', SQL_CUR_USE_ODBC);
		if($this->link){
			//echo "恭喜你,数据库连接成功!";
		}else{
			debug("数据库连接失败!");
		}
		return $this->link;
	}

	function query($sql)
	{
	   //echo $sql.'<br />';
       //var_dump($this->link);
	   return odbc_exec($this->link, $sql);
	}

	function first_array($sql)
	{
		return odbc_fetch_array($this->query($sql));
	}
    function fetch_array($sql,$i)
    {
        return odbc_fetch_array($this->query($sql),$i);
    }

	function fetch_row($query)
	{
		return odbc_fetch_row($query);
	}

	function total_num($sql)//取得记录总数
	{
		return odbc_num_rows($this->query($sql));
	}

	function close()//关闭数据库连接函数
	{
		odbc_close($this->link);
	}

	function insert($table, $data)//插入记录函数
	{
		$fields = $values = '';
		foreach ($data as $k => $v)
		{
			$fields[] = $k;
			$values[] = is_numeric($v) ? $v : "'{$v}'";
		}
		$fields = implode(",", $fields);
		$values = implode(",", $values);
		$sql="INSERT INTO ".$table." (".$fields.") VALUES (".$values.")";
		//debug($sql);
		$this->query($sql);
	}

	function getinfo($table,$field,$id,$colnum)//取得当条记录详细信息
	{
		$sql="SELECT * FROM ".$table." WHERE ".$field."=".$id."";
		$query=$this->query($sql);
        //VAR_DUMP($query);DIE;
		if($this->fetch_row($query))
		{
			for ($i=1;$i<$colnum;$i++)
			{
				$info[$i]=odbc_result($query,$i);
			}
		}
		return $info;
	}
    

	function getlist($table,$field,$colnum,$condition,$sort="ORDER BY id DESC")//取得记录列表
	{
		$sql="SELECT TOP 100 * FROM ".$table." ".$condition." ".$sort;
		$query=$this->query($sql);
		$i=0;
		while ($this->fetch_row($query))
		{
			//$recordlist[$i]=$this->getinfo($table,$field,odbc_result($query,1),$colnum);
            $recordlist[$i]=$this->fetch_array($sql,$i);            
			$i++;
		}
		return $recordlist;
	}

	function getfieldlist($table,$field,$fieldnum,$condition="",$sort="")//取得记录列表
	{
		$sql="SELECT ".$field." FROM ".$table." ".$condition." ".$sort;
		$query=$this->query($sql);
		$i=0;
		while ($this->fetch_row($query))
		{
			for ($j=0;$j<$fieldnum;$j++)
			{
				$info[$j]=odbc_result($query,$j+1);
			}
			$rdlist[$i]=$info;
			$i++;
		}
		return $rdlist;
	}

	function updateinfo($table,$field,$id,$set)//更新记录
	{
		$sql="UPDATE ".$table." SET ".$set." WHERE ".$field."=".$id;
		$this->query($sql);
	}

	function deleteinfo($table,$field,$id)//删除记录
	{
		$sql="DELETE FROM ".$table." WHERE ".$field."=".$id;
		$this->query($sql);
	}

	function deleterecord($table,$condition)//删除指定条件的记录
	{
		$sql="DELETE FROM ".$table." WHERE ".$condition;
		$this->query($sql);
	}

	function getcondrecord($table,$condition="")// 取得指定条件的记录数
	{
		$sql="SELECT COUNT(*) AS num FROM ".$table." ".$condition;
		$query=$this->query($sql);
		$this->fetch_row($query);
		$num=odbc_result($query,1);
		return $num;
	}
}

function debug($s){
	echo "<pre>";
	print_r($s);
	exit;
}