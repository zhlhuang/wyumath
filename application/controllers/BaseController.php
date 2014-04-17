<?php
class BaseController extends Zend_Controller_Action{
    //����һ���ܳ�ʼ�����ݿ�Ŀ����������ǿ���ͨ�����������������ʡ�Գ�ʼ����һ��
    

    public function init(){
        //������Ƕ����ݿ�ĳ�ʼ��
        $url = constant ( "APPLICATION_PATH" ) . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'application.ini';
        $dbconfig = new Zend_Config_Ini ( $url, "mysql" );
        $db = Zend_Db::factory ( $dbconfig->db );
        $db->query ( 'SET NAMES GB2312' );
        Zend_Db_Table::setDefaultAdapter ( $db );
    }
    public function toshow($news,$toback=null){//������ת��ȫ�ֿ�������ʾ�� 
        
        //$new ��Ҫ��ʾ����Ϣ  $toback��������ת��ҳ��
        $this->view->news=$news;
        $this->view->toback=$toback;
        $this->forward("show","globals");  
    }
}

//������������Ҫ�Ĺ�������

//����ɸѡ����
function towhere($typeid,$bookid,$chapterid,$class){
	$where = '';
	$chaptemodel=new dede_chapter();
	$db=$chaptemodel->getAdapter();
	if ($typeid  != null)
		$where = $db->quoteInto("q.typeid=?", $typeid);//��ֹsqlע��
	// ��ȡ��Ŀ����
	if ($class!=null) {
		$where.=$db->quoteInto(" AND q.class=?", $class);
	}
	if($bookid!=null){
		$cares=$chaptemodel->fetchAll("bookid=".$bookid)->toArray();
		$inarray=array();
		for($i=0;$i<count($cares);$i++){
			$inarray[$i]=$cares[$i]["chapterid"];
		}//ͨ��forѭ��������  ���뵽in  ������

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
	$pagesize=ceil($count/10);//�ܹ��ּ�ҳ
	$tip="<a href='./".$toback."&pagenow=0'>��ҳ</a>&nbsp;&nbsp;";
	if($pagenow>0){
		$tip.="<a href='./".$toback."&pagenow=".($pagenow-1)."'>��һҳ</a>&nbsp;&nbsp;";
	}
	if(($pagenow+1)<$pagesize){
		$tip.="<a href='./".$toback."&pagenow=".($pagenow+1)."'>��һҳ</a>&nbsp;&nbsp;";
	}
	$tip.="��".$pagesize."ҳ/".$count."��";
	return $tip;
}
