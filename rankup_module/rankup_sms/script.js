// 특수문자, 이모티콘 스크립트
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);

function MM_findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring()(p+1)].document; n=n.substring()(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MM_showHideLayers() { //v3.0
  var i,p,v,obj,args=MM_showHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) if ((obj=MM_findObj(args[i]))!=null) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'visible':(v='hide')?'hidden':v; }
    obj.visibility=v; }
}

function OnCheckPhone(oTa) {
	var oForm = oTa.form ;
	var onlynum = "" ;
	var substr = "";
	onlynum = oForm.phone123.value;

	if (GetMsgLen(onlynum) == 3)
	{
		if (onlynum == '011' || onlynum =='016' || onlynum == '017' || onlynum == '018' || onlynum == '019' || onlynum =='013')
		{

		}
		else
		{
			alert('유효하지 않은 통신사 번호입니다.\n\n011 ~ 019, 0130 로 입력하세요.');
			oTa.value = '';
			return;
		}
	}
	if (GetMsgLen(onlynum) == 4)
	{
		substr = onlynum.substring(0,3);

		if (substr != '010' && substr != '011' && substr != '016' && substr != '017' && substr != '018' && substr != '019' && onlynum != '0130'){
			alert('유효하지 않은 통신사 번호입니다.\n\n011 ~ 019, 0130 로 입력하세요.');
			oTa.value = '';
			return;
		}
	}
}

function onlynum()
{
	if (((event.keyCode<48)||(event.keyCode>57)) && event.keyCode!=44)
		event.returnValue=false;
}

function GetMsgLen(sMsg)
{ // 0-127 1byte, 128~ 2byte
	var count = 0
	for(var i=0; i<sMsg.length; i++)
	{
		if ( sMsg.charCodeAt(i) > 127 ) 
		{			
			count += 2
		}
		else {
			count++
		}
	}
	return count
}

function SelText()
{
 	if (document.visual_phone.txtMessage.value == "<메시지는 여기에 입력해주세요>")
 	{
   		document.visual_phone.txtMessage.value = "";
 	}
	document.visual_phone.txtMessage.msglen = 0;
}

//메시지 입력시 string() 길이 체크
function checklen()
{
	var msgtext, msglen;
	
	msgtext = document.visual_phone.txtMessage.value;
	msglen = document.visual_phone.msglen.value;
	
	var i=0,l=0;
	var temp,lastl;
	
	//길이를 구한다.
	while(i < msgtext.length)
	{
		temp = msgtext.charAt(i);
		
		if (escape(temp).length > 4)
			l+=2;
		else if (temp!='\r')
			l++;
		// OverFlow
		if(l>80)
		{
			alert("메시지란에 허용 길이 이상의 글을 쓰셨습니다.\n 메시지란에는 한글 40자, 영문80자까지만 쓰실 수 있습니다.");
			temp = document.visual_phone.txtMessage.value.substr(0,i);
			document.visual_phone.txtMessage.value = temp;
			l = lastl;
			break;
		}
		lastl = l;
		i++;
	}
	visual_phone.msglen.value=l;
}

function resv_divopen() 
{	
	if (document.visual_phone.calltype.checked == true) 
	{
		document.all["divResv"].style.display = "";	
	} 
	else  
	{
		document.all["divResv"].style.display = "none";	
	}
}

