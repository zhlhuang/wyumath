document.form1.onsubmit=function(){
	var answerR=document.form1.answerR;
	var content=editor.getContent();
	var flag=0;//这是 一个信息是否正常的标志
	var msg='';//这是提示信息的存放
	
	for( var i=0;i<answerR.length;i++){
		if(answerR[i].checked){
			flag=1;
			break;
		}
		else{
			flag=2;//2代表没有选中正确答案
			msg="请选择正确答案";
		}
	}
	var answerA=document.getElementById("answerA").value;
	var answerB=document.getElementById("answerB").value;
	
	
	if(answerA==''&&answerB==''){
		flag=4;
		msg="A，B答案不能为空";
	}
	
	if(content==''){
		flag=5;
		msg="题目内容不能为空";
	}
	if(flag==1){
		return true;
	}else{
		alert(msg);
		return false;
	}
} ;

function addbook(){
	window.open("./addbook","这是添加课本窗口","width=640,height=480," +
			"scrollbars=no,resizable=yes,status=no,toolbar=no,menubar=no,location=no," +
			"titlebar=no,top=100,channelmode=yes,resizable=no");
}



function addchapter(){
	window.open("./addchapter","这是添加章节窗口","width=640,height=480," +
			"scrollbars=no,resizable=yes,status=no,toolbar=no,menubar=no," +
			"location=no,titlebar=no,top=100,channelmode=yes,resizable=no");
}


