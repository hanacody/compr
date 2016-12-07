var net = new Object();
net.ready_state_unready=0;
net.ready_state_loading=1;
net.ready_state_loaded=2;
net.ready_state_ineractive=3;
net.ready_state_complete=4;

net.content_loader=function(url,param,method,header,obj){	
		//encodeURIComponent(param);
		this.req=null;		

		this.load_xml_doc(url,param,method,header,obj);	
		
}

net.content_loader.prototype={
		load_xml_doc:function(url,param,method,header,obj){		
			if(!method)
				method="POST";
			if(method=="POST" && !header)
				header="application/x-www-form-urlencoded";

			if(window.XMLHTTPRequest)
				this.req=new XMLHTTPRequest();
			else if(window.ActiveXObject)
				this.req=new ActiveXObject("Microsoft.XMLHTTP");
			if(this.req){
					try{
							var loader=this;
							this.req.onreadystatechange=function(){
								loader.on_ready_state.call(loader,obj);
							}
							try{
								this.req.open(method,url,true);
							} catch(err){
								alert("연결에러");
							}
							try{
							if(header && method=="POST")
									this.req.setRequestHeader("Content-Type",header);
									//this.req.setRequestHeader("enctype","multipart/form-data");
							} catch(err){
								alert("헤더에러");
							}
							try{
								if(param)
									this.req.send(param);
								else 
									this.req.send(null);
							} catch(err){
								alert("send에러");
							}
					} catch(err){	
						alert("연결 에러");
					}
			}
		},
		on_ready_state:function(obj){	
				
				var req=this.req;
				var ready=req.readyState;				
				if(ready==net.ready_state_complete){
						var status=req.status;	
				
						if(status==200){
							var result=req.responseXML;			
							var result2=req.responseText;
							//alert(result2);							
							if(obj){
								obj.deal_xml(req);
							} else if(result2=='yes') {
								alert('저장되었습니다.');								
							} else if(result2 =='no'){
								alert('저장이 실패하였습니다.');
							} else{
								alert(result2);
							}
							
							
						}
				}
		}
		
}