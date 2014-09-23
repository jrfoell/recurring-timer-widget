<?php
/*
Plugin Name: Recurring Timer Widget
Plugin URI: http://wordpress.org/extend/plugins/recurring-timer-widget
Description: Displays a countdown timer for a recurring event
Author: Justin Foell
Author URI: http://foell.org/justin
Version: 1.5
*/

class RecurringTimerWidget extends WP_Widget {
	
	const JS_DATE_FORMAT = 'D, d M Y h:i:s A T';
	const WP_DATE_FORMAT = 'Y-m-d H:i:s';
	
	public function RecurringTimerWidget() {
		// widget actual processes
		parent::__construct( false, $name = 'Recurring Timer Widget' );
	}

	public function init() {
		//queue if widget is active
		if ( !is_admin() && is_active_widget( false, false, $this->id_base, true ) ) {
			wp_register_script( 'rt-javascript', plugins_url( 'recurring-timer.js', __FILE__ ), array( 'jquery' ) );
			wp_enqueue_script( 'rt-javascript' );

			//add style if there is one
			if ( file_exists( get_stylesheet_directory() . '/recurring-timer.css' ) ) {
				wp_register_style( 'rt-style', get_stylesheet_directory_uri() . '/recurring-timer.css' );
				wp_enqueue_style( 'rt-style' );
			} else if ( file_exists( get_template_directory() . '/recurring-timer.css' ) ) {
				wp_register_style( 'rt-style', get_template_directory_uri() . '/recurring-timer.css' );
				wp_enqueue_style( 'rt-style' );
			} else {
				wp_register_style( 'rt-style', plugins_url( 'recurring-timer.css', __FILE__ ) );
				wp_enqueue_style( 'rt-style' );
			}
		}
	}
	
