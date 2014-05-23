<?php
class dede_student extends Zend_Db_Table{
    protected  $_name="dede_student";
    protected  $_primary="mid";
    function getstudent($mid){
    	$student=$this->fetchAll("mid=".$mid)->toArray();
    	return $student;
    }
}