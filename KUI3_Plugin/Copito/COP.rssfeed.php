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

if(preg_match('/^\/sources (.*) (.*)$/is',$Message_Text,$Matches))
{
    Copito_Message_Delete($Telegram_Chat,$Message_ID);
    $Command_UserToBan = $Matches[2];
    $Command_UserSet = $Matches[1];

    if($Role_Is_Moderator_Senior)
    {
        switch ($Command_UserSet)
        {
            case 'add':
                preg_match_all('#(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))#',$Command_UserToBan,$matches);
                if(isset($matches[0]))
                {
                    if(file_get_contents($Command_UserToBan))
                    {
                        Copito_DBarray_Add('wp_copito_rss_sources',$Command_UserToBan);
                        telegram_sendmessage($Telegram_Chat,"🗞 *FeedlyCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nGestión de listas RSS de Copito\n\n*URL:* $Command_UserToBan\n*Acción:* Añadir.",false,true);
                    }
                    else
                    {
                        telegram_sendmessage($Telegram_Chat,"🗞 *FeedlyCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nNo ha sido posible procesar la URL.");
                    }
                }
                else
                {
                    telegram_sendmessage($Telegram_Chat,"🗞 *FeedlyCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nNo ha sido posible procesar la URL.");
                }
                break;
            case 'remove':
                Copito_DBarray_Delete('wp_copito_rss_sources',$Command_UserToBan);
                telegram_sendmessage($Telegram_Chat,"🗞 *FeedlyCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nGestión de listas RSS de Copito\n\n*URL:* $Command_UserToBan\n*Acción:* Suprimir.",false,true);
                break;
            default:
                telegram_sendmessage($Telegram_Chat,"🗞 *FeedlyCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nSe esperaba 'add', 'remove' o 'list' como primer argumento.");
                break;
        }
    }
    else
    {
        telegram_sendmessage($Telegram_Chat,"🗞 *FeedlyCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nPermiso denegado - Requiere Mod. Senior o superior.");
    }
}
elseif(preg_match('/^\/sources list$/is',$Message_Text,$Matches))
{
    Copito_Message_Delete($Telegram_Chat,$Message_ID);
    $Command_UserToBan = $Matches[2];
    $Command_UserSet = $Matches[1];

    if($Role_Is_Moderator_Senior)
    {
        $RSS_Sources = explode(';',get_option('wp_copito_rss_sources'));
        $Text = [];
        $Text[] = "🗞 *FeedlyCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n";
        foreach ($RSS_Sources as $Source)
        {
            $Text[] = "🔹 $Source";
        }
        telegram_sendmessage($Telegram_Chat,implode("\n",$Text),false,true);
    }
    else
    {
        telegram_sendmessage($Telegram_Chat,"🗞 *FeedlyCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nPermiso denegado - Requiere Mod. Senior o superior.");
    }
}
elseif(preg_match('/^\/sources(@.*)?$/is',$Message_Text,$Matches))
{
    $Text = [];
    $Text[] = "🗞 *FeedlyCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))";
    $Text[] = "- Comandos para gestionar las listas de noticias.";
    $Text[] = "/sources list\nMuestra la lista de las fuentes RSS añadidas.";
    $Text[] = "/sources add #\nAñade # a la lista de fuentes RSS.";
    $Text[] = "/sources remove #\nElimina # de la lista de fuentes RSS.";
    telegram_sendmessage($Telegram_Chat,implode("\n\n",$Text));
}