	public function form( $instance ) {
		// outputs the options form on admin
        $event_day = isset( $instance['event_day'] ) ? esc_attr( $instance['event_day'] ) : '';
        $event_time = isset( $instance['event_time'] ) ? esc_attr( $instance['event_time'] ) : '';
        $event_duration = isset( $instance['event_duration'] ) ? esc_attr( $instance['event_duration'] ) : '';
		$separator = isset( $instance['separator'] ) ? esc_attr( $instance['separator'] ) : '';
		$event_name = isset( $instance['event_name'] ) ? esc_attr( $instance['event_name'] ) : '';
        $event_until = isset( $instance['event_until'] ) ? esc_attr( $instance['event_until'] ) : '';
        $event_now = isset( $instance['event_now'] ) ? esc_attr( $instance['event_now'] ) : '';

		//provide some defaults
        $event_day = $event_day ? $event_day : 'this saturday';
        $event_time = $event_time ? $event_time : '11:00AM';
        $event_duration = $event_duration ? $event_duration : '+1 hour';
		$separator = $separator ? $separator : ',';
		$event_name = $event_name ? $event_name : 'My Event';
        $event_until = $event_until ? $event_until : 'until';
        $event_now = $event_now ? $event_now : 'is happening now!';
				
        ?>
		<p><i>* These must be strtotime() friendly. See <a href="http://php.net/strtotime">PHP strtotime()</a> and <a href="http://www.gnu.org/software/tar/manual/html_node/Date-input-formats.html">GNU tar date input formats</a></i></p>
        <p>
			<label for="<?php echo $this->get_field_id( 'event_day' ); ?>"><?php _e( '* Event Day: (ex: this saturday)' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'event_day' ); ?>" name="<?php echo $this->get_field_name( 'event_day' ); ?>" type="text" value="<?php echo $event_day; ?>" />
        </p>
        <p>
			<label for="<?php echo $this->get_field_id( 'event_time' ); ?>"><?php _e( '* Event Time of Day: (ex: 11:00AM)' ); ?></label> 
        	<input class="widefat" id="<?php echo $this->get_field_id( 'event_time' ); ?>" name="<?php echo $this->get_field_name( 'event_time' ); ?>" type="text" value="<?php echo $event_time; ?>" />
        </p>
        <p>
			<label for="<?php echo $this->get_field_id( 'event_duration' ); ?>"><?php _e( '* Event Duration: (ex: +1 hour)' ); ?></label> 
        	<input class="widefat" id="<?php echo $this->get_field_id( 'event_duration' ); ?>" name="<?php echo $this->get_field_name( 'event_duration' ); ?>" type="text" value="<?php echo $event_duration; ?>" />
        </p>
        <p>
			<label for="<?php echo $this->get_field_id( 'separator' ); ?>"><?php _e( 'Separator: (ex: ,)' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'separator' ); ?>" name="<?php echo $this->get_field_name( 'separator' ); ?>" type="text" value="<?php echo $separator; ?>" />
        </p>
        <p>
			<label for="<?php echo $this->get_field_id( 'event_name' ); ?>"><?php _e( 'Event Name:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'event_name' ); ?>" name="<?php echo $this->get_field_name( 'event_name' ); ?>" type="text" value="<?php echo $event_name; ?>" />
        </p>
        <p>
			<label for="<?php echo $this->get_field_id( 'event_until' ); ?>"><?php _e( 'Event "Until":' ); ?></label> 
        	<input class="widefat" id="<?php echo $this->get_field_id( 'event_until' ); ?>" name="<?php echo $this->get_field_name( 'event_until' ); ?>" type="text" value="<?php echo $event_until; ?>" />
        </p>
        <p>
			<label for="<?php echo $this->get_field_id( 'event_now' ); ?>"><?php _e( 'Event "Now":' ); ?></label> 
        	<input class="widefat" id="<?php echo $this->get_field_id( 'event_now' ); ?>" name="<?php echo $this->get_field_name( 'event_now' ); ?>" type="text" value="<?php echo $event_now; ?>" />
        </p>
        <p>Examples:</p>
		<p>1 day, 1 hour, 1 minute, 1 second &lt;Event Until&gt; &lt;Event Name&gt;</p>
		<p>&lt;Event Name&gt; &lt;Event Now&gt;</p>
        </p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved from the admin
		$instance = $old_instance;
		$instance['event_day'] = strip_tags( $new_instance['event_day'] );
		$instance['event_time'] = strip_tags( $new_instance['event_time'] );
		$instance['event_duration'] = strip_tags( $new_instance['event_duration'] );
		$instance['separator'] = strip_tags( $new_instance['separator'] );
		$instance['event_name'] = strip_tags( $new_instance['event_name'] );
		$instance['event_until'] = strip_tags( $new_instance['event_until'] );
		$instance['event_now'] = strip_tags( $new_instance['event_now'] );
        return $instance;
	}

	public function widget( $args, $instance ) {
		//extract args like $before_widget and $after_widget
		extract( $args );

		$event = $this->get_event( $instance );
		
		// outputs the content of the widget to the user
		echo $before_widget;
		?>
<script type="text/javascript">
jQuery(document).ready(function($){
	rt_timers.push(new RT_Widget(
		"<?php echo get_gmt_from_date( $event['start'], self::JS_DATE_FORMAT ) ?>",
		"<?php echo get_gmt_from_date( $event['end'], self::JS_DATE_FORMAT ) ?>",
		"<?php echo get_gmt_from_date( $event['start_next'], self::JS_DATE_FORMAT ) ?>",
		"<?php echo $widget_id ?>"
	));
});
</script>
<div class="rt-content">
	<span class="rt-countdown">
		<span class="rt-pair"><span class="rt-days">00</span><label for="rt-days">days</label><span class="rt-separator"><?php echo $instance['separator']; ?></span></span>
		<span class="rt-pair"><span class="rt-hours">00</span><label for="rt-hours">hours</label><span class="rt-separator"><?php echo $instance['separator']; ?></span></span>
		<span class="rt-pair"><span class="rt-minutes">00</span><label for="rt-minutes">minutes</label><span class="rt-separator"><?php echo $instance['separator']; ?></span></span>
		<span class="rt-pair"><span class="rt-seconds">00</span><label for="rt-seconds">seconds</label></span>
	</span>
	<span class="rt-description">
		<span class="rt-until"><?php echo $instance['event_until']; ?></span>
		<span class="rt-event"><?php echo $instance['event_name']; ?></span>
		<span class="rt-on"><?php echo $instance['event_now']; ?></span>
	</span>
</div>
<?php
		echo $after_widget;
	}

	function get_event( $instance ) {
		//all must be strtotime() friendly
		$event_day = $instance['event_day'];
		$event_time = $instance['event_time'];
		$event_duration = trim( $instance['event_duration'] );
		//add a '+' if they forgot it
		$event_duration = strpos( $event_duration, '+' ) === 0 ? $event_duration : '+' . $event_duration;

		$now = current_time( 'timestamp' );
		//initialize event_start and event_start_next to the same value
		$event_start = $event_start_next = strtotime( $event_time, strtotime( $event_day ) );

		do {
			//update event_start to event_start_next (only effectively changes after 1st loop)
			$event_start = $event_start_next;
			$event_end = strtotime( $event_duration, $event_start );
			$event_start_next = $this->get_start_next( $event_time, $event_day, $event_start );

		} while ( $now >= $event_end );
				
		//put into WP datetime format
		return array(
			'start' => date( self::WP_DATE_FORMAT, $event_start ),
			'end' => date( self::WP_DATE_FORMAT, $event_end ),
			'start_next' => date( self::WP_DATE_FORMAT, $event_start_next ) );
	}

	private function get_start_next( $event_time, $event_day, $event_start ) {
		//increment by days in the case of a monthly event using 'this month'.
		//the '+n days' should probably be changed if two events could occur within
		//a span < 24 hours
		$n = 0;
		do {
			$n++;
			$event_start_next = strtotime( $event_time,
										   strtotime( $event_day, strtotime( "+{$n} days",
																			 $event_start ) ) );
		} while ( $event_start_next <= $event_start );
		return $event_start_next;
	}
}

$rt_widget = new RecurringTimerWidget();

add_action( 'widgets_init', create_function( '', 'return register_widget( "RecurringTimerWidget" );' ) );
add_action( 'init', array( $rt_widget, 'init' ) );
