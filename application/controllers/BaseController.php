<?php
class BaseController extends Zend_Controller_Action{
    //这是一个能初始化数据库的控制器，我们可以通过集成这个控制器来省略初始化这一项
    

    public function init(){
        //下面就是对数据库的初始化
        $url = constant ( "APPLICATION_PATH" ) . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'application.ini';
        $dbconfig = new Zend_Config_Ini ( $url, "mysql" );
        $db = Zend_Db::factory ( $dbconfig->db );
        $db->query ( 'SET NAMES GB2312' );
        Zend_Db_Table::setDefaultAdapter ( $db );
    }
    public function toshow($news,$toback=null){//这是跳转到全局控制器显示的 
        
        //$new 是要提示的信息  $toback是我们跳转的页面
        $this->view->news=$news;
        $this->view->toback=$toback;
        $this->forward("show","globals");  
    }
}

//下面是我们需要的公共函数

//这是筛选条件
function towhere($typeid,$bookid,$chapterid,$class){
	$where = '';
	$chaptemodel=new dede_chapter();
	$db=$chaptemodel->getAdapter();
	if ($typeid  != null)
		$where = $db->quoteInto("q.typeid=?", $typeid);//防止sql注入
	// 获取题目类型
	if ($class!=null) {
		$where.=$db->quoteInto(" AND q.class=?", $class);
	}
	if($bookid!=null){
		$cares=$chaptemodel->fetchAll("bookid=".$bookid)->toArray();
		$inarray=array();
		for($i=0;$i<count($cares);$i++){
			$inarray[$i]=$cares[$i]["chapterid"];
		}//通过for循环将数据  导入到in  数组中

		if ($chapterid != null)
			$where .= $db->quoteInto("AND q.chapterid =?", $chapterid);
		else if(count($cares)>0) {
			$where.=$db->quoteInto("AND q.chapterid in(?)", $inarray);
		}
		else {
			$where.='';
		}
	}
	if($where==''){
		$where=1;
	}
	return $where;
}

function tourl($typeid,$bookid,$chapterid,$class,$paperid){
        $url="?qtype=".$typeid."&book=".$bookid."&chapter=".$chapterid."&class=".$class;
	return $url;
}

function showtip($pagenow=0,$count=0,$toback){
	$pagesize=ceil($count/10);//总共分几页
	$tip="<a href='./".$toback."&pagenow=0'>首页</a>&nbsp;&nbsp;";
	if($pagenow>0){
		$tip.="<a href='./".$toback."&pagenow=".($pagenow-1)."'>上一页</a>&nbsp;&nbsp;";
	}
	if(($pagenow+1)<$pagesize){
		$tip.="<a href='./".$toback."&pagenow=".($pagenow+1)."'>下一页</a>&nbsp;&nbsp;";
	}
	$tip.="共".$pagesize."页/".$count."条";
	return $tip;
}
