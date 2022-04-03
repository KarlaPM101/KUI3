<?php
function KUIrest_Suscription($Data)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

        $Interest1 = (bool) sanitize_text_field($Data['interest1'] ?: false);
        $Interest2 = (bool) sanitize_text_field($Data['interest2'] ?: false);
        $Interest3 = (bool) sanitize_text_field($Data['interest3'] ?: false);

        $UserID = false;
        if ($MyUser->have_posts()) {
            $MyUser->the_post();
            $UserID   = get_the_ID();
            $SubEmail = get_post_meta($UserID, 'user_email', true);
        } else {
            $SubEmail = $Data['email'];
            $SubEmail = sanitize_text_field($SubEmail);

            if (!$SubEmail) {
                return new WP_Error('invalid_input', 'Parámetro vacío: email');
            }

            if (strlen($SubEmail) < 5 || strlen($SubEmail) > 145) {
                return new WP_Error('invalid_input', 'Parámetro de longitud inválida: email');
            }

            if (!filter_var($SubEmail, FILTER_VALIDATE_EMAIL)) {
                return new WP_Error('invalid_input', 'Parámetro con formato incorrecto: email');
            }
        }

        $CurrentSuscription = get_page_by_title($UserID ? get_post_meta($UserID, 'user_email', true) : $SubEmail, OBJECT, 'kui_suscription');
        if ($CurrentSuscription) {
            update_post_meta($CurrentSuscription->ID, 'interest_1', $Interest1);
            update_post_meta($CurrentSuscription->ID, 'interest_2', $Interest2);
            update_post_meta($CurrentSuscription->ID, 'interest_3', $Interest3);

            return [
                'Email'  => get_the_title($CurrentSuscription->ID),
                'Action' => 'MODIFY',
                'Status' => true,
                'Int1'   => $Interest1,
                'Int2'   => $Interest2,
                'Int3'   => $Interest3,
            ];
        } else {
            $Suscription_Reference = implode('-', [
                substr(md5(uniqid()), 0, 8),
                substr(md5(uniqid()), 0, 4),
                substr(md5(uniqid()), 0, 4),
                substr(md5(uniqid()), 0, 4),
                substr(md5(uniqid()), 0, 12),
            ]);

            if (!$UserID) {
                $Generated_Code = implode('-', [
                    substr(md5(uniqid()), 0, 8),
                    substr(md5(uniqid()), 0, 4),
                    substr(md5(uniqid()), 0, 4),
                    substr(md5(uniqid()), 0, 4),
                    substr(md5(uniqid()), 0, 12),
                ]);

                $Boletine_Strings = file_get_contents(WP_CONTENT_DIR . "/plugins/KUI3/Mail_Templates/Type_Suscribe.html");
                $Boletine_Body    = str_replace([
                    '%ACTURL%',
                    '%SUBID%',
                ], [
                    "https://karlaperezyt.com?kui_val=mail&sso={$SSOticket}&code={$Generated_Code}",
                    $Suscription_Reference,
                ], $Boletine_Strings);

                wp_mail($SubEmail, 'Validar suscripción en Karla\'s Project', $Boletine_Body, ['Content-Type: text/html; charset=UTF-8']);
            }

            $New_Suscriber = wp_insert_post([
                'post_title'  => $UserID ? get_post_meta($UserID, 'user_email', true) : $SubEmail,
                'post_type'   => 'kui_suscription',
                'post_status' => 'publish',
                'post_author' => 1,
            ]);
            update_post_meta($New_Suscriber, 'reference', $Suscription_Reference);
            update_post_meta($New_Suscriber, 'activation_code', $UserID ? '' : $Generated_Code);
            update_post_meta($New_Suscriber, 'activation_status', $UserID ? 1 : 0);
            update_post_meta($New_Suscriber, 'interest_1', $Interest1);
            update_post_meta($New_Suscriber, 'interest_2', $Interest2);
            update_post_meta($New_Suscriber, 'interest_3', $Interest3);
        }

        return [
            'Email'  => $UserID ? get_post_meta($UserID, 'user_email', true) : $SubEmail,
            'Action' => 'CREATE',
            'Status' => $UserID ? true : false,
            'Int1'   => $Interest1,
            'Int2'   => $Interest2,
            'Int3'   => $Interest3,
        ];
    } else if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
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

        if ($MyUser->have_posts()) {
            $MyUser->the_post();
            $CurrentUser = get_the_ID();

            $CurrentSuscription = get_page_by_title(get_post_meta($CurrentUser, 'user_email', true), OBJECT, 'kui_suscription');
            if ($CurrentSuscription) {
                $Email = get_the_title($CurrentSuscription->ID);
                wp_delete_post($CurrentSuscription->ID);

                return [
                    'Email'  => $Email,
                    'Action' => 'DELETE',
                ];
            } else {
                return new WP_Error('suscription_not_found', 'No se ha encontrado la suscripción', ['sso_ticket' => $SSOticket]);
            }
        } else {
            $ReferenceCode = sanitize_text_field($Data['subuuid']);
            if (!$ReferenceCode) {
                return new WP_Error('suscription_not_provided', 'Se requiere una referencia');
            }
            $SuscriptionSearch = new WP_Query([
                'post_type'  => ['kui_suscription'],
                'meta_query' => [
                    [
                        'key'   => 'reference',
                        'value' => $ReferenceCode,
                    ],
                ],
            ]);
            if (!$SuscriptionSearch->have_posts()) {
                return new WP_Error('suscription_not_found', 'No se ha encontrado la suscripción', ['reference' => $ReferenceCode]);
            }

            $SuscriptionSearch->the_post();
            $Email = get_the_title();
            wp_delete_post(get_the_ID());

            return [
                'Email'  => $Email,
                'Action' => 'DELETE',
            ];
        }
    } else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $SubscriptionUUID = $Data['subuuid'];
        $SubscriptionUUID = sanitize_text_field($SubscriptionUUID);
        if (!$SubscriptionUUID) {
            return new WP_Error('subscription_uuid_not_specified', 'No se ha especificado el UUID de suscripción.');
        }

        $SubscriptionSearch = new WP_Query([
            'post_type'  => ['kui_suscription'],
            'meta_query' => [
                [
                    'key'   => 'reference',
                    'value' => $SubscriptionUUID,
                ],
            ],
        ]);

        if ($SubscriptionSearch->have_posts()) {
            $SubscriptionSearch->the_post();

            return [
                'Email' => get_the_title(),
                'Int1'  => (bool) get_post_meta(get_the_ID(), 'interest_1', true),
                'Int2'  => (bool) get_post_meta(get_the_ID(), 'interest_2', true),
                'Int3'  => (bool) get_post_meta(get_the_ID(), 'interest_3', true),
            ];
        } else {
            return new WP_Error('subscription_not_found', 'Suscripción no encontrada.');
        }
    }

    return [];
}

add_action('rest_api_init', function () {
    register_rest_route('kui', 'subscription',
        [
            'methods'  => 'POST,DELETE,GET',
            'callback' => 'KUIrest_Suscription',
        ]
    );
});
