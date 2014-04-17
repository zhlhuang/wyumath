<?php
require APPLICATION_PATH."/controllers/BaseController.php";
require_once APPLICATION_PATH.'/models/dede_book.php';
require_once APPLICATION_PATH.'/models/dede_chapter.php';
require_once APPLICATION_PATH.'/models/dede_qtype.php';
require_once APPLICATION_PATH.'/models/dede_question.php';
require_once APPLICATION_PATH.'/models/dede_selectanswer.php';
require_once APPLICATION_PATH.'/models/dede_fillanswer.php';
class TestController extends BaseController
{

    public function indexAction()
    { 

        $qtypemodel=new dede_qtype();
        $this->view->restype=$qtypemodel->fetchAll()->toArray();
    }
    
    public function chkAction(){
    
    	$flag='';
    	//���������Ǽ��뵽question���е�
    	$typeid=1;
    	$class=$this->getRequest()->getParam("class");
    	$mark=$this->getRequest()->getParam("mark");
    	$chapterid=$this->getRequest()->getParam("chapter");
    	if(($content=$this->getRequest()->getParam("content"))==''){
    		echo "11";
    		$this->toshow("��Ŀ���ݲ���Ϊ��");
    	}else{
    
    		$questionmodel=new dede_question();
    		//���溯���ǽ������װ�����ݿ��е�model��
    		$flag=$questionmodel->addquestion($typeid, $class, $mark, $chapterid, $content);
    	}
    	if($flag>0){//�����Ŀ¼��ɹ����ſ���¼���
    		//������answer�������
    		$answerA=$this->getRequest()->getParam("A");
    		$answerB=$this->getRequest()->getParam("B");
    		$answerC=$this->getRequest()->getParam("C");
    		$answerD=$this->getRequest()->getParam("D");
    		$answerR=$this->getRequest()->getParam("answerR");
    
    		$answermodel=new dede_selectanswer();
    		$flag=$answermodel->addselectanswer($flag, $answerA, $answerB, $answerC, $answerD, $answerR);
    		if($flag>0)
    		{
    			$this->toshow("¼��ɹ�");
    		}
    		else {
    			$this->toshow("¼��ʧ��");
    		}   
    	}
    }
  
    public function addbookAction(){
    }
    
    public function addbookchkAction(){
    	//�������鱾��ӵ���֤����
    	$book= $this->getRequest()->getParam("book");
    
    	$bookmodel=new dede_book();
    	$bookarray=array(
    			"bookname"=>$book
    	);
    	$res=$bookmodel->insert($bookarray);
    	if($res>0){
    		$this->toshow("��ӿα��ɹ�idΪ��".$res);
    	}else{
    		$this->toshow("���ʧ��");
    	}
    }
    
    public function addchapterAction(){
    	$bookmodel=new dede_book();
    	$this->view->resbook=$bookmodel->fetchAll()->toArray();
    }
    
    public function addchapterchkAction(){
    	$bookid=$this->getRequest()->getParam("book")."</br>";
    	$chaptername=$this->getRequest()->getParam("chaptername");
    
    	$chaptermodel=new dede_chapter();
    	$res=$chaptermodel->addchapter($chaptername, $bookid);
    	if($res>0){
    		$this->toshow("����½ڳɹ�idΪ��".$res);
    	}else{
    		$this->toshow("���ʧ��");
    	}
    }
    
    public function readAction(){
    	//�������ǿ��Բ鿴��Ŀ
    	$questionmodel=new dede_question();
    	$db=$questionmodel->getAdapter();
    
    	$typeid = $this->getRequest()->getParam("qtype");
    	$bookid=$this->getRequest()->getParam("book");
    	$chapterid = $this->getRequest()->getParam("chapter");
    	$class=$this->getRequest()->getParam("class");
    	//���滹�ǲ鿴ɸѡ������
    	$where=towhere($typeid, $bookid, $chapterid,$class);
    	echo $where;
    	echo $url=tourl($typeid, $bookid, $chapterid, $class);
    
    
    	//�����Ƿ�ҳ����
    	$pagenow=$this->getRequest()->getParam("pagenow");
    	if($pagenow==null){
    		$pagenow=0;//����ǵ�ǰҳ��
    	}
    	$limit=($pagenow*10).",10";
    	 
    	 
    	$sql="SELECT a.answerA, a.answerC, a.answerB, a.answerD,a.answerR,
                    a.id,q.typeid,q.content FROM dede_selectanswer AS a Inner Join dede_question AS q ON a.id = q.id
                    WHERE ".$where;
    	$res1= $db->query($sql." LIMIT ".$limit)->fetchAll();
    
    	//�����ҳ��
    	$res2= $db->query($sql)->fetchAll();
    	$this->view->tip=showtip($pagenow,count($res2),"read".$url);
    	$this->view->res1=$res1;
    }
    
