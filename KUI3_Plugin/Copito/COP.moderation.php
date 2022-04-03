<?php
/*
 * PLUGIN DE COPITO : Comandos de Moderación. (Versión 2.2)
 * Fecha de actualización: 01/09/2021 Karla Pérez Maldonado.
 *
 * Copito posee una serie de comandos para gestionar las expulsiones y muteos dentro del grupo (en la versión 1.0 estos
 * comandos formaban parte del MEM [Mod's Extended Moderation]). Cada comando asi como sus diferentes argumentos están
 * documentados mediante el uso del comando `/man`. A continuación, se muestra una lista con cada uno de ellos (y sus
 * respectivos comandos de ayuda).
 *
 *       *+* *Expulsión*
 *       Expulsar usuario del grupo, sea permanente o durante un periodo de tiempo determinado.
 *       (Véase `/man ban`)
 *       `/ban`
 *
 *       *+* *Mutear*
 *       Mutear usuario del grupo, sea permanente o durante un periodo de tiempo determinado.
 *       (Véase `/man mute`)
 *       `/mute`
 *
 *       *+* *Retirar expulsión*
 *       Retirar expulsión de un usuario previamente expulsado.
 *       (Véase `/man unban`)
 *       `/unban`
 *
 *       *+* *Retirar muteo*
 *       Retirar muteo de un usuario previamente muteado.
 *       (Véase `/man unmute`)
 *       `/unmute`
 *
 *       *+* *Kickear*
 *       Echa del grupo a un usuario (expulsión de 5 minutos)
 *       (Véase `/man kick`)
 *       `/kick`
 *
 *       *+* *Strike*
 *       Aplica un Strike a un usuario. A los 3 o 10, el usuario es expulsado PERMANENTEMENTE. También es posible
 *       consultar, resetear o mostrar una lista de los usuarios con strikes.
 *       (Véase `/man strike`)
 *       `/strike`
 *
 *       *+* *Borrar mensaje*
 *       Borra mensajes en el chat del grupo.
 *       (Véase `/man delete`)
 *       `/delete`
 *
 * Los comandos anteriores tienen definidos alias (p.ej. `/delete` acepta `/remove` como alias). Algunos comandos
 * también o frecen equivalencias con los comandos de la versión 1.0 (p. ej. `/memctl ban`). Para más información
 * consultar documentación.
 */

/** @var array      $Message_Data */
/** @var int        $Message_ID */
/** @var int        $Message_WP_ID */
/** @var int        $Telegram_Chat */
/** @var string     $Message_Text */
/** @var int        $Message_User_ID */
/** @var string     $Message_User_Name_First */
/** @var string     $Message_User_Name_Last */
/** @var string     $Rendered_User */
/** @var bool       $Is_File */
/** @var bool       $Is_Image */
/** @var bool       $Is_Reply */
/** @var int        $Reply_ID */
/** @var bool       $Role_Is_Moderator_Junior */
/** @var bool       $Role_Is_Moderator_Senior */
/** @var bool       $Role_Is_Moderator_MemCtl */

/*
 * DEFINICIÓN DE LAS FUNCIONES UTILIZADAS POR EL PLUGIN.
 *
 * Argumentos de las funciones:
 *      'Telegram_Chat_ID'      => 0,
 *      'Message_ID'            => 0,
 *      'Query_UserStr'         => "",
 *      'Query_TimeStr'         => "",
 *      'Query_StrikeStr'       => "",
 *      'Role_IsJunior'         => false,
 *      'Role_IsSenior'         => false,
 *      'User_Rendered'         => "",
 *      'Query_Reply'           => "",
 */
