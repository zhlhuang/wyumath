<?php
class dede_question extends Zend_Db_Table{
    protected  $_name="dede_question";
    protected  $_primary="id";
    
    //添加题目
    public function addquestion($typeid,$class,$mark,$chapterid,$content){
        $question=$this->makearray($typeid, $class, $mark, $chapterid, $content);
        return $this->insert($question);
    }
    
    public function updatequestion($typeid,$class,$mark,$chapterid,$content, $id){
        $question=$this->makearray($typeid, $class, $mark, $chapterid, $content);
        
        $db=$this->getAdapter();
        $where=$db->quoteInto("id=?", $id);
        
        return $this->update($question, $where);
    }
    
    public function deletequestion($id){
        $db=$this->getAdapter();
        $where=$db->quoteInto("id=?", $id);
     
        return $this->delete($where);
    }
    
    public function makearray($typeid,$class,$mark,$chapterid,$content){
        if(get_magic_quotes_gpc()){
        	$content=stripslashes($content);
        }
        return $question=array(
        		"typeid"=>$typeid,
        		"class"=>$class,
        		"mark"=>$mark,
        		"chapterid"=>$chapterid,
        		"content"=>$content
        );//需要录入数据库中的数据
    }
    
    public function selectquestion($id){
        //批量取出选择题内容还有答案
        $db=$this->getAdapter();
        $where=$db->quoteInto("q.id in (?)", $id);
        $sql="SELECT q.id, q.typeid, q.class, q.mark, q.chapterid,q.content, s.answerA, s.answerB, s.answerC, s.answerD, s.answerR
                 FROM dede_question AS q Inner Join dede_selectanswer AS s ON q.id = s.id WHERE ".$where." AND q.typeid =  '1'";
     //这是多表查询的语句
        return $db->query($sql)->fetchAll();
    }
    public function fillquestion($id){
        $db=$this->getAdapter();
        $where=$db->quoteInto("q.id in (?)", $id);
        $sql="SELECT q.id, q.typeid, q.class, q.mark, q.chapterid,q.content,f.count, f.answerR
                 FROM dede_question AS q Inner Join dede_fillanswer AS f ON q.id = f.id WHERE ".$where." AND q.typeid =  '2'";

        return $db->query($sql)->fetchAll();
    }
    
    
    
    
    
    
}