//##################################################################################
//## 이미지 슬라이더
//##################################################################################
var protoFade = {
	initialize: function(element, options) {
		this.options = {
      		duration: 1,
			delay: 7.0,
			randomize: false,
			autostart:true,
			eSquare:true,
			eRows: 3,
			eCols:5,
			eColor: '#FFFFFF'
    	}
		Object.extend(this.options, options || {});
    	this.element        = $(element);
		this.slides			= this.element.childElements();
		this.num_slides		= this.slides.length;
		this.current_slide 	= (this.options.randomize) ? (Math.floor(Math.random()*this.num_slides)) : 0;
		this.end_slide		= this.num_slides - 1;

		this.slides.invoke('hide');
		this.slides[this.current_slide].show();
		if(this.end_slide == 0) return false; //이미지가 1개인경우 실행노 
		if (this.options.autostart) {
			this.startSlideshow();
		}
		if (this.options.eSquare) {
			this.buildEsquare();
		}
	},
	buildEsquare: function() {
		this.eSquares 	= [];
		var elDimension	 	= this.element.getDimensions();
		var elWidth  		= elDimension.width;
		var elHeight 		= elDimension.height;
		var sqWidth 		= elWidth / this.options.eCols;
		var sqHeight 		= elHeight / this.options.eRows;
		$R(0, this.options.eCols-1).each(function(col) {
			this.eSquares[col] = [];
			$R(0, this.options.eRows-1).each(function(row) {
				var sqLeft = col * sqWidth;
			    var sqTop  = row * sqHeight;
				this.eSquares[col][row] = new Element('div').setStyle({
 														    opacity: 0, backgroundColor: this.options.eColor,
															position: 'absolute', 'z-index': 5,
															left: sqLeft + 'px', top: sqTop + 'px',
															width: sqWidth + 'px', height: sqHeight + 'px'
														});
				this.element.insert(this.eSquares[col][row]);
			}.bind(this))
		}.bind(this));
	},

	startSlideshow: function(event) {
		if (event) { Event.stop(event); }
		if (!this.running)	{
			this.executer = new PeriodicalExecuter(function(){
	  			this.updateSlide(this.current_slide+1);
	 		}.bind(this),this.options.delay);
			this.running=true;
		}
	},
	stopSlideshow: function(event) {
		if (event) { Event.stop(event); }
		if (this.executer) {
			this.executer.stop();
			this.running=false;
		}
	},
	moveToPrevious: function (event) {
		if (event) { Event.stop(event); }
		this.stopSlideshow();
  		this.updateSlide(this.current_slide-1);
	},

	moveToNext: function (event) {
		if (event) { Event.stop(event); }
		this.stopSlideshow();
  		this.updateSlide(this.current_slide+1);
	},
	updateSlide: function(next_slide) {
		if(this.end_slide == 0) return false; //이미지가 1개인경우 실행노 
		if(next_slide == this.current_slide) return false;
		if (next_slide > this.end_slide) {
			next_slide = 0;
		}
		else if ( next_slide == -1 ) {
			next_slide = this.end_slide;
		}
		this.fadeInOut(next_slide, this.current_slide);
	},
 	fadeInOut: function (next, current) {
		new Effect.Parallel([
			new Effect.Fade(this.slides[current], { sync: true }),
			new Effect.Appear(this.slides[next], { sync: true })
  		], { duration: this.options.duration });
		this.current_slide = next;

		var items = $('img_button').select('img');
		$(items).each(function(item, idx){
			if(idx==next) item.src = item.getAttribute('on');
			else item.src = item.getAttribute('out');
		});

	},
	delayedAppear: function(eSquare)	{
		var opacity = Math.random();
		new Effect.Parallel([
			new Effect.Appear ( eSquare, { from: 0, to: opacity, duration: this.options.duration/4 } ),
			new Effect.Appear ( eSquare, { from: opacity, to: 0, duration: this.options.duration/1.25} )
		], { sync: false });
	}
}

init.protofade = protoFade;