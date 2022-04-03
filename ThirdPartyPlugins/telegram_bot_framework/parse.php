<?php
try{
    $Copito_JSON = file_get_contents('php://input');

	if (!$Copito_JSON) {
		return;
	}

	$Copito_Data = (array)json_decode($Copito_JSON,true);

    if(!isset($Copito_Data['update_id'])){
        return;
    }

    $Copito_Data = array_merge([
        'update_id'                     => 0,
        'message'                       => [
            'message_id'                => 0,
            'from'                      => [
                'id'                    => 0,
                'is_bot'                => false,
                'first_name'            => "",
                'last_name'             => "",
                'username'              => "",
            ],
            'sender_chat'               => [
                'id'                    => 0,
                'title'                 => "",
                'username'              => "",
                'type'                  => "" // channel
            ],
            'date'                      => 0,
            'chat'                      => [
                'id'                    => 0,
                'type'                  => "",
                'title'                 => "",
                'username'              => "",
                'first_name'            => "",
                'last_name'             => "",
                'photo'                 => [],
                'bio'                   => "",
                'description'           => "",
                'invite_link'           => "",
                'pinned_message'        => [],
                'permissions'           => [],
                'slow_mode_delay'       => 0,
                'sticker_set_name'      => 0,
                'can_set_sticker_set'   => 0,
                'linked_chat_id'        => 0,
                'location'              => 0,
            ],
            'forward_from'              => [],
            'forward_from_chat'         => [],
            'forward_from_message_id'   => 0,
            'forward_signature'         => "",
            'forward_sender_name'       => "",
            'forward_date'              => 0,
            'reply_to_message'          => [],
            'via_bot'                   => [],
            'edit_date'                 => 0,
            'media_group_id'            => "",
            'author_signature'          => "",
            'text'                      => "",
            'entities'                  => [],
            'animation'                 => [],
            'audio'                     => [],
            'document'                  => [],
            'photo'                     => [],
            'sticker'                   => [],
            'video'                     => [],
            'video_note'                => [],
            'voice'                     => [],
            'caption'                   => "",
            'caption_entities'          => [],
            'contact'                   => [],
            'dice'                      => [],
            'game'                      => [],
            'poll'                      => [],
            'venue'                     => [],
            'location'                  => [],
            'new_chat_members'          => [],
            'left_chat_member'          => [],
            'new_chat_title'            => "",
            'new_chat_photo'            => [],
            'delete_chat_photo'         => false,
            'group_chat_created'        => false,
            'supergroup_chat_created'   => false,
            'channel_chat_created'      => [],
            'migrate_to_chat_id'        => 0,
            'migrate_from_chat_id'      => 0,
            'pinned_message'            => "",
            'invoice'                   => [],
            'successful_payment'        => [],
            'connected_website'         => "",
            'passport_data'             => [],
            'proximity_alert_triggered' => [],
            'voice_chat_scheduled'      => [],
            'voice_chat_started'        => [],
            'voice_chat_ended'          => [],
            'reply_markup'              => [],
        ],
        'edited_message'                => [],
        'channel_post'                  => [],
        'edited_channel_post'           => [],
        'inline_query'                  => [],
        'chosen_inline_result'          => [],
        'callback_query'                => [],
        'shipping_query'                => [],
        'pre_checkout_query'            => [],
        'poll'                          => [],
        'poll_answer'                   => [],
        'my_chat_member'                => [],
        'chat_member'                   => [],
    ],$Copito_Data);

    //telegram_log('TEST','',json_encode($Copito_Data));

    if($Copito_Data['edited_message'] && isset($Copito_Data['edited_message']['edit_date']) && $Copito_Data['edited_message']['edit_date']>0){
        $Copito_Data['message']         = $Copito_Data['edited_message'];

        define('COPITO_IS_EDITION',true);
    }
    if($Copito_Data['callback_query']){
        $Copito_Data['message']         = $Copito_Data['callback_query']['message'];
        $Copito_Data['message']['from'] = $Copito_Data['callback_query']['from'];

        define('COPITO_IS_CALLBACK',true);
    }

    $Message_ID                 = $Copito_Data['message']['message_id'];
    $Message_User_ID            = $Copito_Data['message']['from']['id'];
    $Message_Chat_ID            = $Copito_Data['message']['chat']['id'];

    if($Copito_Data['message']['sender_chat']['id']){
        $Message_User_ID        = $Copito_Data['message']['sender_chat']['id'];

        define('COPITO_IS_FROM_CHANNEL',true);

        $Copito_Data['message']['from']['username']     = $Copito_Data['message']['sender_chat']['username'];
        $Copito_Data['message']['from']['first_name']   = $Copito_Data['message']['sender_chat']['title'];
        $Copito_Data['message']['from']['last_name']    = "";
    }

    if($Message_ID===0 || $Message_User_ID===0 || $Message_Chat_ID===0){
        return;
    }

    if(in_array($Message_Chat_ID,['-1001275565694','605772539'])){
        define('COPITO_DO_COPETE',true);
    }

    if(!defined('COPITO_DO_COPETE')){
        return;
    }

    if (!telegram_getid($Message_User_ID)){
        $NewUser_ID = wp_insert_post([
            'post_title'        => $Message_User_ID,
            'post_content'      => '',
            'post_type'         => 'telegram_subscribers',
            'post_status'       => 'publish',
            'post_author'       => 1,
        ]);

        update_post_meta($NewUser_ID, 'telegram_custom',1);
        update_post_meta($NewUser_ID, 'copito_captcha_status',1);
        update_post_meta($NewUser_ID, 'telegram_username',$Copito_Data['message']['from']['username']);
        update_post_meta($NewUser_ID, 'telegram_last_name',$Copito_Data['message']['from']['last_name']);
        update_post_meta($NewUser_ID, 'telegram_first_name',$Copito_Data['message']['from']['first_name']);

        $Default_Profile_Path = WP_CONTENT_DIR."/uploads/2020/07/avatar.jpg";
        $NewUser_Profile_Path = WP_CONTENT_DIR."/uploads/kui_system/telegram_profiles/$NewUser_ID.jpg";

        if(file_exists($Default_Profile_Path) && !file_exists($NewUser_Profile_Path)){
            copy($Default_Profile_Path,$NewUser_Profile_Path);
        }
    }
    else {
        $Message_User_WPID = telegram_getid($Message_User_ID);

        list($Message_User_Name_A,$Message_User_Name_First,$Message_User_Name_Last) = [
            get_post_meta($Message_User_WPID,'telegram_username',true),
            get_post_meta($Message_User_WPID,'telegram_first_name',true),
            get_post_meta($Message_User_WPID,'telegram_last_name',true)
        ];

        if($Message_User_Name_A!=$Copito_Data['message']['from']['username']){
            update_post_meta($Message_User_WPID, 'telegram_username',$Copito_Data['message']['from']['username']);
            $Message_User_Name_A = $Copito_Data['message']['from']['username'];
        }
        if($Message_User_Name_First!=$Copito_Data['message']['from']['first_name']){
            update_post_meta($Message_User_WPID, 'telegram_first_name',$Copito_Data['message']['from']['first_name']);
            $Message_User_Name_First = $Copito_Data['message']['from']['first_name'];
        }
        if($Message_User_Name_Last!=$Copito_Data['message']['from']['last_name']){
            update_post_meta($Message_User_WPID, 'telegram_last_name',$Copito_Data['message']['from']['last_name']);
            $Message_User_Name_Last = $Copito_Data['message']['from']['last_name'];
        }

        wp_update_post([
            'ID'                => $Message_User_WPID,
            'post_modified'     => current_time('mysql'),
            'post_modified_gmt' => get_gmt_from_date(current_time('mysql')),
        ]);
        $Message_User_Dispatches = get_post_meta($Message_User_WPID, 'telegram_custom',true);
        update_post_meta($Message_User_WPID, 'telegram_custom', $Message_User_Dispatches+1);
    }

    try {
        do_action( 'copito_runtime',$Copito_Data);
    }
    catch(Throwable $Error) {
        telegram_sendmessage($Message_Chat_ID,"🐳 *PHPERROR*\n\n".$Error->getMessage());
        telegram_log('ERROR', 'BOT_ERROR',$Error->getMessage());
    }
}
catch(Throwable $Error) {
    telegram_log('ERROR', 'BOT_ERROR',$Error->getMessage());
}
?>
