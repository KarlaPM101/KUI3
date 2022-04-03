<?php
function t_subscribers_columns($columns)
{
    unset($columns['title']);
    unset($columns['date']);

    $columns['thumbnail'] = 'M';
    $columns['first_name'] = 'Nombre';
    $columns['last_name'] = 'Apellido';
    $columns['username'] = 'Username';
    $columns['udate'] = 'Última vez';
    $columns['messages'] = 'Mensajes';

    return apply_filters('manage_edit-telegram_subscribers_columns_filter', $columns);
}

function t_suscribers_sortable($cols)
{
    $cols['udate'] = 'udate';
    $cols['messages'] = 'messages';
    return $cols;
}

add_filter('manage_edit-telegram_subscribers_sortable_columns', 't_suscribers_sortable');

add_action( 'pre_get_posts', 't_suscribers_column_orderby' );
function t_suscribers_column_orderby( $query ) {
    if(!is_admin()) return;

    $orderby = $query->get( 'orderby');

    if( 'udate' == $orderby ) {
        $query->set('orderby','modified');
        $query->set('meta_type','DATETIME');
    }
    if( 'messages' == $orderby ) {
        $query->set('orderby','meta_value_num');
        $query->set('meta_key','telegram_custom');
    }
}


add_filter('manage_edit-telegram_subscribers_columns', 't_subscribers_columns');
function t_groups_columns($columns)
{
    $columns['name'] = 'Group Name';
    $columns['sdate'] = 'Subscribe Date';
    unset($columns['cb']);
    unset($columns['date']);
    if (defined('WP_DEBUG') && false === WP_DEBUG) {
        unset($columns['title']);
    }
    return apply_filters('manage_edit-telegram_groups_columns_filter', $columns);
}

add_filter('manage_edit-telegram_groups_columns', 't_groups_columns');

//  add_filter('bulk_actions-edit-telegram_subscribers', function($actions){ unset( $actions['edit'] ); return apply_filters( 'bulk_actions-edit-telegram_subscribers_filter', $actions ); });
add_filter('bulk_actions-edit-telegram_groups', function ($actions) {
    unset($actions['edit']);
    return apply_filters('bulk_actions-edit-telegram_groups_filter', $actions);
});

add_action('manage_telegram_subscribers_posts_custom_column', 't_manage_columns', 10, 2);
add_action('manage_telegram_groups_posts_custom_column', 't_manage_columns', 10, 2);
function t_manage_columns($column, $post_id)
{
    global $post;
    switch ($column) {
        case 'thumbnail':
            $internal = WP_CONTENT_DIR."/uploads/telegram/".get_the_title($post_id).".jpg";
            $image = "https://karlaperezyt.com/wp-content/uploads/telegram/".get_the_title($post_id).".jpg";
            if(!file_exists($internal))
            {
                $image = "https://karlaperezyt.com/wp-content/uploads/2020/07/avatar.jpg";
            }
            printf(sprintf('<img src="%1$s" style="width:32px;height: 32px;border-radius: 32px;vertical-align: middle;">',$image));
            break;
        case 'first_name':
            printf(htmlspecialchars(get_post_meta($post_id, 'telegram_first_name', true)));
            break;
        case 'last_name':
            printf(htmlspecialchars(get_post_meta($post_id, 'telegram_last_name', true)));
            break;
        case 'username':
            printf(htmlspecialchars("@".get_post_meta($post_id, 'telegram_username', true)));
            break;
        case 'isadmin':
            break;
        case 'sdate':
            printf(get_the_date());
            break;
        case 'udate':
            printf(get_the_modified_date());
            break;
        case 'messages':
            printf(get_post_meta($post_id, 'telegram_custom', true));
            break;
        default:
            break;
    }
}

?>
