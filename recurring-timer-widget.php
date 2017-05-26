<?php
/**
 * Plugin Name: Recurring Timer Widget
 * Plugin URI: http://wordpress.org/extend/plugins/recurring-timer-widget
 * Description: Displays a countdown timer for a recurring event
 * Author: Justin Foell
 * Author URI: http://foell.org/justin
 * Version: 1.7
 * Text Domain: recurring-timer-widget
 * Domain Path: /languages
 */

class RecurringTimerWidget extends WP_Widget {

	const JS_DATE_FORMAT = 'D, d M Y h:i:s A T';
	const WP_DATE_FORMAT = 'Y-m-d H:i:s';

	public function __construct() {
		// widget actual processes
		parent::__construct( false, $name = __( 'Recurring Timer Widget', 'recurring-timer-widget' ) );
	}

	public function init() {
		//queue if widget is active
		if ( ! is_admin() && is_active_widget( false, false, $this->id_base, true ) ) {
			wp_register_script( 'rt-javascript', plugins_url( 'recurring-timer-widget.js', __FILE__ ), array( 'jquery' ) );
			wp_enqueue_script( 'rt-javascript' );

			//add style if there is one
			if ( file_exists( get_stylesheet_directory() . '/recurring-timer-widget.css' ) ) {
				wp_register_style( 'rt-style', get_stylesheet_directory_uri() . '/recurring-timer-widget.css' );
				wp_enqueue_style( 'rt-style' );
			} elseif ( file_exists( get_template_directory() . '/recurring-timer-widget.css' ) ) {
				wp_register_style( 'rt-style', get_template_directory_uri() . '/recurring-timer-widget.css' );
				wp_enqueue_style( 'rt-style' );
			} else {
				wp_register_style( 'rt-style', plugins_url( 'recurring-timer-widget.css', __FILE__ ) );
				wp_enqueue_style( 'rt-style' );
			}
		}
	}

	public function plugins_loaded() {
		load_plugin_textdomain( 'recurring-timer-widget', false, trailingslashit( dirname( __FILE__ ) ) . 'languages/' );
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
		<p><i>
			<?php
			printf( __( '* These must be strtotime() friendly. See <a target="_blank" href="%1$s">PHP strtotime()</a> 
				and <a target="_blank" href="%2$s">GNU tar date input formats</a>', 'recurring-timer-widget' ),
				'http://php.net/strtotime', // PHP strtotime() URL.
				'http://www.gnu.org/software/tar/manual/html_node/Date-input-formats.html' // GNU tar date input URL.
			 );
			?>
		</i></p>
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
		<?php printf( __( '<p>Examples:</p>
			<p>1 day, 1 hour, 1 minute, 1 second &lt;Event Until&gt; &lt;Event Name&gt;</p>
			<p>&lt;Event Name&gt; &lt;Event Now&gt;</p>', 'recurring-timer-widget' ) ); ?>
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
jQuery( document ).ready( function( $ ) {
	rt_timers.push( new RT_Widget(
		"<?php echo get_gmt_from_date( $event['start'], self::JS_DATE_FORMAT ); ?>",
		"<?php echo get_gmt_from_date( $event['end'], self::JS_DATE_FORMAT ); ?>",
		"<?php echo get_gmt_from_date( $event['start_next'], self::JS_DATE_FORMAT ); ?>",
		"<?php echo $widget_id; ?>"
	) );
} );
</script>
<div class="rt-content">
	<span class="rt-countdown">
		<span class="rt-pair rt-pair-days"><span class="rt-days">00</span><label class="rt-label rt-label-days" for="rt-days"><?php _e( 'days', 'recurring-timer-widget' ) ?></label><span class="rt-separator"><?php echo $instance['separator']; ?></span></span>
		<span class="rt-pair rt-pair-hours"><span class="rt-hours">00</span><label class="rt-label rt-label-hours" for="rt-hours"><?php _e( 'hours', 'recurring-timer-widget' ) ?></label><span class="rt-separator"><?php echo $instance['separator']; ?></span></span>
		<span class="rt-pair rt-pair-minutes"><span class="rt-minutes">00</span><label class="rt-label rt-label-minutes" for="rt-minutes"><?php _e( 'minutes', 'recurring-timer-widget' ) ?></label><span class="rt-separator"><?php echo $instance['separator']; ?></span></span>
		<span class="rt-pair rt-pair-seconds"><span class="rt-seconds">00</span><label class="rt-label rt-label-seconds" for="rt-seconds"><?php _e( 'seconds', 'recurring-timer-widget' ) ?></label></span>
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

	private function get_event( $instance ) {

		// All must be strtotime() friendly.
		$event_day = $instance['event_day'];
		$event_time = $instance['event_time'];
		$event_duration = trim( $instance['event_duration'] );

		// Add a '+' if they forgot it.
		$event_duration = strpos( $event_duration, '+' ) === 0 ? $event_duration : '+' . $event_duration;

		// This was easier when WP didn't use date_default_timezone_set('UTC'), but maybe less readable?
		// Old way: $now = current_time( 'timestamp' );
		$tz = get_option( 'timezone_string' );
		$now = $tz ? date_create( 'now', new DateTimeZone( $tz ) ) : date_create( 'now', new DateTimeZone( 'UTC' ) );

		// Old way: $event_start = $event_start_next = strtotime( $event_time, strtotime( $event_day ) );
		// Makin' copies!
		$event_start = clone $now;
		$event_start->modify( $event_day ); // Set the date first (will reset time to midnight).
		$event_start->modify( $event_time ); // Set the time second.

		// Initialize event_start and event_start_next to the same value.
		$event_start_next = clone $event_start;

		do {
			// Update event_start to event_start_next (only effectively changes after 1st loop).
			$event_start = clone $event_start_next;
			// Old way: $event_end = strtotime( $event_duration, $event_start );
			$event_end = clone $event_start;
			$event_end = $event_end->modify( $event_duration );
			$event_start_next = $this->get_start_next( $event_time, $event_day, $event_start );
		} while ( $now >= $event_end );

		// Put into WP datetime format.
		$times = array(
			'start' => $event_start->format( self::WP_DATE_FORMAT ),
			'end' => $event_end->format( self::WP_DATE_FORMAT ),
			'start_next' => $event_start_next->format( self::WP_DATE_FORMAT ),
		);

		return $times;
	}

	private function get_start_next( $event_time, $event_day, $event_start ) {
		/**
		 * Increment by days in the case of a monthly event using 'this month'.
		 * The '+n days' should probably be changed if two events could occur within
		 * a span < 24 hours.
		 */
		$n = 0;
		do {
			$n++;
			// Old way: $event_start_next = strtotime( $event_time, strtotime( $event_day, strtotime( "+{$n} days", $event_start ) ) );
			$event_start_next = clone $event_start;
			$event_start_next->modify( "+{$n} days" );
			$event_start_next->modify( $event_day );
			$event_start_next->modify( $event_time );
		} while ( $event_start_next <= $event_start );
		return $event_start_next;
	}
}

$rt_widget = new RecurringTimerWidget();

add_action( 'widgets_init', create_function( '', 'return register_widget( "RecurringTimerWidget" );' ) );
add_action( 'plugins_loaded', array( $rt_widget, 'plugins_loaded' ) );
add_action( 'init', array( $rt_widget, 'init' ) );
