<?php
class dede_selectanswer extends Zend_Db_Table{
    protected  $_name="dede_selectanswer";
    protected  $_primary="id";
    //���
    public function addselectanswer($id,$answerA,$answerB,$answerC,$answerD,$answerR){
        $answer=$this->makearray($id, $answerA, $answerB, $answerC, $answerD, $answerR);
        return $this->insert($answer);
    }
    //����
    
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
    
    public function selectall($res=null){//$res �Ǵ��ж����Ŀ��id
     //ѡ����ѡ����   
     $db=$this->getAdapter();
     
        $where= $db->quoteInto("q.id in(?)", $res);//��ֹsqlע�뽫������Ϊin��������where���
        
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
        //��ȷ�𰸵����
        $res2=array();
        foreach ($res1 as $value){
            $answerU=$par["answer".$value["id"]];//�û���ѡ�Ĵ�
        	//$res1[$i++]["answerU"]=$answerU;
        
        	if($value["answerR"]==$answerU)
        	{
        		$res2[$value["id"]]="<font color='green'>����Ŀ��ȷ�Ĵ��ǣ�".$value['answerR']."��ѡ��Ĵ���"
        				.$answerU."</font>";
        	}
        	else {
        		$res2[$value["id"]]="<font color='red'>����Ŀ��ȷ�Ĵ��ǣ�".$value['answerR']."��ѡ��Ĵ���"
        				.$answerU."</font>";
        	}
        }
        	return $res2;
    }
}