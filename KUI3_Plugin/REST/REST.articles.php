<?php
function KUIREST_ENDPOINT_Article($REST_DATA)
{
    $KUI3 = new KUI_REST(['sso' => $REST_DATA['sso'], 'slug' => $REST_DATA['slug']]);
    $KUI3->UserSession_Authenticate();

    $KUI3->Articles_Get();

    return $KUI3->Articles_Get_Array(true, false, true, true);
}
function KUIREST_ENDPOINT_Article_Content($REST_DATA)
{
    $KUI3 = new KUI_REST(['sso' => $REST_DATA['sso'], 'slug' => $REST_DATA['slug']]);
    $KUI3->UserSession_Authenticate();

    $KUI3->Articles_Get();

    return $KUI3->Articles_Get_Array(false, true);
}
function KUIREST_ENDPOINT_Article_Refresh($REST_DATA)
{
    $KUI3 = new KUI_REST(['sso' => $REST_DATA['sso'], 'slug' => $REST_DATA['slug']]);
    $KUI3->UserSession_Authenticate();

    $KUI3->Articles_Get();

    return $KUI3->Articles_Get_Array(false, false, false, true);
}
function KUIREST_ENDPOINT_Article_Comments($REST_DATA)
{
    $KUI3 = new KUI_REST([
        'sso'  => $REST_DATA['sso'],
        'slug' => $REST_DATA['slug'],
        'text' => $REST_DATA['text'],
    ]);
    $KUI3->UserSession_Authenticate();

    $KUI3->Articles_Get();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $KUI3->UserSession_Requiered();

        $KUI3->Floody_Dispatch('Comment_Publish', 3, 60, 3600);

        $Comment_Text = sanitize_text_field($REST_DATA['text']);

        if (!$Comment_Text) {
            return new WP_Error('invalid_input', 'Parámetro vacío: text');
        }
        if (strlen($Comment_Text) < 5 || strlen($Comment_Text) > 512) {
            return new WP_Error('invalid_input', 'Parámetro de longitud inválida: text');
        }

        $Comment_ID = wp_insert_post([
            'post_title'   => strlen($Comment_Text) > 145 ? substr($Comment_Text, 0, 145) . '...' : $Comment_Text,
            'post_content' => $Comment_Text,
            'post_type'    => 'kui_comment',
            'post_status'  => 'publish',
            'post_author'  => 1,
        ]);
        update_post_meta($Comment_ID, 'comment_user_id', $KUI3->UserSession_CurrentUser());
        update_post_meta($Comment_ID, 'comment_display_name', $KUI3->Users_Get_Name());
        update_post_meta($Comment_ID, 'comment_article_id', $KUI3->Articles_Get_ID());

        return ['OK' => true];
    } else {
        $CommentsQuery = new WP_Query([
            'post_type'      => ['kui_comment'],
            'order'          => 'ASC',
            'posts_per_page' => -1,
            'meta_query'     => [
                [
                    'key'   => 'comment_article_id',
                    'value' => $KUI3->Articles_Get_ID(),
                ],
            ],
        ]);
        $Comments = [];

        if ($CommentsQuery->have_posts()) {
            while ($CommentsQuery->have_posts()) {
                $CommentsQuery->the_post();
                $Comment_User_ID = (int) get_post_meta(get_the_ID(), 'comment_user_id', true);

                $Comments[] = [
                    'Photo' => $KUI3->Users_Get_Picture($Comment_User_ID),
                    'Name'  => $Comment_User_ID !== 0 ? $KUI3->Users_Get_Name($Comment_User_ID) : get_post_meta(get_the_ID(), 'comment_display_name', true),
                    'Text'  => get_the_content(),
                    'Data'  => sprintf('%1$s a las %2$s', get_the_date(), get_the_time()),
                ];
            }
        }

        return [
            'Comments' => $Comments,
            'User'     => [
                'Signed' => $KUI3->UserSession_IsLogged(),
                'Name'   => $KUI3->Users_Get_Name(),
                'Photo'  => $KUI3->Users_Get_Picture(),
            ],
        ];
    }
}

