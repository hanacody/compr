//공통적으로 사용하는 유틸리티 클래스
var rankup_util = function (){
	

	this.chkJumin=function(form,ssn1,ssn2){	//주민등록의 유효성을 검사 하는 함수
		var FM = document.forms[form]; 	
		var obj1 = FM.elements[ssn1];
		var obj2 = FM.elements[ssn2];
		if(!obj1.value || !obj2.value || obj1.value.lenghg<6 || obj2.value.lenght<7){ 
			alert('주민등록 번호를 확인하세요.'); 
			obj1.focus(); 
			return false; 
		} 

		var f1=obj1.value.substring(0,1) 
		var f2=obj1.value.substring(1,2) 
		var f3=obj1.value.substring(2,3) 
		var f4=obj1.value.substring(3,4) 
		var f5=obj1.value.substring(4,5) 
		var f6=obj1.value.substring(5,6) 
		var hap=f1*2+f2*3+f3*4+f4*5+f5*6+f6*7 
		var l1=obj2.value.substring(0,1) 
		var l2=obj2.value.substring(1,2) 
		var l3=obj2.value.substring(2,3) 
		var l4=obj2.value.substring(3,4) 
		var l5=obj2.value.substring(4,5) 
		var l6=obj2.value.substring(5,6) 
		var l7=obj2.value.substring(6,7) 
		hap=hap+l1*8+l2*9+l3*2+l4*3+l5*4+l6*5 
		hap=hap%11 
		hap=11-hap 
		hap=hap%10 

		if (hap != l7){ 
			alert('잘못된 주민등록번호 입니다'); 
			obj1.value='';obj2.value='';
			obj1.focus(); 
			return false; 
		} 
	}

	this.chkPW=function(form,pw1,pw2){
		var pw1=document.forms[form].elements[pw1];
		var pw2=document.forms[form].elements[pw2];	
		if(pw1.value != pw2.value){
			alert('비밀번호가 일치하지 않습니다.');
			pw1.value=pw2.value='';
			pw1.focus();
			return false;
		}
	}


	//프레임의 크기를 실시간으로 제어하는 스크립트		
	this.resizeFrame=function(iframeObj){
		var innerBody = iframeObj.contentWindow.document.body;
		oldEvent = innerBody.onclick;
		//innerBody.onclick = function(){ resizeFrame(iframeObj, 1);oldEvent; };
		var innerHeight = innerBody.scrollHeight + (innerBody.offsetHeight - innerBody.clientHeight);        
		iframeObj.style.height = innerHeight;
		//var innerWidth = innerBody.scrollWidth + (innerBody.offsetWidth - innerBody.clientWidth);
		//iframeObj.style.width = innerWidth;             
	}
	this.loadData=function(source,target,tname,fname) {
		var trigger = source.value;
		var form = fname;
		//dynamic.src = "/Libs/dynamic.php?form=" + form + "&trigger=" + trigger + "&target=" + target + "&tname="+ tname;	
	}
	
	this.confirmId=function(form,field){
		win_open('../member/id_check.html?form='+form+'&field='+field,400,160);
	}
	
	/*
	function changeImage(val,name){
		if(val)
			imgShow.style.display='';
		var img=new Image();
		img.src=val;
		var obj=document.images[name];
		obj.src=img.src;
		obj.width=200;
		obj.height=200;

	}
	*/
	this.chkObj=function(form,field, i){
		var objID   = document.forms[form].elements[field];
		return (objID.length > 0 ? objID[i] : objID);
	}//end method 

	// 체크 박스 총 갯수
	this.checkBoxLength=function(form,field){
		var objID   = document.forms[form].elements[field];
		var len   = 1;
		// 체크 박스가 없을때 ~
		if( objID == null)
			return len = 0;
		// 체크 박스가 1개 이상일때
		if(objID.length > 1 )
			len=objID.length;
		return len; 

	}//end function 

	// 체크박스에 체크한 총 갯수
	this.checkAllCount=function(form,field){
		var checkBoxTotalCount = 0;
		for (var i=0; i < this.checkBoxLength(form,field); i++){
			if(this.chkObj(form,field,i).checked){
				++checkBoxTotalCount;
			}//end if
		}//end for
		return checkBoxTotalCount;
	}//end function 

	this.plusComma=function(num){			
		if (num < 0) { num *= -1; var minus = true}
			else var minus = false

		var dotPos = (num+"").split(".")
		var dotU = dotPos[0]
		var dotD = dotPos[1]
		var commaFlag = dotU.length%3

		if(commaFlag) {
			var out = dotU.substring(0, commaFlag) 
			if (dotU.length > 3) out += ","
		}
		else var out = ""

		for (var i=commaFlag; i < dotU.length; i+=3) {
			out += dotU.substring(i, i+3) 
			if( i < dotU.length-3) out += ","
		}

		if(minus) out = "-" + out
		if(dotD)
			return out + "." + dotD
		else 
			return out 
	}

	this.getCookie=function(name) {
		var nameOfCookie = name + "=";
		var x = 0;
		while ( x <= document.cookie.length ) {
			var y = (x+nameOfCookie.length);
			if ( document.cookie.substring( x, y ) == nameOfCookie ) {
			if ( (endOfCookie=document.cookie.indexOf( ";", y )) == -1 )
				endOfCookie = document.cookie.length;
				return unescape( document.cookie.substring( y, endOfCookie ) );
			}
			x = document.cookie.indexOf( " ", x ) + 1;
			if ( x == 0 )
				break;
		}
		return "";
	}
	this.setCookie=function(name, value, expiredays){ 
		var todayDate = new Date(); 
		todayDate.setDate(todayDate.getDate() + expiredays); 
		document.cookie = name + "=" + escape(value) + "; path=/; expires=" + todayDate.toGMTString() + ";" 
	}
	
}

rankup_util.win_open=function(val,x,y,left,top){//새 창을 띄우는 함수
		//새창의 크기
		cw=x;
		ch=y;

		//스크린의 크기
		sw=screen.availWidth;
		sh=screen.availHeight;

		//열 창의 포지션
		if(!left)
			px=(sw-cw)/2;
		else
			px=Number(left)+20;
		if(!top)
			py=(sh-ch)/2;
		else
			py=Number(top)+20; 

		//창을 여는부분
		var new_win=window.open(val,'new_win','left='+px+',top='+py+',width='+cw+',height='+ch+',toolbar=no,menubar=no,status=no,scrollbars=yes,resizable=no');
		return new_win;
}

rankup_util.onlyDigit = function(el) {
		el.value = el.value.replace(/\D/g,'');
}

rankup_util.goNextTab =function(form,el,el2){	//주민등록 번호 다음단계로 이동하기
		onlyDigit(el);	
		var str = document.forms[form].elements[el].value;
		if(str.length == 6) document.forms[form].elements[el2].focus();	 
}