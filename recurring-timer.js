var RT_Widget;
var rt_timers = [];

function rt_update() {
	setTimeout('rt_update()', 1000);
	for ( var i = 0; i < rt_timers.length; i++ ) {
		rt_timers[i].update();
	}
}

(function($){
	RT_Widget = function(start, end, start_next, css_class) {

		this.update = function() {
			var now = new Date();
			//alert(now.toLocaleString());

			if(now >= this.start && now < this.end) {
				$(this.css_class + ' .rt-countdown').hide();
				$(this.css_class + ' .rt-until').hide();
				$(this.css_class + ' .rt-on').show();
				return;
			} else if(now > this.end) {
				this.start = this.start_next;
			}
			
			//there is probably a better way to do this
			var total = (this.start - now) / 1000; //remove millis
			var days = parseInt(total / 60 / 60 / 24);
			total -= days * 60 * 60 * 24; //remove days
			var hours = parseInt(total / 60 / 60);
			total -= hours * 60 * 60; //remove hours
			var minutes = parseInt(total / 60);
			total -= minutes * 60; //remove minutes
			var seconds = parseInt(total);
			
			$(this.css_class + ' .rt-on').hide();
			$(this.css_class + ' .rt-days').html(days);
			$(this.css_class + ' .rt-hours').html(hours);
			$(this.css_class + ' .rt-minutes').html(minutes);
			$(this.css_class + ' .rt-seconds').html(seconds);	
			$(this.css_class + ' .rt-countdown').show();
			$(this.css_class + ' .rt-until').show();
			$(this.css_class + ' .rt-event').show();
		};

		this.get_js_date = function(date_string) {
			var date_millis = Date.parse(date_string);
			if(isNaN(date_millis)) {
				alert('Error parsing ' + date_string);
			}
			var date = new Date();
			date.setTime(date_millis);
			return date;
		};

		//constructor stuff
		this.start = this.get_js_date( start );
		this.end = this.get_js_date( end );
		this.start_next = this.get_js_date( start_next );
		this.css_class = '#' + css_class;

	};

	$(document).ready(function($) {
		//kick it off, unless there's no widget(s)
		if( $('.rt-content').length > 0 ) 
			rt_update(); 		
	});

}(jQuery));
