document.getElementById("book").onchange=onchk;
chapter=document.getElementById("chapter");
var chapterXmlHttprequest=getXmlHttpObject();
	function  onchk(){//������ʧȥ�����¼��Ĵ���
	if(chapterXmlHttprequest){
		var url="../Chkajax/chapter";
		var data='bookid='+book.value;
		chapterXmlHttprequest.open("post",url,true);
		//post�ύ����������Ӧ�ü�����ôһ�仰	
		
		chapter.length=1;
		
		chapterXmlHttprequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		chapterXmlHttprequest.onreadystatechange=function(){
			if(chapterXmlHttprequest.readyState==4){
				if(chapterXmlHttprequest.status==200){
				str=chapterXmlHttprequest.responseText;//��������json���������ж�������
				str=eval("("+str+")");//���php������json����һ���ַ���
				for(var i=0;i<str.length;i++){
					var chapteroption=document.createElement("option");
					chapteroption.value=str[i].chapterid;
					chapteroption.textContent=str[i].chaptername;
					chapter.appendChild(chapteroption);
				}
				}
			}
		};
		chapterXmlHttprequest.send(data);
	}
}