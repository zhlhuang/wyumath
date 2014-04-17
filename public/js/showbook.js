if(myXmlHttprequest){
	var url="../Chkajax/book";
	myXmlHttprequest.open("post",url,true);
	//post提交在这里我们应该加入这么一句话	
	myXmlHttprequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	myXmlHttprequest.onreadystatechange=function(){
		if(myXmlHttprequest.readyState==4){
			if(myXmlHttprequest.status==200){
			str=myXmlHttprequest.responseText;//这里我们json对象，里面有对象数组
			str=eval("("+str+")");//获得php传来的json还是一个字符串
			for(var i=0;i<str.length;i++){
				var bookoption=document.createElement("option");//创建一个子节点
				bookoption.value=str[i].bookid;
				bookoption.textContent=str[i].bookname;
				document.getElementById("book").appendChild(bookoption);//将子节点添加到相应的节点中
			}
			}
		}
	};
	myXmlHttprequest.send();
}