function Copito_Plugin_Modaration_Ban($Arguments=[]){

    $Arguments = array_merge([
        'Telegram_Chat_ID'      => 0,
        'Message_ID'            => 0,
        'Message_Text'          => "",
        'Query_UserStr'         => "",
        'Query_TimeStr'         => "",
        'Role_IsSenior'         => false,
        'User_Rendered'         => "",
        'Query_Reply'           => "",
    ],$Arguments);

    $Telegram_Chat              = $Arguments['Telegram_Chat_ID'];
    $Message_ID                 = $Arguments['Message_ID'];
    $Message_Text               = $Arguments['Message_Text'];
    $Query_UserStr              = $Arguments['Query_UserStr'];
    $Query_TimeStr              = $Arguments['Query_TimeStr'];
    $Role_Is_Moderator_Senior   = $Arguments['Role_IsSenior'];
    $Rendered_User              = $Arguments['User_Rendered'];

    $Arguments_UserQuery        = $Query_UserStr;
    $Arguments_Time             = $Query_TimeStr;
    $UserQuery_Data             = Copito_Subjects_User($Arguments_UserQuery);

    Copito_Message_Delete($Telegram_Chat,$Message_ID);

    if($Role_Is_Moderator_Senior){
        if($UserQuery_Data){
            if(Copito_RoleSet_Check_Senior($UserQuery_Data['Telegram_ID'])){
                if(!preg_match('/([0-9]{1,6})([mhd]{1})/',$Arguments_Time,$Set_Config)){
                    if($Arguments_Time == '-1' ){
                        Copito_Member_Ban($UserQuery_Data['Telegram_ID']);
                        Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Ban_Ok,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered']));
                    }
                    else {
                        Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Err_Time,$Rendered_User,$Message_Text));
                    }
                }
                else {
                    switch ($Set_Config[2]){
                        case 'd':
                            Copito_Member_Ban($UserQuery_Data['Telegram_ID'],$Set_Config[1]*3600*24);
                            Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Ban_Ok_Timed_Days,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered'],$Set_Config[1]));
                            break;
                        case 'h':
                            Copito_Member_Ban($UserQuery_Data['Telegram_ID'],$Set_Config[1]*3600);
                            Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Ban_Ok_Timed_Hour,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered'],$Set_Config[1]));
                            break;
                        default:
                            Copito_Member_Ban($UserQuery_Data['Telegram_ID'],$Set_Config[1]*60);
                            Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Ban_Ok_Timed_Min,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered'],$Set_Config[1]));
                            break;
                    }
                }
            }
            else {
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotRoleSet,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered']));
            }
        }
        else {
            if($Arguments_UserQuery){
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotFound,$Rendered_User,$Message_Text,$Arguments_UserQuery));
            }
            else {
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Err_Reply,$Rendered_User,$Message_Text));
            }
        }
    }
    else {
        Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotRoles_Senior,$Rendered_User,$Message_Text));
    }
}
function Copito_Plugin_Modaration_UnBan($Arguments=[]){

    $Arguments = array_merge([
        'Telegram_Chat_ID'      => 0,
        'Message_ID'            => 0,
        'Message_Text'          => "",
        'Query_UserStr'         => "",
        'Role_IsSenior'         => false,
        'User_Rendered'         => "",
        'Query_Reply'           => "",
    ],$Arguments);

    $Telegram_Chat              = $Arguments['Telegram_Chat_ID'];
    $Message_ID                 = $Arguments['Message_ID'];
    $Message_Text               = $Arguments['Message_Text'];
    $Query_UserStr              = $Arguments['Query_UserStr'];
    $Role_Is_Moderator_Senior   = $Arguments['Role_IsSenior'];
    $Rendered_User              = $Arguments['User_Rendered'];

    $Arguments_UserQuery        = $Query_UserStr;
    $UserQuery_Data             = Copito_Subjects_User($Arguments_UserQuery);

    Copito_Message_Delete($Telegram_Chat,$Message_ID);

    if($Role_Is_Moderator_Senior){
        if($UserQuery_Data){
            if(Copito_RoleSet_Check_Senior($UserQuery_Data['Telegram_ID'])){
                Copito_Member_Unban($UserQuery_Data['Telegram_ID']);
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Unban_Ok,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered']));
            }
            else {
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotRoleSet,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered']));
            }
        }
        else {
            if($Arguments_UserQuery){
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotFound,$Rendered_User,$Message_Text,$Arguments_UserQuery));
            }
            else {
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Err_Reply,$Rendered_User,$Message_Text));
            }
        }
    }
    else {
        Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotRoles_Senior,$Rendered_User,$Message_Text));
    }
}
function Copito_Plugin_Modaration_Kick($Arguments=[]){

    $Arguments = array_merge([
        'Telegram_Chat_ID'      => 0,
        'Message_ID'            => 0,
        'Message_Text'          => "",
        'Query_UserStr'         => "",
        'Role_IsSenior'         => false,
        'User_Rendered'         => "",
        'Query_Reply'           => "",
    ],$Arguments);

    $Telegram_Chat              = $Arguments['Telegram_Chat_ID'];
    $Message_ID                 = $Arguments['Message_ID'];
    $Message_Text               = $Arguments['Message_Text'];
    $Query_UserStr              = $Arguments['Query_UserStr'];
    $Role_Is_Moderator_Senior   = $Arguments['Role_IsSenior'];
    $Rendered_User              = $Arguments['User_Rendered'];

    $Arguments_UserQuery        = $Query_UserStr;
    $UserQuery_Data             = Copito_Subjects_User($Arguments_UserQuery);

    Copito_Message_Delete($Telegram_Chat,$Message_ID);

    if($Role_Is_Moderator_Senior){
        if($UserQuery_Data){
            if(Copito_RoleSet_Check_Senior($UserQuery_Data['Telegram_ID'])){
                Copito_Member_Kick($UserQuery_Data['Telegram_ID']);
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Kick_Ok,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered']));
            }
            else {
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotRoleSet,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered']));
            }
        }
        else {
            if($Arguments_UserQuery){
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotFound,$Rendered_User,$Message_Text,$Arguments_UserQuery));
            }
            else {
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Err_Reply,$Rendered_User,$Message_Text));
            }
        }
    }
    else {
        Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotRoles_Senior,$Rendered_User,$Message_Text));
    }
}
function Copito_Plugin_Modaration_Mute($Arguments=[]){

    $Arguments = array_merge([
        'Telegram_Chat_ID'      => 0,
        'Message_ID'            => 0,
        'Message_Text'          => "",
        'Query_UserStr'         => "",
        'Query_TimeStr'         => "",
        'Role_IsJunior'         => false,
        'User_Rendered'         => "",
        'Query_Reply'           => "",
    ],$Arguments);

    $Telegram_Chat              = $Arguments['Telegram_Chat_ID'];
    $Message_ID                 = $Arguments['Message_ID'];
    $Message_Text               = $Arguments['Message_Text'];
    $Query_UserStr              = $Arguments['Query_UserStr'];
    $Query_TimeStr              = $Arguments['Query_TimeStr'];
    $Role_Is_Moderator_Junior   = $Arguments['Role_IsJunior'];
    $Rendered_User              = $Arguments['User_Rendered'];

    $Arguments_UserQuery        = $Query_UserStr;
    $Arguments_Time             = $Query_TimeStr;
    $UserQuery_Data             = Copito_Subjects_User($Arguments_UserQuery);

    Copito_Message_Delete($Telegram_Chat,$Message_ID);

    if($Role_Is_Moderator_Junior){
        if($UserQuery_Data){
            if(Copito_RoleSet_Check_Junior($UserQuery_Data['Telegram_ID'])){
                if(!preg_match('/([0-9]{1,6})([mhd]{1})/',$Arguments_Time,$Set_Config)){
                    if($Arguments_Time == '-1' ){
                        Copito_Member_Mute($UserQuery_Data['Telegram_ID']);
                        Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Mute_Ok,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered']));
                    }
                    else {
                        Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Err_Time,$Rendered_User,$Message_Text));
                    }
                }
                else {
                    switch ($Set_Config[2]){
                        case 'd':
                            Copito_Member_Mute($UserQuery_Data['Telegram_ID'],$Set_Config[1]*3600*24);
                            Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Mute_Ok_Timed_Days,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered'],$Set_Config[1]));
                            break;
                        case 'h':
                            Copito_Member_Mute($UserQuery_Data['Telegram_ID'],$Set_Config[1]*3600);
                            Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Mute_Ok_Timed_Hour,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered'],$Set_Config[1]));
                            break;
                        default:
                            Copito_Member_Mute($UserQuery_Data['Telegram_ID'],$Set_Config[1]*60);
                            Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Mute_Ok_Timed_Min,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered'],$Set_Config[1]));
                            break;
                    }
                }
            }
            else {
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotRoleSet,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered']));
            }
        }
        else {
            if($Arguments_UserQuery){
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotFound,$Rendered_User,$Message_Text,$Arguments_UserQuery));
            }
            else {
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Err_Reply,$Rendered_User,$Message_Text));
            }
        }
    }
    else {
        Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotRoles_Junior,$Rendered_User,$Message_Text));
    }
}
function Copito_Plugin_Modaration_UnMute($Arguments=[]){

    $Arguments = array_merge([
        'Telegram_Chat_ID'      => 0,
        'Message_ID'            => 0,
        'Message_Text'          => "",
        'Query_UserStr'         => "",
        'Role_IsJunior'         => false,
        'User_Rendered'         => "",
        'Query_Reply'           => "",
    ],$Arguments);

    $Telegram_Chat              = $Arguments['Telegram_Chat_ID'];
    $Message_ID                 = $Arguments['Message_ID'];
    $Message_Text               = $Arguments['Message_Text'];
    $Query_UserStr              = $Arguments['Query_UserStr'];
    $Role_Is_Moderator_Junior   = $Arguments['Role_IsJunior'];
    $Rendered_User              = $Arguments['User_Rendered'];

    $Arguments_UserQuery        = $Query_UserStr;
    $UserQuery_Data             = Copito_Subjects_User($Arguments_UserQuery);

    Copito_Message_Delete($Telegram_Chat,$Message_ID);

    if($Role_Is_Moderator_Junior){
        if($UserQuery_Data){
            if(Copito_RoleSet_Check_Junior($UserQuery_Data['Telegram_ID'])){
                Copito_Member_Mute($UserQuery_Data['Telegram_ID'],0,true);

                $Flood_Count = (int)get_post_meta($UserQuery_Data['Wordpress_ID'],'floddy_count',true);

                if($Flood_Count>1){
                    Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_CHT_Floody_Reset,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered']));
                }
                else {
                    Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Unmute_Ok,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered']));
                }

                update_post_meta($UserQuery_Data['Wordpress_ID'],'floddy_count',1);
            }
            else {
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotRoleSet,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered']));
            }
        }
        else {
            if($Arguments_UserQuery){
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotFound,$Rendered_User,$Message_Text,$Arguments_UserQuery));
            }
            else {
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Err_Reply,$Rendered_User,$Message_Text));
            }
        }
    }
    else {
        Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotRoles_Junior,$Rendered_User,$Message_Text));
    }
}
function Copito_Plugin_Modaration_Strike($Arguments=[]){

    $Arguments = array_merge([
        'Telegram_Chat_ID'      => 0,
        'Message_ID'            => 0,
        'Message_Text'          => "",
        'Query_UserStr'         => "",
        'Query_StrikeStr'       => "",
        'Role_IsJunior'         => false,
        'User_Rendered'         => "",
        'Query_Reply'           => "",
    ],$Arguments);

    $Telegram_Chat              = $Arguments['Telegram_Chat_ID'];
    $Message_ID                 = $Arguments['Message_ID'];
    $Message_Text               = $Arguments['Message_Text'];
    $Query_UserStr              = $Arguments['Query_UserStr'];
    $Query_StrikeStr            = $Arguments['Query_StrikeStr'];
    $Role_Is_Moderator_Junior   = $Arguments['Role_IsJunior'];
    $Rendered_User              = $Arguments['User_Rendered'];

    $Arguments_UserQuery        = $Query_UserStr;
    $Arguments_Strikes          = $Query_StrikeStr;
    $UserQuery_Data             = Copito_Subjects_User($Arguments_UserQuery);
    $Arguments_Strikes_Max      = 3;

    Copito_Message_Delete($Telegram_Chat,$Message_ID);

    if($Role_Is_Moderator_Junior){
        if($UserQuery_Data){
            $Strike_Current = (int)get_post_meta($UserQuery_Data['Wordpress_ID'],'copito_warnings',true);

            if($Arguments_Strikes=='print'){
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Strike_Print,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered'],$Strike_Current));
            }
            elseif($Arguments_Strikes=='reset'){
                $Strike_Current = 0;
                update_post_meta($UserQuery_Data['Wordpress_ID'],'copito_warnings',"0");

                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Strike_Reset,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered'],$Strike_Current));
            }
            else {
                if(Copito_RoleSet_Check_Junior($UserQuery_Data['Telegram_ID'])){
                    if($Arguments_Strikes && $Arguments_Strikes!=='' && is_numeric($Arguments_Strikes) && $Arguments_Strikes > 0){
                        $Strike_Current = (int)$Arguments_Strikes;

                        update_post_meta($UserQuery_Data['Wordpress_ID'],'copito_warnings',(string)$Strike_Current);

                        if($Strike_Current < $Arguments_Strikes_Max) {
                            Copito_Message_Send($Telegram_Chat, sprintf(Copito_i18l_MOD_Strike_Chng, $Rendered_User, $Message_Text, $UserQuery_Data['Display_Rendered'], $Strike_Current));
                        }
                    }
                    else {
                        $Strike_Current++;

                        update_post_meta($UserQuery_Data['Wordpress_ID'],'copito_warnings',(string)$Strike_Current);

                        if($Strike_Current < $Arguments_Strikes_Max){
                            Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Strike_Ok,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered'],$Strike_Current));
                        }
                    }

                    if($Strike_Current >= $Arguments_Strikes_Max){
                        Copito_Member_Ban($UserQuery_Data['Telegram_ID']);
                        Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Strike_Ban,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered'],$Strike_Current));
                    }
                }
                else {
                    Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotRoleSet,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered']));
                }
            }
        }
        else {
            if($Arguments_UserQuery){
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotFound,$Rendered_User,$Message_Text,$Arguments_UserQuery));
            }
            else {
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Err_Reply,$Rendered_User,$Message_Text));
            }
        }
    }
    else {
        Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotRoles_Junior,$Rendered_User,$Message_Text));
    }
}


