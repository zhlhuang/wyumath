<?php
class dede_question extends Zend_Db_Table{
    protected  $_name="dede_question";
    protected  $_primary="id";
    
    //�����Ŀ
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
        );//��Ҫ¼�����ݿ��е�����
    }
    
    public function selectquestion($id){
        //����ȡ��ѡ�������ݻ��д�
        $db=$this->getAdapter();
        $where=$db->quoteInto("q.id in (?)", $id);
        $sql="SELECT q.id, q.typeid, q.class, q.mark, q.chapterid,q.content, s.answerA, s.answerB, s.answerC, s.answerD, s.answerR
                 FROM dede_question AS q Inner Join dede_selectanswer AS s ON q.id = s.id WHERE ".$where." AND q.typeid =  '1'";
     //���Ƕ���ѯ�����
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