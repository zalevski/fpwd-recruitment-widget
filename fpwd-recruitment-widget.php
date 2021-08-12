<?php

/*
Plugin Name: Recruitment widget
Description: Recruitment widget for From Poland With Dev
Author: Arkadiusz Zalewski
Version: 1.0.0
*/
class fpwd_widget extends WP_Widget
{
    function __construct()
    {
        parent::__construct(false, $name = 'From Poland With Dev - sample widget');
    }

    function widget($args, $instance)
    {
        wp_enqueue_script('fpwd_widget_script', plugins_url('fpwd-recruitment-widget/assets/js/fpwd-recruitment-widget-front.js'), array('jquery'));
        wp_enqueue_style('fpwd_widget_style', plugins_url('fpwd-recruitment-widget/assets/css/fpwd-recruitment-widget-front.css'));
        wp_localize_script(
            'fpwd_widget_script',
            'ajax_object',
            array('ajax_url' => admin_url('admin-ajax.php'))
        );

        extract($args);
?>
        <div class="fpwd_widget">
            <form class="fpwd_widget__form" id="fpwd_widget_form">
                <input type="text" name="order_id" placeholder="<?php _e('Wpisz numer zamówienia'); ?>" />
                <input type="submit" value="Sprawdź wpłatę">
            </form>
            <div class="fpwd_widget__response" id="fpwd_response"></div>
        </div>

    <?php
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['endpoint_url'] = strip_tags($new_instance['endpoint_url']);
        return $instance;
    }

    function form($instance)
    {
        $title = esc_attr($instance['title']);
    ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
<?php
    }
}

add_action('widgets_init', function () {
    return register_widget("fpwd_widget");
});

add_action('wp_ajax_check_payment', 'fpwd_check_payment');
add_action('wp_ajax_nopriv_check_payment', 'fpwd_check_payment');
function fpwd_check_payment()
{
    global $wpdb;
    $order_id = intval($_POST['data']['order_id']);
    $result = $wpdb->get_row("SELECT * FROM `{$wpdb->base_prefix}fpwd_payments` WHERE order_id={$order_id}");
    if ($result) {
        $result = json_encode($result);
        echo $result;
        wp_die();
    } else {
        wp_send_json_error('Error: Invalid data!');
    }
}
?>