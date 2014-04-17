<?php
require APPLICATION_PATH."/controllers/BaseController.php";
require_once APPLICATION_PATH.'/models/dede_book.php';
require_once APPLICATION_PATH.'/models/dede_chapter.php';
require_once APPLICATION_PATH.'/models/dede_qtype.php';
require_once APPLICATION_PATH.'/models/dede_question.php';
require_once APPLICATION_PATH.'/models/dede_selectanswer.php';
require_once APPLICATION_PATH.'/models/dede_fillanswer.php';
require_once APPLICATION_PATH.'/models/dede_paper.php';
class IndexController extends BaseController
{

    public function indexAction()
    {
       //   
     
    }
    public function leftmenuAction (){
        $qtypemodel=new dede_qtype();
        $this->view->restype=$qtypemodel->fetchAll()->toArray();
        $bookmodel=new dede_book();
        $this->view->resbook=$bookmodel->fetchAll()->toArray();
        $chaptermodel=new dede_chapter();
        $this->view->reschapter=$chaptermodel->fetchAll()->toArray();
    }

    public function showAction ()
    {
        
        $questionmodel = new dede_question();//����һ������ı�ģ��
        $db = $questionmodel->getAdapter();
        // ����������Ҫ����ѯ
        $where = '';
        if (($typeid = $this->getRequest()->getParam("qtype")) != null)
            $where = $db->quoteInto(" typeid=?", $typeid);//��ֹsqlע��
            // ��ȡ��Ŀ����
        if(($book=$this->getRequest()->getParam("book"))!=null){
            $chaptemodel=new dede_chapter();
            $cares=$chaptemodel->fetchAll("bookid=".$book)->toArray(); 
            $inarray=array();
         for($i=0;$i<count($cares);$i++){
             $inarray[$i]=$cares[$i]["chapterid"];
         }//ͨ��forѭ��������  ���뵽in  ������
            
            if (($chapterid = $this->getRequest()->getParam("chapterid")) != null)
            	$where .= $db->quoteInto("AND q.chapterid =?", $chapterid);
            else if(count($cares)>0) {
                $where.=$db->quoteInto("AND q.chapterid in(?)", $inarray);
            }
            else {
                $this->toshow("���鱾û���½�");
            }
        } 
        
        
            // ��ȡ��Ŀ�������½�
        if (($class = $this->getRequest()->getParam("class")) != null)
            $where .= $db->quoteInto("AND q.class =?", $class);
        
        $set = array(
                "typeid" => $typeid,
                "chapterid" => $chapterid,
                "class" => $class
        );
        
        
        if ($typeid == 1) {// ��������ѡ����
            
            $sql = "SELECT a.answerA, a.answerC, a.answerB, a.answerD,
                    a.id,q.content FROM dede_selectanswer AS a Inner Join dede_question AS q ON a.id = q.id
                     WHERE " . $where;
            
            $this->view->res = $db->query($sql)->fetchAll();
        }elseif ($typeid==2){//���������
             $sql = "SELECT a.count,a.id,q.content FROM dede_fillanswer AS a Inner Join dede_question AS q ON a.id = q.id
                     WHERE " . $where;
             
             $res=$db->query($sql)->fetchAll();
             
             foreach ($res as $key=>$value){//����Ŀ����ת����textfield�����û�����
                $parm="____";
                $resp="<input type='text' style='border:0px;' name='fillname".$value['id']."[]' value='________'>";
                $res[$key]["content"]=str_replace($parm, $resp, $value["content"]);
             }
             
           $this->view->res=$res;
            $this->render("fillshow");
        }

    }
    
    
    public function chkanswerAction(){

        $res=$this->getRequest()->getParam("id");//�õ��û�ѡ����Ŀѡ���id
       
        $questionmodel=new dede_question();
        $selectanswemodel=new dede_selectanswer();
        $db=$questionmodel->getAdapter();
     
        $res1=$selectanswemodel->selectall($res);//�õ���ɸѡ����Ŀ

        $par= $this->getRequest()->getParams();

        $res2=array();//�����洢�𰸵���ȷ������ʾ��Ϣ

        $res2=$selectanswemodel->tureanswer($res1,$par);
        
        $this->view->res1=$res1;
        $this->view->res2=$res2;
    }
    
    public function chkfillanswerAction(){

        $res=$this->getRequest()->getParam("id");//�õ��û�ѡ����Ŀѡ���id
        $resall=$this->getRequest()->getParams();//�õ��û��ύ�Ĵ�
        
        $fillanswer=new dede_fillanswer();

        $res1=$fillanswer->selectall($res);
        

        $res2=$fillanswer->showanswer($res1, $resall);
        $this->view->res1=$fillanswer->turnanswer1($res1);//��Ŀ����
        $this->view->res2=$res2;//�Ƿ���ȷ������ʾ
    }
    
    public function showpaperAction(){
        $paperid=$this->getRequest()->getParam("paperid");
        $papermodel=new dede_paper();
        $questionmodel=new dede_question();
        $fillmodel=new dede_fillanswer();
        
        $res=$papermodel->fetchRow("pid=".$paperid)->toArray();
        $this->view->paper=$res;

        session_save_path("../sessions/");
        session_start();
        $member=$_SESSION['member'];
       // var_dump($member);
        if($member!=''){
            $this->view->member=$member;
           $qid=$papermodel->getqid($paperid);//��ȡ���Ծ��ϵ���Ŀid
          $selectres=$questionmodel->selectquestion($qid);//����Ŀid���� Ȼ�󷵻���Ŀ����
          $fillres=$questionmodel->fillquestion($qid);
          
          
          $this->view->selectres=$selectres;
          $this->view->fillres=$fillmodel->showfill($fillres);
        }else{
            $this->toshow("�㻹û�е�¼");
        }
    }
    public function chkpaperAction(){
        $res=$this->getRequest()->getParam("id");//�õ��û�ѡ����Ŀѡ���id
        
        $questionmodel=new dede_question();
        $selectanswemodel=new dede_selectanswer();
        $db=$questionmodel->getAdapter();
         
        $res1=$selectanswemodel->selectall($res);//�õ���ɸѡ����Ŀ
        
        $par= $this->getRequest()->getParams();
        
        $res2=array();//�����洢�𰸵���ȷ������ʾ��Ϣ
        
        $res2=$selectanswemodel->tureanswer($res1,$par);
        
        $this->view->res1=$res1;
        $this->view->res2=$res2;
        
      //  $res=$this->getRequest()->getParam("id");//�õ��û�ѡ����Ŀѡ���id
       $resall=$this->getRequest()->getParams();//�õ��û��ύ�Ĵ�
        
        $fillanswer=new dede_fillanswer();
        
        $res4=$fillanswer->selectall($res);
           
        $res5=$fillanswer->showanswer($res4, $resall);
        $this->view->res4=$fillanswer->turnanswer1($res4);//��Ŀ����
        $this->view->res5=$res5;//�Ƿ���ȷ������ʾ 
           
    }
}