/*
 * EXPULSAR USUARIO
 *
 * Comando: /ban, /memctl ban, /expulsar
 */
if(preg_match('/^\/ban \?$/is',$Message_Text,$Matches)
    || preg_match('/^\/expulsar \?$/is',$Message_Text,$Matches)
    || preg_match('/^\/ban help$/is',$Message_Text,$Matches)
    || preg_match('/^\/expulsar help$/is',$Message_Text,$Matches)
    || preg_match('/^\/man ban$/is',$Message_Text,$Matches)
    || preg_match('/^\/man expulsar$/is',$Message_Text,$Matches)) {
    Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Help_Ban,$Rendered_User,$Message_Text));
}
elseif(preg_match('/^\/ban (.*) (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/memctl ban (.*) (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/expulsar (.*) (.*)$/is',$Message_Text,$Matches)) {
    Copito_Plugin_Modaration_Ban([
        'Telegram_Chat_ID'      => $Telegram_Chat,
        'Message_ID'            => $Message_ID,
        'Message_Text'          => $Message_Text,
        'Query_UserStr'         => $Matches[1],
        'Query_TimeStr'         => $Matches[2],
        'Role_IsSenior'         => $Role_Is_Moderator_Senior,
        'User_Rendered'         => $Rendered_User,
    ]);
}
elseif(preg_match('/^\/ban (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/memctl ban (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/expulsar (.*)$/is',$Message_Text,$Matches)) {
    if(preg_match('/([0-9]{1,5})([mhd]{1})/',$Matches[1])){
        Copito_Plugin_Modaration_Ban([
            'Telegram_Chat_ID'      => $Telegram_Chat,
            'Message_ID'            => $Message_ID,
            'Message_Text'          => $Message_Text,
            'Query_UserStr'         => $Reply_ID,
            'Query_TimeStr'         => $Matches[1],
            'Role_IsSenior'         => $Role_Is_Moderator_Senior,
            'User_Rendered'         => $Rendered_User,
        ]);
    }
    else {
        Copito_Plugin_Modaration_Ban([
            'Telegram_Chat_ID'      => $Telegram_Chat,
            'Message_ID'            => $Message_ID,
            'Message_Text'          => $Message_Text,
            'Query_UserStr'         => $Matches[1],
            'Query_TimeStr'         => "-1",
            'Role_IsSenior'         => $Role_Is_Moderator_Senior,
            'User_Rendered'         => $Rendered_User,
        ]);
    }
}
elseif(preg_match('/^\/ban(@.*)?$/is',$Message_Text,$Matches)
    || preg_match('/^\/memctl ban(@.*)?$/is',$Message_Text,$Matches)
    || preg_match('/^\/expulsar(@.*)?$/is',$Message_Text,$Matches)) {
    Copito_Plugin_Modaration_Ban([
        'Telegram_Chat_ID'      => $Telegram_Chat,
        'Message_ID'            => $Message_ID,
        'Message_Text'          => $Message_Text,
        'Query_UserStr'         => $Reply_ID,
        'Query_TimeStr'         => "-1",
        'Role_IsSenior'         => $Role_Is_Moderator_Senior,
        'User_Rendered'         => $Rendered_User,
    ]);
}


