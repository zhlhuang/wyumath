<?php
require APPLICATION_PATH."/controllers/BaseController.php";
require_once APPLICATION_PATH.'/models/dede_student.php';
class StusysController extends BaseController
{ 
	public function indexAction(){
		$member=$this->getloginmember();
		if($member==NULL){
			$this->toshow("Äã»¹Ã»ÓĞµÇÂ¼","http://202.192.242.1:8080/member/login.php");
		}else {
			$studentmodel=new dede_student();
			$res=$studentmodel->getstudent($member["mid"]);
			if($res){
				$this->render("ok");				
			}else{
				$this->render("err");
			}
		}

	}
}

