var xmlHttpRequest;
function getXmlHttpObject(){
	
	if(window.ActiveXObject){
		xmlHttpRequest=new ActiveXObject("Microsoft.XMLHTTP");
	}else{
		xmlHttpRequest=new XMLHttpRequest();
	}
	return xmlHttpRequest;

}	
var myXmlHttprequest=getXmlHttpObject();
