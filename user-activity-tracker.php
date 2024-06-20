<?php
/**
 * Plugin Name: User Activity Tracker
 * Description: Tracks and records backend user activities and saves them as JSON files on the server.
 * Version: 1.0
 * Author: ithubdeveloper
 * Author URI: https://github.com/ithubdeveloper/
 * Text Domain: user-activity-tracker
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


register_activation_hook(__FILE__, 'uat_activate');
register_deactivation_hook(__FILE__, 'uat_deactivate');

function uat_activate() {
    $upload_dir = wp_upload_dir();
    $base_dir = $upload_dir['basedir'] . '/user-activity';
    
    if (!file_exists($base_dir)) {
        wp_mkdir_p($base_dir);
    }

    $htaccess_file = "{$base_dir}/.htaccess";
    if (!file_exists($htaccess_file)) {
        $htaccess_content = "Order deny,allow\nDeny from all";
        file_put_contents($htaccess_file, $htaccess_content);
    }
}

function uat_deactivate() {
    // Code to execute on plugin deactivation
}

add_action('admin_enqueue_scripts', 'uat_enqueue_scripts');

function uat_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('uat-script', plugins_url('js/uat-script.js', __FILE__), array('jquery'), time(), true);
    wp_enqueue_style('uat-style', plugins_url('css/uat-style.css', __FILE__));
    $ajax_nonce = wp_create_nonce('uat_fetch_activities_nonce');
    wp_localize_script('uat-script', 'uat_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'security' => $ajax_nonce,
    ));
}

add_action('save_post', 'uat_record_activity');
add_action('delete_post', 'uat_record_activity');

function uat_record_activity($post_id) {
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
    $post = get_post($post_id);
    $user_id = get_current_user_id();
    $user_info = get_userdata($user_id);
    $user_name = $user_info->user_login;
    $action = current_filter();
    
    $activity = array(
        'user_id' => $user_id,
        'user_name' => $user_name,
        'post_id' => $post_id,
        'post_title' => $post->post_title,
        'post_type' => $post->post_type,
        'action' => $action,
        'page_reference' => wp_get_referer(), 
        'timestamp' => current_time('mysql')
    );
    
    uat_save_activity($activity);
}

function uat_save_activity($activity) {
    $upload_dir = wp_upload_dir();
    $base_dir = $upload_dir['basedir'] . '/user-activity';
    
    $date = new DateTime($activity['timestamp']);
    $year = $date->format('Y');
    $month = $date->format('m');
    $day = $date->format('d');
    $user_id = $activity['user_id'];
    
    $dir = "{$base_dir}/{$year}/{$month}/{$day}/";
    
    if (!file_exists($dir)) {
        wp_mkdir_p($dir);
        $htaccess_content = "Order deny,allow\nDeny from all";
        file_put_contents("{$base_dir}/.htaccess", $htaccess_content);
    }
    
    $filename = "{$dir}/activity.json";
    
    $activities = array();
    if (file_exists($filename)) {
        $activities = json_decode(file_get_contents($filename), true);
    }
    
    $activities[] = $activity;
    
    file_put_contents($filename, json_encode($activities, JSON_PRETTY_PRINT));
}

add_action('admin_menu', 'uat_admin_menu');

function uat_admin_menu() {
    add_menu_page('User Activity Tracker', 'User Activity Tracker', 'manage_options', 'uat', 'uat_display_page');
}

function uat_display_page() {
    ?>
    <div class="wrap">
        <h1>User Activity Tracker</h1>
        
        <label for="uat-year-filter">Year:</label>
        <select id="uat-year-filter"></select>
        
        <label for="uat-month-filter">Month:</label>
        <select id="uat-month-filter">
            <option value="all">All</option>
            <?php for($m = 1; $m <= 12; $m++) : ?>
                <option value="<?php echo date('m', mktime(0, 0, 0, $m, 10)); ?>"><?php echo date('F', mktime(0, 0, 0, $m, 10)); ?></option>
            <?php endfor; ?>
        </select>
        
        <label for="uat-date-filter">Date:</label>
        <select id="uat-date-filter">
            <option value="all">All</option>
            <?php for($d = 1; $d <= 31; $d++) : ?>
                <option value="<?php echo $d; ?>"><?php echo $d; ?></option>
            <?php endfor; ?>
        </select>
        
        <input type="text" id="uat-search" placeholder="Search by user name">
        
        <table id="uat-activity-table" class="widefat fixed">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>User Name</th>
                    <th>Post ID</th>
                    <th>Post Title</th>
                    <th>Post Type</th>
                    <th>Action</th>
                    <th>URL</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        
        
    </div>
    <?php
}


add_action('wp_ajax_uat_fetch_activities', 'uat_fetch_activities');

function uat_fetch_activities() {
    check_ajax_referer('uat_fetch_activities_nonce', 'security');

    $year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
    $month = isset($_POST['month']) ? $_POST['month'] : date('m');
    $date = isset($_POST['date']) ? $_POST['date'] : date('d');
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $per_page = 200;
    $offset = ($page - 1) * $per_page;
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

    $upload_dir = wp_upload_dir();
    $base_dir = $upload_dir['basedir'] . '/user-activity';
    $activities = array();
    
    $year_dir = "{$base_dir}/{$year}";
    if (file_exists($year_dir)) {
        $month_dirs = ($month !== 'all') ? array("{$year_dir}/" . str_pad($month, 2, '0', STR_PAD_LEFT)) : glob("{$year_dir}/*", GLOB_ONLYDIR);
        foreach ($month_dirs as $month_dir) {
            $date_dirs = ($date !== 'all') ? array("{$month_dir}/" . str_pad($date, 2, '0', STR_PAD_LEFT)) : glob("{$month_dir}/*", GLOB_ONLYDIR);
            foreach ($date_dirs as $date_dir) {
                $files = glob("{$date_dir}/*.json");
                foreach ($files as $file) {
                    $file_activities = json_decode(file_get_contents($file), true);
                    if ($file_activities) {
                        foreach ($file_activities as $activity) {
                            if ($search === '' || stripos($activity['user_name'], $search) !== false) {
                                $activities[] = $activity;
                            }
                        }
                    }
                }
            }
        }
    }

    $total_activities = count($activities);
    $paged_activities = array_slice($activities, $offset, $per_page);

    wp_send_json(array(
        'activities' => $paged_activities,
        'total' => $total_activities,
        'per_page' => $per_page,
        'page' => $page
    ));
}