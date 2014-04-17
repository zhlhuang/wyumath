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
        //���������Ǽ��뵽question���е�
        $typeid=1;
        $class=$this->getRequest()->getParam("class");
        $mark=$this->getRequest()->getParam("mark");
        $chapterid=$this->getRequest()->getParam("chapter");
        if(($content=$this->getRequest()->getParam("content"))==''){
            
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

    }

    public function updataAction(){
        //��������Ŀ�޸�
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
        //�޸���Ŀ����
        $id=$this->getRequest()->getParam("id");
        $class=$this->getRequest()->getParam("class");
        $mark=$this->getRequest()->getParam("mark");
        $chapterid=$this->getRequest()->getParam("chapter");
        $content=$this->getRequest()->getParam("content");
        
        $questionmodel=new dede_question();
        $resq=$questionmodel->updatequestion(1,$class, $mark, $chapterid, $content, $id);
        
        //�޸���Ŀ�Ĵ�
        $answerA=$this->getRequest()->getParam("A");
        $answerB=$this->getRequest()->getParam("B");
        $answerC=$this->getRequest()->getParam("C");
        $answerD=$this->getRequest()->getParam("D");
        $answerR=$this->getRequest()->getParam("answerR");
        
        $answermodel=new dede_selectanswer();
        $resa=$answermodel->updateselectanswer($id, $answerA, $answerB, $answerC, $answerD, $answerR);
        	
        $flag=false;//��־
        if($resq>0){
            $flag=true;
        }
        if ($resa>0) {
        	$flag=true;
        }
        	if ($flag) {
        		$this->toshow("�޸ĳɹ�");
        	}
        	else {
        	    $this->toshow("û�����κ��޸�");
        	}

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
    
    //���������鿴��
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
            $this->toshow("�޸ĳɹ�","updatafill?id=".$id);
        }else {
            $this->toshow("���޸���Ŀ","updatafill?id=".$id);
        }
    }
    
}
