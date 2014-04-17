<?php
class dede_selectanswer extends Zend_Db_Table{
    protected  $_name="dede_selectanswer";
    protected  $_primary="id";
    //添加
    public function addselectanswer($id,$answerA,$answerB,$answerC,$answerD,$answerR){
        $answer=$this->makearray($id, $answerA, $answerB, $answerC, $answerD, $answerR);
        return $this->insert($answer);
    }
    //更新
    
    public function updateselectanswer($id,$answerA,$answerB,$answerC,$answerD,$answerR){
        $answer=$this->makearray($id, $answerA, $answerB, $answerC, $answerD, $answerR);
        
        $db=$this->getAdapter();
        $where=$db->quoteInto("id=?", $id);
        
        return $this->update($answer, $where);
    }
    public function deleteanswer($id){
        $where= $this->getAdapter()->quoteInto("id=?", $id);
        return $this->delete($where);
    }
    
    public function  makearray($id,$answerA,$answerB,$answerC,$answerD,$answerR){
        return   $answer=array(
        		"id"=>$id,
        		"answerA"=>$answerA,
        		"answerB"=>$answerB,
        		"answerC"=>$answerC,
        		"answerD"=>$answerD,
        		"answerR"=>$answerR,
        );
    }
    
    public function selectall($res=null){//$res 是存有多个题目的id
     //选择多道选择题   
     $db=$this->getAdapter();
     
        $where= $db->quoteInto("q.id in(?)", $res);//防止sql注入将数据作为in条件穿给where语句
        
        $sql="SELECT a.answerA, a.answerC, a.answerB, a.answerD,a.answerR,
                    a.id,q.content FROM dede_selectanswer AS a Inner Join dede_question AS q ON a.id = q.id
                     WHERE " . $where." AND typeid=1";
        $res1= $db->query($sql)->fetchAll();
        if($res1>0){
            return  $db->query($sql)->fetchAll();
        }else {
            return null;
        }
    }
    public function tureanswer($res1,$par){
        //正确答案的输出
        $res2=array();
        foreach ($res1 as $value){
            $answerU=$par["answer".$value["id"]];//用户所选的答案
        	//$res1[$i++]["answerU"]=$answerU;
        
        	if($value["answerR"]==$answerU)
        	{
        		$res2[$value["id"]]="<font color='green'>该题目正确的答案是：".$value['answerR']."你选择的答案是"
        				.$answerU."</font>";
        	}
        	else {
        		$res2[$value["id"]]="<font color='red'>该题目正确的答案是：".$value['answerR']."你选择的答案是"
        				.$answerU."</font>";
        	}
        }
        	return $res2;
    }
}