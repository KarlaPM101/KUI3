<?php
/** @var array      $Message_Data */
/** @var int        $Message_ID */
/** @var int        $Message_WP_ID */
/** @var int        $Telegram_Chat */
/** @var string     $Message_Text */
/** @var int        $Message_User_ID */
/** @var string     $Message_User_Name_First */
/** @var string     $Message_User_Name_Last */
/** @var bool       $Is_File */
/** @var bool       $Is_Image */
/** @var bool       $Is_Reply */
/** @var bool       $Role_Is_Moderator_Junior */
/** @var bool       $Role_Is_Moderator_Senior */
/** @var bool       $Role_Is_Moderator_MemCtl */

if ( ! defined( 'ABSPATH' ) ) exit;

$Captcha_Config_Enabled = get_option('wp_copito_captcha_config_enabled');

// GENERADOR DE CAPTCHA
$Captcha_Status = get_post_meta($Message_WP_ID,'copito_captcha_status',true)?true:false;
if($Captcha_Config_Enabled && $Captcha_Status && $Telegram_Chat==COPITO_GROUP_ID)
{
    // CONFIGURACIÓN
    $Captcha_Config_Time  = get_option('wp_copito_captcha_config_time')?:1800;
    $Captcha_Config_Tries = get_option('wp_copito_captcha_config_tries')?:3;

    // GENERAR CAPTCHA NUEVO Y ENVIAR
    $Captcha_Pharse = get_post_meta($Message_WP_ID,'copito_captcha_pharse',true);
    $Captcha_Tries  = get_post_meta($Message_WP_ID,'copito_captcha_count',true)?:0;
    $Captcha_Msgs   = get_post_meta($Message_WP_ID,'copito_captcha_msgs',true);
    $Captcha_Time   = get_post_meta($Message_WP_ID,'copito_captcha_timestamp',true);
    if(!$Captcha_Pharse)
    {
        Copito_Message_Delete($Telegram_Chat,$Message_ID);

        $Captcha_Pharse = Copito_Captcha_Generate();
        //$Img_Id = telegram_sendphoto($Telegram_Chat,'',COPITO_PUBLIC."/captcha/$Captcha_Pharse.png");
        $Send_Id = Copito_Message_Image(
            $Telegram_Chat,
            COPITO_PUBLIC."/captcha/$Captcha_Pharse.png",
            "Hola [$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID), escribe el texto del CAPTCHA para poder ingresar en el grupo. *Tienes ".round($Captcha_Config_Time/60)." min. para resolver*",
            [
                "inline_keyboard" => [
                    [
                        [
                            "text" => "Cambiar CAPTCHA",
                            "callback_data" => "Captcha_Refresh"
                        ]
                    ]
                ]
            ]
        );
        update_post_meta($Message_WP_ID, 'copito_captcha_pharse',$Captcha_Pharse);
        update_post_meta($Message_WP_ID, 'copito_captcha_count',0);
        //update_post_meta($Message_WP_ID, 'copito_captcha_first_img',$Img_Id);
        update_post_meta($Message_WP_ID, 'copito_captcha_first_msg',$Send_Id);
        $Captcha_Msgs .= ";$Send_Id";
        //$Captcha_Msgs .= ";$Img_Id";
        update_post_meta($Message_WP_ID,'copito_captcha_msgs',$Captcha_Msgs);
        update_post_meta($Message_WP_ID,'copito_captcha_timestamp',time());
    }
    else
    {
        // USUARIO BLOQUEADO :: TIEMPO EXPIRADO
        if($Captcha_Time+$Captcha_Config_Time<time())
        {
            $Send_Id = telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nCAPTCHA expirado (Se ha acabado el tiempo). Usuario BLOQUEADO. Contacta con un moderador para quitar restricción.");
            Copito_Member_Mute($Message_User_ID,0);
            $Messages = explode(';',get_post_meta($Message_WP_ID,'copito_captcha_msgs',true));
            if($Messages)
            {
                foreach ($Messages as $i)
                {
                    if(!$i) continue;
                    Copito_Message_Delete($Telegram_Chat,$i);
                }
                delete_post_meta($Message_WP_ID,'copito_captcha_msgs');
            }
        }
        // USUARIO BLOQUEADO :: INTENTOS FALLIDOS
        elseif($Captcha_Tries==$Captcha_Config_Tries-1)
        {
            telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nCAPTCHA expirado (Se han acabado los intentos). Usuario BLOQUEADO. Contacta con un moderador para quitar restricción.");
            Copito_Member_Mute($Message_User_ID,0);
            $Messages = explode(';',get_post_meta($Message_WP_ID,'copito_captcha_msgs',true));
            if($Messages)
            {
                foreach ($Messages as $i)
                {
                    if(!$i) continue;
                    Copito_Message_Delete($Telegram_Chat,$i);
                }
                delete_post_meta($Message_WP_ID,'copito_captcha_msgs');
            }
        }
        // USUARIO NO BLOQUEADO :: COMPROBAR CAPTCHA
        else
        {
            if(strtoupper($Message_Text)==strtoupper($Captcha_Pharse))
            {
                Copito_Message_Delete($Telegram_Chat,$Message_ID);

                Copito_Message_Delete($Telegram_Chat,get_option('copito_lastwelcome'));

                $texttosend = [];
                $texttosend[] = "Bienvenido al grupo, [$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID) 💙";
                $texttosend[] = '';
                $texttosend[] = 'Esto es como una cafetería para todos los suscriptores ☕️. Podéis hablar sobre los temas expuestos en los vídeos del canal y de temas relacionados con software, Windows y GNU/Linux 💻.';
                $WelcomeID = telegram_sendmessage($Telegram_Chat,implode("\n",$texttosend),[
                    "inline_keyboard" => [
                        [
                            [
                                "text" => "Normas",
                                "url" => "https://karlaperezyt.com/telegram/normas/"
                            ],
                            [
                                "text" => "Comandos",
                                "url" => "https://karlaperezyt.com/telegram/comandos/"
                            ]
                        ],
                        [
                            [
                                "text" => "Política de Privacidad",
                                "url" => "https://karlaperezyt.com/telegram/privacidad/"
                            ]
                        ]
                    ]
                ],true);

                update_option( 'copito_lastwelcome',$WelcomeID);

                $Messages = explode(';',get_post_meta($Message_WP_ID,'copito_captcha_msgs',true));
                if($Messages)
                {
                    foreach ($Messages as $i)
                    {
                        if(!$i) continue;
                        Copito_Message_Delete($Telegram_Chat,$i);
                    }
                    delete_post_meta($Message_User_ID,'copito_captcha_msgs');
                }

                delete_post_meta($Message_WP_ID, 'copito_captcha_status');
                delete_post_meta($Message_WP_ID, 'copito_captcha_first_msg');
                delete_post_meta($Message_WP_ID, 'copito_captcha_first_img');
                delete_post_meta($Message_WP_ID, 'copito_captcha_timestamp');
                delete_post_meta($Message_WP_ID, 'copito_captcha_count');
                delete_post_meta($Message_WP_ID, 'copito_captcha_pharse');
            }
            else
            {
                Copito_Message_Delete($Telegram_Chat,$Message_ID);
                $Send_Id = telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nCAPTCHA incorrecto. Vuelve a intentarlo.");
                update_post_meta($Message_WP_ID, 'copito_captcha_count',$Captcha_Tries+1);
                $Captcha_Msgs .= ";$Send_Id";
                update_post_meta( $Message_WP_ID,'copito_captcha_msgs',$Captcha_Msgs);
            }
        }
    }
}