/*
 * RETIRAR EXPULSIÓN
 *
 * Comando: /unban, /memctl unban
 */
if(preg_match('/^\/unban \?$/is',$Message_Text,$Matches)
    || preg_match('/^\/unban help$/is',$Message_Text,$Matches)
    || preg_match('/^\/man unban$/is',$Message_Text,$Matches)){
    Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Help_Unban,$Rendered_User,$Message_Text));
}
elseif(preg_match('/^\/unban (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/memctl unban (.*)$/is',$Message_Text,$Matches)) {
    Copito_Plugin_Modaration_UnBan([
        'Telegram_Chat_ID'      => $Telegram_Chat,
        'Message_ID'            => $Message_ID,
        'Message_Text'          => $Message_Text,
        'Query_UserStr'         => $Matches[1],
        'Role_IsSenior'         => $Role_Is_Moderator_Senior,
        'User_Rendered'         => $Rendered_User,
    ]);
}
elseif(preg_match('/^\/unban(@.*)?$/is',$Message_Text,$Matches)
    || preg_match('/^\/memctl unban(@.*)?$/is',$Message_Text,$Matches)) {
    Copito_Plugin_Modaration_UnBan([
        'Telegram_Chat_ID'      => $Telegram_Chat,
        'Message_ID'            => $Message_ID,
        'Message_Text'          => $Message_Text,
        'Query_UserStr'         => $Reply_ID,
        'Role_IsSenior'         => $Role_Is_Moderator_Senior,
        'User_Rendered'         => $Rendered_User,
    ]);
}


