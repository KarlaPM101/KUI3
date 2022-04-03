<?php
function Feedy_Update()
{
    // Update Youtube Videos
    $Feedy_YouTube_Execution = get_option('wp_feedy_query_yt');
    $Feedy_YouTube_RSS       = "https://www.youtube.com/feeds/videos.xml?channel_id=UCgHXvTpaNOBCIDqCNhOxPkg";
    $Feedy_YouTube_Parser    = simplexml_load_file($Feedy_YouTube_RSS);

    if(isset($Feedy_YouTube_Parser->entry))
    {
        foreach ($Feedy_YouTube_Parser->entry as $Entry)
        {
            $Entry_Title        = $Entry->title;
            $Entry_Link         = str_replace('https://www.youtube.com/watch?v=','',$Entry->link->attributes()['href']);
            $Entry_TimeStamp    = strtotime($Entry->published);

            if($Entry_TimeStamp>$Feedy_YouTube_Execution){
                $Feedy_New_ID = wp_insert_post([
                    'post_title'            => $Entry_Title,
                    'post_type'             => 'feedy',
                    'post_status'           => 'publish',
                    'post_date'             => get_date_from_gmt(date('Y-m-d H:i:s',$Entry_TimeStamp)),
                    'post_date_gmt'         => date('Y-m-d H:i:s',$Entry_TimeStamp)
                ]);

                update_post_meta($Feedy_New_ID,'feedy_type','youtube');
                update_post_meta($Feedy_New_ID,'feedy_metadata_1',$Entry_Link);

                $Fetcher_Directory          = WP_CONTENT_DIR."/uploads/kui_system/ytfetcher";
                $Fetcher_Url                = "https://i.ytimg.com/vi/$Entry_Link/maxresdefault.jpg";
                $Thumbnail_Directory        = "$Fetcher_Directory/$Entry_Link.jpg";

                if($Fetcher_Data = file_get_contents($Fetcher_Url)){

                    file_put_contents($Thumbnail_Directory,$Fetcher_Data);

                    $Thumbnail_MimeType = wp_check_filetype($Thumbnail_Directory, null);
                    $Thumbnail_Attachment = [
                        'post_mime_type'    => $Thumbnail_MimeType['type'],
                        'post_parent'       => $Feedy_New_ID,
                        'post_title'        => sanitize_file_name($Thumbnail_Directory),
                        'post_content'      => '',
                        'post_status'       => 'inherit'
                    ];
                    $Thumbnail_ID = wp_insert_attachment($Thumbnail_Attachment,$Thumbnail_Directory,$Feedy_New_ID);
                    set_post_thumbnail($Feedy_New_ID,$Thumbnail_ID);

                    if (!function_exists('wp_crop_image')) {
                        include(ABSPATH.'wp-admin/includes/image.php');
                    }
                    wp_generate_attachment_metadata($Thumbnail_ID,$Thumbnail_Directory);

                    $Folders_ID = FileBird\Model\Folder::newOrGet('YT Thumbs',FileBird\Model\Folder::newOrGet('KUI3 System',0));
                    FileBird\Model\Folder::setFoldersForPosts($Thumbnail_ID,$Folders_ID);
                }

                update_option('wp_feedy_query_yt',$Entry_TimeStamp+1);

                break;
            }
        }
    }

    // Update YT Members
    $Feedy_Query_YTM = get_option('wp_feedy_query_ytm');
    $Posts = new WP_Query([
        'posts_per_page' => 50,
        'orderby'    => 'post_date',
        'order'      => 'DESC',
        'post_type' => ['yt_miembro'],
        'date_query' => [
            [
                'after'    => array(
                    'year'  => date('Y',$Feedy_Query_YTM),
                    'month' => date('m',$Feedy_Query_YTM),
                    'day'   => date('d',$Feedy_Query_YTM),
                    'hour'   => date('H',$Feedy_Query_YTM),
                    'minute'   => date('i',$Feedy_Query_YTM),
                    'second'   => date('a',$Feedy_Query_YTM),
                ),
                'inclusive' => true,
            ],
        ],
    ]);
    if ($Posts->have_posts()) {
        while ($Posts->have_posts()) {
            $Posts->the_post();

            $Data = str_replace([
                "<div><img alt='' src='",
                "' style='max-width:600px;' /><br/><div>",
                ", http://www.youtube.com/channel/",
            ],[
                "",
                "|",
                "|",
            ],get_the_content());

            $Data = explode('|',$Data);
            list($Channel_Profile,$Channel_Name,$Channel_Url) = $Data;
            $Channel_Url = 'http://www.youtube.com/channel/'.substr($Channel_Url,0,24);

            update_post_meta(get_the_ID(),'channel_profile',$Channel_Profile);
            update_post_meta(get_the_ID(),'channel_url',$Channel_Url);
            update_post_meta(get_the_ID(),'channel_name',$Channel_Name);

            $Feedy_New_ID = wp_insert_post([
                'post_title'            => get_the_title(),
                'post_type'             => 'feedy',
                'post_status'           => 'publish',
            ]);
            wp_update_post(
                [
                    'ID'            => $Feedy_New_ID,
                    'post_date'     => get_the_time('Y-m-d H:i:s'), // '2010-02-23 18:57:33';
                    'post_date_gmt' => get_gmt_from_date(get_the_time('Y-m-d H:i:s'))
                ]
            );
            update_post_meta($Feedy_New_ID,'feedy_type','ytmember');
            update_post_meta($Feedy_New_ID,'feedy_metadata_1',get_the_ID());
        }
        update_option('wp_feedy_query_ytm',strtotime(date_i18n('Y-m-d H:i:s')));
    }

    // Instagram
    $Feedy_Query_Instagram = get_option('wp_feedy_query_instagram');
    $Posts = new WP_Query([
        'posts_per_page' => 50,
        'orderby'    => 'post_date',
        'order'      => 'DESC',
        'post_type' => ['instagram'],
        'date_query' => [
            [
                'after'    => array(
                    'year'  => date('Y',$Feedy_Query_Instagram),
                    'month' => date('m',$Feedy_Query_Instagram),
                    'day'   => date('d',$Feedy_Query_Instagram),
                    'hour'   => date('H',$Feedy_Query_Instagram),
                    'minute'   => date('i',$Feedy_Query_Instagram),
                    'second'   => date('a',$Feedy_Query_Instagram),
                ),
                'inclusive' => true,
            ],
        ],
    ]);
    if ($Posts->have_posts()) {
        while ($Posts->have_posts()) {
            $Posts->the_post();

            $Endpoint = file_get_contents(trim(get_the_content()));
            $Endpoint_Time = time();
            file_put_contents($FilePath = WP_CONTENT_DIR."/uploads/kui_system/instagram/{$Endpoint_Time}.jpg",$Endpoint);

            $MimeType = wp_check_filetype($FilePath, null);
            $Attachment = [
                'post_mime_type'    => $MimeType['type'],
                'post_parent'       => get_the_ID(),
                'post_title'        => sanitize_file_name($FilePath),
                'post_content'      => '',
                'post_status'       => 'inherit'
            ];
            $Attachment_ID = wp_insert_attachment($Attachment,$FilePath,get_the_ID());
            set_post_thumbnail(get_the_ID(),$Attachment_ID);

            if (!function_exists('wp_crop_image')) {
                include(ABSPATH.'wp-admin/includes/image.php');
            }
            wp_generate_attachment_metadata($Attachment_ID,$FilePath);

            $Feedy_New_ID = wp_insert_post([
                'post_title'            => get_the_title(),
                'post_type'             => 'feedy',
                'post_status'           => 'publish',
            ]);
            wp_update_post(
                [
                    'ID'            => $Feedy_New_ID,
                    'post_date'     => get_the_time('Y-m-d H:i:s'), // '2010-02-23 18:57:33';
                    'post_date_gmt' => get_gmt_from_date(get_the_time('Y-m-d H:i:s'))
                ]
            );
            update_post_meta($Feedy_New_ID,'feedy_type','instagram');
            update_post_meta($Feedy_New_ID,'feedy_metadata_1',get_the_ID());

            $Folders_ID = FileBird\Model\Folder::newOrGet('Instagram',FileBird\Model\Folder::newOrGet('KUI3 System',0));
            FileBird\Model\Folder::setFoldersForPosts($Attachment_ID,$Folders_ID);
        }
        update_option('wp_feedy_query_instagram',strtotime(date_i18n('Y-m-d H:i:s')));
    }

    // Viernes de Escritorio - Scoreboard
    if(date('N')==5)
    {
        $Posts = new WP_Query([
            'post_type' => ['feedy'],
            'meta_query' => [
                [
                    'key'     => 'feedy_type',
                    'value'   => 'friday_board',
                    'compare' => '=',
                ],
                [
                    'key'     => 'feedy_metadata_1',
                    'value'   => date('W'),
                    'compare' => '=',
                ]
            ],
        ]);
        if(!$Posts->have_posts())
        {
            $Query = new WP_Query([
                'post_type'  => 'viernesdeescritorio',
                'orderby'    => [
                    'Score_Clause' => 'DESC',
                    'Time_Clause' => 'ASC',
                ],
                'meta_query' => [
                    'Week_Clause' =>[
                        'key'           => 'week',
                        'value'         => date('W'),
                        'meta_compare'  => '=',
                        'type'          => 'NUMERIC'
                    ],
                    'Score_Clause' => [
                        'key' => 'score',
                        'compare' => 'EXISTS',
                    ],
                    'Time_Clause' => [
                        'key' => 'vtimestamp',
                        'value' => '-1',
                        'compare' => '>=',
                        'type' => 'NUMERIC',
                    ],
                ],
                'posts_per_page' => 5
            ]);
            if($Query->have_posts())
            {
                $Feedy_New_ID = wp_insert_post([
                    'post_title'            => sprintf('Viernes de escritorio #%1$s - Puntuaciones',date('W')),
                    'post_type'             => 'feedy',
                    'post_status'           => 'publish',
                ]);
                update_post_meta($Feedy_New_ID,'feedy_type','friday_board');
                update_post_meta($Feedy_New_ID,'feedy_metadata_1',date('W'));
            }
        }
    }

    // Twitter
    $Feedy_Query_Twitter = get_option('wp_feedy_query_twitter');
    $Posts = new WP_Query([
        'posts_per_page' => 50,
        'orderby'    => 'post_date',
        'order'      => 'DESC',
        'post_type' => ['tuit'],
        'date_query' => [
            [
                'after'    => array(
                    'year'  => date('Y',$Feedy_Query_Twitter),
                    'month' => date('m',$Feedy_Query_Twitter),
                    'day'   => date('d',$Feedy_Query_Twitter),
                    'hour'   => date('H',$Feedy_Query_Twitter),
                    'minute'   => date('i',$Feedy_Query_Twitter),
                    'second'   => date('a',$Feedy_Query_Twitter),
                ),
                'inclusive' => true,
            ],
        ],
    ]);
    if ($Posts->have_posts()) {
        while ($Posts->have_posts())
        {
            $Posts->the_post();

            list($Tuit_Text) = explode(';;',trim(get_the_content()));

            $Feedy_New_ID = wp_insert_post([
                'post_title'            => get_the_title(),
                'post_type'             => 'feedy',
                'post_status'           => 'publish',
            ]);
            wp_update_post(
                [
                    'ID'            => $Feedy_New_ID,
                    'post_date'     => get_the_time('Y-m-d H:i:s'), // '2010-02-23 18:57:33';
                    'post_date_gmt' => get_gmt_from_date(get_the_time('Y-m-d H:i:s'))
                ]
            );
            update_post_meta($Feedy_New_ID,'feedy_type','twitter');
            update_post_meta($Feedy_New_ID,'feedy_metadata_1',get_the_ID());
            update_post_meta($Feedy_New_ID,'feedy_campus',$Tuit_Text);
        }
        update_option('wp_feedy_query_twitter',strtotime(date_i18n('Y-m-d H:i:s')));
    }
}

add_action('Cronjob_Feedy_Service','Feedy_Update',100);
if (!wp_next_scheduled ( 'Cronjob_Feedy_Service' )){
    wp_schedule_event(time(),'hourly', 'Cronjob_Feedy_Service');
}