<?php
function RSS_Crawler()
{
    $RSS_Sources = new WP_Query([
        'post_type'      => 'feedrss',
        'posts_per_page' => -1,
    ]);
    $RSS_Array = [];
    if($RSS_Sources->have_posts()){
        while($RSS_Sources->have_posts()){
            $RSS_Sources->the_post();
            $RSS_Array[] = [
                get_the_ID(),
                get_the_title(),
                get_post_meta(get_the_ID(),'source',true),
                get_post_meta(get_the_ID(),'lastcrawl',true),
                get_post_meta(get_the_ID(),'favicon',true),
            ];
        }
    }

    shuffle($RSS_Array);

    while ($RSS_Array){
        list($RSS_InternalID,$RSS_WebTitle,$RSS_Url,$RSS_CrawlTime,$RSS_Icon) = array_shift($RSS_Array);

        $RSS_Parser = simplexml_load_file($RSS_Url);

        $RSS_Item_Link          = $RSS_Parser->channel->item[0]->link;
        $RSS_Item_Date          = strtotime($RSS_Parser->channel->item[0]->pubDate);

        $RSS_Crawler_LastTime   = (int)$RSS_CrawlTime;

        try
        {
            if($RSS_Item_Date>$RSS_Crawler_LastTime)
            {
                update_post_meta($RSS_InternalID,'lastcrawl',$RSS_Item_Date);

                if($RSS_Item_Link)
                {
                    $Item_Content = file_get_contents($RSS_Item_Link);

                    $DOC_Parser = new DOMDocument();
                    libxml_use_internal_errors(false);
                    $DOC_Parser->loadHTML($Item_Content);

                    $OG_Elements    = $DOC_Parser->getElementsByTagName('meta');
                    $OG_Title       = false;
                    $OG_Description = false;
                    $OG_Image       = false;
                    $OG_WebTitle    = false;
                    $OG_WebURL      = false;

                    if (!is_null($OG_Elements))
                    {
                        foreach ($OG_Elements as $OG_Element)
                        {
                            if($OG_Element->getAttribute('property')=='og:title')
                            {
                                $OG_Title = $OG_Element->getAttribute('content');
                            }
                            if($OG_Element->getAttribute('property')=='og:description')
                            {
                                $OG_Description = $OG_Element->getAttribute('content');
                            }
                            if($OG_Element->getAttribute('property')=='og:image')
                            {
                                $OG_Image = $OG_Element->getAttribute('content');
                            }
                            if($OG_Element->getAttribute('property')=='og:site_name')
                            {
                                $OG_WebTitle = $OG_Element->getAttribute('content');
                            }
                            if($OG_Element->getAttribute('property')=='og:url')
                            {
                                $OG_WebURL = $OG_Element->getAttribute('content');
                            }
                        }
                    }

                    if($OG_Title && $OG_Description && $OG_Image)
                    {
                        $Article_UUID = md5(time().$OG_Title);

                        $PostNew = wp_insert_post([
                            'post_title'    => $OG_Title,
                            'post_content'  => '',
                            'post_type'     => 'feedy',
                            'post_status'   => 'publish',
                            'post_author'   => 1,
                        ]);

                        update_post_meta($PostNew, 'feedy_type', 'externalrss');
                        update_post_meta($PostNew, 'feedy_metadata_1', $OG_WebURL?:$RSS_Item_Link);
                        update_post_meta($PostNew, 'feedy_metadata_2', $OG_WebTitle?:$RSS_WebTitle);
                        update_post_meta($PostNew, 'feedy_metadata_3', $RSS_Icon);
                        update_post_meta($PostNew, 'feedy_campus', $OG_Description);
                    }
                }

                break;
            }
        }
        catch (Throwable $E)
        {

        }
    }
}

add_action('Cronjob_Crawler_RSS','RSS_Crawler',100);
if (!wp_next_scheduled ( 'Cronjob_Crawler_RSS' )){
    wp_schedule_event(time(),'daily', 'Cronjob_Crawler_RSS');
}