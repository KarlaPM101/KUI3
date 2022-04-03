<?php
function KUI3_Script_MailTransport()
{
    $MailTransport_Last_Execution = (int)get_option('kui3_mailtransport_execution');
    $MailTransport_Last_Holded    = get_option('kui3_mailtransport_hold');

    if($MailTransport_Last_Holded){
        return;
    }

    $Posts_Array = [];

    /*
     * GET Posts Types: Post, Videopost & Noticia
     */
    foreach (['post','videopost','noticia'] as $Boletin_Types){
        $Posts_Query = new WP_Query([
            'post_type' => $Boletin_Types,
            'posts_per_page' => 2,
            'order' => 'DESC',
            'orderby' => 'date',
            'date_query' => [
                [
                    'after' => date('Y-m-d H:i:s',$MailTransport_Last_Execution)
                ]
            ]
        ]);
        if($Posts_Query->have_posts()){
            while ($Posts_Query->have_posts()){
                $Posts_Query->the_post();

                $Posts_Array[] = get_the_ID();
            }
        }
    }

    /*
     * GET Posts Types: Feedy (YouTube)
     */
    $Feedy_Query = new WP_Query([
        'post_type' => 'feedy',
        'posts_per_page' => 1,
        'order' => 'DESC',
        'orderby' => 'date',
        'date_query' => [
            [
                'after' => date('Y-m-d H:i:s',$MailTransport_Last_Execution)
            ]
        ],
        'meta_query' => [
            [
                'key' => 'feedy_type',
                'value' => 'youtube'
            ]
        ]
    ]);
    if($Feedy_Query->have_posts()){
        while ($Feedy_Query->have_posts()){
            $Feedy_Query->the_post();

            $Posts_Array[] = get_the_ID();
        }
    }

    var_dump($Posts_Array);
    /*
     * GET Suscribers
     */

    $Schedule_Time = current_time('timestamp',true)+1800;
    $Schedule_Hold = [];

    if($Posts_Array){
        $Suscriber_Query = new WP_Query([
            'post_type' => 'kui_suscription',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => 'activation_status',
                    'value' => 1,
                    'type' => 'NUMERIC',
                    'compare' => '='
                ]
            ]
        ]);
        if($Suscriber_Query->have_posts()){
            while ($Suscriber_Query->have_posts()){
                $Suscriber_Query->the_post();

                $Boletin_Posts_To_Implement = [];

                $Interest1 = (boolean)get_post_meta(get_the_ID(),'interest_1',true);
                $Interest2 = (boolean)get_post_meta(get_the_ID(),'interest_2',true);
                $Interest3 = (boolean)get_post_meta(get_the_ID(),'interest_3',true);

                $Suscriptor_Email = get_the_title();
                $Suscriptor_Reference = get_post_meta(get_the_ID(),'reference',true);

                foreach ($Posts_Array as $Current_Post){
                    if(in_array(get_post_type($Current_Post),['post','videopost','noticia'])){
                        $Current_Post_Categories = wp_get_post_categories($Current_Post);
                        $Current_Post_Categories_Slugs = [];
                        foreach ($Current_Post_Categories as $Current_Category){
                            $Current_Category_Data = get_category($Current_Category);
                            if($Current_Category_Data){

                                $Current_Post_Categories_Slugs[] = $Current_Category_Data->slug;
                            }
                        }
                        $Boletin_Include = false;

                        foreach ($Current_Post_Categories_Slugs as $Current_Slug){
                            if($Interest1 && in_array($Current_Slug,['blog-linux','review-linux','tutorial-linux'])){
                                $Boletin_Include = true;
                            }
                            if($Interest2 && in_array($Current_Slug,['blog-windows','review-windows','tutorial-windows'])){
                                $Boletin_Include = true;
                            }
                        }
                        if($Boletin_Include===true){
                            $Boletin_Posts_To_Implement[] = $Current_Post;
                        }
                    }

                    if(get_post_type($Current_Post)==='feedy' && get_post_meta($Current_Post,'feedy_type',true)=='youtube'){
                        if($Interest3){
                            $Boletin_Posts_To_Implement[] = $Current_Post;
                        }
                    }
                }

                if($Boletin_Posts_To_Implement){
                    $Schedule_Hold[] = [
                        $Schedule_Time,
                        $Suscriptor_Email,
                        $Suscriptor_Reference,
                        $Boletin_Posts_To_Implement
                    ];

                    $Schedule_Time += 60;
                }
            }
        }

        update_option('kui3_mailtransport_hold',json_encode($Schedule_Hold));
        update_option('kui3_mailtransport_execution',current_time('timestamp'));
    }
}

function KUI3_Script_MailTransport_UnHold(){
    $MailTransport_Last_Holded    = get_option('kui3_mailtransport_hold');

    if(!$MailTransport_Last_Holded){
        return;
    }

    $MailTransport_Holded_Decoded = json_decode($MailTransport_Last_Holded,true);

    for($I=0;$I<50;$I++){
        $Hold_Data = array_shift($MailTransport_Holded_Decoded);

        list($Schedule_Time,$Suscriptor_Email,$Suscriptor_Reference,$Boletin_Posts_To_Implement) = $Hold_Data;

        wp_schedule_single_event($Schedule_Time,'Cronjob_Boletines_MailTransport_Proccess',[
            $Suscriptor_Email,
            $Suscriptor_Reference,
            $Boletin_Posts_To_Implement
        ]);
    }

    if($MailTransport_Holded_Decoded){
        update_option('kui3_mailtransport_hold',json_encode($MailTransport_Holded_Decoded));
    }
    else {
        update_option('kui3_mailtransport_hold',false);
    }
}

