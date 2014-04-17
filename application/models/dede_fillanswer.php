<?php
class dede_fillanswer extends Zend_Db_Table{
    protected  $_name="dede_fillanswer";
    protected  $_primary="id";
    
    public function deletequestion($id){
    	$where= $this->getAdapter()->quoteInto("id=?", $id);
    	return $this->delete($where);
    }
    
    public function turnanswer1($resout=array()){
           $res=$resout;
           
          foreach ($res as $key=> $value){
            $res[$key]['answerR']=preg_split("/;/", $value['answerR']);
            for ($i=0;$i<$value['count'];$i++){
                $replace="<font color='green'>". $res[$key]['answerR'][$i]."</font>";
                 $res[$key]['content']=preg_replace("/____/",$replace,$res[$key]['content'],1);       
            }//分别将内容中的____换成对应的答案
           
        }//将answerR转化成数组存入  
       return $res;
    }
    public function turnanswer2($resout=array()){
    	$res=$resout;
  
    	foreach ($res as $key=> $value){
    		$res[$key]['answerR']=preg_split("/;/", $value['answerR']);
    		for ($i=0;$i<$value['count'];$i++){
    			$replace="<input name=answer[] value='". $res[$key]['answerR'][$i]."'/>";
    			$res[$key]['content']=preg_replace("/____/",$replace,$res[$key]['content'],1);
    		}//分别将内容中的____换成对应的答案
    		 
    	}//将answerR转化成数组存入
    	return $res;
    }
    
    public function showfill($res){
        
        foreach ($res as $key=>$value){//将题目内容转换成textfield接受用户输入
        	$parm="____";
        	$resp="<input type='text' style='border:0px;' name='fillname".$value['id']."[]' value='________'>";
        	$res[$key]["content"]=str_replace($parm, $resp, $value["content"]);
        }
        return $res;
    }
    
    
    public function selectall($res=null){
        $db=$this->getAdapter();
        $where= $db->quoteInto("q.id in(?)", $res);//防止sql注入将数据作为in条件穿给where语句
        
        $sql="SELECT a.answerR,a.count,
                    a.id,q.content FROM dede_fillanswer AS a Inner Join dede_question AS q ON a.id = q.id
                     WHERE " . $where." AND typeid=2";
        $res1= $db->query($sql)->fetchAll();
        return $res1;
      }
      
      public function showanswer($res1,$resall){
          $res2=array();
          foreach ($res1 as $value){
          	$resanswer=$resall['fillname'.$value['id']];
          	 
          	$answer=preg_split('/;/', $value["answerR"]);//将答案分割成数组
          
          	foreach ($answer as $key=> $val){
          		if(strcmp(trim($val), trim($resanswer[$key]))==0){// 这是两个字符串的比较
          			$res2[$value['id']][$key]='<font color="green">'.$resanswer[$key].'</font>';
          		}else {
          			$res2[$value['id']][$key]='<font color="red">'.$resanswer[$key].'</font>';
          		}
          	}
          }
          return $res2;
      }
    
}