    public function deleAction(){
    	//����ɾ��������
    	$id= $this->getRequest()->getParam("id");
    	$typeid=$this->getRequest()->getParam("typeid");
    	$flag=-1;//������¼�Ƿ���Գɹ�ɾ��
    	$toback='';//��¼��תҳ��
    	 
    	$questionmodel=new dede_question();
    
    	$selectanswermodel=new dede_selectanswer();//ѡ����ģ��
    	$fillanswermodel=new dede_fillanswer();//�����ģ��
    	$res= $questionmodel->deletequestion($id);
    	if ($res==0) {
    		if ($typeid==1) {
    			$selectanswermodel->deleteanswer($id);
    			$flag=1;
    			$toback="read";
    		}elseif ($typeid==2){
    			$fillanswermodel->deletequestion($id);
    			$flag=2;
    			$toback="readfill";
    		}
    		 
    	}
    	if($flag>0){
    		$this->toshow("ɾ���ɹ�",$toback);
    	}else {
    		$this->toshow("ɾ��ʧ��",$toback);
    	}
    	echo $flag;
    }
    

    public function fillAction(){
    	//�����ҳ��
    }
    
    
    public  function chkfillAction(){
    	$typeid=2;
    	$class=$this->getRequest()->getParam("class");
    	$mark=$this->getRequest()->getParam("mark");
    	$chapterid=$this->getRequest()->getParam("chapter");
    	$content=$this->getRequest()->getParam("content");
    	$fillname=$this->getRequest()->getParam("fillname");
    
    	$answerR='';//��������ȷ�𰸵��ַ���
    
    	for($i=0;$i<count($fillname);$i++){//�������ǽ����еĴ����ϵ�һ���ַ�����
    		if($i==count ($fillname)-1){
    			$answerR.=trim($fillname[$i]);
    		}
    		else {
    			$answerR.=trim($fillname[$i]).";";
    		}
    	}
    	//$array=split(";", $answer);  ���ǿ��Խ��ַ����ָ�  �ٴγ�Ϊ����
    	//������������Ҫ����Ŀ���ݽ��д������ǽ��õ�������ʽ
    	$parm="{%fill%}";
    	$content=str_replace($parm,"____", $content);
    
    	$questionmodel=new dede_question();
    	$fillanswermodel=new dede_fillanswer();
    
    	$inres=$questionmodel->addquestion($typeid, $class, $mark, $chapterid, $content);
    	if($inres>0){
    		$answer=array(
    				"id"=>$inres,
    				"count"=>count($fillname),
    				"answerR"=>$answerR
    		);
    
    		if($fillanswermodel->insert($answer)>0){
    			$this->toshow("¼��ɹ�");
    		}
    		else {
    			$this->toshow("��¼��ʧ��");
    		}
    	}else {
    		$this->toshow("¼��ʧ��");
    	}
    }
    
    public function readfillAction(){
    	$fillanswermodel=new dede_fillanswer();
    	$db=$fillanswermodel->getAdapter();
    
    	$typeid = $this->getRequest()->getParam("qtype");
    	$bookid=$this->getRequest()->getParam("book");
    	$chapterid = $this->getRequest()->getParam("chapter");
    	$class=$this->getRequest()->getParam("class");
    
    	//���滹�ǲ鿴ɸѡ������
    	echo $where=towhere($typeid, $bookid, $chapterid);
    	echo $url=tourl($typeid, $bookid, $chapterid, $class);
    
    	//�����Ƿ�ҳ����
    	$pagenow=$this->getRequest()->getParam("pagenow");
    	if($pagenow==null){
    		$pagenow=0;//����ǵ�ǰҳ��
    	}
    	$limit=($pagenow*10).",10";
    
    
    	$sql='SELECT q.id, f.`count`, f.answerR,q.typeid, q.content FROM dede_question AS q Inner Join dede_fillanswer AS f ON q.id = f.id
        Where '.$where;
    	$res=$db->query($sql." LIMIT ".$limit)->fetchAll();
    
    
    	$res= $fillanswermodel->turnanswer1($res);//ת������
    	$res2=$db->query($sql)->fetchAll();
    	$this->view->tip=showtip($pagenow,count($res2),"readfill".$url);
    	$this->view->res1=$res;
    }
    
}

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

function tourl($typeid,$bookid,$chapterid,$class){
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
