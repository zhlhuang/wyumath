if(myXmlHttprequest){
	var url="../Chkajax/book";
	myXmlHttprequest.open("post",url,true);
	//post�ύ����������Ӧ�ü�����ôһ�仰	
	myXmlHttprequest.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	myXmlHttprequest.onreadystatechange=function(){
		if(myXmlHttprequest.readyState==4){
			if(myXmlHttprequest.status==200){
			str=myXmlHttprequest.responseText;//��������json���������ж�������
			str=eval("("+str+")");//���php������json����һ���ַ���
			for(var i=0;i<str.length;i++){
				var bookoption=document.createElement("option");//����һ���ӽڵ�
				bookoption.value=str[i].bookid;
				bookoption.textContent=str[i].bookname;
				document.getElementById("book").appendChild(bookoption);//���ӽڵ���ӵ���Ӧ�Ľڵ���
			}
			}
		}
	};
	myXmlHttprequest.send();
}