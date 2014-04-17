<?php
require APPLICATION_PATH."/controllers/BaseController.php";
require_once APPLICATION_PATH.'/models/dede_book.php';
require_once APPLICATION_PATH.'/models/dede_chapter.php';
require_once APPLICATION_PATH.'/models/dede_qtype.php';
require_once APPLICATION_PATH.'/models/dede_question.php';
require_once APPLICATION_PATH.'/models/dede_selectanswer.php';
require_once APPLICATION_PATH.'/models/dede_fillanswer.php';
class AdminController extends BaseController
{

    public function indexAction()
    { 
        $qtypemodel=new dede_qtype();
        $this->view->restype=$qtypemodel->fetchAll()->toArray();
    }
    public function chkAction(){
        
        $flag='';
        //下面数据是加入到question表中的
        $typeid=1;
        $class=$this->getRequest()->getParam("class");
        $mark=$this->getRequest()->getParam("mark");
        $chapterid=$this->getRequest()->getParam("chapter");
        if(($content=$this->getRequest()->getParam("content"))==''){
            
            $this->toshow("题目内容不能为空");
        }else{
 
            $questionmodel=new dede_question();
            //下面函数是将代码封装到数据库中的model中
            $flag=$questionmodel->addquestion($typeid, $class, $mark, $chapterid, $content);
        }
        if($flag>0){//如果题目录入成功，才可以录入答案
            //下面是answer表的数据
            $answerA=$this->getRequest()->getParam("A");
            $answerB=$this->getRequest()->getParam("B");
            $answerC=$this->getRequest()->getParam("C");
            $answerD=$this->getRequest()->getParam("D");
            $answerR=$this->getRequest()->getParam("answerR");

            $answermodel=new dede_selectanswer();
            $flag=$answermodel->addselectanswer($flag, $answerA, $answerB, $answerC, $answerD, $answerR);
            if($flag>0)
            {
            	$this->toshow("录入成功");
            }
            else {
            	$this->toshow("录入失败");
            }
            
        }
    }
    
    public function addbookAction(){
     
    }
    
    public function addbookchkAction(){
        //这里是书本添加的验证内容
        $book= $this->getRequest()->getParam("book");
        
        $bookmodel=new dede_book();
        $bookarray=array(
        	"bookname"=>$book
        );
        $res=$bookmodel->insert($bookarray);
        if($res>0){
            $this->toshow("添加课本成功id为：".$res);
        }else{
            $this->toshow("添加失败");
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
        	$this->toshow("添加章节成功id为：".$res);
        }else{
        $this->toshow("添加失败");
        }
    }
    
    public function readAction(){
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
    
    
    
    public function deleAction(){
        //这是删除控制器
        $id= $this->getRequest()->getParam("id");
        $typeid=$this->getRequest()->getParam("typeid");
        $flag=-1;//用来记录是否可以成功删除
        $toback='';//记录跳转页面
             
        $questionmodel=new dede_question();
        
        $selectanswermodel=new dede_selectanswer();//选择题模型
        $fillanswermodel=new dede_fillanswer();//填空题模型
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
            $this->toshow("删除成功",$toback);
        }else {
            $this->toshow("删除失败",$toback);
        }

    }

    public function updataAction(){
        //这里是题目修改
        $id= $this->getRequest()->getParam("id");
        $questionmodel=new dede_question();
        $chaptermodel=new dede_chapter();
        $db=$questionmodel->getAdapter(); 
         
        $where=$db->quoteInto("q.id=?", $id);
        
        $sql="SELECT a.*,q.* FROM dede_selectanswer AS a Inner Join dede_question AS q ON a.id = q.id
                    WHERE ".$where;
        $res1= $db->query($sql)->fetchAll();
       
        $res1=$res1[0];
        $chapterres=$chaptermodel->fetchAll("chapterid=".$res1['chapterid'])->toArray();
        $bookid=$chapterres[0]['bookid'];
        $res1['bookid']=$bookid;
        
        $res1['content']=urldecode(iconv("gbk", "utf-8", $res1['content']));
        $res1=Zend_Json_Encoder::encode($res1);
        $this->view->res1=$res1;
    } 

