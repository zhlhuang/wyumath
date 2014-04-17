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
    	//下面数据是加入到question表中的
    	$typeid=1;
    	$class=$this->getRequest()->getParam("class");
    	$mark=$this->getRequest()->getParam("mark");
    	$chapterid=$this->getRequest()->getParam("chapter");
    	if(($content=$this->getRequest()->getParam("content"))==''){
    		echo "11";
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
    	echo $flag;
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
    
}

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

function tourl($typeid,$bookid,$chapterid,$class){
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