/*
 * ECHAR USUARIO
 *
 * Comando: /kick, /memctl kic, /echar
 */
if(preg_match('/^\/kick \?$/is',$Message_Text,$Matches)
    || preg_match('/^\/echar \?$/is',$Message_Text,$Matches)
    || preg_match('/^\/kick help$/is',$Message_Text,$Matches)
    || preg_match('/^\/echar help$/is',$Message_Text,$Matches)
    || preg_match('/^\/man kick$/is',$Message_Text,$Matches)
    || preg_match('/^\/man echar$/is',$Message_Text,$Matches)) {
    Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Help_Kick,$Rendered_User,$Message_Text));
}
elseif(preg_match('/^\/kick (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/echar (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/memctl kick (.*)$/is',$Message_Text,$Matches)) {
    Copito_Plugin_Modaration_Kick([
        'Telegram_Chat_ID'      => $Telegram_Chat,
        'Message_ID'            => $Message_ID,
        'Message_Text'          => $Message_Text,
        'Query_UserStr'         => $Matches[1],
        'Role_IsSenior'         => $Role_Is_Moderator_Senior,
        'User_Rendered'         => $Rendered_User,
    ]);
}
elseif(preg_match('/^\/kick(@.*)?$/is',$Message_Text,$Matches)
    || preg_match('/^\/echar(@.*)?$/is',$Message_Text,$Matches)
    || preg_match('/^\/memctl kick(@.*)?$/is',$Message_Text,$Matches)) {
    Copito_Plugin_Modaration_Kick([
        'Telegram_Chat_ID'      => $Telegram_Chat,
        'Message_ID'            => $Message_ID,
        'Message_Text'          => $Message_Text,
        'Query_UserStr'         => $Reply_ID,
        'Role_IsSenior'         => $Role_Is_Moderator_Senior,
        'User_Rendered'         => $Rendered_User,
    ]);
}


