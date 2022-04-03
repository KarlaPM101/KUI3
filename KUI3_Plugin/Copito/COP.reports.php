<?php
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
/** @var bool       $Role_Is_Moderator_Junior */
/** @var bool       $Role_Is_Moderator_Senior */
/** @var bool       $Role_Is_Moderator_MemCtl */

if(preg_match('/^\/report(@.*)?$/is',$Message_Text,$Matches))
{
    if(isset($Message_Data['message']['reply_to_message']['from']['id']))
    {
        $Command_Subject_ID   = $Message_Data['message']['reply_to_message']['from']['id'];
        $Command_Subject_Name = $Message_Data['message']['reply_to_message']['from']['first_name'];
        $Command_Subject_Last = $Message_Data['message']['reply_to_message']['from']['last_name'];
        $Command_Subject_All = trim($Command_Subject_Name.' '.$Command_Subject_Last);
        $Command_Subject_Rendered = "[$Command_Subject_All](tg://user?id=$Command_Subject_ID)";

        if(isset($Message_Data['message']['reply_to_message']['caption']) && $Message_Data['message']['reply_to_message']['caption']){
            $Command_Original = $Message_Data['message']['reply_to_message']['caption'];
        }
        else
        {
            $Command_Original = $Message_Data['message']['reply_to_message']['text'];
        }

        $Internal_ID = telegram_getid($Command_Subject_ID);

        if($Internal_ID)
        {
            $M = Copito_Message_Send($Telegram_Chat,"📢 Report ($Rendered_User)\n\nSe ha reportado un mensaje del chat.\n\n*Mensaje:* - Adjuntado -\n*Texto original:* $Command_Original\n*Usuario:* $Command_Subject_Rendered.",false,$Message_Data['message']['reply_to_message']['message_id']);

            $Moderators_IDs = Copito_Member_Moderators();
            if($Moderators_IDs){
                foreach ($Moderators_IDs as $Mod){
                    Copito_Message_Forward($Telegram_Chat,$Mod['ID'],$M);
                    Copito_Message_Forward($Telegram_Chat,$Mod['ID'],$Message_Data['message']['reply_to_message']['message_id']);
                }
            }
        }
    }
    else
    {
        Copito_Message_Send($Telegram_Chat,"📢 Report ($Rendered_User)\n\nDeberás responder el mensaje a reportar.");
    }
}