function KUIREST_ENDPOINT_Article_List($Data)
{
    $KUI3 = new KUI_REST(['sso' => $Data['sso']]);

    $Query_Metas = [];
    $Posts_Array = [];

    $Query_Max_Results = (int) sanitize_text_field(isset($Data['Limit']) ? $Data['Limit'] : 10);
    if ($Query_Max_Results > 30) {
        $Query_Max_Results = 30;
    }
    if ($Query_Max_Results < 1) {
        $Query_Max_Results = 1;
    }

    $Query_Post_Type = (isset($Data['Type']) ? $Data['Type'] : 'post');
    if ($Query_Post_Type != 'post') {
        $Query_Post_Type = sanitize_text_field($Query_Post_Type);

        $Query_Post_Type = explode(',', $Query_Post_Type);
    }
    if (!is_array($Query_Post_Type)) {
        $Query_Post_Type = [$Query_Post_Type];
    }

    $Query_Feedy_Type = (isset($Data['FType']) ? $Data['FType'] : false);
    if ($Query_Feedy_Type) {
        $Query_Feedy_Type          = sanitize_text_field($Query_Feedy_Type);
        $Query_Metas['Feedy_Type'] = [
            'key'     => 'feedy_type',
            'value'   => $Query_Feedy_Type,
            'compare' => '=',
            'type'    => 'CHAR',
        ];

        $Query_Post_Type[] = 'feedy';
    }

    $Query_Post_Type_Filtered = [];
    foreach ($Query_Post_Type as $Type) {
        if (in_array($Type, ['post', 'videopost', 'noticia', 'colaboracion', 'feedy', 'evento', 'viernesdeescritorio', 'wiki_post'])) {
            $Query_Post_Type_Filtered[] = $Type;
        }
    }

    $Query_Paged = (int) sanitize_text_field(isset($Data['Page']) ? $Data['Page'] : 1);
    if ($Query_Paged < 1) {
        $Query_Paged = 1;
    }

    $Query_Array = [
        'post_type'      => $Query_Post_Type_Filtered,
        'meta_query'     => $Query_Metas,
        'posts_per_page' => $Query_Max_Results,
        'paged'          => $Query_Paged,
    ];

    if (in_array('post', $Query_Post_Type_Filtered) && (isset($Data['Category']) || isset($Data['SubCategory']))) {
        $Categories = [];

        $Query_Category    = (isset($Data['Category']) ? sanitize_text_field($Data['Category']) : false);
        $Query_SubCategory = (isset($Data['SubCategory']) ? sanitize_text_field($Data['SubCategory']) : false);

        if (substr($Query_SubCategory, 0, 1) == '/') {
            $Query_SubCategory = substr($Query_SubCategory, 1);
        }

        if ($Query_Category == 'noticias') {
            $Query_Array['post_type'] = ['noticia'];

            if ($Query_SubCategory == 'windows') {
                $Categories[] = 'blog-windows';
            } else if ($Query_SubCategory == 'linux') {
                $Categories[] = 'blog-linux';
            }
        } else if ($Query_Category == 'articulos') {
            if ($Query_SubCategory == 'windows') {
                $Categories[] = 'blog-windows';
            } else if ($Query_SubCategory == 'linux') {
                $Categories[] = 'blog-linux';
            } else {
                $Categories[] = 'karlas-blogs';
            }
        } else if ($Query_Category == 'reviews') {
            if ($Query_SubCategory == 'windows') {
                $Categories[] = 'review-windows';
            } else if ($Query_SubCategory == 'linux') {
                $Categories[] = 'review-linux';
            } else {
                $Categories[] = 'reviews';
            }
        } else {
            if ($Query_SubCategory == 'windows') {
                $Categories[] = 'review-windows,blog-windows';
            } else if ($Query_SubCategory == 'linux') {
                $Categories[] = 'review-linux,blog-linux';
            }
            $Query_Array['post_type'] = ['post', 'noticia'];
        }
        if ($Categories) {
            $Query_Array['category_name'] = implode(',', $Categories);
        }
    }

    if (in_array('videopost', $Query_Post_Type_Filtered)) {
        $Query_Category    = (isset($Data['Category']) ? sanitize_text_field($Data['Category']) : false);
        $Query_SubCategory = (isset($Data['SubCategory']) ? sanitize_text_field($Data['SubCategory']) : false);

        if ($Query_Category === 'undefined') {
            $Query_Category = false;
        }

        if ($Query_SubCategory === 'undefined') {
            $Query_SubCategory = false;
        }

        if ($Query_SubCategory) {
            $Query_Array['tax_query'] = [
                [
                    'taxonomy' => 'list',
                    'field'    => 'slug',
                    'terms'    => $Query_SubCategory,
                ],
            ];
        } else if ($Query_Category) {
            $Query_Array['tax_query'] = [
                [
                    'taxonomy' => 'list',
                    'field'    => 'slug',
                    'terms'    => $Query_Category,
                ],
            ];
        }
    }

    if (in_array('viernesdeescritorio', $Query_Post_Type_Filtered)) {
        $Desktops = [];

        $Int_Count  = 0;
        $Int_Errors = 0;

        $Query_Category = (isset($Data['Category']) ? sanitize_text_field($Data['Category']) : false);

        $Desktops_Featured = $Query_Category === 'featured';
        $Desktops_Year     = (int) (isset($Data['SubCategory']) ? sanitize_text_field($Data['SubCategory']) : date('Y'));
        $Desktops_Week     = (int) (isset($Data['Category']) ? sanitize_text_field($Data['Category']) : date('W'));

        if ($Desktops_Featured) {
            $Desktops_Week = date('W');
        }
        if ($Desktops_Year == 'undefined' || !$Desktops_Year) {
            $Desktops_Year = date('Y');
        }
        if ($Desktops_Week == 'undefined' || !$Desktops_Week) {
            $Desktops_Week = date('W');
        }
        if ($Desktops_Year < 2020) {
            $Desktops_Year = 2020;
        }
        if ($Desktops_Week <= 0) {
            $Desktops_Week = 1;
        }
        if ($Desktops_Week > 55) {
            $Desktops_Week = 55;
        }

        $Query_Metas                 = [];
        $Query_Metas['Score_Clause'] = [
            'key'     => 'score',
            'compare' => 'EXISTS',
            'type'    => 'NUMERIC',
        ];
        $Query_Metas['Time_Clause'] = [
            'key'     => 'vtimestamp',
            'value'   => '-1',
            'compare' => '>=',
            'type'    => 'NUMERIC',
        ];

        if (isset($Data['UserId']) && $Data['UserId'] > 0) {
            $Query_User = (int) sanitize_text_field($Data['UserId']);
            if ($Query_User > 0) {
                
                unset($Query_Metas['Score_Clause']);
                unset($Query_Metas['Time_Clause']);
                
                if (
                    (get_post_status($Query_User) !== false || get_post_status(telegram_getid($Query_User)) !== false) &&
                    (get_post_type(telegram_getid($Query_User)) == 'telegram_subscribers' || get_post_type($Query_User) == 'kui_user')
                ) {
                    if (get_post_type(telegram_getid($Query_User)) === 'telegram_subscribers') {
                        $Rel_User_Queruy = new WP_Query([
                            'post_type'  => 'kui_user',
                            'meta_query' => [
                                [
                                    'key'   => 'rel_telegram_user_id',
                                    'value' => $Query_User,
                                    'type'  => 'NUMERIC',
                                ],
                            ],
                        ]);
                        $Rel_KUI_ID = 0;
                        while ($Rel_User_Queruy->have_posts()) {
                            $Rel_User_Queruy->the_post();
                            $Rel_KUI_ID = get_the_ID();
                        }
                        if ($Rel_KUI_ID) {
                            $Query_Metas['User_Clause'] = [
                                'relation' => 'OR',
                                [
                                    'key'     => 'kui_user_id',
                                    'value'   => $Rel_KUI_ID,
                                    'compare' => '=',
                                    'type'    => 'NUMERIC',
                                ],
                                [
                                    'key'     => 'telegram_user_id',
                                    'value'   => $Query_User,
                                    'compare' => '=',
                                    'type'    => 'NUMERIC',
                                ],
                            ];
                        } else {
                            $Query_Metas['User_Clause'] = [
                                [
                                    'key'     => 'telegram_user_id',
                                    'value'   => $Query_User,
                                    'compare' => '=',
                                    'type'    => 'NUMERIC',
                                ],
                            ];
                        }
                    } else {
                        $Rel_Telegram_ID = (int) get_post_meta($Query_User, 'rel_telegram_user_id', true);
                        if ($Rel_Telegram_ID > 0 && telegram_getid($Rel_Telegram_ID)) {
                            $Query_Metas['User_Clause'] = [
                                'relation' => 'OR',
                                [
                                    'key'     => 'kui_user_id',
                                    'value'   => $Query_User,
                                    'compare' => '=',
                                    'type'    => 'NUMERIC',
                                ],
                                [
                                    'key'     => 'telegram_user_id',
                                    'value'   => $Rel_Telegram_ID,
                                    'compare' => '=',
                                    'type'    => 'NUMERIC',
                                ],
                            ];
                        } else {
                            $Query_Metas['User_Clause'] = [
                                [
                                    'key'     => 'kui_user_id',
                                    'value'   => $Query_User,
                                    'compare' => '=',
                                    'type'    => 'NUMERIC',
                                ],
                            ];
                        }
                    }
                }
            }
        }

        while ($Int_Count < $Query_Max_Results) {
            if ($Int_Errors > $Query_Max_Results * 3) {
                break;
            }

            $DesktopSearch = new WP_Query($Query_User && $Query_User>0?[
                'post_type'      => 'viernesdeescritorio',
                'meta_query'     => $Query_Metas,
                'posts_per_page' => 1000,
                'orderby'        => 'date',
            ]:[
                'post_type'      => 'viernesdeescritorio',
                'meta_query'     => $Query_Metas,
                'posts_per_page' => $Desktops_Featured === true ? 1 : $Query_Max_Results,
                'orderby'        => [
                    'Score_Clause' => 'DESC',
                    'Time_Clause'  => 'ASC',
                ],
                'date_query'     => [
                    [
                        'year' => $Desktops_Year,
                        'week' => $Desktops_Week,
                    ],
                ],
            ]);
            if ($DesktopSearch->have_posts()) {
                while ($DesktopSearch->have_posts()) {
                    $DesktopSearch->the_post();
                    $Int_Count++;

                    $User_KUI              = (int) get_post_meta(get_the_ID(), 'kui_user_id', true) ?: 0;
                    $User_Telegram         = (int) get_post_meta(get_the_ID(), 'telegram_user_id', true) ?: 0;
                    $Display_Profile_Name  = $KUI3->Displays_Users_Name($User_KUI, $User_Telegram);
                    $Display_Profile_Photo = $KUI3->Displays_Users_Photo($User_KUI, $User_Telegram);

                    $Desktops[] = [
                        'Display_Name'  => $Display_Profile_Name,
                        'Display_Photo' => $Display_Profile_Photo,
                        'Score'         => (string) get_post_meta(get_the_ID(), 'score', true) ?: "0",
                        'Year'          => get_the_date('Y'),
                        'Week'          => get_the_date('W'),
                        'Image'         => get_the_post_thumbnail_url(null, 'full'),
                        'Text'          => get_post_meta(get_the_ID(), 'message_text', true),
                        'Url'           => get_the_permalink(),
                    ];
                }
            } else {
                $Int_Errors++;

                $Desktops_Week--;
                if ($Desktops_Week <= 0) {
                    $Desktops_Year--;
                    $Desktops_Week = 55;
                }
                continue;
            }

            $Desktops_Week--;

            if (isset($Data['Category']) && $Desktops_Featured === false) {
                break;
            }
            if (isset($Query_User) && $Query_User>0) {
                break;
            }

            if ($Desktops_Week <= 0) {
                $Desktops_Year--;
                $Desktops_Week = 55;
            }
        }

        return $Desktops;
    } else {
        $Posts_Query = new WP_Query($Query_Array);

        if ($Posts_Query->have_posts()) {
            while ($Posts_Query->have_posts()) {
                $Posts_Query->the_post();

                $Feedy_Array = [];

                if (get_post_type() === 'feedy') {
                    $Feedy_Array['Type'] = get_post_meta(get_the_ID(), 'feedy_type', true);

                    $FeedyMeta = [
                        false,
                        get_post_meta(get_the_ID(), 'feedy_metadata_1', true),
                        get_post_meta(get_the_ID(), 'feedy_metadata_2', true),
                        get_post_meta(get_the_ID(), 'feedy_metadata_3', true),
                    ];
                    switch ($Feedy_Array['Type']) {
                        case 'friday_board':
                            $Feedy_Array['Year'] = get_the_date('Y');
                            $Feedy_Array['Week'] = $FeedyMeta[1];
                            break;
                        case 'ytmember':
                            $Feedy_Array['Profile'] = get_post_meta($FeedyMeta[1], 'channel_profile', true);
                            $Feedy_Array['Name']    = get_post_meta($FeedyMeta[1], 'channel_name', true);
                            $Feedy_Array['URL']     = get_post_meta($FeedyMeta[1], 'channel_url', true);
                            break;
                        case 'twitter':
                            preg_match_all('#(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))#', get_post_meta(get_the_ID(), 'feedy_campus', true), $Matches);

                            if (isset($Matches[0][0])) {
                                $Crawler_Link_Rendered = $Matches[0][0];
                            } else {
                                $Crawler_Link_Rendered = false;
                            }

                            $Feedy_Array['Text'] = get_post_meta(get_the_ID(), 'feedy_campus', true);
                            $Feedy_Array['Url']  = $Crawler_Link_Rendered;
                            break;
                        case 'instagram':
                            $Feedy_Array['Text']  = get_the_title();
                            $Feedy_Array['Image'] = get_the_post_thumbnail_url($FeedyMeta[1], 'full');
                            break;
                        case 'youtube':
                            $Feedy_Array['Title'] = get_the_title();
                            $Feedy_Array['VID']   = $FeedyMeta[1];
                            break;
                        case 'externalrss':
                            $Feedy_Array['Text']     = get_post_meta(get_the_ID(), 'feedy_campus', true);
                            $Feedy_Array['Url']      = get_post_meta(get_the_ID(), 'feedy_metadata_1', true);
                            $Feedy_Array['SiteName'] = get_post_meta(get_the_ID(), 'feedy_metadata_2', true);
                            $Feedy_Array['SiteIcon'] = get_post_meta(get_the_ID(), 'feedy_metadata_3', true);
                            break;
                    }
                }

                $Post_Chapters = [];
                if (get_post_type() === 'videopost') {
                    $Post_Data = get_the_content(null, false, get_the_ID());

                    preg_match_all('/(<h([1-6]{1})[^>]*>)(.*)<\/h\2>/msuU', $Post_Data, $matches, PREG_SET_ORDER);

                    foreach ($matches as $Chapter) {
                        $Post_Chapters[] = strip_tags($Chapter[0]);
                    }
                }

                $Current_Title = get_the_title();
                $Current_Date  = [
                    'All'  => sprintf('%1$s a las %2$s UTC', get_post_time('d \d\e F \d\e\l Y', true, get_the_ID(), true), get_post_modified_time('H:i', true, get_the_ID())),
                    'Date' => sprintf('%1$s', get_post_time('d \d\e F \d\e\l Y', true, get_the_ID(), true)),
                    'Time' => sprintf('%1$s UTC', get_post_time('H:i', true, get_the_ID(), true)),
                ];

                if (get_post_type() == 'wiki_post') {
                    if ($SEOtitle = get_post_meta(get_the_ID(), '_yoast_wpseo_title', true)) {
                        $Rendered      = wpseo_replace_vars($SEOtitle, get_post(get_the_ID()));
                        $Current_Title = substr($Rendered, 0, strpos($Rendered, ' - '));
                    }
                    $Current_Date = [
                        'All'  => sprintf('%1$s a las %2$s UTC', get_post_modified_time('d \d\e F \d\e\l Y', true, get_the_ID(), true), get_post_modified_time('H:i', true, get_the_ID())),
                        'Date' => sprintf('%1$s', get_post_modified_time('d \d\e F \d\e\l Y', true, get_the_ID(), true)),
                        'Time' => sprintf('%1$s UTC', get_post_modified_time('H:i', true, get_the_ID(), true)),
                    ];
                }

                $Posts_Array[] = [
                    'PostType' => get_post_type(),
                    'Feedy'    => get_post_type() === 'feedy' ? $Feedy_Array : false,
                    'Title'    => $Current_Title,
                    'Excerpt'  => get_the_excerpt(),
                    'Image'    => get_the_post_thumbnail_url(get_the_ID(), 'full'),
                    'Url'      => get_the_permalink(),
                    'Date'     => $Current_Date,
                    'Time'     => [
                        'Local' => get_post_time(),
                        'GMT'   => get_post_time('U', true),
                    ],
                    'Chapters' => $Post_Chapters,
                ];
            }
        }

        return $Posts_Array;
    }
}

add_action('rest_api_init', function () {
    register_rest_route('kui', 'article/byslug/(?P<slug>.*)/content',
        [
            'methods'  => 'GET',
            'callback' => 'KUIREST_ENDPOINT_Article_Content',
        ]
    );

    register_rest_route('kui', 'article/byslug/(?P<slug>.*)/comments',
        [
            'methods'  => 'GET,POST',
            'callback' => 'KUIREST_ENDPOINT_Article_Comments',
        ]
    );

    register_rest_route('kui', 'article/byslug/(?P<slug>.*)/refresh',
        [
            'methods'  => 'GET',
            'callback' => 'KUIREST_ENDPOINT_Article_Refresh',
        ]
    );

    register_rest_route('kui', 'article/byslug/(?P<slug>.*(?!content|refresh|comments))',
        [
            'methods'  => 'GET',
            'callback' => 'KUIREST_ENDPOINT_Article',
        ]
    );

    register_rest_route('kui', 'article/list',
        [
            'methods'  => 'GET',
            'callback' => 'KUIREST_ENDPOINT_Article_List',
        ]
    );
});