function Check()
{
	var i;
	var form;
	form = document.visual_phone;
	
	if(navigator.appVersion.indexOf("MSIE 5") != -1 || navigator.appVersion.indexOf("MSIE 6") != -1){
		if(document.visual_phone.txtMessage.value.length == 0 || document.visual_phone.txtMessage.value == "<메시지는 여기에 입력해주세요>")
		{
			alert("메시지를 입력 해주세요");
			SelText();
			document.visual_phone.txtMessage.focus();
			return ;
		}
	}

	for (i=0; i<document.visual_phone.reqnumber.value.length;i++)
		{
			if (document.visual_phone.reqnumber.value.charCodeAt(i) < 48 || document.visual_phone.reqnumber.value.charCodeAt(i) > 57)
			{
				alert("특수문자가 들어갔습니다.");
				document.visual_phone.reqnumber.focus();
				return ;
			}
	}

	if(form.phone123.value == "")	
	{
		alert("전송할 번호를 넣어주세요");
		form.phone123.focus();
		return;
	}

	if(GetMsgLen(form.phone123.value) > 11 || GetMsgLen(form.phone123.value) < 10)
	{
		alert("전송할 번호의 길이가 길거나 짧습니다.");
		form.phone123.focus();
		return;
	}
	
	if(document.visual_phone.calltype.checked == true)
	{
		var time = new Date();
		//윤년 체크
		var leap_year = 0;
		if( (!(Number(form.yy.value)%4) && Number(form.yy.value)%100) || !(Number(form.yy.value)%400) )
		{
			leap_year=1;		//윤년일때..
		}
		if(form.yy.value.length == 0) {
			alert("년도를 입력하세요.");
			form.yy.focus();
			return;
		}
		if(isKindStr(form.yy.value) == false) {
			alert("년도를 정확하게 입력하세요.");
			document.visual_phone.yy.focus();
			return ;
		}
		if(form.mm.value.length == 0) {
			alert("월을 입력하세요.");
			document.visual_phone.mm.focus();
			return ;
		}
		if(isKindStr(form.mm.value) == false) {
			alert("월을 정확하게 입력하세요.");
			document.visual_phone.mm.focus();
			return ;
		}
		if(form.mm.value < 1 || form.mm.value > 12) {
			alert("월을 정확하게 입력하세요(1∼12).");
			document.visual_phone.mm.focus();
			return ;
		}
		if(form.dd.value.length == 0) {
			alert("일자를 입력하세요.");
			document.visual_phone.dd.focus();
			return ;
		}
		if(isKindStr(form.dd.value) == false) {
			alert("일자를 정확하게 입력하세요.");
			document.visual_phone.dd.focus();
			return ;
		}
// 2월 체크하는부분...
		if(form.mm.value == 2 && (form.dd.value < 1 || form.dd.value > (28 + Number(leap_year)))) {
			alert("일자를 정확하게 입력하세요. (1∼"+(28 + Number(leap_year))+").");
			document.visual_phone.dd.focus();
			return ;
		}
// 30일까지 있늘 달 체크
		if( (form.mm.value == 4 || form.mm.value == 6 || form.mm.value == 9 || form.mm.value == 11 ) && (form.dd.value < 1 || form.dd.value > 30) ){
			alert("일자를 정확하게 입력하세요(1∼30).");
			document.visual_phone.dd.focus();
			return ;
		}
// 31일까지 있는 달 체크
		else if(form.dd.value < 1 || form.dd.value > 31) {
			alert("일자를 정확하게 입력하세요(1∼31).");
			document.visual_phone.dd.focus();
			return ;
		}
		if(form.h.value.length == 0) {
			alert("시간을 입력하세요.");
			document.visual_phone.h.focus();
			return ;
		}
		if(isKindStr(form.h.value) == false) {
			alert("시간을 정확하게 입력하세요.");
			document.visual_phone.h.focus();
			return ;
		}
		if(form.h.value < 0 || form.h.value > 23) {
			alert("시간을 정확하게 입력하세요(00∼23).");
			document.visual_phone.h.focus();
			return;
		}
		/*# 취침시간 설정 #######################*/
		if(form.sleep_mode.value=="yes") {
			if(parseInt(form.sleep_s_time.value,10) <= parseInt(form.h.value,10) || parseInt(form.sleep_e_time.value,10) > parseInt(form.h.value,10)) {
				alert("죄송합니다. 취침설정된 시간에는 문자메시지를 발송할 수 없습니다.          \n\n취침시간대를 피해 예약시간을 입력하여 주십시오.(취침설정:"+form.sleep_s_time.value+"시부터 "+form.sleep_e_time.value+"시까지)");
				document.visual_phone.h.focus();
				return;
			}
		}
		/*###################################*/
		if(form.m.value.length == 0) {
			alert("시간을 입력하세요.");
			document.visual_phone.m.focus();
			return ;
		}
		if(isKindStr(form.m.value) == false) {
			alert("시간을 정확하게 입력하세요.");
			document.visual_phone.m.focus();
			return ;
		}
		if(form.m.value < 0 || form.h.value > 59) {
			alert("시간을 정확하게 입력하세요.(00∼59).");
			document.visual_phone.m.focus();
			return ;
		}
		if(Number(form.yy.value) < Number(form.current_yy.value)) {
			alert("현재 이후의 연도를 입력하세요.");
			document.visual_phone.yy.focus();
			return ;
		}
		else { 
			if((Number(form.yy.value) == Number(form.current_yy.value)) && (Number(form.mm.value) < Number(form.current_mm.value))) {
				alert("현재 이후의 월을 입력하세요.");
				document.visual_phone.mm.focus();
				return ;
			}
			else {
				if((Number(form.yy.value) == Number(form.current_yy.value)) && (Number(form.mm.value) == (Number(form.current_mm.value))) && (Number(form.dd.value) < Number(form.current_dd.value))) {
					alert("현재 이후의 날자를 입력하세요.");
					document.visual_phone.dd.focus();
					return ;
				}
				else {
					if((Number(form.yy.value) == Number(form.current_yy.value)) && (Number(form.mm.value) == (Number(form.current_mm.value))) && (Number(form.dd.value) == Number(form.current_dd.value)) && (Number(form.h.value) < Number(form.current_h.value))) {
						alert("현재 이후의 시간을 입력하세요.");
						document.visual_phone.h.focus();
						return ;
					}
					else {
						if((Number(form.yy.value) == Number(form.current_yy.value)) && (Number(form.mm.value) == (Number(form.current_mm.value))) && (Number(form.dd.value) == Number(form.current_dd.value)) && (Number(form.h.value) == Number(form.current_h.value)) && (Number(form.m.value) < Number(form.current_m.value))) {
							alert("현재 이후의 시간(분)을 입력하세요.");
							document.visual_phone.m.focus();
							return ;
						}
					}
				}
			}
		}
	}

	if(navigator.appVersion.indexOf("MSIE 5") != -1 || navigator.appVersion.indexOf("MSIE 6") != -1){
		if(confirm('메시지 :\n'+document.visual_phone.txtMessage.value+'\n'+'받는사람 : '+  document.visual_phone.phone123.value +  '\n\n전송하시겠습니까??'))
		{
			//document.visual_phone.action='regist.php';
			//document.visual_phone.submit();
			try {
				var mode = 'send_sms_one';
				var user = form.user.value;
				var phone123 = form.phone123.value;
				var txtMessage = form.txtMessage.value;
				txtMessage = encodeURIComponent(txtMessage);
				var reqnumber = form.reqnumber.value;
				var calltype = form.calltype.checked==true ? 2 : '';
				var yy = form.yy.value;
				var mm = form.mm.value;
				var dd = form.dd.value;
				var h = form.h.value;
				var m = form.m.value;
			} catch(e){
				alert(e.message);
			}


			var param = "&mode="+mode+"&user="+user+"&phone123="+phone123+"&txtMessage="+txtMessage+"&reqnumber="+reqnumber;
			param+="&calltype="+calltype+"&yy="+yy+"&mm="+mm+"&dd="+dd+"&h="+h+"&m="+m;

			var url = "./regist.php";

			//alert(param);
			
			try {
				new net.content_loader(url, param);
			} catch(e){
				alert(e.message);
			}
		}
		else
		{
			return;
		}
	}
	else{
		if(confirm('메시지 :\n'+document.visual_phone.txtMessage.value+'\n'+'받는사람 : '+  document.visual_phone.phone123.value +  '\n\n전송하시겠습니까??'))
		{
			//document.visual_phone.action='regist.php';
			//document.visual_phone.submit();
			try {
				var mode = 'send_sms_one';
				var user = form.user.value;
				var phone123 = form.phone123.value;
				var txtMessage = form.txtMessage.value;
				txtMessage = encodeURIComponent(txtMessage);
				var reqnumber = form.reqnumber.value;
				var calltype = form.calltype.checked==true ? 2 : '';
				var yy = form.yy.value;
				var mm = form.mm.value;
				var dd = form.dd.value;
				var h = form.h.value;
				var m = form.m.value;
			} catch(e){
				alert(e.message);
			}
			
		

			var param = "&mode="+mode+"&user="+user+"&phone123="+phone123+"&txtMessage="+txtMessage+"&reqnumber="+reqnumber;
			param+="&calltype="+calltype+"&yy="+yy+"&mm="+mm+"&dd="+dd+"&h="+h+"&m="+m;

			var url = "./regist.php";

			try {
				new net.content_loader(url, param);
			} catch(e){
				alert(e.message);
			}


		}
		else
		{
			return;
		}
	}
}

