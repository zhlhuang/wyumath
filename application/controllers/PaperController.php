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
        	$this->toshow("添加成功","show");
        }
        else {
            $this->toshow("添加失败");
        }
    }
    public function delepaperAction(){
        $paperid=$this->getRequest()->getParam("paperid");
        $papermodel=new dede_paper();
        $db=$papermodel->getAdapter();
        $where=$db->quoteInto("pid=?", $paperid);
        if($papermodel->delete($where)==0){
            $this->toshow("删除成功","show");
        }else {
            $this->toshow("删除失败");
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
    		$this->toshow("请选择对应的试卷","show");
    	}else {
    	    $this->view->paper=$paper;
    	}
  //这里我们可以查看题目  	        
    	$questionmodel=new dede_question();
    	$db=$questionmodel->getAdapter();
    
    	$typeid = $this->getRequest()->getParam("qtype");
    	$bookid=$this->getRequest()->getParam("book");
    	$chapterid = $this->getRequest()->getParam("chapter");
    	$class=$this->getRequest()->getParam("class");
    	
    	//下面还是查看筛选的条件
    	$where=towhere($typeid, $bookid, $chapterid,$class);
    	echo $where;
    	echo $url=tourl($typeid, $bookid, $chapterid, $class);
    
    
    	//下面是分页内容
    	$pagenow=$this->getRequest()->getParam("pagenow");
    	if($pagenow==null){
    		$pagenow=0;//这个是当前页面
    	}
    	$limit=($pagenow*10).",10";
    	 
    	 
    	$sql="SELECT a.answerA, a.answerC, a.answerB, a.answerD,a.answerR,
                    a.id,q.typeid,q.content FROM dede_selectanswer AS a Inner Join dede_question AS q ON a.id = q.id
                    WHERE ".$where;
    	$res1= $db->query($sql." LIMIT ".$limit)->fetchAll();
    
    	//获得总页数
    	$res2= $db->query($sql)->fetchAll();
    	$this->view->tip=showtip($pagenow,count($res2),"read".$url);
    	$this->view->res1=$res1;
    }
    public function readfillAction(){
        $session=new Zend_Session_Namespace("paper");
        $paper=$session->__get("paper");
        if (count($paper)==0) {
        	$this->toshow("请选择对应的试卷","show");
        }else {
        	$this->view->paper=$paper;
        }
    	$fillanswermodel=new dede_fillanswer();
    	$db=$fillanswermodel->getAdapter();
    
    	$typeid = $this->getRequest()->getParam("qtype");
    	$bookid=$this->getRequest()->getParam("book");
    	$chapterid = $this->getRequest()->getParam("chapter");
    	$class=$this->getRequest()->getParam("class");
    
    	//下面还是查看筛选的条件
    	echo $where=towhere($typeid, $bookid, $chapterid);
    	echo $url=tourl($typeid, $bookid, $chapterid, $class);
    
    	//下面是分页内容
    	$pagenow=$this->getRequest()->getParam("pagenow");
    	if($pagenow==null){
    		$pagenow=0;//这个是当前页面
    	}
    	$limit=($pagenow*10).",10";
    
    
    	$sql='SELECT q.id, f.`count`, f.answerR,q.typeid, q.content FROM dede_question AS q Inner Join dede_fillanswer AS f ON q.id = f.id
        Where '.$where;
    	$res=$db->query($sql." LIMIT ".$limit)->fetchAll();
    
    
    	$res= $fillanswermodel->turnanswer1($res);//转化代码
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
            $this->toshow("编辑失败");
        }
    }
    
    public function addpaperAction(){
        $id=$this->getRequest()->getParam("id");
        $paperid=$this->getRequest()->getParam("paperid");
        
        $papermodel=new dede_paper();
         $str=$papermodel->subarray($id);
        
        
         if ($papermodel->updataqid($str, $paperid)>0) {
         	$this->toshow("添加成功");
         }else {
             $this->toshow("添加失败");
         }
       
    }
    
    public function  showpaperAction(){
        //下面是试卷查看功能
         $paperid=$this->getRequest()->getParam("paperid");
         $papermodel=new dede_paper();
         $questionmodel=new dede_question();
         $fillmodel=new dede_fillanswer();
         
         $paper=$papermodel->fetchRow("pid=".$paperid)->toArray();
         $this->view->paper=$paper;
         if ($paper["qid"]==0) {
         	$this->toshow("该试卷没有挑选题目","addsession?paperid=".$paperid);
         }else{
             $qid=$papermodel->getqid($paperid);//获得该数据中的题目
         
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
        unset($qidarr[array_search($qid, $qidarr)]);//将所选到的qid从字符串中删除
        
        $str=$papermodel->subarray($qidarr);
        
        $data=array(
        	"qid"=>$str
        );
        $where="pid=".$paperid;
        
        $flag=$papermodel->update($data, $where);
        if($flag>0){
            echo 1;//成功删除返回1
        }else {
            echo 0;//删除失败返回0
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
       );//将发布时间还有发布标记更改
       $papermodel=new dede_paper();
       $where=$papermodel->getAdapter()->quoteInto("pid=?", $paperid);
       
       $res=$papermodel->update($set, $where);
       if ($res>0) {
       	$this->toshow("发布成功");
       }else {
           $this->toshow("发布失败");
       }
    }
}