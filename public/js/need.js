document.form1.onsubmit=function(){
	var answerR=document.form1.answerR;
	var content=editor.getContent();
	var flag=0;//���� һ����Ϣ�Ƿ������ı�־
	var msg='';//������ʾ��Ϣ�Ĵ��
	
	for( var i=0;i<answerR.length;i++){
		if(answerR[i].checked){
			flag=1;
			break;
		}
		else{
			flag=2;//2����û��ѡ����ȷ��
			msg="��ѡ����ȷ��";
		}
	}
	var answerA=document.getElementById("answerA").value;
	var answerB=document.getElementById("answerB").value;
	
	
	if(answerA==''&&answerB==''){
		flag=4;
		msg="A��B�𰸲���Ϊ��";
	}
	
	if(content==''){
		flag=5;
		msg="��Ŀ���ݲ���Ϊ��";
	}
	if(flag==1){
		return true;
	}else{
		alert(msg);
		return false;
	}
} ;

function addbook(){
	window.open("./addbook","������ӿα�����","width=640,height=480," +
			"scrollbars=no,resizable=yes,status=no,toolbar=no,menubar=no,location=no," +
			"titlebar=no,top=100,channelmode=yes,resizable=no");
}



function addchapter(){
	window.open("./addchapter","��������½ڴ���","width=640,height=480," +
			"scrollbars=no,resizable=yes,status=no,toolbar=no,menubar=no," +
			"location=no,titlebar=no,top=100,channelmode=yes,resizable=no");
}