function AddChar(ch)
{
	var t;
	var msglen;
	var tempstr;
	msglen = 0;
	if (document.visual_phone.txtMessage.value == "<메시지는 여기에 입력해주세요>")
	{
			document.visual_phone.txtMessage.value = "";
			document.visual_phone.txtMessage.msglen = 0;
	}
	document.visual_phone.txtMessage.value = document.visual_phone.txtMessage.value + ch;
	tempstr = document.visual_phone.txtMessage.value;
	l = document.visual_phone.txtMessage.value.length;
	for(k=0;k<l;k++){
	t = document.visual_phone.txtMessage.value.charAt(k);
	if (escape(t).length > 4)
		msglen += 2;
	else
		msglen++;
	}
	if (msglen>80)
	{
		alert("비고란에 허용 길이 이상의 글을 쓰셨습니다.\n 비고란에는 한글 40자, 영문80자까지만 쓰실 수 있습니다.");
		cut_text(tempstr);
	}
	document.visual_phone.msglen.value = msglen;
}

function isKindStr(str)
{
   for (i = 0; i < str.length; i++) 
   {
	if (('0' <= str.charAt(i))&&(str.charAt(i) <= '9')){
		 continue;
	}
	else {
		 return false;
	}
  }
  return true;
}

function openWindow(mobile)
{
	/*
	if (mobile.substring(0, 2) != '01') {
		alert("핸드폰 번호가 아닙니다.");
		return;
	}
	*/
	window.open("../surem/visual_phone.html?mobile="+mobile, "vpWin", "width=154,height=400");
}
function onlyDigit(el) {
el.value = el.value.replace(/\D/g,'');
}