/*
 * MUTEAR USUARIO
 *
 * Comando: /mute, /memctl mute, /mutear
 */
if(preg_match('/^\/mute \?$/is',$Message_Text,$Matches)
    || preg_match('/^\/mutear \?$/is',$Message_Text,$Matches)
    || preg_match('/^\/mute help$/is',$Message_Text,$Matches)
    || preg_match('/^\/mutear help$/is',$Message_Text,$Matches)
    || preg_match('/^\/man mute$/is',$Message_Text,$Matches)
    || preg_match('/^\/man mutear$/is',$Message_Text,$Matches)) {
    Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Help_Mute,$Rendered_User,$Message_Text));
}
elseif(preg_match('/^\/mute (.*) (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/memctl mute (.*) (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/mutear (.*) (.*)$/is',$Message_Text,$Matches)) {
    Copito_Plugin_Modaration_Mute([
        'Telegram_Chat_ID'      => $Telegram_Chat,
        'Message_ID'            => $Message_ID,
        'Message_Text'          => $Message_Text,
        'Query_UserStr'         => $Matches[1],
        'Query_TimeStr'         => $Matches[2],
        'Role_IsJunior'         => $Role_Is_Moderator_Junior,
        'User_Rendered'         => $Rendered_User,
    ]);
}
elseif(preg_match('/^\/mute (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/memctl mute (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/mutear (.*)$/is',$Message_Text,$Matches)) {
    if(preg_match('/([0-9]{1,5})([mhd]{1})/',$Matches[1])){
        Copito_Plugin_Modaration_Mute([
            'Telegram_Chat_ID'      => $Telegram_Chat,
            'Message_ID'            => $Message_ID,
            'Message_Text'          => $Message_Text,
            'Query_UserStr'         => $Reply_ID,
            'Query_TimeStr'         => $Matches[1],
            'Role_IsJunior'         => $Role_Is_Moderator_Junior,
            'User_Rendered'         => $Rendered_User,
        ]);
    }
    else {
        Copito_Plugin_Modaration_Mute([
            'Telegram_Chat_ID'      => $Telegram_Chat,
            'Message_ID'            => $Message_ID,
            'Message_Text'          => $Message_Text,
            'Query_UserStr'         => $Matches[1],
            'Query_TimeStr'         => "-1",
            'Role_IsSenior'         => $Role_Is_Moderator_Senior,
            'User_Rendered'         => $Rendered_User,
        ]);
    }
}
elseif(preg_match('/^\/mute(@.*)?$/is',$Message_Text,$Matches)
    || preg_match('/^\/memctl mute(@.*)?$/is',$Message_Text,$Matches)
    || preg_match('/^\/mutear(@.*)?$/is',$Message_Text,$Matches)) {
    Copito_Plugin_Modaration_Mute([
        'Telegram_Chat_ID'      => $Telegram_Chat,
        'Message_ID'            => $Message_ID,
        'Message_Text'          => $Message_Text,
        'Query_UserStr'         => $Reply_ID,
        'Query_TimeStr'         => "-1",
        'Role_IsJunior'         => $Role_Is_Moderator_Junior,
        'User_Rendered'         => $Rendered_User,
    ]);
}


/*
 * RETIRAR MUTEO
 *
 * Comando: /unmute, /memctl unmute
 */
