<?php
class dede_paper extends Zend_Db_Table{
    protected  $_name="dede_paper";
    protected  $_primary="pid";
    
    //�����Ŀ
    public function makearray($class,$bookid,$chapterid,$date,$title){
        return        $paper=array(
        	"bookid"=>$bookid,
                "chapterid"=>$chapterid,
                "class"=>$class,
                "date"=>$date,
                "title"=>$title,
                "qid"=>"0"
        );
    }
    
    public function updataqid($str,$paperid){
        $db=$this->getAdapter();
        $where=$db->quoteInto("pid=?", $paperid);
        
        $res=$this->fetchRow($where)->toArray();
        $str=$str.$res["qid"];//ȡ�����ݿ������е���Ŀ����ƴ��
        
        $str=$this->arrtostr($str);
        $qidarr=array(
        	"qid"=>$str
        );
        return $this->update($qidarr, $where);
    }
    
    public function getqid($paperid){
        //��ȡ��ѡ��Ŀ��id
        $db=$this->getAdapter();
        $where=$db->quoteInto("pid=?", $paperid);
        $res=$this->fetchRow($where)->toArray();
        
        $qid=$res["qid"];
        return $this->substr($qid);//����һ�����鷵��
    }
    
    public function subarray($id){
       //������ת�����ַ���
       $str='';
       foreach ($id as $value){
           $str.=$value.";";
       }
       return $str;
    }
    
    public function substr($str){
        //���ַ���ת��������
        $arr=array();
        $arr=preg_split("/;/", $str);
        unset($arr[count($arr)-1]);//�����һ����Ԫ�س�ȥ
        return $arr;
    }
    
    public function arrtostr($str){
        //��ȥ�������ظ���ֵ
        $arr=$this->substr($str);
        $arr=array_unique($arr);
        return $this->subarray($arr);
    }
    
}