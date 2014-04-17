<?php
require APPLICATION_PATH."/controllers/BaseController.php";
require_once APPLICATION_PATH.'/models/dede_book.php';
require_once APPLICATION_PATH.'/models/dede_chapter.php';
require_once APPLICATION_PATH.'/models/dede_qtype.php';
require_once APPLICATION_PATH.'/models/dede_question.php';
require_once APPLICATION_PATH.'/models/dede_selectanswer.php';
require_once APPLICATION_PATH.'/models/dede_fillanswer.php';
require_once APPLICATION_PATH.'/models/dede_paper.php';
class PaperController extends BaseController
{
    public function indexAction(){

    }
    public function chkAction(){
         $bookid=$this->getRequest()->getParam("book");
         $chapterid=$this->getRequest()->getParam("chapter");
         $class=$this->getRequest()->getParam("class");
         $title=trim($this->getRequest()->getParam("title"));
         
         $date=time();
         $papermodel=new dede_paper();  
        $paper=$papermodel->makearray($class, $bookid, $chapterid,$date, $title);
   
        $res=$papermodel->insert($paper);
        if ($res>0) {
        	$this->toshow("��ӳɹ�","show");
        }
        else {
            $this->toshow("���ʧ��");
        }
    }
    public function delepaperAction(){
        $paperid=$this->getRequest()->getParam("paperid");
        $papermodel=new dede_paper();
        $db=$papermodel->getAdapter();
        $where=$db->quoteInto("pid=?", $paperid);
        if($papermodel->delete($where)==0){
            $this->toshow("ɾ���ɹ�","show");
        }else {
            $this->toshow("ɾ��ʧ��");
        }
        
    }
    
    public function showAction(){
        $papermodel=new dede_paper();
        $res=$papermodel->fetchAll()->toArray();
        $this->view->res=$res;
    }
    
    public function readAction(){
    	
    	$session=new Zend_Session_Namespace("paper");
    	$paper=$session->__get("paper");
    	if (count($paper)==0) {
    		$this->toshow("��ѡ���Ӧ���Ծ�","show");
    	}else {
    	    $this->view->paper=$paper;
    	}
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
    public function readfillAction(){
        $session=new Zend_Session_Namespace("paper");
        $paper=$session->__get("paper");
        if (count($paper)==0) {
        	$this->toshow("��ѡ���Ӧ���Ծ�","show");
        }else {
        	$this->view->paper=$paper;
        }
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
    
    public  function addsessionAction(){
        $pid=$this->getRequest()->getParam("paperid");
        $papermodel=new dede_paper();
        $res=$papermodel->fetchRow("pid=".$pid)->toArray();
       // session_save_path("../sessions/");
        $session=new Zend_Session_Namespace("paper");
        
        $session->__set("paper", $res);
        if(count($session->__get("paper"))>0){
            $this->forward("read");
        }else {
            $this->toshow("�༭ʧ��");
        }
    }
    
    public function addpaperAction(){
        $id=$this->getRequest()->getParam("id");
        $paperid=$this->getRequest()->getParam("paperid");
        
        $papermodel=new dede_paper();
         $str=$papermodel->subarray($id);
        
        
         if ($papermodel->updataqid($str, $paperid)>0) {
         	$this->toshow("��ӳɹ�");
         }else {
             $this->toshow("���ʧ��");
         }
       
    }
    
    public function  showpaperAction(){
        //�������Ծ�鿴����
         $paperid=$this->getRequest()->getParam("paperid");
         $papermodel=new dede_paper();
         $questionmodel=new dede_question();
         $fillmodel=new dede_fillanswer();
         
         $paper=$papermodel->fetchRow("pid=".$paperid)->toArray();
         $this->view->paper=$paper;
         if ($paper["qid"]==0) {
         	$this->toshow("���Ծ�û����ѡ��Ŀ","addsession?paperid=".$paperid);
         }else{
             $qid=$papermodel->getqid($paperid);//��ø������е���Ŀ
         
         $selectres=$questionmodel->selectquestion($qid);
         $fillres=$questionmodel->fillquestion($qid);
         
         $this->view->selectres=$selectres;
         $this->view->fillres=$fillmodel->turnanswer1($fillres);
         }
    }
    
    public function deleqidAction(){
        header("Content-Type:text/html;charset=gb2312");
        header("Cache-Control: no-cache");
        
        
        $qid=$this->getRequest()->getParam("qid");
        $paperid=$this->getRequest()->getParam("paperid");
        
        $papermodel=new dede_paper();
        $res=$papermodel->fetchRow("pid=".$paperid);
        $qidarr=$papermodel->substr($res["qid"]);
        unset($qidarr[array_search($qid, $qidarr)]);//����ѡ����qid���ַ�����ɾ��
        
        $str=$papermodel->subarray($qidarr);
        
        $data=array(
        	"qid"=>$str
        );
        $where="pid=".$paperid;
        
        $flag=$papermodel->update($data, $where);
        if($flag>0){
            echo 1;//�ɹ�ɾ������1
        }else {
            echo 0;//ɾ��ʧ�ܷ���0
        }      
        exit();
    }
    public function submitpaperAction(){
        $paperid=$this->getRequest()->getParam("paperid");
        $date=time();
        $flag=1;
       $set=array(
       	"date"=>$date,
               "flag"=>$flag
       );//������ʱ�仹�з�����Ǹ���
       $papermodel=new dede_paper();
       $where=$papermodel->getAdapter()->quoteInto("pid=?", $paperid);
       
       $res=$papermodel->update($set, $where);
       if ($res>0) {
       	$this->toshow("�����ɹ�");
       }else {
           $this->toshow("����ʧ��");
       }
    }
}