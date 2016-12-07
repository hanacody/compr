function rankup_mailing(){		
	var startday;
	var endday;
	var id;
	var name;
	var email;
	var address;
	var mailing;
	var sex;	
	var date_chk;
	var url;	
	
	this.set_val=function(startday,endday,date_chk,id,name,email,address,mailing,sex){
		this.url='./regist.php';
		this.date_chk=date_chk;	//날짜 검색을 할것인가의 여부
		this.startday=startday;	//날짜 검색 시작일
		this.endday=endday;	//날짜 검색 종료일
		this.id=id;	//검색할 아이디
		this.name=name;	//이름
		this.email=email;	//이메일 주소
		this.address=address;	//주소
		this.mailing=mailing;	//메일링 허용여부
		this.sex=sex;	//성별		
		
	}
	this.search=function(){	
		var param = "mode=search&date_chk"+this.date_chk+"&startday="+this.start_day+"&endday="+this.endday+"&id="+this.id+"&name="+this.name;
		param+="&email="+this.email+"&address="+this.address+"&mailing="+this.mailing+"&sex="+this.sex;
		try{
			new net.content_loader(this.url,param,'','',this);
		} catch(err){
			alert(err.message);
		}
	}
	this.deal_xml=function(req){
		var result='';
		try{
			result=req.responseXML;
		} catch(err){
			alert(err.message);
		}
		try{
			alert(req.responseText);
		} catch(err){
			alert(err.message);
		}

		//메일을 보낼 값들
		var mail_type='default';	//기본 이미지 사용


		

		//해당 결과를 처리할 객체 추출
		var view=document.getElementById("result");
		view.innerHTML='';

		//전체 이메일 리스팅의 갯수
		try{
			var total_num=parseInt(result.getElementsByTagName("total")[0].firstChild.nodeValue);
		} catch(err){
			alert(err.message);
		}


		//결과 값이 있다면, 리스팅을 시작
		if(!total_num) {				
			//검색결과 출력
			var total_obj=document.createElement("div");
			total_obj.id="total_num";
			total_obj.style.height='20px';
			total_obj.innerHTML='총 '+total_num+'개의 게시물이 검색되었습니다. | 메일발송될주소보기';
			view.appendChild(total_obj);
			//메일작성 방법 선택
			var basic_show=document.createElement("basic_show");
			basic_show.id="basic_show";
			basic_show.innerHTML="<input type=radio name='type' onclick=\"this.mail_type='default';\">기본디자인으로 발송|<a href='javascript://' onclick=\"alert('---');\">기본디자인 보기</a> <input type=radio name='type' onclick=\"this.mail_type='direct';\">직접 등록";
			view.appendChild(basic_show);

			//제목 및 내용 입력 부분
			var mail_subject=document.createElement("div");
			mail_subject.id="mail_subject";
			mail_subject.innerHTML="메일 제목 : <input type='text' size=60><br>";
			mail_subject.innerHTML+="메일 내용 : <textarea id='mail_content'></textarea>";
			view.appendChild(mail_subject);

			var send_mail = document.createElement("div");
			send_mail.id="send_mail";
			send_mail.innerHTML="<input type=button value='메일보내기'>";
			view.appendChild(send_mail);
			try{
				if(result.getElementsByTagName("query")[0])
					var query=result.getElementsByTagName("query")[0].firstChild.nodeValue;
				else
					var query='';
			} catch(err){
				alert(err.message);
			}
			//send_mail.onclick=this.send_mail.call(this,query,mail_type,mail_subject.value,mail_content.value);
			
		} else {
			var total_obj=document.createElement("div");
			total_obj.id="total_num";
			total_obj.style.height='20px';
			total_obj.innerText='총 0개의 메일이 검색되었습니다.';
			view.appendChild(total_obj);
		}


	}
	this.get_value=function(obj,name){
		if(obj.getElementsByTagName(name)[0].firstChild)
			return obj.getElementsByTagName(name)[0].firstChild.nodeValue;
		else 
			return '';
	}
	this.send_mail=function(query,type,subject,content){
	}
}

	