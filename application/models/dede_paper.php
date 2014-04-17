<?php
class dede_paper extends Zend_Db_Table{
    protected  $_name="dede_paper";
    protected  $_primary="pid";
    
    //添加题目
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
        $str=$str.$res["qid"];//取出数据库中已有的题目进行拼接
        
        $str=$this->arrtostr($str);
        $qidarr=array(
        	"qid"=>$str
        );
        return $this->update($qidarr, $where);
    }
    
    public function getqid($paperid){
        //获取所选题目的id
        $db=$this->getAdapter();
        $where=$db->quoteInto("pid=?", $paperid);
        $res=$this->fetchRow($where)->toArray();
        
        $qid=$res["qid"];
        return $this->substr($qid);//生成一个数组返回
    }
    
    public function subarray($id){
       //将数组转化成字符串
       $str='';
       foreach ($id as $value){
           $str.=$value.";";
       }
       return $str;
    }
    
    public function substr($str){
        //将字符串转化成数组
        $arr=array();
        $arr=preg_split("/;/", $str);
        unset($arr[count($arr)-1]);//将最后一个空元素出去
        return $arr;
    }
    
    public function arrtostr($str){
        //出去数组中重复的值
        $arr=$this->substr($str);
        $arr=array_unique($arr);
        return $this->subarray($arr);
    }
    
}