function KUI3_Script_MailTransport_Proccess($Suscriptor_Email,$Suscriptor_Reference,$Boletin_Posts_To_Implement){
    $Boletin_Posts_HTML = [];

    $Boletine_Strings_Header = file_get_contents(WP_CONTENT_DIR."/plugins/KUI3/Mail_Templates/Type_Boletine.html");
    $Boletine_Strings_Posts  = file_get_contents(WP_CONTENT_DIR."/plugins/KUI3/Mail_Templates/Type_Boletine_Body.html");

    $Boletine_Title = false;

    foreach ($Boletin_Posts_To_Implement as $Current_Post){
        if(get_post_type($Current_Post)==='feedy' && get_post_meta($Current_Post,'feedy_type',true)=='youtube'){
            $Boletin_Posts_Body = str_replace([
                '%POST_IMAGE%',
                '%POST_TITLE%',
                '%POST_DESC%',
                '%POST_URL%',
                'Sigue leyendo',
            ],[
                get_the_post_thumbnail_url($Current_Post,'full'),
                get_the_title($Current_Post),
                '',
                'https://youtu.be/'.get_post_meta($Current_Post,'feedy_metadata_1',true),
                'Ver vídeo en Yutuf'
            ],$Boletine_Strings_Posts);
        }
        else {
            $Boletin_Posts_Body = str_replace([
                '%POST_TITLE%',
                '%POST_IMAGE%',
                '%POST_DESC%',
                '%POST_URL%',
            ],[
                get_the_title($Current_Post),
                get_the_post_thumbnail_url($Current_Post,'full'),
                get_the_excerpt($Current_Post),
                get_the_permalink($Current_Post)
            ],$Boletine_Strings_Posts);
        }

        $Boletin_Posts_HTML[] = $Boletin_Posts_Body;

        if($Boletine_Title===false){
            $Boletine_Title = sprintf('%1$s - Karla\'s Project',get_the_title($Current_Post));
        }
    }

    $Boletine_Reference = implode('-',[
        substr(md5(uniqid()),0,8),
        substr(md5(uniqid()),0,4),
        substr(md5(uniqid()),0,4),
        substr(md5(uniqid()),0,4),
        substr(md5(uniqid()),0,12),
    ]);

    $Boletine_ID = wp_insert_post([
        'post_type' => 'kui_boletin',
        'post_title' => sprintf('%1$s %2$s',$Suscriptor_Email,$Boletine_Title),
        'post_status' => 'publish',
        'post_content' => str_replace([
            '%SUBID%',
            '%REFERENCE%',
            '%BODY%',
        ],[
            $Suscriptor_Reference,
            $Boletine_Reference,
            implode('',$Boletin_Posts_HTML),
        ],$Boletine_Strings_Header)
    ]);

    update_post_meta($Boletine_ID,'boletine_mail',$Suscriptor_Email);
    update_post_meta($Boletine_ID,'boletine_sended',false);
    update_post_meta($Boletine_ID,'boletine_opened',false);
    update_post_meta($Boletine_ID,'boletine_reference',$Boletine_Reference);
}


add_action('init',function (){
    if(substr($_SERVER['REQUEST_URI'],0,strlen('/wp-content/plugins/KUI3/assets/mail_assets/mail_header.png'))==='/wp-content/plugins/KUI3/assets/mail_assets/mail_header.png'){
        header('Content-type: image/png');
        $Resource = imagecreatefrompng(WP_CONTENT_DIR."/plugins/KUI3/assets/mail_assets/profile_rnd.png");
        imagesavealpha($Resource, true);
        imagepng($Resource);
        $Boletine_Reference = sanitize_text_field($_GET['reference']);
        if($Boletine_Reference){
            $Boletine_Query = new WP_Query([
                'post_type' => 'kui_boletin',
                'posts_per_page' => 1,
                'meta_query' => [
                    [
                        'key' => 'boletine_reference',
                        'value' => $Boletine_Reference,
                    ]
                ]
            ]);
            if($Boletine_Query->have_posts()){
                while ($Boletine_Query->have_posts()){
                    $Boletine_Query->the_post();
                    update_post_meta(get_the_ID(),'boletine_opened',true);
                }
            }
        }
        die();
    }
},10,0);

add_action('Cronjob_Boletines_MailTransport','KUI3_Script_MailTransport',100);
add_action('Cronjob_Boletines_MailHold','KUI3_Script_MailTransport_UnHold',100);

add_action('Cronjob_Boletines_MailTransport_Proccess','KUI3_Script_MailTransport_Proccess',100,3);

if (!wp_next_scheduled ( 'Cronjob_Boletines_MailTransport' )){
    wp_schedule_event(time(),'hourly', 'Cronjob_Boletines_MailTransport');
}
if (!wp_next_scheduled ( 'Cronjob_Boletines_MailHold' )){
    wp_schedule_event(time(),'hourly', 'Cronjob_Boletines_MailHold');
}