// COMANDO PARA QUITAR CAPTCHA
if(preg_match('/^\/captcha resolve$/is',$Message_Text,$Matches))
{
    if($Role_Is_Moderator_Junior)
    {
        if(isset($Message_Data['message']['reply_to_message']['from']['id']))
        {
            $Command_Subject = $Message_Data['message']['reply_to_message']['from']['id'];
            $Command_Subject_ID = Copito_Member_Query2ID($Command_Subject);
            $Command_Subject_WP_ID = telegram_getid($Command_Subject_ID);
            if($Command_Subject_ID)
            {
                $Command_Subject_Name = $Message_Data['message']['reply_to_message']['from']['first_name'];
                $Command_Subject_Last = $Message_Data['message']['reply_to_message']['from']['last_name'];
                $Command_Subject_ID = $Message_Data['message']['reply_to_message']['from']['id'];

                Copito_Member_Mute($Command_Subject_ID,0,true);
                delete_post_meta($Command_Subject_WP_ID, 'copito_captcha_status');
                telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nUsuario [$Command_Subject_Name $Command_Subject_Last](tg://user?id=$Command_Subject_ID) desbloqueado.");
            }
            else
            {
                telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nError. Usuario no encontrado.");
            }
        }
        else
        {
            telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nError. Especificar usuario o responder mensaje.");
        }
    }
    else
    {
        telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nError. Permiso denegado.");
    }
}
if(preg_match('/^\/captcha resolve (.*)$/is',$Message_Text,$Matches))
{
    if($Role_Is_Moderator_Junior)
    {
        if(isset($Matches[1]) && Copito_Member_Query2ID($Matches[1]) && telegram_getid(Copito_Member_Query2ID($Matches[1])))
        {
            $Command_Subject_WP_ID = telegram_getid(Copito_Member_Query2ID($Matches[1]));
            $Command_Subject_Name = get_post_meta($Command_Subject_WP_ID,'telegram_first_name',true);
            $Command_Subject_Last = get_post_meta($Command_Subject_WP_ID,'telegram_last_name',true);
            $Command_Subject_ID = get_the_title($Command_Subject_WP_ID);

            Copito_Member_Mute($Command_Subject_ID,0,true);
            delete_post_meta($Command_Subject_WP_ID, 'copito_captcha_status');
            telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nUsuario [$Command_Subject_Name $Command_Subject_Last](tg://user?id=$Command_Subject_ID) desbloqueado.");

        }
        else
        {
            telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nError. Usuario no encontrado.");
        }
    }
    else
    {
        telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nError. Permiso denegado.");
    }

}

// COMANDO PARA ESTABLECER CAPTCHA
if(preg_match('/^\/captcha refresh$/is',$Message_Text,$Matches))
{
    if($Role_Is_Moderator_Junior)
    {
        if(isset($Message_Data['message']['reply_to_message']['from']['id']))
        {
            $Command_Subject = $Message_Data['message']['reply_to_message']['from']['id'];
            $Command_Subject_ID = Copito_Member_Query2ID($Command_Subject);
            $Command_Subject_WP_ID = telegram_getid($Command_Subject_ID);
            if($Command_Subject_ID)
            {
                $Command_Subject_Name = $Message_Data['message']['reply_to_message']['from']['first_name'];
                $Command_Subject_Last = $Message_Data['message']['reply_to_message']['from']['last_name'];
                $Command_Subject_ID = $Message_Data['message']['reply_to_message']['from']['id'];

                update_post_meta($Command_Subject_WP_ID, 'copito_captcha_status',1);
                update_post_meta($Command_Subject_WP_ID, 'copito_captcha_pharse',false);
                update_post_meta($Command_Subject_WP_ID, 'copito_captcha_count',0);
                update_post_meta($Command_Subject_WP_ID, 'copito_captcha_msgs',0);
                update_post_meta($Command_Subject_WP_ID, 'copito_captcha_timestamp',0);
                telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nSe asigna un CAPTCHA nuevo a [$Command_Subject_Name $Command_Subject_Last](tg://user?id=$Command_Subject_ID).");
            }
            else
            {
                telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nError. Usuario no encontrado.");
            }
        }
        else
        {
            telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nError. Especificar usuario o responder mensaje.");
        }
    }
    else
    {
        telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nError. Permiso denegado.");
    }

}
if(preg_match('/^\/captcha refresh (.*)$/is',$Message_Text,$Matches))
{
    if($Role_Is_Moderator_Junior)
    {
        if(isset($Matches[1]) && Copito_Member_Query2ID($Matches[1]) && telegram_getid(Copito_Member_Query2ID($Matches[1])))
        {
            $Command_Subject_WP_ID = telegram_getid(Copito_Member_Query2ID($Matches[1]));
            $Command_Subject_Name = get_post_meta($Command_Subject_WP_ID,'telegram_first_name',true);
            $Command_Subject_Last = get_post_meta($Command_Subject_WP_ID,'telegram_last_name',true);
            $Command_Subject_ID = get_the_title($Command_Subject_WP_ID);

            update_post_meta($Command_Subject_WP_ID, 'copito_captcha_status',1);
            update_post_meta($Command_Subject_WP_ID, 'copito_captcha_pharse',false);
            update_post_meta($Command_Subject_WP_ID, 'copito_captcha_count',0);
            update_post_meta($Command_Subject_WP_ID, 'copito_captcha_msgs',0);
            update_post_meta($Command_Subject_WP_ID, 'copito_captcha_timestamp',0);
            telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nSe asigna un CAPTCHA nuevo a [$Command_Subject_Name $Command_Subject_Last](tg://user?id=$Command_Subject_ID).");
        }
        else
        {
            telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nError. Usuario no encontrado.");
        }
    }
    else
    {
        telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nError. Permiso denegado.");
    }
}

// COMANDO PARA ACTIVAR/DESACTIVAR CAPTCHA
if(preg_match('/^\/captcha on$/is',$Message_Text,$Matches))
{
    if($Role_Is_Moderator_MemCtl)
    {
        update_option('wp_copito_captcha_config_enabled',1);
        telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nSe ha actualizado la configuración del Captcha\nEstado: *ACTIVADO*");
    }
    else
    {
        telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nError. Permiso denegado - Requiere Mod. MemCtl.");
    }
}
if(preg_match('/^\/captcha off$/is',$Message_Text,$Matches))
{
    if($Role_Is_Moderator_MemCtl)
    {
        delete_option('wp_copito_captcha_config_enabled');
        telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nSe ha actualizado la configuración del Captcha\nEstado: *DESACTIVADO*");
    }
    else
    {
        telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nError. Permiso denegado - Requiere Mod. MemCtl.");
    }
}

// COMANDO PARA ESTABLECER INTENTOS
if(preg_match('/^\/captcha attempts (.*)$/is',$Message_Text,$Matches))
{
    if($Role_Is_Moderator_MemCtl)
    {
        if(isset($Matches[1]))
        {
            if(!preg_match('/([0-9]{1,2})/',$Matches[1]))
            {
                telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nNo se ha especificado ningún número válido.\nEjemplo de uso: `/captcha attempts 5`");
            }
            else
            {
                update_option('wp_copito_captcha_config_tries',$Matches[1]);
                telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nSe ha actualizado la configuración del Captcha\nNúmero de intentos: *$Matches[1]*");
            }
        }
        else
        {
            telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nNo se ha especificado ningún número.\nEjemplo de uso: `/captcha attempts 5`");
        }
    }
    else
    {
        telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nPermiso denegado - Requiere Mod. MemCtl.");
    }
}
elseif(preg_match('/^\/captcha attempts$/is',$Message_Text,$Matches))
{
    if($Role_Is_Moderator_MemCtl)
    {
        telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nNo se ha especificado ningún número.\n_Ejemplo de uso:_ `/captcha attempts 5`");
    }
    else
    {
        telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nPermiso denegado - Requiere Mod. MemCtl.");
    }
}

// COMANDO PARA ESTABLECER TIEMPO
if(preg_match('/^\/captcha time (.*)$/is',$Message_Text,$Matches))
{
    if($Role_Is_Moderator_MemCtl)
    {
        if(isset($Matches[1]))
        {
            if(!preg_match('/([0-9]{1,6})([mhd]{1})/',$Matches[1],$Set_Config))
            {
                telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nNo se ha especificado ningún número de tiempo válido.\nEjemplo de uso: `/captcha time 30m`");
            }
            else
            {
                if($Set_Config[2]=='d')
                {
                    update_option('wp_copito_captcha_config_time',$Set_Config[1]*3600*24);
                    telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nSe ha actualizado la configuración del Captcha\nTiempo máximo de respuesta: *".($Set_Config[1])." días*");
                }
                elseif($Set_Config[2]=='h')
                {
                    update_option('wp_copito_captcha_config_time',$Set_Config[1]*3600);
                    telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nSe ha actualizado la configuración del Captcha\nTiempo máximo de respuesta: *".($Set_Config[1])." horas*");
                }
                else
                {
                    update_option('wp_copito_captcha_config_time',$Set_Config[1]*60);
                    telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nSe ha actualizado la configuración del Captcha\nTiempo máximo de respuesta: *".($Set_Config[1])." minutos*");
                }
            }
        }
        else
        {
            telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nNo se ha especificado ningún número de tiempo.\n_Ejemplo de uso:_ `/captcha time 30m`");
        }
    }
    else
    {
        telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nPermiso denegado - Requiere Mod. MemCtl.");
    }
}
elseif(preg_match('/^\/captcha time$/is',$Message_Text,$Matches))
{
    if($Role_Is_Moderator_MemCtl)
    {
        telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nNo se ha especificado ningún número de tiempo.\n_Ejemplo de uso:_ `/captcha time 30m`");
    }
    else
    {
        telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nPermiso denegado - Requiere Mod. MemCtl.");
    }
}

if(preg_match('/^\/captcha(@.*)?$/is',$Message_Text,$Matches))
{
    if($Role_Is_Moderator_Junior)
    {
        $Text = [];
        $Text[] = "📛 *CaptchaCTL*";
        $Text[] = "- Comandos para gestionar el captcha que deberá de resolver cualquier usuario que ingrese por primera vez en el grupo.";
        $Text[] = "/captcha on\nActiva el Captcha.";
        $Text[] = "/captcha off\nDesactiva el Captcha.";
        $Text[] = "/captcha attempts X\nEstablecer el tnúmero de intentos que tendrá el usuario para resolver el Captcha. Por defecto: 3 intentos.";
        $Text[] = "/captcha time X\nEstablecer el tiempo máximo con el cuál cada usuario dispondrá para resolver el Captcha. Por defecto: 30 minutos. X es el número, acompañado de: m - minuto, d - días, h - horas; Ejemplo: 5m";
        $Text[] = "/captcha resolve @...\nForzar validación del captcha para el usuario mencionado.";
        $Text[] = "/captcha refresh @...\nForzar nueva generación de imagen del captcha para el usuario mencionado. Al ejecutar este comando, se reinicia el contador de intentos fallidos.";
        telegram_sendmessage($Telegram_Chat,implode("\n\n",$Text));
    }
    else
    {
        telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nError. Permiso denegado.");
    }
}