if(preg_match('/^\/unmute \?$/is',$Message_Text,$Matches)
    || preg_match('/^\/unmute help$/is',$Message_Text,$Matches)
    || preg_match('/^\/man unmute$/is',$Message_Text,$Matches)){
    Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Help_Unmute,$Rendered_User,$Message_Text));
}
elseif(preg_match('/^\/unmute (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/memctl unmute (.*)$/is',$Message_Text,$Matches)) {
    Copito_Plugin_Modaration_UnMute([
        'Telegram_Chat_ID'      => $Telegram_Chat,
        'Message_ID'            => $Message_ID,
        'Message_Text'          => $Message_Text,
        'Query_UserStr'         => $Matches[1],
        'Role_IsJunior'         => $Role_Is_Moderator_Junior,
        'User_Rendered'         => $Rendered_User,
    ]);
}
elseif(preg_match('/^\/unmute(@.*)?$/is',$Message_Text,$Matches)
    || preg_match('/^\/memctl unmute(@.*)?$/is',$Message_Text,$Matches)) {
    Copito_Plugin_Modaration_UnMute([
        'Telegram_Chat_ID'      => $Telegram_Chat,
        'Message_ID'            => $Message_ID,
        'Message_Text'          => $Message_Text,
        'Query_UserStr'         => $Reply_ID,
        'Role_IsJunior'         => $Role_Is_Moderator_Junior,
        'User_Rendered'         => $Rendered_User,
    ]);
}

/*
 * BORRAR MENSAJE
 *
 * Comando: /delete, /memctl delete, /remove, /memctl remove, /borrar, /quitar
 */
if(preg_match('/^\/delete \?$/is',$Message_Text,$Matches)
    || preg_match('/^\/delete help$/is',$Message_Text,$Matches)
    || preg_match('/^\/man delete$/is',$Message_Text,$Matches)
    || preg_match('/^\/remove \?$/is',$Message_Text,$Matches)
    || preg_match('/^\/remove help$/is',$Message_Text,$Matches)
    || preg_match('/^\/man remove$/is',$Message_Text,$Matches)
    || preg_match('/^\/borrar \?$/is',$Message_Text,$Matches)
    || preg_match('/^\/borrar help$/is',$Message_Text,$Matches)
    || preg_match('/^\/man borrar$/is',$Message_Text,$Matches)
    || preg_match('/^\/quitar \?$/is',$Message_Text,$Matches)
    || preg_match('/^\/quitar help$/is',$Message_Text,$Matches)
    || preg_match('/^\/man quitar$/is',$Message_Text,$Matches)){
    Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Help_Delete,$Rendered_User,$Message_Text));
}
elseif(preg_match('/^\/delete(@.*)?$/is',$Message_Text,$Matches)
    || preg_match('/^\/memctl delete$/is',$Message_Text,$Matches)
    || preg_match('/^\/remove(@.*)?$/is',$Message_Text,$Matches)
    || preg_match('/^\/memctl remove$/is',$Message_Text,$Matches)
    || preg_match('/^\/borrar(@.*)?$/is',$Message_Text,$Matches)
    || preg_match('/^\/quitar$/is',$Message_Text,$Matches)) {

    Copito_Message_Delete($Telegram_Chat,$Message_ID);

    if($Role_Is_Moderator_Junior){
        if($Reply_ID){
            $UserQuery_Data = Copito_Subjects_User($Reply_ID);

            if($UserQuery_Data){
                if(Copito_RoleSet_Check_Junior($UserQuery_Data['Telegram_ID'])){
                    Copito_Message_Delete($Telegram_Chat,$Message_Data['message']['reply_to_message']['message_id']);
                }
                else {
                    Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotRoleSet,$Rendered_User,$Message_Text,$UserQuery_Data['Display_Rendered']));
                }
            }
            else {
                Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotFound,$Rendered_User,$Message_Text,$Reply_ID));
            }
        }
        else {
            Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Err_Reply,$Rendered_User,$Message_Text));
        }
    }
    else {
        Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_NotRoles_Junior,$Rendered_User,$Message_Text));
    }
}


/*
 * CONSULTAR, ESPECIFICAR, RESETEAR O APLICAR STRIKE
 *
 * Comando: /warn, /strike, /aviso
 */
