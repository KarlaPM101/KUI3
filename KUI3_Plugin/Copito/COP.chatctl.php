<?php
/*
 * PLUGIN DE COPITO : Control activo de Chat (Versión 2.1)
* Fecha de actualización: 01/09/2021 Karla Pérez Maldonado.
 *
 * ...
 *

 *
 *
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
/** @var bool       $Is_Edition */
/** @var int        $Reply_ID */
/** @var bool       $Role_Is_Moderator_Junior */
/** @var bool       $Role_Is_Moderator_Senior */
/** @var bool       $Role_Is_Moderator_MemCtl */

/*
 * DEFINICIÓN DE LAS FUNCIONES UTILIZADAS POR EL PLUGIN.
 */
function Copito_Plugin_ChatCTL_Fluddy_Dispatch($Arguments=[]){

    $Arguments = array_merge([
        'Telegram_Chat_ID'      => 0,
        'Message_ID'            => 0,
        'Message_Text'          => "",
        'Query_UserStr'         => "",
        'Role_IsJunior'         => false,
        'User_Rendered'         => "",
        'Floody_Time'           => 0,
        'Floody_Count'          => 0,
        'System_Internal'       => false, // Internal Use Only
    ],$Arguments);

    $Telegram_Chat              = $Arguments['Telegram_Chat_ID'];
    $Message_ID                 = $Arguments['Message_ID'];
    $Message_Text               = $Arguments['Message_Text'];
    $Query_UserStr              = $Arguments['Query_UserStr'];
    $Role_Is_Moderator_Junior   = $Arguments['Role_IsJunior'];
    $Rendered_User              = $Arguments['User_Rendered'];
    $Floody_Time                = $Arguments['Floody_Time'];
    $Floddy_Count               = $Arguments['Floody_Count'];
    $Is_System                  = $Arguments['System_Internal'];

    $Copito_Floddy_Count        = 7;
    $Copito_Floddy_Reset        = 30;

    $Arguments_UserQuery        = $Query_UserStr;
    $UserQuery_Data             = Copito_Subjects_User($Arguments_UserQuery);

    if(!$Is_System){
        Copito_Message_Delete($Telegram_Chat,$Message_ID);
    }

    if($Role_Is_Moderator_Junior || $Is_System){
        if($UserQuery_Data){
            if(Copito_RoleSet_Check_Junior($UserQuery_Data['Telegram_ID']) || $Is_System){
                if($Floddy_Count>$Copito_Floddy_Count || !$Is_System)
                {
                    if($UserQuery_Data['Display_Name']!='Telegram'
                        && $UserQuery_Data['Display_Name']!='Karla\'s Project Stream'
                        && $UserQuery_Data['Display_Name']!='Copito\'s Bot'
                        && !$UserQuery_Data['Telegram_Roles']['Is_Senior']
                        && !$UserQuery_Data['Telegram_Roles']['Is_MemCtl'])
                    {
                        Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_CHT_Floody_Dispatch,$Is_System?"SYSTEM":$Rendered_User,"/fluddy dispatch",$UserQuery_Data['Display_Rendered'],5),false,$Message_ID);
                        Copito_Member_Mute($Query_UserStr,60*5);
                    }
                }
                elseif($Floddy_Count==$Copito_Floddy_Count)
                {
                    Copito_Message_Send($Telegram_Chat,"fluddy $Rendered_User 😖");
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

/*
 * DEFINICIÓN DE LAS VARIABLES UTILIZADAS POR EL PLUGIN.
 */
$Copito_Chat_Delete = false;

/*
 * CONTROL DE FLOOD
 *
 * Ejecuta automáticamente el control de flood en el chat. Cuando un usuario envia mensajes en una frecuencia inferior
 * a $Copito_Floddy_Reset segundos, el contador aumenta. Cuando el contador llega a $Copito_Floddy_Count se envia un
 * mensaje de alerta. Cuando el contador supera $Copito_Floddy_Count, el usuario es silencia por el sistema.
 *
 * El sistema ejecuta /fluddy dispatch VIRTUALMENTE, ejecutando Copito_Plugin_ChatCTL_Fluddy_Dispatch. Cuando alguien
 * escribe "/fluddy dispatch" también ejecuta la función. Esta funcion mutea igual que "/mute" o "/memctl mute".
 *
 * Cuando se ejecuta "/fluddy dispatch" manualmente, siempre se mutea el usuario forzadamente.
 *
 */
$Copito_Floddy              = true;
$Copito_Floddy_Count        = 7;
$Copito_Floddy_Reset        = 30;
if($Copito_Floddy==true && !$Is_Edition && $Message_User_Name_First=='Telegram')
{
    $Floddy_Internal_ID = telegram_getid($Message_User_ID);
    if($Floddy_Internal_ID)
    {
        $Floddy_Time  = (int)get_post_meta($Floddy_Internal_ID,'floddy_time',true);
        $Floddy_Count = (int)get_post_meta($Floddy_Internal_ID,'floddy_count',true);

        if($Floddy_Time+$Copito_Floddy_Reset>time())
        {
            Copito_Plugin_ChatCTL_Fluddy_Dispatch([
                'Telegram_Chat_ID'      => $Telegram_Chat,
                'Message_ID'            => $Message_ID,
                'Message_Text'          => $Message_Text,
                'Query_UserStr'         => $Message_User_ID,
                'Role_IsJunior'         => true,
                'User_Rendered'         => $Rendered_User,
                'Floody_Time'           => $Floddy_Time,
                'Floody_Count'          => $Floddy_Count,
                'System_Internal'       => true
            ]);
        }
        else
        {
            $Floddy_Count = 0;
            $Floddy_Time = 0;
        }

        $Floddy_Count++;
        $Floddy_Time = time();

        update_post_meta($Floddy_Internal_ID,'floddy_time',$Floddy_Time);
        update_post_meta($Floddy_Internal_ID,'floddy_count',$Floddy_Count);
    }
}

if(preg_match('/^\/fluddy \?$/is',$Message_Text,$Matches)
    || preg_match('/^\/fluddy help$/is',$Message_Text,$Matches)
    || preg_match('/^\/man fluddy$/is',$Message_Text,$Matches)) {
    Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_CHT_Floody_Help,$Rendered_User,$Message_Text));
}
elseif(preg_match('/^\/fluddy dispatch (.*)$/is',$Message_Text,$Matches)) {
    Copito_Plugin_ChatCTL_Fluddy_Dispatch([
        'Telegram_Chat_ID'      => $Telegram_Chat,
        'Message_ID'            => $Message_ID,
        'Message_Text'          => $Message_Text,
        'Query_UserStr'         => $Matches[1],
        'Role_IsJunior'         => $Role_Is_Moderator_Junior,
        'User_Rendered'         => $Rendered_User,
        'Floody_Time'           => 0,
        'Floody_Count'          => 0,
        'System_Internal'       => false
    ]);
}
elseif(preg_match('/^\/fluddy dispatch(@.*)?$/is',$Message_Text,$Matches)) {
    Copito_Plugin_ChatCTL_Fluddy_Dispatch([
        'Telegram_Chat_ID'      => $Telegram_Chat,
        'Message_ID'            => $Message_ID,
        'Message_Text'          => $Message_Text,
        'Query_UserStr'         => $Reply_ID,
        'Role_IsJunior'         => $Role_Is_Moderator_Junior,
        'User_Rendered'         => $Rendered_User,
        'Floody_Time'           => 0,
        'Floody_Count'          => 0,
        'System_Internal'       => false
    ]);
}
elseif(preg_match('/^\/fluddy$/is',$Message_Text,$Matches)) {
    Copito_Message_Send($Telegram_Chat,sprintf(Copito_i18l_CHT_Floody_Help,$Rendered_User,$Message_Text));
}



// CONTROL DE MAYÚSCULAS
if(preg_match("/[a-z]/i", $Message_Text) && $Message_Text==strtoupper($Message_Text) && strlen($Message_Text)>10)
{
    $Copito_Chat_Delete = true;
    Copito_Message_Send($Telegram_Chat,"mufiis [$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID) 😭");
}

// CONTROL DE PALABRAS (badword)
$Copito_Badword             = false;//get_option('wp_telegram_badword','no')=='yes'?true:false;
$Copito_Badword_Words       = array_filter(array_unique(explode(',',get_option('wp_telegram_badword_words'))));
$Copito_Badword_Strictwords = array_filter(array_unique(explode(',',get_option('wp_telegram_badword_strict'))));
$Copito_Badword_Delete      = false;
if($Copito_Badword==true)
{
    $Message_Text_Badword_Rendered = $Message_Text;
    $Message_Text_Badword_Rendered = str_replace([
        '.',
        ',',
        '?',
        '<',
        '>',
        '#',
        '-',
        '_',
    ],[
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
    ],$Message_Text_Badword_Rendered);

    $fragments = explode(' ',$Message_Text_Badword_Rendered);
    foreach($fragments as $fragment)
    {
        foreach($Copito_Badword_Words as $word)
        {
            if(Copito_Badword_Filter($fragment,$word))
            {
                telegram_log('ALERT','BadWord2',sprintf('Palabra bloqueada %1$s (%1$s == %3$s) en %2$s',$word,$Message_Text_Badword_Rendered,$fragment));
                $Copito_Badword_Delete = true;
            }
        }
        foreach($Copito_Badword_Strictwords as $word)
        {
            if(Copito_Badword_Filter($fragment,$word))
            {
                telegram_log('ALERT','BadWord2',sprintf('Palabra bloqueada %1$s (%1$s == %3$s) en %2$s',$word,$Message_Text_Badword_Rendered,$fragment));
                $Copito_Badword_Delete = true;
            }
        }
    }
    /*foreach($Copito_Badword_Strictwords as $word)
    {
        if(Copito_Badword_Filter(str_replace(' ','',$Message_Text_Badword_Rendered),$word,true))
        {
            telegram_log('ALERT','BadWord2',sprintf('STRICT Palabra bloqueada %1$s (%1$s == %3$s) en %2$s',$word,$Message_Text_Badword_Rendered,$fragment));
            $Copito_Badword_Delete = true;
        }
    }*/
    if($Copito_Badword_Delete)
    {
        $Copito_Chat_Delete = true;
        Copito_Message_Send($Telegram_Chat,"badi [$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID) 🤢");
    }
}

// CONTROL DE ENLACES
preg_match_all('#(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))#',$Message_Text,$matches);
if(isset($matches[0]))
{
    $Badlink_List   = array_filter(array_unique(explode(',',get_option('wp_telegram_badlinks'))));
    $User_Msg_Count = get_post_meta(telegram_getid($Message_User_ID),'telegram_custom',true);
    foreach($matches[0] as $m)
    {
        if(!strpos(' '.$m,'http')) $m = 'http://'.$m;
        $result = parse_url($m);
        $domain = str_replace('www.','',$result['host']);
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs))
        {
            $domain = $regs['domain'];
        }
        if(in_array(strtolower($domain),$Badlink_List))
        {
            $Copito_Chat_Delete = true;
            Copito_Message_Send($Telegram_Chat,"linki [$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID) 😡");
        }
    }
}

