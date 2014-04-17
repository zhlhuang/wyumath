<?php 
require APPLICATION_PATH."/controllers/BaseController.php";
require_once APPLICATION_PATH.'/models/dede_book.php';
require_once APPLICATION_PATH.'/models/dede_chapter.php';
require_once APPLICATION_PATH.'/models/dede_qtype.php';
require_once APPLICATION_PATH.'/models/dede_question.php';
require_once APPLICATION_PATH.'/models/dede_selectanswer.php';
class ChkajaxController extends BaseController{
    public function bookAction(){
         $this->getRequest()->getParam("name");  
         
         header("Content-Type:text/html;charset=gb2312");
         header("Cache-Control: no-cache");
         $bookmodel=new dede_book();
         $resbook=$bookmodel->fetchAll()->toArray();
         foreach ($resbook as $key=> $value){
             $resbook[$key]['bookname']=urldecode(iconv("gbk", "utf-8", $value["bookname"]));
         }//因为我们编码是gbk  所以我们不能使用json函数直接转化
         
         
         $resbook=Zend_Json_Encoder::encode($resbook);
         echo $resbook;
           
        exit();
    }
    
    public function chapterAction(){
    	$bookid=$this->getRequest()->getParam("bookid");
    	 
    	header("Content-Type:text/html;charset=gb2312");
    	header("Cache-Control: no-cache");
    	$chaptermodel=new dede_chapter();
    	
    	$db=$chaptermodel->getAdapter();
    	$where=$db->quoteInto("bookid=?", $bookid);
    	
    	$reschapter=$chaptermodel->fetchAll($where)->toArray();

    	foreach ($reschapter as $key=> $value){
    		$reschapter[$key]['chaptername']=urldecode(iconv("gbk", "utf-8", $value["chaptername"]));
    	}//因为我们编码是gbk  所以我们不能使用json函数直接转化
    	 
    	 
    	$reschapter=Zend_Json_Encoder::encode($reschapter);
    	echo $reschapter;
    	 
    	exit();
    }
}
?>