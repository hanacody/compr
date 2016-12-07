/*========================================
js_rolling.js

#���ܼ���
<div><img /><img /></div>
��� ���� ��� div���� img�� ��,������,�Ʒ�,�������� �Ѹ� ��Ų��.


# ����
<script type="text/javascript" src="js_rolling.js"></script> 
//JS��ũ��Ʈ �ε�

<div id='div1'><img /><img /><img /><img /><img /></div>
//ó�� ������ div�� �ʺ�� ���̴� �� �����ֱ� �ٶ��ϴ�.

var roll = new js_rolling('rolling');
or
var roll = new js_rolling(document.getElementById('rolling'));
// id�̸��� ������, ���� ����� �����ؼ� �Ѹ� Ŭ������ ��ü�� ����

roll.set_direction(4); // ������ �ٲ�. 1: top, 2:right, 3:bottom 4:left �׿��� ��� ���۾���
roll.move_gap = 1;	//�����̴� �ȼ�����
roll.time_dealy = 10; //�����̴� Ÿ�ӵ�����
roll.time_dealy_pause = 5000;//�ϳ��� ����� ���� ������ �� ���ߴ� �ð�, 0 �̸� ���� ����
roll.start(); //�Ѹ� ����

#����
������ top,bottom�� ��� ���� �±״� block���(div)��
������ left,right�� ��� �����±״� inline���(a,span)���� ���ּ���.
FF���� top,bottom�� ��� inline����� ��� offsetHeight�� �߸��˾ƿɴϴ�.


#�������
���� "���뿩�ڴ� ���ڴ�"�� ��Ÿ���ž��մϴ�.

���糯 : 2007-06-07
������ : mins01,mins,���뿩��
Ȩ������ : http://mins01.zerock.net 
NateOn&MSN : mins01(at)lycos.co.kr
========================================*/
var js_rolling = function(this_s){
	// �ð������� ms�� 1000�� 1��
	if(this_s.nodeType==1){
		this.this_s = this_s;
	}else{
		this.this_s = document.getElementById(this_s);
	}
	this.is_rolling = false;
	this.direction = 1; //1:top, 2:right, 3:bottom, 4:left (�ð����) // 1���� 4���� ��
	this.children =	null;
	this.move_gap = 1;	//�����̴� �ȼ�����
	this.time_dealy = 100; //�����̴� Ÿ�ӵ�����
	this.time_dealy_pause = 1000;//�ϳ��� ����� ���� ������ �� ���ߴ� �ð�, 0 �̸� ���� ����
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
js_rolling.prototype.start = function(){ //�Ѹ� ����
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
js_rolling.prototype.pause = function(){ //�Ͻ� ����
	this.is_rolling = false;
}
js_rolling.prototype.resume = function(){ //�Ͻ� ���� ����
	if(!this.mouseover){
		this.is_rolling = true;
	}
}
js_rolling.prototype.stop = function(){ //�Ѹ��� ����
	this.is_rolling = false;
	if(!this.time_timer){
		clearInterval(this.time_timer);
	}
	this.time_timer = null
}