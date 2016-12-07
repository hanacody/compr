/*========================================
js_rolling.js

#간단설명
<div><img /><img /></div>
라고 했을 경우 div안의 img를 위,오른쪽,아래,왼쪽으로 롤링 시킨다.


# 사용법
<script type="text/javascript" src="js_rolling.js"></script> 
//JS스크립트 로드

<div id='div1'><img /><img /><img /><img /><img /></div>
//처럼 구성후 div의 너비와 높이는 꼭 정해주기 바랍니다.

var roll = new js_rolling('rolling');
or
var roll = new js_rolling(document.getElementById('rolling'));
// id이름을 적던지, 직접 대상을 지목해서 롤링 클래스로 객체를 만듬

roll.set_direction(4); // 방향을 바꿈. 1: top, 2:right, 3:bottom 4:left 그외의 경우 동작안함
roll.move_gap = 1;	//움직이는 픽셀단위
roll.time_dealy = 10; //움직이는 타임딜레이
roll.time_dealy_pause = 5000;//하나의 대상이 새로 시작할 때 멈추는 시간, 0 이면 적용 안함
roll.start(); //롤링 동작

#주의
반향이 top,bottom일 경우 내부 태그는 block요소(div)로
반향이 left,right일 경우 내부태그는 inline요소(a,span)으로 해주세요.
FF에서 top,bottom의 경우 inline요소일 경우 offsetHeight를 잘못알아옵니다.


#사용제약
사용시 "공대여자는 예쁘다"를 나타내셔야합니다.

만든날 : 2007-06-07
만든이 : mins01,mins,공대여자
홈페이지 : http://mins01.zerock.net 
NateOn&MSN : mins01(at)lycos.co.kr
========================================*/
var js_rolling = function(this_s){
	// 시간단위는 ms로 1000이 1초
	if(this_s.nodeType==1){
		this.this_s = this_s;
	}else{
		this.this_s = document.getElementById(this_s);
	}
	this.is_rolling = false;
	this.direction = 1; //1:top, 2:right, 3:bottom, 4:left (시계방향) // 1번과 4번만 됨
	this.children =	null;
	this.move_gap = 1;	//움직이는 픽셀단위
	this.time_dealy = 100; //움직이는 타임딜레이
	this.time_dealy_pause = 1000;//하나의 대상이 새로 시작할 때 멈추는 시간, 0 이면 적용 안함
	this.time_timer=null;
	this.time_timer_pause=null;
	this.mouseover=false;
	this.init();
	this.set_direction(this.direction);
}
js_rolling.prototype.init = function(){
	this.this_s.style.position='relative';
	this.this_s.style.overflow='hidden';
	var children = this.this_s.childNodes;
	for(var i=(children.length-1);0<=i;i--){
		if(children[i].nodeType==1){
			children[i].style.position='relative';
		}else{
			this.this_s.removeChild(children[i]);
		}
	}
	var this_s=this;
	this.this_s.onmouseover=function(){
		this_s.mouseover=true;
		if(!this_s.time_timer_pause){
			this_s.pause();
		}
	}
	this.this_s.onmouseout=function(){
		this_s.mouseover=false;
		if(!this_s.time_timer_pause){
			this_s.resume();
		}
	}	
}
js_rolling.prototype.set_direction = function(direction){
	this.direction=direction;
	if(this.direction==2 ||this.direction==4){
		this.this_s.style.whiteSpace='nowrap';
	}else{
		this.this_s.style.whiteSpace='normal';
	}
	var children = this.this_s.childNodes;
	for(var i=(children.length-1);0<=i;i--){
			if(this.direction==1){
				children[i].style.display='block';
			}else if(this.direction==2){
				children[i].style.textAlign='right';
				children[i].style.display='inline';
			}else if(this.direction==3){
				children[i].style.display='block';
			}else if(this.direction==4){
				children[i].style.display='inline';
			}
	}
	this.init_element_children();	
}
js_rolling.prototype.init_element_children = function(){
	var children = this.this_s.childNodes;
	this.children = children;
	for(var i=(children.length-1);0<=i;i--){
			if(this.direction==1){
				children[i].style.top='0px';
			}else if(this.direction==2){
				children[i].style.left='-'+this.this_s.firstChild.offsetWidth+'px';
			}else if(this.direction==3){
				children[i].style.top='-'+this.this_s.firstChild.offsetHeight+'px';
			}else if(this.direction==4){
				children[i].style.left='0px';
			}
	}
}
js_rolling.prototype.act_move_up = function(){
	for(var i = 0,m=this.children.length;i<m;i++){
		var child = this.children[i];
		child.style.top=(parseInt(child.style.top)-this.move_gap)+'px';
	}
	if((this.children[0].offsetHeight+parseInt(this.children[0].style.top))<=0){
		this.this_s.appendChild(this.children[0]);
		this.init_element_children();
		if(this.time_dealy_pause){
			var this_s = this;
			var act = function(){this_s.resume();this_s.time_timer_pause=null;}
			this.time_timer_pause = setTimeout(act,this.time_dealy_pause);
			this.pause();
		}		
	}
}
js_rolling.prototype.act_move_down = function(){
	for(var i = 0,m=this.children.length;i<m;i++){
		var child = this.children[i];
		child.style.top=(parseInt(child.style.top)+this.move_gap)+'px';
	}
	if(parseInt(this.children[0].style.top)>=0){
		this.this_s.insertBefore(this.this_s.lastChild,this.this_s.firstChild);
		this.init_element_children();
		if(this.time_dealy_pause){
			var this_s = this;
			var act = function(){this_s.resume();this_s.time_timer_pause=null;}
			this.time_timer_pause = setTimeout(act,this.time_dealy_pause);
			this.pause();
		}		
	}
}
js_rolling.prototype.act_move_left = function(){
	for(var i = 0,m=this.children.length;i<m;i++){
		var child = this.children[i];
		child.style.left=(parseInt(child.style.left)-this.move_gap)+'px';
	}
	if((this.children[0].offsetWidth+parseInt(this.children[0].style.left))<=0){
		this.this_s.appendChild(this.this_s.firstChild);
		this.init_element_children();
		if(this.time_dealy_pause){
			var this_s = this;
			var act = function(){this_s.resume();this_s.time_timer_pause=null;}
			this.time_timer_pause = setTimeout(act,this.time_dealy_pause);
			this.pause();
		}				
	}
}
js_rolling.prototype.act_move_right = function(){
	for(var i = 0,m=this.children.length;i<m;i++){
		var child = this.children[i];
		child.style.left=(parseInt(child.style.left)+this.move_gap)+'px';
	}
	
	if(parseInt(this.this_s.lastChild.style.left)>=0){
		this.this_s.insertBefore(this.this_s.lastChild,this.this_s.firstChild);
		this.init_element_children();
		if(this.time_dealy_pause){
			var this_s = this;
			var act = function(){this_s.resume();this_s.time_timer_pause=null;}
			this.time_timer_pause = setTimeout(act,this.time_dealy_pause);
			this.pause();
		}				
	}
}
js_rolling.prototype.start = function(){ //롤링 시작
	var this_s = this;
	this.stop();
	this.is_rolling = true;
	var act = function(){
		if(this_s.is_rolling){
			if(this_s.direction==1){this_s.act_move_up();}
			else if(this_s.direction==2){this_s.act_move_right();}
			else if(this_s.direction==3){this_s.act_move_down();}
			else if(this_s.direction==4){this_s.act_move_left();}
		}
	}
	this.time_timer = setInterval(act,this.time_dealy);
}
js_rolling.prototype.pause = function(){ //일시 멈춤
	this.is_rolling = false;
}
js_rolling.prototype.resume = function(){ //일시 멈춤 해제
	if(!this.mouseover){
		this.is_rolling = true;
	}
}
js_rolling.prototype.stop = function(){ //롤링을 끝냄
	this.is_rolling = false;
	if(!this.time_timer){
		clearInterval(this.time_timer);
	}
	this.time_timer = null
}