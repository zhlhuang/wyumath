function addcontent(res){
	document.getElementById("id").value=res.id;
	
	book.value=res.bookid;//�α�
	onchk();
	setTimeout("chkchapter(+res.chapterid)",2000);//��������ѡ�½ں�
	
	if(res.class!=null){
		document.getElementsByName("class")[res.class-1].checked="checked";//ѡ���Ѷ�
	}
	if(res.mark!=null){
		document.getElementById("mark").value=res.mark;
	}
		
	editor.setContent(res.content);
	document.getElementById("answerA").value=res.answerA;
	document.getElementById("answerB").value=res.answerB;
	if(res.answerC!=null){
		document.getElementById("answerC").value=res.answerC;
	}
	if(res.answerD!=null){
		document.getElementById("answerD").value=res.answerD;
	}
	
	
	for(var i=0;i<4;i++){
		if(document.getElementsByName("answerR")[i].value==res.answerR){
			document.getElementsByName("answerR")[i].checked="checked";
		}
	}
}
function chkchapter(chapterid){
	document.getElementById("chapter").value=chapterid;
   }