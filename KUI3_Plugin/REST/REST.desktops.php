<?php
function KUIREST_ENDPOINT_Desktops_List($REST_DATA)
{
    $KUI3 = new KUI_REST([
        'sso'     => $REST_DATA['sso'],
        'message' => $REST_DATA['message'],
    ]);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $KUI3->UserSession_Authenticate();
        $KUI3->UserSession_Requiered();

        $Input_Text = sanitize_text_field($REST_DATA['message']);

        if (!$Input_Text) {
            return new WP_Error('invalid_input', 'Parámetro vacío: message');
        }
        if (strlen($Input_Text) < 5 || strlen($Input_Text) > 512) {
            return new WP_Error('invalid_input', 'Parámetro de longitud inválida: message');
        }

        $Query_Published = new WP_Query([
            'post_type'   => 'viernesdeescritorio',
            'meta_query'  => [
                [
                    'key'   => 'kui_user_id',
                    'value' => $KUI3->UserSession_CurrentUser(),
                ],
            ],
            'date_query'  => [
                [
                    'year' => date('Y'),
                    'week' => date('W'),
                ],
            ],
            'post_status' => ['publish', 'future'],
        ]);
        if ($Query_Published->have_posts()) {
            return new WP_Error('publish_done', 'Ya publicaste un escritorio');
        }

        $Desktop_Months = [
            '',
            'Enero',
            'Febrero',
            'Marzo',
            'Abril',
            'Mayo',
            'Junio',
            'Julio',
            'Agosto',
            'Septiembre',
            'Octubre',
            'Noviembre',
            'Diciembre',
        ];
        $Accepted_MimeTypes = [
            'image/png',
            'image/jpg',
            'image/jpeg',
            'image/bmp',
            'image/x-ms-bmp',
        ];
        $DateTime_Offset            = strtotime('next friday') - time();
        $File_Directory_Destination = WP_CONTENT_DIR . "/uploads/kui_system/desktops/" . date('Y') . "/";
        $File_Directory_Temporal    = $_FILES['desktop']['tmp_name'];
        $File_MimeType              = mime_content_type($File_Directory_Temporal);
        $File_Size                  = filesize($File_Directory_Temporal);
        $Publish_Status             = 'future';

        if ((date('N') == 5) || (date('N') == 6 && date('H') < 12)) {
            $DateTime_Offset = 0;
            $Publish_Status  = 'publish';
        }
        if (!file_exists($File_Directory_Destination)) {
            return new WP_Error('invalid_file', 'El archivo no se ha subido');
        }
        if (!in_array($File_MimeType, $Accepted_MimeTypes)) {
            return new WP_Error('invalid_mimetype', 'MimeType no aceptado', ['mime' => $File_MimeType]);
        }
        if ($File_Size > 10 * 1000 * 1000) {
            return new WP_Error('invalid_size', 'Tamaño de archivo excedido', ['size' => $File_Size]);
        }

        $Desktop_ID = wp_insert_post([
            'post_title'    => sprintf('Viernes de Escritorio #%1$s por %2$s', date('W'), trim(get_post_meta($KUI3->UserSession_CurrentUser(), 'user_display_name', true))),
            'post_content'  => sprintf('🖌 #ViernesDeEscritorio - Escritorio de %1$s', trim(get_post_meta($KUI3->UserSession_CurrentUser(), 'user_display_name', true))),
            'post_type'     => 'viernesdeescritorio',
            'post_status'   => $Publish_Status,
            'post_author'   => 1,
            'post_date'     => date('Y-m-d H:i:s', strtotime(current_time('mysql')) + $DateTime_Offset),
            'post_date_gmt' => date('Y-m-d H:i:s', strtotime(current_time('mysql', 1)) + $DateTime_Offset),
        ]);

        update_post_meta($Desktop_ID, 'year',date('Y'));
        update_post_meta($Desktop_ID, 'week', date('W'));
        update_post_meta($Desktop_ID, 'message_text', $Input_Text);
        update_post_meta($Desktop_ID, 'score', 0);
        update_post_meta($Desktop_ID, 'vtimestamp', 0);
        update_post_meta($Desktop_ID, 'kui_user_id', $KUI3->UserSession_CurrentUser());

        if (in_array($File_MimeType, ['image/jpg', 'image/jpeg'])) {
            if (!move_uploaded_file($File_Directory_Temporal, $File_Directory_Destination . date('W') . "_{$Desktop_ID}.jpg")) {
                return new WP_Error('invalid_file', 'No ha sido posible subir el archivo.');
            }
        } elseif ($File_MimeType === 'image/png' || $File_MimeType === 'image/bmp' || $File_MimeType === 'image/x-ms-bmp') {
            if ($File_MimeType === 'image/png') {
                $GD_Resource = imagecreatefrompng($File_Directory_Temporal);
            }
            if ($File_MimeType === 'image/bmp' || $File_MimeType === 'image/x-ms-bmp') {
                $GD_Resource = imagecreatefrombmp($File_Directory_Temporal);
            }

            if ($GD_Resource) {
                $GD_Background = imagecreatetruecolor(imagesx($GD_Resource), imagesy($GD_Resource));

                imagefill($GD_Background, 0, 0, imagecolorallocate($GD_Background, 255, 255, 255));
                imagealphablending($GD_Background, true);
                imagecopy($GD_Background, $GD_Resource, 0, 0, 0, 0, imagesx($GD_Resource), imagesy($GD_Resource));
                imagedestroy($GD_Resource);
                imagejpeg($GD_Background, $File_Directory_Destination . date('W') . "_{$Desktop_ID}.jpg", 100);
                imagedestroy($GD_Background);
            }

            unlink($File_Directory_Temporal);
        }

        $Desktop_Attachment = [
            'post_mime_type' => $File_MimeType,
            'post_parent'    => $Desktop_ID,
            'post_title'     => get_the_title($Desktop_ID),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ];
        $Desktop_Attachment_ID = wp_insert_attachment($Desktop_Attachment, $File_Directory_Destination . date('W') . "_{$Desktop_ID}.jpg", $Desktop_ID);
        set_post_thumbnail($Desktop_ID, $Desktop_Attachment_ID);

        if (!function_exists('wp_crop_image')) {
            include(ABSPATH.'wp-admin/includes/image.php');
        }
        wp_generate_attachment_metadata($Desktop_Attachment_ID,$File_Directory_Destination . date('W') . "_{$Desktop_ID}.jpg");

        $Folders_ID = FileBird\Model\Folder::newOrGet('ViernesDeEscritorio', FileBird\Model\Folder::newOrGet('KUI3 System', 0));
        FileBird\Model\Folder::setFoldersForPosts($Desktop_Attachment_ID, $Folders_ID);

        $Telegram_Reference = get_post_meta($KUI3->UserSession_CurrentUser(), 'rel_telegram_user_id', true);
        if ($Telegram_Reference && get_post_status($Telegram_Reference) !== false) {
            $Telegram_ID = get_the_title($Telegram_Reference);
            update_post_meta($Desktop_ID, 'telegram_user_id', $Telegram_ID);
            //update_post_meta($DesktopID, 'message_id',$Message_ID);
        } else {
            update_post_meta($Desktop_ID, 'telegram_user_id', 0);
        }

        return [
            'Display_Name'  => $KUI3->Users_Get_Name(),
            'Display_Photo' => $KUI3->Users_Get_Picture(),
            'Score'         => 0,
            'Year'          => date('Y'),
            'Week'          => date('W'),
            'Image'         => "/wp-content/uploads/kui_system/desktops/" . date('Y') . "/" . date('W') . "_{$Desktop_ID}.jpg",
            'Text'          => $Input_Text,
            'Mode'          => $Publish_Status,
            'Schedule'      => date('Y-m-d H:i:s', strtotime(current_time('mysql')) + $DateTime_Offset),
            'ScheduleGMT'   => date('Y-m-d H:i:s', strtotime(current_time('mysql', 1)) + $DateTime_Offset),
            'd'             => date('d', strtotime(current_time('mysql', 1)) + $DateTime_Offset),
            'm'             => $Desktop_Months[(int) date('m', strtotime(current_time('mysql', 1)) + $DateTime_Offset)],
            'Y'             => date('Y', strtotime(current_time('mysql', 1)) + $DateTime_Offset),
        ];
    } else {
        $MaxSearch = (int) (isset($REST_DATA['Limit']) ? $REST_DATA['Limit'] : 10);
        $MaxSearch = sanitize_text_field($MaxSearch);
        if ($MaxSearch > 30) {
            $MaxSearch = 30;
        }

        if ($MaxSearch < 1) {
            $MaxSearch = 1;
        }

        $StartYear = (int) (isset($REST_DATA['Year']) ? $REST_DATA['Year'] : date('Y'));
        $StartWeek = (int) (isset($REST_DATA['Week']) ? $REST_DATA['Week'] : date('W'));

        if ($StartYear < 2020) {
            $StartYear = 2020;
        }

        if ($StartWeek <= 0) {
            $StartWeek = 1;
        }

        if ($StartWeek > 55) {
            $StartWeek = 55;
        }

        $Featured = true;
        if (isset($REST_DATA['Year']) || isset($REST_DATA['Week'])) {
            $Featured = false;
        }
        if (isset($REST_DATA['Featured'])) {
            $Featured = (bool) sanitize_text_field($REST_DATA['Featured']);
            if ($REST_DATA['Featured'] === 'false') {
                $Featured = false;
            }
        }

        $Search_Metas                 = [];
        $Search_Metas['Score_Clause'] = [
            'key'     => 'score',
            'compare' => 'EXISTS',
            'type'    => 'NUMERIC',
        ];
        $Search_Metas['Time_Clause'] = [
            'key'     => 'vtimestamp',
            'value'   => '-1',
            'compare' => '>=',
            'type'    => 'NUMERIC',
        ];

        if (isset($REST_DATA['UserId']) && $REST_DATA['UserId'] > 0) {
            $SearchUser = (int) sanitize_text_field($REST_DATA['UserId']);
            if ($SearchUser > 0) {
                if ((get_post_status($SearchUser) !== false || get_post_status(telegram_getid($SearchUser)) !== false) && (get_post_type(telegram_getid($SearchUser)) == 'telegram_subscribers' || get_post_type($SearchUser) == 'kui_user')) {
                    $Search_Metas['User_Clause'] = [
                        'relation' => 'OR',
                        [
                            'key'     => 'kui_user_id',
                            'value'   => $SearchUser,
                            'compare' => '=',
                            'type'    => 'NUMERIC',
                        ],
                        [
                            'key'     => 'telegram_user_id',
                            'value'   => $SearchUser,
                            'compare' => '=',
                            'type'    => 'NUMERIC',
                        ],
                    ];
                }
            }

        }

        $MaxCount = $MaxSearch;
        $CurCount = 0;
        $ErrCount = 0;

        $Desktops = [];

        while ($CurCount < $MaxCount) {
            if ($ErrCount > $MaxCount * 3) {
                break;
            }

            $DesktopSearch = new WP_Query([
                'post_type'      => 'viernesdeescritorio',
                'date_query'     => [
                    [
                        'year' => $StartYear,
                        'week' => $StartWeek,
                    ],
                ],
                'orderby'        => [
                    'Score_Clause' => 'DESC',
                    'Time_Clause'  => 'ASC',
                ],
                'meta_query'     => $Search_Metas,
                'posts_per_page' => $Featured === true ? 1 : $MaxSearch,
            ]);
            if ($DesktopSearch->have_posts()) {
                while ($DesktopSearch->have_posts()) {
                    $DesktopSearch->the_post();
                    $CurCount++;

                    $UserKUI      = (int) get_post_meta(get_the_ID(), 'kui_user_id', true);
                    $UserTelegram = (int) get_post_meta(get_the_ID(), 'telegram_user_id', true);

                    $Display_Profile_Name  = get_post_meta(get_the_ID(), 'display_name', true);
                    $Display_Profile_Photo = get_post_meta(get_the_ID(), 'display_profile', true);

                    if ($UserKUI && get_post_status($UserKUI) !== false) {
                        $Display_Profile_Name  = get_post_meta($UserKUI, 'user_display_name', true);
                        $Display_Profile_Photo = '/wp-content/uploads/kui_system/users_profiles/' . get_post_meta($UserKUI, 'user_display_photo', true) . '.jpg';
                    } else if ($UserTelegram && get_post_status(telegram_getid($UserTelegram)) !== false) {
                        $Display_Profile_Name  = trim(get_post_meta(telegram_getid($UserTelegram), 'telegram_first_name', true) . get_post_meta(telegram_getid($UserTelegram), 'telegram_last_name', true));
                        $Display_Profile_Photo = '/wp-content/uploads/kui_system/telegram_profiles/' . get_the_title(telegram_getid($UserTelegram)) . '.jpg';
                    }

                    $Desktops[] = [
                        'Display_Name'  => $Display_Profile_Name,
                        'Display_Photo' => $Display_Profile_Photo,
                        'Score'         => get_post_meta(get_the_ID(), 'score', true) ?: "0",
                        'Year'          => get_the_date('Y'),
                        'Week'          => get_the_date('W'),
                        'Image'         => get_the_post_thumbnail_url(null, 'full'),
                        'Text'          => get_post_meta(get_the_ID(), 'message_text', true),
                        'Url'           => get_the_permalink(),
                    ];
                }
            } else {
                $ErrCount++;

                $StartWeek--;
                if ($StartWeek <= 0) {
                    $StartYear--;
                    $StartWeek = 55;
                }
                continue;
            }

            if ($Featured === false) {
                break;
            }

            $StartWeek--;
            if ($StartWeek <= 0) {
                $StartYear--;
                $StartWeek = 55;
            }
        }

        return $Desktops;
    }
}
function KUIREST_ENDPOINT_Desktops_Podium()
{
    $KUI3 = new KUI_REST([]);

    $List_Score  = [];
    $List_Podium = [];

    global $wpdb;
    $Query = $wpdb->get_results("SELECT CAST(meta_value AS UNSIGNED) AS score,post_id AS ID, (SELECT post_type FROM wp_posts WHERE ID = post_id) AS ptype,(SELECT post_title FROM wp_posts WHERE ID = post_id) AS pname FROM wp_postmeta WHERE meta_key = 'desktop_points' ORDER BY score DESC;");

    $List_Podium = [];

    foreach ($Query as $Query_Row)
    {
        switch ($Query_Row->ptype){
            case 'telegram_subscribers':
                $List_Podium[] = [
                    'Display_Name'  => $KUI3->Telegram_Get_Name($Query_Row->ID),
                    'Display_Photo' => $KUI3->Telegram_Get_Picture($Query_Row->pname),
                    'Score'         => $Query_Row->score,
                    'Profile'       => $KUI3->Telegram_Get_URL($Query_Row->pname),
                ];
                break;
            default:
                $List_Podium[] = [
                    'Display_Name'  => $KUI3->Users_Get_Name($Query_Row->ID),
                    'Display_Photo' => $KUI3->Users_Get_Picture($Query_Row->ID),
                    'Score'         => $Query_Row->score,
                    'Profile'       => $KUI3->Users_Get_URL($Query_Row->ID)
                ];
        }
    }

    return $List_Podium;
}
function KUIREST_ENDPOINT_Desktops_Upvote($Data)
{
    $SSOticket = $Data['sso'];
    $SSOticket = sanitize_text_field($SSOticket);

    if (!$SSOticket) {
        return new WP_Error('sso_not_defined', 'No se ha iniciado sesión.');
    }

    if (strlen($SSOticket) !== 36) {
        return new WP_Error('sso_ticket_length', 'SSO Ticket Inválido');
    }

    $MyUser = new WP_Query([
        'post_type'  => ['kui_user'],
        'meta_query' => [
            [
                'key'   => 'sso_ticket',
                'value' => $SSOticket,
            ],
        ],
    ]);

    if (!$MyUser->have_posts()) {
        return new WP_Error('session', 'Requiere sesión iniciada');
    }

    $MyUser->the_post();
    $UserID = get_the_ID();

    $DesktopID = (int) sanitize_text_field($Data['desktop']);
    if (!$DesktopID) {
        return new WP_Error('desktop_not_defined', 'No se ha especificado el escritorio.');
    }

    $MyDesktop = get_post($DesktopID);
    if (!$MyDesktop) {
        return new WP_Error('desktop_not_found', 'No se ha encontrado el escritorio.');
    }

    if (get_post_type($MyDesktop->ID) !== 'viernesdeescritorio') {
        return new WP_Error('desktop_not_found', 'No se ha encontrado el escritorio.');
    }

    if (get_post_meta($MyDesktop->ID, 'kui_user_id', true) == $UserID) {
        return new WP_Error('upvote_done', 'Ya has votado esta publicación.');
    }

    $Scorers   = get_post_meta($MyDesktop->ID, 'scorers', true);
    $ScoreList = explode(';', $Scorers);

    if (in_array('K' . $UserID, $ScoreList)) {
        return new WP_Error('upvote_done', 'Ya has votado esta publicación.');
    }

    $Score = get_post_meta($MyDesktop->ID, 'score', true);

    update_post_meta($MyDesktop->ID, 'score', $Score + 1);

    update_post_meta($MyDesktop->ID, 'vtimestamp', time());

    update_post_meta($MyDesktop->ID, 'scorers', $Scorers . ';' . 'K' . $UserID);

    $UserKUI      = (int) get_post_meta($MyDesktop->ID, 'kui_user_id', true);
    $UserTelegram = (int) get_post_meta($MyDesktop->ID, 'telegram_user_id', true);

    $Display_Profile_Name  = get_post_meta($MyDesktop->ID, 'display_name', true);
    $Display_Profile_Photo = get_post_meta($MyDesktop->ID, 'display_profile', true);
    $Display_Profile_URL   = false;

    if ($UserKUI && get_post_status($UserKUI) !== false) {
        $Display_Profile_Name  = get_post_meta($UserKUI, 'user_display_name', true);
        $Display_Profile_Photo = '/wp-content/uploads/kui_system/users_profiles/' . get_post_meta($UserKUI, 'user_display_photo', true) . 'jpg';
        $Display_Profile_URL   = "https://karlaperezyt.com/usuarios/$UserKUI";
        $Markdown_Display      = "[$Display_Profile_Name](https://karlaperezyt.com/usuarios/$UserKUI)";
    } else if ($UserTelegram && get_post_status(telegram_getid($UserTelegram)) !== false) {
        $Display_Profile_Name  = trim(get_post_meta(telegram_getid($UserTelegram), 'telegram_first_name', true) . get_post_meta(telegram_getid($UserTelegram), 'telegram_last_name', true));
        $Display_Profile_Photo = '/wp-content/uploads/kui_system/telegram_profiles/' . get_the_title(telegram_getid($UserTelegram)) . '.jpg';
        $Display_Profile_URL   = "https://karlaperezyt.com/telegram/miembros/$UserTelegram";
        $Markdown_Display      = "[$Display_Profile_Name](tg://user?id=$UserTelegram)";
    }

    $Original     = get_post_meta($MyDesktop->ID, 'message_id', true);
    $DesktopImage = get_the_post_thumbnail_url($MyDesktop->ID);

    $MyUserName = get_post_meta($UserID, 'user_display_name', true);

    $Last = get_option('wp_copito_lastfriday_msg');
    Copito_Message_Delete(COPITO_GROUP_ID, $Last);

    $Snd_Id = Copito_Message_Image(COPITO_GROUP_ID, $DesktopImage, "🍇 *WP KarlaKUI3* ([$MyUserName](https://karlaperezyt.com/usuarios/$UserID))\n\n[$MyUserName](https://karlaperezyt.com/usuarios/$UserID) ha hecho un upvote *+* *1* 🖌 al escritorio de $Markdown_Display.\n\n*Autor:* $Markdown_Display\n*Fecha:* " . get_the_date('d.m.Y H:i:s', $MyDesktop->ID) . "\n*Puntuación:* " . ($Score + 1) . " votos.", [
        "inline_keyboard" => [
            [
                [
                    "text"          => "Votar +1",
                    "callback_data" => "Viernes_Upvote_ID_{$MyDesktop->ID}",
                ],
            ],
            [
                [
                    "text" => "Ver Escritorio",
                    "url"  => "tg://privatepost?channel=" . COPITO_GROUP_ID_PRIV . "&post=$Original",
                ],
                [
                    "text" => "Ver en la Web",
                    "url"  => get_the_permalink($MyDesktop->ID),
                ],
            ],
        ],
    ], $Original);
    update_option('wp_copito_lastfriday_msg', $Snd_Id);

    return [
        'Score' => $Score + 1,
    ];
}

add_action('rest_api_init', function () {
    register_rest_route('kui', 'desktops/list',
        [
            'methods'  => 'GET,POST',
            'callback' => 'KUIREST_ENDPOINT_Desktops_List',
        ]
    );
    register_rest_route('kui', 'desktops/podium',
        [
            'methods'  => 'GET',
            'callback' => 'KUIREST_ENDPOINT_Desktops_Podium',
        ]
    );
    register_rest_route('kui', 'desktops/upvote',
        [
            'methods'  => 'POST',
            'callback' => 'KUIREST_ENDPOINT_Desktops_Upvote',
        ]
    );
});