// CONTROL MIME
if($Is_File)
{
    $MimeCtrl_List = array_filter(array_unique(explode(',',get_option('wp_telegram_mimetypes'))));
    if(!in_array($Message_Data['message']['document']['mime_type'],$MimeCtrl_List))
    {
        $Copito_Chat_Delete = true;
        Copito_Message_Send($Telegram_Chat,"mimi tipi [$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID) 🤢");
        telegram_log('CHATCTL',$Telegram_Chat,"MimeType no permitido: {$Message_Data['message']['document']['mime_type']}");
    }
}

IF($Message_User_Name_First=='Telegram'){
    //telegram_sendmessage($Telegram_Chat,"📜 *System* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nMSG BLOCK");
    $Copito_Chat_Delete = true;
    Copito_Message_Delete($Telegram_Chat,$Message_ID);
    Copito_Member_Mute($Message_User_ID,1*3600*24);
}

// ---
if($Copito_Chat_Delete)
{
    //if($user_name=='Telegram' || $user_name=='Karla\'s Project Stream' || $user_name=='Copito\'s Bot') $delete = false;
    if($Message_User_Name_First!='Telegram'
        && $Message_User_Name_First!='Karla\'s Project Stream'
        && $Message_User_Name_First!='Copito\'s Bot'
        && !$Role_Is_Moderator_Senior
        && !$Role_Is_Moderator_MemCtl)
    {
        Copito_Message_Delete($Telegram_Chat,$Message_ID);
    }

}
