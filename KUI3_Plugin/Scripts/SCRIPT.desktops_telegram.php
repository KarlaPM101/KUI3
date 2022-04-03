<?php
function FridayDesktop_Desktop2Telegram()
{
    if((date('N')==5 && date('H')>15) || (date('N')==6 && date('H')<12))
    {
        $KUI3 = new KUI_REST([]);

        $Publish_Timestamp = get_option('wp_friday_local_lastfetch');

        $Desktop_Query = new WP_Query([
            'post_type' => 'viernesdeescritorio',
            'post_status' => 'publish',
            'date_query' => [
                'after' => get_date_from_gmt(date('Y-m-d H:i:s',$Publish_Timestamp))
            ],
            'meta_query' => [
                [
                    'key' => 'kui_user_id',
                    'value' => 0,
                    'type' => 'numeric',
                    'compare' => '>'
                ]
            ],
            'order' => 'ASC',
            'orderby' => 'date',
            'posts_per_page' => 1
        ]);

        if ($Desktop_Query->have_posts()) {
            while ($Desktop_Query->have_posts()){
                $Desktop_Query->the_post();

                $Desktop_ID                 = get_the_ID();
                $Desktop_User_ID            = get_post_meta($Desktop_ID,'kui_user_id',true);
                $Desktop_User_Name          = $KUI3->Users_Get_Name($Desktop_User_ID);
                $Desktop_User_URL           = $KUI3->Users_Get_URL($Desktop_User_ID);
                $Desktop_Text               = get_post_meta($Desktop_ID,'message_text',true);
                $Telegram_User_Rendered     = "[$Desktop_User_Name]($Desktop_User_URL)";

                $Telegram_Message           = Copito_Message_Image(
                    COPITO_GROUP_ID,
                    get_the_post_thumbnail_url($Desktop_ID,'large'),
                    "🍇 *WP KarlaKUI3* ($Telegram_User_Rendered)\n\n¡$Desktop_User_Name ha publicado su escritorio!\n\n$Desktop_Text",[
                    "inline_keyboard" => [
                        [
                            [
                                "text" => "Votar +1",
                                "callback_data" => "Viernes_Upvote_ID_{$Desktop_ID}"
                            ],
                            [
                                "text" => "Ver en la Web",
                                "url" => get_the_permalink()
                            ]
                        ]
                    ]
                ]);

                update_post_meta($Desktop_ID,'message_id',$Telegram_Message);

                update_option('wp_friday_local_lastfetch',get_post_time('U',true)+10);
            }
        }
    }
}

add_action('Cronjob_FridayDesktop','FridayDesktop_Desktop2Telegram',100);

if (!wp_next_scheduled ( 'Cronjob_FridayDesktop' )){
    wp_schedule_event(time(),'hourly', 'Cronjob_FridayDesktop');
}
