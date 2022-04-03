<?php
function KUI3_Script_MailCalculates(){
    $Boletines_Query = new WP_Query([
        'post_type' => 'kui_boletin',
        'posts_per_page' => -1,
    ]);

    $Rate_Totals = [];
    $Rate_Opens  = [];

    if($Boletines_Query->have_posts()){
        while ($Boletines_Query->have_posts()){
            $Boletines_Query->the_post();

            $Boletine_Opened    = (bool)get_post_meta(get_the_ID(),'boletine_opened',true);
            $Boletine_Email     = get_post_meta(get_the_ID(),'boletine_mail',true);

            if(!isset($Rate_Totals[$Boletine_Email])){
                $Rate_Totals[$Boletine_Email] = 0;
            }
            if(!isset($Rate_Opens[$Boletine_Email])){
                $Rate_Opens[$Boletine_Email] = 0;
            }

            $Rate_Totals[$Boletine_Email]++;

            if($Boletine_Opened===true){
                $Rate_Opens[$Boletine_Email]++;
            }
        }

        foreach ($Rate_Totals as $Rate_Mail=>$Rate_Total){
            $Rate_In100 = round($Rate_Opens[$Rate_Mail]*100/$Rate_Totals[$Rate_Mail]);

            $Suscriber_Data = get_page_by_title($Rate_Mail,OBJECT,'kui_suscription');

            if($Suscriber_Data){
                update_post_meta($Suscriber_Data->ID,'rate',$Rate_In100);
            }
        }
    }
}

add_action('Cronjob_Boletines_MailCalculates','KUI3_Script_MailCalculates',100);

if (!wp_next_scheduled ( 'Cronjob_Boletines_MailCalculates' )){
    wp_schedule_event(time(),'weekly', 'Cronjob_Boletines_MailCalculates');
}