    public function chkupdataAction(){
        //修改题目内容
        $id=$this->getRequest()->getParam("id");
        $class=$this->getRequest()->getParam("class");
        $mark=$this->getRequest()->getParam("mark");
        $chapterid=$this->getRequest()->getParam("chapter");
        $content=$this->getRequest()->getParam("content");
        
        $questionmodel=new dede_question();
        $resq=$questionmodel->updatequestion(1,$class, $mark, $chapterid, $content, $id);
        
        //修改题目的答案
        $answerA=$this->getRequest()->getParam("A");
        $answerB=$this->getRequest()->getParam("B");
        $answerC=$this->getRequest()->getParam("C");
        $answerD=$this->getRequest()->getParam("D");
        $answerR=$this->getRequest()->getParam("answerR");
        
        $answermodel=new dede_selectanswer();
        $resa=$answermodel->updateselectanswer($id, $answerA, $answerB, $answerC, $answerD, $answerR);
        	
        $flag=false;//标志
        if($resq>0){
            $flag=true;
        }
        if ($resa>0) {
        	$flag=true;
        }
        	if ($flag) {
        		$this->toshow("修改成功");
        	}
        	else {
        	    $this->toshow("没有做任何修改");
        	}

    }
    

    public function fillAction(){
        //填空题页面
    }
    
    
    public  function chkfillAction(){
        $typeid=2;
        $class=$this->getRequest()->getParam("class");
        $mark=$this->getRequest()->getParam("mark");
        $chapterid=$this->getRequest()->getParam("chapter");
         $content=$this->getRequest()->getParam("content");
        $fillname=$this->getRequest()->getParam("fillname");
        
        $answerR='';//用来存正确答案的字符串
        
        for($i=0;$i<count($fillname);$i++){//这里我们将所有的答案整合到一个字符串中
            if($i==count ($fillname)-1){
                $answerR.=trim($fillname[$i]);
            }
            else {
                $answerR.=trim($fillname[$i]).";";
            }
        }
          //$array=split(";", $answer);  我们可以将字符串分割  再次成为数组
        //接下来，我们要将题目内容进行处理，我们将用到正则表达式             
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
                $this->toshow("录入成功");
            }
            else {
                $this->toshow("答案录入失败");
            }
        }else {
            $this->toshow("录入失败");
        } 
    }
    
    //这是填空题查看器
    public function readfillAction(){
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

    public function updatafillAction(){
        $id=$this->getRequest()->getParam("id");
        $questionmodel=new dede_question();
        $fillanswermodel=new dede_fillanswer();
        $db=$questionmodel->getAdapter();
        $where=$db->quoteInto("q.id=?", $id);
        
        $sql='SELECT q.id, f.`count`, f.answerR,q.typeid, q.content FROM dede_question AS q Inner Join dede_fillanswer AS f ON q.id = f.id
        Where '.$where;
        $res=$db->query($sql)->fetchAll();
        $res=$fillanswermodel->turnanswer2($res);
        $this->view->res=$res;
    }
    
    public function chkupdatafillAction(){
       $id= $this->getRequest()->getParam("id");
        $answer='';
        $answerres=$this->getRequest()->getParams();
        foreach ( $answerres["answer"]  as $value ){
            $answer.=$value.";";
        } 
        $answerR=array("answerR"=>$answer);
        $fillanswrmodel=new dede_fillanswer();
        $res=$fillanswrmodel->update($answerR, "id=".$id);
        if($res>0){
            $this->toshow("修改成功","updatafill?id=".$id);
        }else {
            $this->toshow("无修改题目","updatafill?id=".$id);
        }
    }
    
}
