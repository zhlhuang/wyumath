function deleqid(qid,paperid){
	flag=confirm("ȷ��ɾ��");
	if(flag){
		//alert(qid+"...."+paperid);
		if(myXmlHttprequest){
			var url="../paper/deleqid?qid="+qid+"&paperid="+paperid;
			myXmlHttprequest.open("get",url,true);
			
			myXmlHttprequest.onreadystatechange=function(){
				if(myXmlHttprequest.readyState==4){
					
					if(myXmlHttprequest.status==200){
						flag=myXmlHttprequest.responseText;
						if(flag>0){
							document.getElementById("deleqid"+qid).innerHTML="<font color='red'>����Ŀ�Ѿ�ɾ��</font>";
							document.getElementById("deleqid"+qid).onclick="";
						}else{
							alert("ɾ��ʧ�ܣ�������Ŀ������");
						}
					}
				}
			};
		   myXmlHttprequest.send();
		}
	}
}