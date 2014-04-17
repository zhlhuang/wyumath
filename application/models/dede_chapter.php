<?php
class dede_chapter extends Zend_Db_Table{
    protected $_name="dede_chapter";
    protected $_primary="chapterid";
    
    public function addchapter($chaptername,$bookid){
        $chapterarray=array(
        		"chaptername"=>$chaptername,
        		"bookid"=>$bookid
        );
        return $this->insert($chapterarray);
    }
}