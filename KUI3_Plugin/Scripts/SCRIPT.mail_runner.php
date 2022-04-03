<?php
function KUI3_Script_MailRunner(){
    global $wpdb;

    $Query = $wpdb->get_results("SELECT SQL_CALC_FOUND_ROWS wp_posts.ID FROM wp_posts INNER JOIN wp_postmeta ON ( wp_posts.ID = wp_postmeta.post_id ) WHERE ( ( wp_postmeta.meta_key = 'boletine_sended' AND wp_postmeta.meta_value != '1' ) ) AND wp_posts.post_type = 'kui_boletin' AND (wp_posts.post_status = 'publish' OR wp_posts.post_status = 'private') GROUP BY wp_posts.ID ORDER BY wp_posts.post_date DESC LIMIT 0, 20; ");

    foreach ($Query as $Query_Row)
    {
        $Boletine_ID = $Query_Row->ID;

        $Boletine_Email     = get_post_meta($Boletine_ID,'boletine_mail',true);
        $Boletine_Status    = get_post_meta($Boletine_ID,'boletine_sended',true);
        $Boletine_Title     = html_entity_decode(str_replace("$Boletine_Email ","",get_the_title($Boletine_ID)));
        $Boletine_Content   = get_the_content(null,false,$Boletine_ID);

        if($Boletine_Status==1) {
            echo "(IGNORED) $Boletine_Email\n";
            continue;
        }

        echo "($Boletine_Status) $Boletine_Email\n";

        wp_mail($Boletine_Email,"📢 $Boletine_Title",$Boletine_Content,['Content-Type: text/html; charset=UTF-8']);

        update_post_meta($Boletine_ID,'boletine_sended',true);
    }
}

add_action('Cronjob_Boletines_MailRunner','KUI3_Script_MailRunner',100);

if (!wp_next_scheduled ( 'Cronjob_Boletines_MailRunner' )){
    wp_schedule_event(time(),'hourly', 'Cronjob_Boletines_MailRunner');
}