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
		this.date_chk=date_chk;	//��¥ �˻��� �Ұ��ΰ��� ����
		this.startday=startday;	//��¥ �˻� ������
		this.endday=endday;	//��¥ �˻� ������
		this.id=id;	//�˻��� ���̵�
		this.name=name;	//�̸�
		this.email=email;	//�̸��� �ּ�
		this.address=address;	//�ּ�
		this.mailing=mailing;	//���ϸ� ��뿩��
		this.sex=sex;	//����		
		
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

		//������ ���� ����
		var mail_type='default';	//�⺻ �̹��� ���


		

		//�ش� ����� ó���� ��ü ����
		var view=document.getElementById("result");
		view.innerHTML='';

		//��ü �̸��� �������� ����
		try{
			var total_num=parseInt(result.getElementsByTagName("total")[0].firstChild.nodeValue);
		} catch(err){
			alert(err.message);
		}


		//��� ���� �ִٸ�, �������� ����
		if(!total_num) {				
			//�˻���� ���
			var total_obj=document.createElement("div");
			total_obj.id="total_num";
			total_obj.style.height='20px';
			total_obj.innerHTML='�� '+total_num+'���� �Խù��� �˻��Ǿ����ϴ�. | ���Ϲ߼۵��ּҺ���';
			view.appendChild(total_obj);
			//�����ۼ� ��� ����
			var basic_show=document.createElement("basic_show");
			basic_show.id="basic_show";
			basic_show.innerHTML="<input type=radio name='type' onclick=\"this.mail_type='default';\">�⺻���������� �߼�|<a href='javascript://' onclick=\"alert('---');\">�⺻������ ����</a> <input type=radio name='type' onclick=\"this.mail_type='direct';\">���� ���";
			view.appendChild(basic_show);

			//���� �� ���� �Է� �κ�
			var mail_subject=document.createElement("div");
			mail_subject.id="mail_subject";
			mail_subject.innerHTML="���� ���� : <input type='text' size=60><br>";
			mail_subject.innerHTML+="���� ���� : <textarea id='mail_content'></textarea>";
			view.appendChild(mail_subject);

			var send_mail = document.createElement("div");
			send_mail.id="send_mail";
			send_mail.innerHTML="<input type=button value='���Ϻ�����'>";
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
			total_obj.innerText='�� 0���� ������ �˻��Ǿ����ϴ�.';
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

	