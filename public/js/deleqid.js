function deleqid(qid,paperid){
	flag=confirm("确认删除");
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
							document.getElementById("deleqid"+qid).innerHTML="<font color='red'>此题目已经删除</font>";
							document.getElementById("deleqid"+qid).onclick="";
						}else{
							alert("删除失败，或者题目不存在");
						}
					}
				}
			};
		   myXmlHttprequest.send();
		}
	}
}