if(preg_match('/^\/warn \?$/is',$Message_Text,$Matches)
    || preg_match('/^\/strike \?$/is',$Message_Text,$Matches)
    || preg_match('/^\/aviso \?$/is',$Message_Text,$Matches)
    || preg_match('/^\/warn help$/is',$Message_Text,$Matches)
    || preg_match('/^\/strike help$/is',$Message_Text,$Matches)
    || preg_match('/^\/aviso help$/is',$Message_Text,$Matches)
    || preg_match('/^\/man warn$/is',$Message_Text,$Matches)
    || preg_match('/^\/man strike$/is',$Message_Text,$Matches)
    || preg_match('/^\/man aviso$/is',$Message_Text,$Matches)) {
    Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Help_Strike,$Rendered_User,$Message_Text));
}
elseif(preg_match('/^\/warn list$/is',$Message_Text,$Matches)
    || preg_match('/^\/strike list$/is',$Message_Text,$Matches)
    || preg_match('/^\/aviso list$/is',$Message_Text,$Matches)) {

    $Strike_Query           = new WP_Query([
        'post_type'         => 'telegram_subscribers',
        'posts_per_page'    => -1,
        'order'             => 'DESC',
        'orderby'           => [
            'Strike_Meta'   => 'DESC',
            'Name_Meta'     => 'ASC',
        ],
        'meta_query'        => [
            'Strike_Meta'   => [
                'key'       => 'copito_warnings',
                'value'     => 1,
                'type'      => 'NUMERIC',
                'compare'   => '>'
            ],
            'Name_Meta'   => [
                'key'       => 'telegram_first_name',
                'compare'   => 'EXISTS'
            ]
        ]
    ]);

    $Print_List = [];

    if($Strike_Query->have_posts()){
        while($Strike_Query->have_posts()){
            $Strike_Query->the_post();

            $Striker_Telegram_ID    = (int)get_the_title();
            $Striker_Name_First     = get_post_meta(get_the_ID(),'telegram_first_name',true);
            $Striker_Name_Last      = get_post_meta(get_the_ID(),'telegram_last_name',true);
            $Striker_Name_All       = trim("$Striker_Name_First $Striker_Name_Last");
            $Striker_Rendered       = "[$Striker_Name_All](tg://user?id=$Striker_Telegram_ID)";
            $Striker_Count          = (int)get_post_meta(get_the_ID(),'copito_warnings',true);;

            $Print_List[] = "$Striker_Rendered : $Striker_Count strike(s).";
        }
    }
    else {
        $Print_List[] = "No hay usuarios en esta lista.";
    }

    Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Strike_List,$Rendered_User,$Message_Text,implode("\n",$Print_List)));
}
elseif(preg_match('/^\/warn (.*) (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/memctl warn (.*) (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/strike (.*) (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/aviso (.*) (.*)$/is',$Message_Text,$Matches)) {
    Copito_Plugin_Modaration_Strike([
        'Telegram_Chat_ID'      => $Telegram_Chat,
        'Message_ID'            => $Message_ID,
        'Message_Text'          => $Message_Text,
        'Query_UserStr'         => $Matches[1],
        'Query_StrikeStr'       => $Matches[2],
        'Role_IsJunior'         => $Role_Is_Moderator_Junior,
        'User_Rendered'         => $Rendered_User,
    ]);
}
elseif(preg_match('/^\/warn (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/memctl warn (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/strike (.*)$/is',$Message_Text,$Matches)
    || preg_match('/^\/aviso (.*)$/is',$Message_Text,$Matches)) {


    if($Matches[1]=='print' || $Matches[1] == 'reset' || is_numeric($Matches[1])){
        Copito_Plugin_Modaration_Strike([
            'Telegram_Chat_ID'      => $Telegram_Chat,
            'Message_ID'            => $Message_ID,
            'Message_Text'          => $Message_Text,
            'Query_UserStr'         => $Reply_ID,
            'Query_StrikeStr'       => $Matches[1],
            'Role_IsJunior'         => $Role_Is_Moderator_Junior,
            'User_Rendered'         => $Rendered_User,
        ]);
    }
    else {
        Copito_Plugin_Modaration_Strike([
            'Telegram_Chat_ID'      => $Telegram_Chat,
            'Message_ID'            => $Message_ID,
            'Message_Text'          => $Message_Text,
            'Query_UserStr'         => $Matches[1],
            'Query_StrikeStr'       => "",
            'Role_IsJunior'         => $Role_Is_Moderator_Junior,
            'User_Rendered'         => $Rendered_User,
        ]);
    }
}
elseif(preg_match('/^\/warn(@.*)?$/is',$Message_Text,$Matches)
    || preg_match('/^\/memctl warn(@.*)?$/is',$Message_Text,$Matches)
    || preg_match('/^\/strike(@.*)?$/is',$Message_Text,$Matches)
    || preg_match('/^\/aviso(@.*)?$/is',$Message_Text,$Matches)) {
    Copito_Plugin_Modaration_Strike([
        'Telegram_Chat_ID'      => $Telegram_Chat,
        'Message_ID'            => $Message_ID,
        'Message_Text'          => $Message_Text,
        'Query_UserStr'         => $Reply_ID,
        'Query_StrikeStr'       => "",
        'Role_IsJunior'         => $Role_Is_Moderator_Junior,
        'User_Rendered'         => $Rendered_User,
    ]);
}


/*
 * CONSULTAR DOCUMENTACIÓN
 *
 * Comando: /memctl, /man
 */
if(preg_match('/^\/memctl$/is',$Message_Text,$Matches)
    || preg_match('/^\/man$/is',$Message_Text,$Matches)
    || preg_match('/^\/man moderation$/is',$Message_Text,$Matches)) {
    Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_MOD_Help,$Rendered_User,$Message_Text));
}