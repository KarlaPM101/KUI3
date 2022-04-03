<?php
function KUIrest_UserSession_Card($Data)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!isset($Data['user_name'])) {
            return new WP_Error('not_specified_input', 'Requiere: user_name');
        }

        if (!isset($Data['user_mail'])) {
            return new WP_Error('not_specified_input', 'Requiere: user_mail');
        }

        if (!isset($Data['user_pass'])) {
            return new WP_Error('not_specified_input', 'Requiere: user_pass');
        }

        $User_Name = $Data['user_name'];
        $User_Mail = $Data['user_mail'];
        $User_Pass = $Data['user_pass'];

        $SSOticket = $Data['sso'];
        $SSOticket = sanitize_text_field($SSOticket);

        if (!$SSOticket) {
            return new WP_Error('sso_not_defined', 'No se ha iniciado sesión.');
        }

        if (strlen($SSOticket) !== 36) {
            return new WP_Error('sso_ticket_length', 'SSO Ticket Inválido');
        }

        $User_Name = sanitize_text_field($User_Name);
        $User_Mail = sanitize_text_field($User_Mail);
        $User_Pass = sanitize_text_field($User_Pass);

        if (!$User_Name) {
            return new WP_Error('invalid_input', 'Parámetro vacío: user_name');
        }

        if (!$User_Mail) {
            return new WP_Error('invalid_input', 'Parámetro vacío: user_mail');
        }

        if (!$User_Pass) {
            return new WP_Error('invalid_input', 'Parámetro vacío: user_pass');
        }

        if (strlen($User_Name) < 5 || strlen($User_Name) > 56) {
            return new WP_Error('invalid_input', 'Parámetro de longitud inválida: user_name');
        }

        if (strlen($User_Mail) < 5 || strlen($User_Mail) > 145) {
            return new WP_Error('invalid_input', 'Parámetro de longitud inválida: user_mail');
        }

        if (strlen($User_Pass) < 5 || strlen($User_Pass) > 56) {
            return new WP_Error('invalid_input', 'Parámetro de longitud inválida: user_pass');
        }

        if (!filter_var($User_Mail, FILTER_VALIDATE_EMAIL)) {
            return new WP_Error('invalid_input', 'Parámetro con formato incorrecto: user_mail');
        }

        $Generated_Code = implode('-', [
            substr(md5(uniqid()), 0, 8),
            substr(md5(uniqid()), 0, 4),
            substr(md5(uniqid()), 0, 4),
            substr(md5(uniqid()), 0, 4),
            substr(md5(uniqid()), 0, 12),
        ]);

        $Search = new WP_Query([
            'post_type'  => ['kui_user'],
            'meta_query' => [
                [
                    'key'   => 'user_email',
                    'value' => $User_Mail,
                ],
            ],
        ]);
        if ($Search->have_posts()) {
            return new WP_Error('user_exists', 'El usuario indicado ya existe.', ['id' => $User_Mail]);
        }

        $Boletine_Strings = file_get_contents(WP_CONTENT_DIR . "/plugins/KUI3/Mail_Templates/Type_NewUser.html");
        $Boletine_Body    = str_replace([
            '%NEWUSER%',
            '%ACTURL%',
        ], [
            $User_Name,
            "https://karlaperezyt.com?kui_val=user&sso={$SSOticket}&code={$Generated_Code}",
        ], $Boletine_Strings);

        wp_mail($User_Mail, 'Validar usuario en Karla\'s Project', $Boletine_Body, ['Content-Type: text/html; charset=UTF-8']);

        $New_User = wp_insert_post([
            'post_title'  => $User_Mail,
            'post_type'   => 'kui_user',
            'post_status' => 'publish',
            'post_author' => 1,
        ]);
        update_post_meta($New_User, 'user_email', $User_Mail);
        update_post_meta($New_User, 'user_display_name', $User_Name);
        update_post_meta($New_User, 'user_passwd', md5('KUI3_PASSWD_' . $User_Pass));
        update_post_meta($New_User, 'sso_ticket', $SSOticket);
        update_post_meta($New_User, 'activation_code', $Generated_Code);
        update_post_meta($New_User, 'activation_status', 0);

        return ['OK' => true];
    } else {
        $SSOticket = $Data['sso'];
        $SSOticket = urldecode($SSOticket);
        $SSOticket = sanitize_text_field($SSOticket);
        if (!$SSOticket) {
            return new WP_Error('sso_not_defined', 'No se ha iniciado sesión.');
        }
        if (strlen($SSOticket) !== 36) {
            return new WP_Error('sso_ticket_length', 'SSO Ticket Inválido');
        }

        $Search = new WP_Query([
            'post_type'  => ['kui_user'],
            'meta_query' => [
                [
                    'key'   => 'sso_ticket',
                    'value' => $SSOticket,
                ],
            ],
        ]);
        if (!$Search->have_posts()) {
            return new WP_Error('sso_invalid', 'Usuario no encontrado', ['sso' => $SSOticket]);
        }
        $Search->the_post();

        $CurrentUser = get_the_ID();

        $Activated = get_post_meta($CurrentUser, 'activation_status', true);
        $Code      = get_post_meta($CurrentUser, 'activation_code', true);
        if (!$Activated || $Code) {
            return new WP_Error('activation', 'Usuario no activado', ['sso' => $SSOticket]);
        }

        $AlreadySearch = new WP_Query([
            'post_type'   => 'viernesdeescritorio',
            'meta_query'  => [
                [
                    'key'   => 'kui_user_id',
                    'value' => $CurrentUser,
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
        $DesktopPublished = $AlreadySearch->have_posts();

        $CurrentSuscription = get_page_by_title(get_post_meta($CurrentUser, 'user_email', true), OBJECT, 'kui_suscription');

        return [
            'ID'               => get_the_ID(),
            'Display_Name'     => get_post_meta(get_the_ID(), 'user_display_name', true),
            'Display_Photo'    => get_post_meta(get_the_ID(), 'user_display_photo', true),
            'Dektop_Published' => $DesktopPublished,
            'Subscription'     => [
                'IsSuscribed' => $CurrentSuscription ? true : false,
                'Interest1'   => $CurrentSuscription ? (bool) get_post_meta($CurrentSuscription->ID, 'interest_1', true) : false,
                'Interest2'   => $CurrentSuscription ? (bool) get_post_meta($CurrentSuscription->ID, 'interest_2', true) : false,
                'Interest3'   => $CurrentSuscription ? (bool) get_post_meta($CurrentSuscription->ID, 'interest_3', true) : false,
            ],
            'Email'            => get_post_meta(get_the_ID(), 'user_email', true),
            'Display_Bio'      => get_post_meta(get_the_ID(), 'user_bio', true),
        ];

    }
}
function KUIrest_UserSession_MailCheck($Data)
{
    $Email = $Data['email'];
    $Email = urldecode($Email);
    $Email = sanitize_text_field($Email);

    if (!$Email) {
        return new WP_Error('email_empty', 'No se ha indicado email.');
    }

    $Search = new WP_Query([
        'post_type'  => ['kui_user'],
        'meta_query' => [
            [
                'key'   => 'user_email',
                'value' => $Email,
            ],
        ],
    ]);

    if (!$Search->have_posts()) {
        return new WP_Error('email_not_found', 'Usuario no encontrado', ['email' => $Email]);
    }

    return ['OK' => $Email];
}
function KUIrest_UserSession_Login($Data)
{

    if (!isset($Data['user_mail'])) {
        return new WP_Error('not_specified_input', 'Requiere: user_mail');
    }

    if (!isset($Data['user_pass'])) {
        return new WP_Error('not_specified_input', 'Requiere: user_pass');
    }

    $User_Mail = $Data['user_mail'];
    $User_Pass = $Data['user_pass'];

    $SSOticket = $Data['sso'];
    $SSOticket = sanitize_text_field($SSOticket);

    if (!$SSOticket) {
        return new WP_Error('sso_not_defined', 'No se ha iniciado sesión.');
    }

    if (strlen($SSOticket) !== 36) {
        return new WP_Error('sso_ticket_length', 'SSO Ticket Inválido');
    }

    $User_Mail = sanitize_text_field($User_Mail);
    $User_Pass = sanitize_text_field($User_Pass);

    if (!$User_Mail) {
        return new WP_Error('invalid_input', 'Parámetro vacío: user_mail');
    }

    if (!$User_Pass) {
        return new WP_Error('invalid_input', 'Parámetro vacío: user_pass');
    }

    if (strlen($User_Mail) < 5 || strlen($User_Mail) > 145) {
        return new WP_Error('invalid_input', 'Parámetro de longitud inválida: user_mail');
    }

    if (strlen($User_Pass) < 5 || strlen($User_Pass) > 56) {
        return new WP_Error('invalid_input', 'Parámetro de longitud inválida: user_pass');
    }

    if (!filter_var($User_Mail, FILTER_VALIDATE_EMAIL)) {
        return new WP_Error('invalid_input', 'Parámetro con formato incorrecto: user_mail');
    }

    $Search = new WP_Query([
        'post_type'  => ['kui_user'],
        'meta_query' => [
            [
                'key'   => 'user_email',
                'value' => $User_Mail,
            ],
            [
                'key'   => 'user_passwd',
                'value' => md5('KUI3_PASSWD_' . $User_Pass),
            ],
            [
                'key'   => 'activation_status',
                'value' => 1,
            ],
        ],
    ]);
    if (!$Search->have_posts()) {
        return new WP_Error('no_login', 'Credenciales incorrectas');
    }

    $Search->the_post();

    update_post_meta(get_the_ID(), 'sso_ticket', $SSOticket);

    return ['OK' => true];
}
function KUIrest_UserSession_Forgot($Data)
{

    if (!isset($Data['user_mail'])) {
        return new WP_Error('not_specified_input', 'Requiere: user_mail');
    }

    $User_Mail = $Data['user_mail'];
    $User_Mail = sanitize_text_field($User_Mail);

    $SSOticket = $Data['sso'];
    $SSOticket = sanitize_text_field($SSOticket);

    if (!$SSOticket) {
        return new WP_Error('sso_not_defined', 'No se ha iniciado sesión.');
    }

    if (strlen($SSOticket) !== 36) {
        return new WP_Error('sso_ticket_length', 'SSO Ticket Inválido');
    }

    if (!$User_Mail) {
        return new WP_Error('invalid_input', 'Parámetro vacío: user_mail');
    }

    if (!filter_var($User_Mail, FILTER_VALIDATE_EMAIL)) {
        return new WP_Error('invalid_input', 'Parámetro con formato incorrecto: user_mail');
    }

    $Search = new WP_Query([
        'post_type'  => ['kui_user'],
        'meta_query' => [
            [
                'key'   => 'user_email',
                'value' => $User_Mail,
            ],
        ],
    ]);
    if (!$Search->have_posts()) {
        return ['OK' => true];
    }

    $Search->the_post();

    $Generated_Code = implode('-', [
        substr(md5(uniqid()), 0, 8),
        substr(md5(uniqid()), 0, 4),
        substr(md5(uniqid()), 0, 4),
        substr(md5(uniqid()), 0, 4),
        substr(md5(uniqid()), 0, 12),
    ]);

    $Boletine_Strings = file_get_contents(WP_CONTENT_DIR . "/plugins/KUI3/Mail_Templates/Type_Forgot.html");
    $Boletine_Body    = str_replace([
        '%ACTURL%',
    ], [
        "https://karlaperezyt.com?kui_forgot&sso={$SSOticket}&code={$Generated_Code}",
    ], $Boletine_Strings);

    wp_mail($User_Mail, 'Recuperar usuario en Karla\'s Project', $Boletine_Body, ['Content-Type: text/html; charset=UTF-8']);

    update_post_meta(get_the_ID(), 'activation_code', $Generated_Code);

    return ['OK' => true];
}
function KUIrest_UserSession_PasswdAdd($Data)
{

    if (!isset($Data['user_pass'])) {
        return new WP_Error('not_specified_input', 'Requiere: user_pass');
    }

    $User_Pass = $Data['user_pass'];

    $SSOticket = $Data['sso'];
    $SSOticket = sanitize_text_field($SSOticket);

    if (!$SSOticket) {
        return new WP_Error('sso_not_defined', 'No se ha iniciado sesión.');
    }

    if (strlen($SSOticket) !== 36) {
        return new WP_Error('sso_ticket_length', 'SSO Ticket Inválido');
    }

    $Generated_Code = $Data['code'];
    $Generated_Code = sanitize_text_field($Generated_Code);

    $User_Pass = sanitize_text_field($User_Pass);

    if (!$User_Pass) {
        return new WP_Error('invalid_input', 'Parámetro vacío: user_pass');
    }

    if (strlen($User_Pass) < 5 || strlen($User_Pass) > 56) {
        return new WP_Error('invalid_input', 'Parámetro de longitud inválida: user_pass');
    }

    $Search = new WP_Query([
        'post_type'  => ['kui_user'],
        'meta_query' => [
            [
                'key'   => 'activation_code',
                'value' => $Generated_Code,
            ],
        ],
    ]);
    if (!$Search->have_posts()) {
        return new WP_Error('code_error', 'El código no hace referencia a ningun usuario.');
    }

    $Search->the_post();

    update_post_meta(get_the_ID(), 'user_passwd', md5('KUI3_PASSWD_' . $User_Pass));
    update_post_meta(get_the_ID(), 'activation_code', '');
    update_post_meta(get_the_ID(), 'sso_ticket', $SSOticket);

    return ['OK' => true];
}

function KUIrest_UserSession_Profile($REST_DATA)
{
    $KUI = new KUI_REST([
        'sso' => $REST_DATA['sso'],
    ]);
    $KUI->UserSession_Authenticate();

    if (!isset($REST_DATA['ID'])) {
        return new WP_Error('query_requiered_field', 'Se esperaba un ID de usuario.');
    }

    $Query_User_ID = (int) sanitize_text_field($REST_DATA['ID']);

    $Query_User_Type = sanitize_text_field($REST_DATA['Type']);

    $Query_User_Type_Sanitized = 'kui';
    if (in_array($Query_User_Type, ['kui', 'telegram'])) {
        $Query_User_Type_Sanitized = $Query_User_Type;
    }

    switch ($Query_User_Type_Sanitized) {
        case 'kui':
            $Query_User = new WP_Query([
                'post_type' => 'kui_user',
                'p'         => $Query_User_ID,
            ]);
            break;
        case 'telegram':
            $Query_User = new WP_Query([
                'post_type' => 'telegram_subscribers',
                'p'         => telegram_getid($Query_User_ID),
            ]);
            break;
        default:
            return new WP_Error('user_type_unknown', 'Se esperaba un TIPO de usuario válido.');
    }

    if (!$Query_User->have_posts()) {
        return new WP_Error('query_empty', 'No se han encontrado usuarios.');
    }

    while ($Query_User->have_posts()) {
        $Query_User->the_post();

        $User_ID    = (int) get_the_ID();
        $User_Array = [];

        $ItsMe = $KUI->UserSession_CurrentUser() === $User_ID ? true : false;

        $KUI_ID      = 0;
        $Telegram_ID = 0;

        switch ($Query_User_Type_Sanitized) {
            case 'kui':
                $Telegram_ID = (int) get_post_meta($User_ID, 'rel_telegram_user_id', true);
                $KUI_ID      = $User_ID;
                $User_Array  = [
                    'ID'          => $User_ID,
                    'Name'        => $KUI->Displays_Users_Name($User_ID),
                    'Photo'       => $KUI->Displays_Users_Photo($User_ID),
                    'Email'       => $ItsMe ? get_post_meta($User_ID, 'user_email', true) : false,
                    'Description' => (string) get_post_meta($User_ID, 'user_bio', true),
                    'Phrase'      => (string) get_post_meta($User_ID, 'user_phrase', true) ?: 'I\'m geek with 💗',
                    'Date'        => [
                        'Created'  => date('d-m-Y H:i', get_post_time('U', true, $User_ID)),
                        'Modified' => date('d-m-Y H:i', get_post_modified_time('U', true, $User_ID)),
                    ],
                    'ItsMe'       => $ItsMe,
                    'Telegram'    => false,
                ];
                break;
            case 'telegram':
                $Telegram_ID   = false;
                $Telegram_Post = get_post($User_ID);
                if ($Telegram_Post) {
                    $Telegram_ID = (int) $Telegram_Post->post_name;
                }

                $KUI_Query = new WP_Query([
                    'post_type'  => 'kui_user',
                    'meta_query' => [
                        [
                            'key'   => 'rel_telegram_user_id',
                            'value' => (int) $Telegram_ID,
                            'type'  => 'NUMERIC',
                        ],
                    ],
                ]);

                while ($KUI_Query->have_posts()) {
                    $KUI_Query->the_post();

                    $KUI_ID = (int) get_the_ID();
                }

                $ItsMe = ($KUI_ID !== 0 && get_post_meta($KUI_ID, 'sso_ticket', true) === $KUI->UserSession_CurrentTicket());

                $User_Array = [
                    'ID'          => $KUI_ID ?: $User_ID,
                    'Name'        => $KUI->Displays_Users_Name($KUI_ID, $Telegram_ID),
                    'Photo'       => $KUI->Displays_Users_Photo($KUI_ID, $Telegram_ID),
                    'Email'       => $ItsMe && $KUI_ID ? get_post_meta($KUI_ID, 'user_email', true) : false,
                    'Description' => (string) $KUI_ID ? get_post_meta($KUI_ID, 'user_bio', true) : 'I\'m geek with 💗',
                    'Phrase'      => (string) $KUI_ID ? (get_post_meta($KUI_ID, 'user_phrase', true) ?: 'I\'m geek with 💗'): 'I\'m geek with 💗',
                    'Date'        => [
                        'Created'  => date('d-m-Y H:i', get_post_time('U', true, $KUI_ID ?: telegram_getid($Telegram_ID))),
                        'Modified' => date('d-m-Y H:i', get_post_modified_time('U', true, $KUI_ID ?: telegram_getid($Telegram_ID))),
                    ],
                    'ItsMe'       => $ItsMe,
                    'Telegram'    => false,
                ];
                break;
        }

        $Comments_Num    = 0;
        $Messages_Num    = 0;
        $Karma_Num       = 0;
        $Strikes_Num     = 0;
        $Telegram_Status = 'USER';
        if ($KUI_ID !== 0) {
            $Comments_Query = new WP_Query([
                'post_type'  => 'kui_comment',
                'meta_query' => [
                    [
                        'key'   => 'comment_user_id',
                        'value' => (int) $KUI_ID,
                        'type'  => 'NUMERIC',
                    ],
                ],
            ]);
            $Comments_Num = $Comments_Query->post_count;
            while ($Comments_Query->have_posts()) {
                $Comments_Query->the_post();

                $Karma_Num += (int) get_post_meta(get_the_ID(), 'comment_karma', true);
            }

            $Desktops_Query = new WP_Query([
                'post_type'  => 'viernesdeescritorio',
                'meta_query' => [
                    [
                        'key'   => 'kui_user_id',
                        'value' => (int) $KUI_ID,
                        'type'  => 'NUMERIC',
                    ],
                ],
            ]);
        }

        if ($Telegram_ID !== 0) {
            $Messages_Num = (int) get_post_meta(telegram_getid($Telegram_ID), 'telegram_custom', true);
            $Karma_Num += (int) get_post_meta(telegram_getid($Telegram_ID), 'copito_karma', true);
            $Strikes_Num += (int) get_post_meta(telegram_getid($Telegram_ID), 'copito_warnings', true);

            $Telegram_Status_Query = json_decode(get_post_meta(telegram_getid($Telegram_ID), 'telegram_status', true), true);
            /*
             * {"ID":605772539,"FirstName":"Karla","LastName":"Pu00e9rez","Is_Junior":true,"Is_Senior":true,"Is_MemCtl":true}
             */
            $Telegram_Status_Query = array_merge([
                'ID'        => false,
                'FirstName' => false,
                'LastName'  => false,
                'Is_Junior' => false,
                'Is_Senior' => false,
                'Is_MemCtl' => false,
            ], $Telegram_Status_Query);

            if ($Telegram_Status_Query['Is_MemCtl'] === true) {
                $Telegram_Status = 'MEM';
            } elseif ($Telegram_Status_Query['Is_Senior'] === true) {
                $Telegram_Status = 'MOD';
            } elseif ($Telegram_Status_Query['Is_Junior'] === true) {
                $Telegram_Status = 'JUNIOR';
            }

            $Desktops_Query = new WP_Query([
                'post_type'  => 'viernesdeescritorio',
                'meta_query' => [
                    [
                        'key'   => 'telegram_user_id',
                        'value' => (int) $Telegram_ID,
                        'type'  => 'NUMERIC',
                    ],
                ],
            ]);
        }

        if ($Telegram_ID !== 0 && $KUI_ID !== 0) {
            $Desktops_Query = new WP_Query([
                'post_type'  => 'viernesdeescritorio',
                'meta_query' => [
                    'relation' => 'OR',
                    [
                        'key'   => 'telegram_user_id',
                        'value' => (int) $Telegram_ID,
                        'type'  => 'NUMERIC',
                    ],
                    [
                        'key'   => 'kui_user_id',
                        'value' => (int) $KUI_ID,
                        'type'  => 'NUMERIC',
                    ],
                ],
            ]);
        }

        $Desktops_Score_Num = 0;
        $Desktops_Count_Num = $Desktops_Query->post_count;
        while ($Desktops_Query->have_posts()) {
            $Desktops_Query->the_post();

            $Desktops_Score_Num += (int) get_post_meta(get_the_ID(), 'score', true);
        }

        $General_Array = [
            'Comments' => $Comments_Num,
            'Messages' => $Messages_Num,
            'Karma'    => $Karma_Num,
            'Telegram' => $Telegram_ID ? [
                'ID'      => $Telegram_ID,
                'Name'    => $KUI->Telegram_Get_Name(telegram_getid($Telegram_ID)),
                'Photo'   => $KUI->Telegram_Get_Picture(telegram_getid($Telegram_ID)),
                'Strikes' => $Strikes_Num,
                'Role'    => $Telegram_Status,
            ] : false,
            'Desktops' => [
                'Count' => $Desktops_Count_Num,
                'Score' => $Desktops_Score_Num,
            ],
        ];

        return array_merge($User_Array, $General_Array);
    }

    return new WP_Error('query_error', 'Punto finalizado inesperadamente.');
}
function KUIrest_UserSession_Photo($REST_DATA)
{
    $KUI = new KUI_REST([
        'sso' => $REST_DATA['sso'],
    ]);
    $KUI->UserSession_Authenticate();
    $KUI->UserSession_Requiered();

    $Generated_Code = implode('-', [
        substr(md5(uniqid()), 0, 8),
        substr(md5(uniqid()), 0, 4),
        substr(md5(uniqid()), 0, 4),
        substr(md5(uniqid()), 0, 4),
        substr(md5(uniqid()), 0, 12),
    ]);

    $File_GUID                  = $Generated_Code;
    $File_Directory_Destination = WP_CONTENT_DIR . "/uploads/kui_system/users_profiles/";
    $File_Directory_Temporal    = $_FILES['profile']['tmp_name'];
    $File_MimeType              = mime_content_type($File_Directory_Temporal);
    $File_Size                  = filesize($File_Directory_Temporal);
    $Accepted_MimeTypes         = [
        'image/png',
        'image/jpg',
        'image/jpeg',
        'image/bmp',
    ];

    if (!file_exists($File_Directory_Temporal)) {
        return new WP_Error('invalid_file', 'El archivo no se ha subido');
    }
    if (!in_array($File_MimeType, $Accepted_MimeTypes)) {
        return new WP_Error('invalid_mimetype', 'MimeType no aceptado', ['mime' => $File_MimeType]);
    }
    if ($File_Size > 10 * 1000 * 1000) {
        return new WP_Error('invalid_size', 'Tamaño de archivo excedido', ['size' => $File_Size]);
    }

    if ($File_MimeType === 'image/png' || $File_MimeType === 'image/bmp' || $File_MimeType === 'image/jpg' || $File_MimeType === 'image/jpeg') {
        if ($File_MimeType === 'image/png') {
            $GD_Resource = imagecreatefrompng($File_Directory_Temporal);
        }
        if ($File_MimeType === 'image/bmp') {
            $GD_Resource = imagecreatefrombmp($File_Directory_Temporal);
        }
        if ($File_MimeType === 'image/jpg' || $File_MimeType === 'image/jpeg') {
            $GD_Resource = imagecreatefromjpeg($File_Directory_Temporal);
        }

        if ($GD_Resource) {

            $thumb_width     = 640;
            $thumb_height    = 640;
            $width           = imagesx($GD_Resource);
            $height          = imagesy($GD_Resource);
            $original_aspect = $width / $height;
            $thumb_aspect    = $thumb_width / $thumb_height;
            if ($original_aspect >= $thumb_aspect) {
                $new_height = $thumb_height;
                $new_width  = $width / ($height / $thumb_height);
            } else {
                $new_width  = $thumb_width;
                $new_height = $height / ($width / $thumb_width);
            }
            $GD_Background = imagecreatetruecolor($thumb_width, $thumb_height);
            imagefill($GD_Background, 0, 0, imagecolorallocate($GD_Background, 255, 255, 255));

            // Resize and crop
            imagecopyresampled($GD_Background,
                $GD_Resource,
                0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
                0 - ($new_height - $thumb_height) / 2, // Center the image vertically
                0, 0,
                $new_width, $new_height,
                $width, $height);
            imagejpeg($GD_Background, $File_Directory_Destination . "$File_GUID.jpg", 100);
            imagedestroy($GD_Resource);
            imagedestroy($GD_Background);
        }
        unlink($File_Directory_Temporal);
    }

    $Old_Image      = get_post_meta($KUI->UserSession_CurrentUser(), 'user_display_photo', true);
    $Old_Image_Path = WP_CONTENT_DIR . "/uploads/kui_system/users_profiles/$Old_Image.jpg";
    if (file_exists($Old_Image_Path)) {
        unlink($Old_Image_Path);
    }

    update_post_meta($KUI->UserSession_CurrentUser(), 'user_display_photo', $File_GUID);

    return [
        'Updated_User' => $KUI->UserSession_CurrentUser(),
        'Image_GUID'   => $File_GUID,
        'Image_Path'   => "/wp-content/uploads/kui_system/users_profiles/$File_GUID.jpg",
    ];
}
function KUIrest_UserSession_Edit($REST_DATA)
{
    $KUI = new KUI_REST([
        'sso' => $REST_DATA['sso'],
    ]);
    $KUI->UserSession_Authenticate();
    $KUI->UserSession_Requiered();

    if (!isset($REST_DATA['user_name'])) {
        return new WP_Error('not_specified_input', 'Requiere: user_name');
    }
    if (!isset($REST_DATA['user_mail'])) {
        return new WP_Error('not_specified_input', 'Requiere: user_mail');
    }

    $User_Name = sanitize_text_field($REST_DATA['user_name']);
    $User_Mail = sanitize_text_field($REST_DATA['user_mail']);
    $User_Bio  = sanitize_text_field($REST_DATA['user_bio']);

    if (!$User_Name) {
        return new WP_Error('invalid_input', 'Parámetro vacío: user_name');
    }
    if (!$User_Mail) {
        return new WP_Error('invalid_input', 'Parámetro vacío: user_mail');
    }

    if (strlen($User_Name) < 5 || strlen($User_Name) > 56) {
        return new WP_Error('invalid_input', 'Parámetro de longitud inválida: user_name');
    }
    if (strlen($User_Mail) < 5 || strlen($User_Mail) > 145) {
        return new WP_Error('invalid_input', 'Parámetro de longitud inválida: user_mail');
    }
    if ($User_Bio && strlen($User_Bio) > 256) {
        return new WP_Error('invalid_input', 'Parámetro de longitud inválida: user_bio');
    }

    if (!filter_var($User_Mail, FILTER_VALIDATE_EMAIL)) {
        return new WP_Error('invalid_input', 'Parámetro con formato incorrecto: user_mail');
    }

    $Old_Mail = get_post_meta($KUI->UserSession_CurrentUser(), 'user_email', true);

    $CurrentUser = $KUI->UserSession_CurrentUser();

    $Mail_Is_Modified = false;

    if ($Old_Mail !== $User_Mail) {
        $Search = new WP_Query([
            'post_type'  => ['kui_user'],
            'meta_query' => [
                [
                    'key'   => 'user_email',
                    'value' => $User_Mail,
                ],
            ],
        ]);
        if ($Search->have_posts()) {
            return new WP_Error('user_exists', 'El usuario indicado ya existe.', ['id' => $User_Mail]);
        }

        $SSOticket = $KUI->UserSession_CurrentTicket();

        $Generated_Code = implode('-', [
            substr(md5(uniqid()), 0, 8),
            substr(md5(uniqid()), 0, 4),
            substr(md5(uniqid()), 0, 4),
            substr(md5(uniqid()), 0, 4),
            substr(md5(uniqid()), 0, 12),
        ]);

        $Boletine_Strings = file_get_contents(WP_CONTENT_DIR . "/plugins/KUI3/Mail_Templates/Type_ChgUser.html");
        $Boletine_Body    = str_replace([
            '%NEWUSER%',
            '%ACTURL%',
        ], [
            $User_Name,
            "https://karlaperezyt.com?kui_val=user&sso={$SSOticket}&code={$Generated_Code}",
        ], $Boletine_Strings);

        wp_mail($User_Mail, 'Validar usuario en Karla\'s Project', $Boletine_Body, ['Content-Type: text/html; charset=UTF-8']);

        update_post_meta($CurrentUser, 'activation_code', $Generated_Code);
        update_post_meta($CurrentUser, 'activation_status', 0);

        update_post_meta($CurrentUser, 'user_email', $User_Mail);

        $Mail_Is_Modified = true;
    }

    update_post_meta($CurrentUser, 'user_display_name', $User_Name);
    update_post_meta($CurrentUser, 'user_bio', $User_Bio);

    return ['OK' => $Mail_Is_Modified ? 'MAIL' : 'OK'];
}

add_action('rest_api_init', function () {
    register_rest_route('kui', 'usersession/card', [
        'methods'  => 'GET,POST',
        'callback' => 'KUIrest_UserSession_Card',
        'args'     => [
            'sso' => [
                'default' => '',
            ],
        ]]);
    register_rest_route('kui', 'usersession/mailcheck', [
        'methods'  => 'GET',
        'callback' => 'KUIrest_UserSession_MailCheck',
        'args'     => [
            'email' => [
                'default' => '',
            ],
        ]]);
    register_rest_route('kui', 'usersession/login', [
        'methods'  => 'POST',
        'callback' => 'KUIrest_UserSession_Login']);
    register_rest_route('kui', 'usersession/passwd', [
        'methods'  => 'POST',
        'callback' => 'KUIrest_UserSession_PasswdAdd']);
    register_rest_route('kui', 'usersession/forgot', [
        'methods'  => 'POST',
        'callback' => 'KUIrest_UserSession_Forgot']);

    register_rest_route('kui', 'user/profile', [
        'methods'  => 'GET',
        'callback' => 'KUIrest_UserSession_Profile']);

    register_rest_route('kui', 'usersession/photo', [
        'methods'  => 'POST',
        'callback' => 'KUIrest_UserSession_Photo']);
    register_rest_route('kui', 'usersession/edit', [
        'methods'  => 'POST',
        'callback' => 'KUIrest_UserSession_Edit']);
});

add_action('after_setup_theme', function () {
    if (isset($_GET['kui_val']) && isset($_GET['code']) && isset($_GET['sso'])) {
        $Activation_Code = sanitize_text_field($_GET['code']);

        if ($Activation_Code) {
            switch ($_GET['kui_val']) {
                case 'user':
                    $Activation_Search = new WP_Query([
                        'post_type'      => 'kui_user',
                        'posts_per_page' => 1,
                        'meta_query'     => [
                            [
                                'key'          => 'activation_code',
                                'value'        => $Activation_Code,
                                'meta_compare' => '=',
                                'type'         => 'CHAR',
                            ],
                        ],
                    ]);
                    break;
                case 'mail':
                    $Activation_Search = new WP_Query([
                        'post_type'      => 'kui_suscription',
                        'posts_per_page' => 1,
                        'meta_query'     => [
                            [
                                'key'          => 'activation_code',
                                'value'        => $Activation_Code,
                                'meta_compare' => '=',
                                'type'         => 'CHAR',
                            ],
                        ],
                    ]);
                    break;
            }

            if ($Activation_Search->have_posts()) {
                while ($Activation_Search->have_posts()) {
                    $Activation_Search->the_post();

                    update_post_meta(get_the_ID(), 'activation_code', '');
                    update_post_meta(get_the_ID(), 'activation_status', '1');
                }
            }
        }
    }

    if (isset($_GET['kui_sub']) && isset($_GET['subuuid'])) {
        $Activation_Code = sanitize_text_field($_GET['subuuid']);

        if ($Activation_Code) {
            $Activation_Search = new WP_Query([
                'post_type'      => 'kui_suscription',
                'posts_per_page' => 1,
                'meta_query'     => [
                    [
                        'key'          => 'reference',
                        'value'        => $Activation_Code,
                        'meta_compare' => '=',
                        'type'         => 'CHAR',
                    ],
                ],
            ]);
            if ($Activation_Search->have_posts()) {
                while ($Activation_Search->have_posts()) {
                    $Activation_Search->the_post();

                    wp_delete_post(get_the_ID());
                }
            }
        }
    }
}, 0);
