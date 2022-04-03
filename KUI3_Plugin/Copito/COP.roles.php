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

if(preg_match('/^\/roleset (.*) (.*)$/is',$Message_Text,$Matches))
{
    Copito_Message_Delete($Telegram_Chat,$Message_ID);
    $Command_UserToBan = $Matches[2];
    $Command_UserSet = $Matches[1];

    if($Role_Is_Moderator_MemCtl)
    {
        if(Copito_Member_Query2ID($Command_UserToBan))
        {
            $Command_Subject_ID = Copito_Member_Query2ID($Command_UserToBan);
            $Command_Subject_WP_ID = telegram_getid(Copito_Member_Query2ID($Matches[2]));
            $Command_Subject_Name = get_post_meta($Command_Subject_WP_ID,'telegram_first_name',true);
            $Command_Subject_Last = get_post_meta($Command_Subject_WP_ID,'telegram_last_name',true);

            $Subject_Status = Copito_Member_Status($Command_Subject_ID);
            if($Subject_Status &&
                (
                    (isset($Subject_Status['Is_MemCtl']) && $Subject_Status['Is_MemCtl'])
                    || ($Role_Is_Moderator_Junior && isset($Subject_Status['Is_Senior']) && $Subject_Status['Is_Senior'])
                ) && $Message_User_ID != COPITO_OWNER
            )
            {
                telegram_sendmessage($Telegram_Chat,"📜 *RoleCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nPermiso denegado - Privilegios insuficientes.");
            }
            else
            {
                switch ($Command_UserSet)
                {
                    case 'user':
                        Copito_Member_Moderation($Command_Subject_ID);
                        Copito_Member_Status($Command_Subject_ID,true);
                        telegram_sendmessage($Telegram_Chat,"📜 *RoleCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nCambiar roles de usuario.\n\n*Usuario:* [$Command_Subject_Name $Command_Subject_Last](tg://user?id=$Command_Subject_ID)\n*ROL:* Usuario.");
                        break;
                    case 'junior':
                        Copito_Member_Moderation($Command_Subject_ID,'JUNIOR');
                        Copito_Member_Status($Command_Subject_ID,true);
                        telegram_sendmessage($Telegram_Chat,"📜 *RoleCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nCambiar roles de usuario\n\n*Usuario:* [$Command_Subject_Name $Command_Subject_Last](tg://user?id=$Command_Subject_ID)\n*ROL:* Moderador Junior.");
                        break;
                    case 'senior':
                        Copito_Member_Moderation($Command_Subject_ID,'SENIOR');
                        Copito_Member_Status($Command_Subject_ID,true);
                        telegram_sendmessage($Telegram_Chat,"📜 *RoleCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nCambiar roles de usuario\n\n*Usuario:* [$Command_Subject_Name $Command_Subject_Last](tg://user?id=$Command_Subject_ID)\n*ROL:* Moderador Senior.");
                        break;
                    case 'mem':
                        Copito_Member_Moderation($Command_Subject_ID,'MEM');
                        Copito_Member_Status($Command_Subject_ID,true);
                        telegram_sendmessage($Telegram_Chat,"📜 *RoleCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nCambiar roles de usuario\n\n*Usuario:* [$Command_Subject_Name $Command_Subject_Last](tg://user?id=$Command_Subject_ID)\n*ROL:* Administrador Mem.");
                        break;
                    default:
                        telegram_sendmessage($Telegram_Chat,"📜 *RoleCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nSe esperaba 'user', 'junior', 'senior' o 'mem' como primer argumento.");
                        break;
                }
            }
        }
        else
        {
            telegram_sendmessage($Telegram_Chat,"📜 *RoleCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nUsuario no encontrado.");
        }
    }
    else
    {
        telegram_sendmessage($Telegram_Chat,"📜 *RoleCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nPermiso denegado - Requiere Mod. MemCtl o superior.");
    }
}
elseif(preg_match('/^\/roleset (.*)$/is',$Message_Text,$Matches))
{
    Copito_Message_Delete($Telegram_Chat,$Message_ID);
    $Command_UserToBan = $Matches[1];

    if($Role_Is_Moderator_MemCtl)
    {
        if(Copito_Member_Query2ID($Command_UserToBan))
        {
            $Command_Subject_ID = Copito_Member_Query2ID($Command_UserToBan);
            $Command_Subject_WP_ID = telegram_getid(Copito_Member_Query2ID($Matches[2]));
            $Command_Subject_Name = get_post_meta($Command_Subject_WP_ID,'telegram_first_name',true);
            $Command_Subject_Last = get_post_meta($Command_Subject_WP_ID,'telegram_last_name',true);

            Copito_Member_Status($Command_Subject_ID,true);
            telegram_sendmessage($Telegram_Chat,"📜 *RoleCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nSe han sincronizado los permisos de Telegram con la base de datos.\n\n*Usuario:* [$Command_Subject_Name $Command_Subject_Last](tg://user?id=$Command_Subject_ID)\n*Acción:* FLUSH ROLES.");
        }
        else
        {
            telegram_sendmessage($Telegram_Chat,"📜 *RoleCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nUsuario no encontrado.");
        }
    }
    else
    {
        telegram_sendmessage($Telegram_Chat,"📜 *RoleCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nPermiso denegado - Requiere Mod. MemCtl o superior.");
    }
}
elseif(preg_match('/^\/roleset(@.*)?$/is',$Message_Text,$Matches))
{
    $Text = [];
    $Text[] = "📜 *RoleCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))";
    $Text[] = "- Comandos para gestionar los permisos de usuario.";
    $Text[] = "/roleset @...\nRefrescar caché de permisos del usuario.";
    $Text[] = "/roleset user @...\nCambia los permisos a *Usuario*";
    $Text[] = "/roleset junior @...\nCambia los permisos a *Moderador Junior*";
    $Text[] = "/roleset senior @...\nCambia los permisos a *Moderador Senior*";
    $Text[] = "/roleset mem @...\nCambia los permisos a *Administrador Mem*";
    telegram_sendmessage($Telegram_Chat,implode("\n\n",$Text));
}