document.getElementById("book").onchange=onchk;
chapter=document.getElementById("chapter");
var chapterXmlHttprequest=getXmlHttpObject();
	function  onchk(){//这里是失去焦点事件的触发
	if(chapterXmlHttprequest){
		var url="../Chkajax/chapter";
		var data='bookid='+book.value;
		chapterXmlHttprequest.open("post",url,true);
		//post提交在这里我们应该加入这么一句话	
		
		chapter.length=1;
		
		chapterXmlHttprequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
		chapterXmlHttprequest.onreadystatechange=function(){
			if(chapterXmlHttprequest.readyState==4){
				if(chapterXmlHttprequest.status==200){
				str=chapterXmlHttprequest.responseText;//这里我们json对象，里面有对象数组
				str=eval("("+str+")");//获得php传来的json还是一个字符串
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