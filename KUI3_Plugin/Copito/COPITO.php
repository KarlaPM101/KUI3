<?php
/*
 * COPITO v3.0. (Fecha de actualización: 28 de Agosto de 2021).
 *
 * Autora: Karla Pérez.
 * Desarrollo juntamente con el tema "Karla's Flexs" y con el plugin "KUI3".
 * Este plugin necesite previamente el plugin "Telegram Bot" para funcionar modificado por Karla Pérez.
 *
 * Copito (Copito's Bot) posee varios comandos, entre ellos: comandos de entretenimiento, comandos para usuarios y
 * comandos de moderación; estos últimos son bastante extensos y permiten controlar tanto a los usuarios como el grupo
 * en sí.
 *
 * Puedes escribir y ejecutar los comandos de Copito libremente, ya sea, como usuario, para entretenerte o, si eres
 * moderador, administrar el grupo. Debido a la variedad de comandos, y a la complejidad de algunos, a continuación
 * podrás obtener información sobre estos comandos, así como instrucciones de utilización.
 *
 * Plugin desarrollado para funcionar únicamente con un sólo grupo de Telegram, en este caso, Karla's Project. Se
 * requiere de una instalación Wordpress ACTUALIZADA.
 */

/*
 * DEFINIR VARIABLES DE ENTORNO
 *
 * Se define a continuación variables para el grupo (dónde se enviarán los mensajes por defecto). Así mismo, el ID de
 * usuario del dueño (dueña) para evitar el aplicado de acciones como expulsión o muteo por parte de otros usuarios.
 *
 * Adicionalmente, URL del plugin original (a pesar de tener algunas modificaciones para ofrecer compatibilidad con
 * el plugin KUI3).
 */
define('COPITO_GROUP_ID','-1001275565694');
define('COPITO_GROUP_ID_PRIV','1275565694');
define('COPITO_OWNER','605772539');
define('COPITO_PUBLIC','https://karlaperezyt.com/wp-content/uploads/copito');
define('COPITO_PLUGIN',WP_CONTENT_DIR."/plugins/telegram_karla_copito");

/*
 * DEFINIR PALABRAS DE COPITO
 *
 * Se definen 4 tipos de palabras en arrays según estado animico de Copito: Enfadado o cansado, normal, amistoso o
 * hambriento. Cada uno de los estados contiene palabras y emojis; el bot ejecutará en el chat una combinación de: solo
 * palabras, solo emojis o palabras ocn emojis. En los strings, el "%1$s" será reemplazado por el nombre de usuario o
 * por un emoji aleatorio.
 */
define('Copito_Words_Hunga',[
    'hungi %1$s',
    'duuuuuuuuuuu %1$s',
    'ugaaa %1$s',
    'duf %1$s',
    'hugiiiii %1$s !!',
    'taki taki %1$s',
    'hungi %1$s',
    '¿du? %1$s',
    '¿dubi? %1$s',
    'hugi, hudi %1$s',
    'taki taka %1$s',
    'taka %1$s',
    'sukiii %1$s',
    'ñumiii %1$s',
]);
define('Copito_Words_Hunga_Emoji',[
    '🍣',
    '🍤',
    '🍕',
    '🍝',
    '🍽',
    '🥪',
    '🥗',
    '🍙',
    '🍜',
    '🦐',
    '🍩',
    '🍼',
]);
define('Copito_Words_Loves',[
    'dub',
    'duf %1$s',
    'ubiii %1$s',
    'hugiii %1$s',
    'duuuu naniiii aaaaahh %1$s !!',
    'taki taki %1$s',
    'duluuu %1$s',
    'duuuu nani aaahhh diiii %1$s',
    'dubi %1$s',
    'dumpi %1$s',
    'dubi daa!! %1$s',
    'divi duuu %1$s',
    'duuuu %1$s',
    'daaaa dubi %1$s',
    'mumy %1$s',
    'mibiiii %1$s',
    'nani %1$s',
    'nani daaaa %1$s',
]);
define('Copito_Words_Loves_Emoji',[
    '💙',
    '💜',
    '❤️',
    '💗',
    '🤩',
    '💓',
    '😚',
    '💐',
    '🌷',
    '🧚',
    '🌹',
]);
define('Copito_Words_Normal',[
    'dub %1$s',
    'duf %1$s',
    'hugiii %1$s',
    'duluuu %1$s',
    'dubi %1$s',
    'dumpi %1$s',
    'dubi!! %1$s',
    'divi duuu %1$s',
    'duuuu %1$s',
    'daaaa %1$s',
    'daaaaaaaaa %1$s',
    'tufiii %1$s',
    'tufaaa %1$s',
    'tuf %1$s',
    'dup %1$s',
]);
define('Copito_Words_Normal_Emoji',[
    '',
    '',
    '🐳',
    '👻',
    '😖️',
    '🤗',
    '😏',
    '😁',
    '😚',
    '💛',
    '😇',
    '🧚',
    '❤️',
    '💗',
    '👀',
]);
define('Copito_Words_Tired',[
    'du %1$s',
    'duf %1$s',
    'hu %1$s',
    'uff %1$s',
    'dup %1$s',
    'um %1$s',
    'daaaa %1$s',
    'argiii %1$s',
    'dabaaaaa %1$s',
    'tufaaaaaaaaaaaaaaaaaaaaa %1$s',
]);
define('Copito_Words_Tired_Emoji',[
    '😡',
    '😈',
    '😖️',
    '💔',
    '💩',
    '☠',
    '🤕',
    '😭',
    '💣',
    '🖤',
    '😒️',
    '🙄',
]);

/*
 * VARIABLES I18L
 *
 * Para los PLUGINS de Copito nuevos, se definirán a continuación como CONSTANTES los strings que Copito utilizará en
 * el chat. Se añade strings de ayuda/documentación.
 */
require_once WP_CONTENT_DIR."/plugins/KUI3/Copito/COPITO_I18L.php";

/*
 * CONTABS Y CRONJOBS
 *
 * Copito utilizará el crontab "hourly" para la ejecución de las tareas a excepción del Captcha, Escritorios y Pet;
 * estos utilizarán crontabs personalizados "copito_min10" y "copito_min30". Los cronjobs sirven para mensajes de
 * servicio, para mantenimiento del bot y para los mensajes random del Tamagochi.
 *
 * add_filter Filtro de contabs personalizados
 * add_action & wp_schedule_event para la programación de los cronjobs.
 */
add_filter('cron_schedules',function ($schedules){
    $schedules['copito_min10'] = array(
        'interval' => 60*10,
        'display' => 'Copito 10 min'
    );
    $schedules['copito_min30'] = array(
        'interval' => 3600/2,
        'display' => 'Copito 30 min'
    );
    return $schedules;
});

add_action('Copito_Captcha','Copito_Captcha_Cleaner',100,2);
add_action('Copito_Pet',function (){
    Copito_Pet_Restores();
    Copito_Random_Meme();
    Copito_Pet_Says(COPITO_GROUP_ID);
},100);
add_action('Copito_Viernes','Copito_Viernes_Service',100);
add_action('Copito_PodiumUpdt','Friday_UpdatePodium',100);
add_action('Copito_Feedy','Copito_Feedly_Cronjob',100);

if(!wp_next_scheduled ( 'Copito_Pet' )){
    wp_schedule_event(time(),'copito_min10', 'Copito_Pet');
}
if(!wp_next_scheduled ( 'Copito_Captcha',[true,"-1001275565694"])){
    wp_schedule_event(time(),'copito_min10', 'Copito_Captcha',[true,"-1001275565694"]);
}
if(!wp_next_scheduled ( 'Copito_Viernes' )){
    wp_schedule_event(time(),'copito_min30', 'Copito_Viernes');
}
if(!wp_next_scheduled ( 'Copito_Feedy' )){
    wp_schedule_event(time(),'hourly', 'Copito_Feedy');
}
if(!wp_next_scheduled ( 'Copito_PodiumUpdt' )){
    wp_schedule_event(time(),'weekly', 'Copito_PodiumUpdt');
}

/*
 * AÑADIR COPITO
 *
 * Se añaden las funciones de Copito, así como los PLUGINS (los COP.*.php) de Copito al PLUGIN principal de Telegram.
 */
add_action('copito_runtime','Copito_RunTime', 10,1);

/*
 * FUNCIONES DE COPITO
 *
 * A continuación las funciones principales de Copito. Estas funciones serán ejecutados por el RUNTIME de Copito y
 * tambien por cada uno de los plugins añadidos al bot.
 */
function Copito_DBarray_Add($Option_Name,$Value){
    $Current_Value = get_option($Option_Name);
    $Array   = explode(';',$Current_Value);
    $Array[] = $Value;
    $Array   = array_filter($Array);
    $Array   = array_unique($Array);
    $Current_Value = implode(';',$Array);
    update_option($Option_Name,$Current_Value);
    return $Current_Value;
}
function Copito_DBarray_Delete($Option_Name,$Value){
    $Current_Value  = get_option($Option_Name);
    $Array          = explode(';',$Current_Value);
    if($Value_Key = array_search($Value,$Array) !== false)
    {
        if(isset($Array[$Value_Key-1]))
        {
            unset($Array[$Value_Key-1]);
        }
    }
    $Array = array_filter($Array);
    $Array = array_unique($Array);
    $Current_Value = implode(';',$Array);
    update_option($Option_Name,$Current_Value);
    return $Current_Value;
}
function Copito_DBarray_Check($Option_Name,$Value){
    $Current_Value  = get_option($Option_Name);
    $Array          = explode(';',$Current_Value);
    if($Value_Key = array_search($Value,$Array) !== false)
    {
        if(isset($Array[$Value_Key-1]))
        {
            return true;
        }
    }
    return false;
}
function Copito_Message_Send($Telegram_Chat,$Text,$Keyboard=false,$Reply_ID=false)
{
    if($Keyboard)
    {
        $Keyboard = json_encode($Keyboard);
    }

    $Text = telegram_parsetext($Text, 'text', $Telegram_Chat);

    if ( !$Text ) { return; }

    $Endpoint_Url   = 'https://api.telegram.org/bot' . telegram_option('token') . '/sendMessage';
    $Endpoint_Data  = [
        'chat_id'                   => $Telegram_Chat,
        'text'                      => $Text,
        'parse_mode'                => 'Markdown',
        'disable_web_page_preview'  => true,
        'disable_notification'      => true,
    ];

    if($Reply_ID)
    {
        $Endpoint_Data['reply_to_message_id'] = $Reply_ID;
    }

    if($Keyboard)
    {
        $Endpoint_Data['reply_markup'] = $Keyboard;
    }

    $Endpoint_Get = file_get_contents($Endpoint_Url . '?' . http_build_query($Endpoint_Data));

    switch ($http_response_header['0'])
    {
        case 'HTTP/1.1 400 Bad Request':
        case 'HTTP/1.1 403 Forbidden':
            telegram_log('Copito', $Telegram_Chat, sprintf('Error de Telegram :: %1$s',$Endpoint_Url . '?' . http_build_query($Endpoint_Data)));
            telegram_log('Copito', $Telegram_Chat,json_encode($Endpoint_Data));
            telegram_log('Copito', $Telegram_Chat,json_encode($Endpoint_Get));
            return false;
    }

    $Data_Array = json_decode($Endpoint_Get,true);
    if(isset($Data_Array['result'])){
        if(isset($Data_Array['result']['message_id'])) return $Data_Array['result']['message_id'];
    }

    return true;
}
function Copito_Message_Forward($Telegram_Chat,$Telegram_To,$Message_ID=false)
{
    $Endpoint_Url   = 'https://api.telegram.org/bot' . telegram_option('token') . '/forwardMessage';
    $Endpoint_Data  = [
        'from_chat_id'              => $Telegram_Chat,
        'chat_id'                   => $Telegram_To,
        'message_id'                => $Message_ID,
    ];

    $Endpoint_Get = file_get_contents($Endpoint_Url . '?' . http_build_query($Endpoint_Data));

    switch ($http_response_header['0'])
    {
        case 'HTTP/1.1 400 Bad Request':
        case 'HTTP/1.1 403 Forbidden':
            telegram_log('Copito', $Telegram_Chat, sprintf('Error de Telegram :: %1$s',$Endpoint_Url . '?' . http_build_query($Endpoint_Data)));
            telegram_log('Copito', $Telegram_Chat,json_encode($Endpoint_Data));
            telegram_log('Copito', $Telegram_Chat,json_encode($Endpoint_Get));
            return false;
    }

    return true;
}
function Copito_Message_Image($Telegram_Chat,$Image,$Text,$Keyboard=false,$Reply_ID=false)
{
    if($Keyboard)
    {
        $Keyboard = json_encode($Keyboard);
    }

    $Text = telegram_parsetext($Text, 'text', $Telegram_Chat);

    if ( !$Text ) { return false; }

    $Endpoint_Url   = 'https://api.telegram.org/bot' . telegram_option('token') . '/sendPhoto';
    $Endpoint_Data  = [
        'chat_id'                   => $Telegram_Chat,
        'photo'                     => $Image,
        'caption'                   => $Text,
        'parse_mode'                => 'Markdown',
        'disable_web_page_preview'  => true,
        'disable_notification'      => true,
    ];

    if($Reply_ID && substr($Telegram_Chat,0,1)=="-")
    {
        $Endpoint_Data['reply_to_message_id'] = $Reply_ID;
    }

    if($Keyboard)
    {
        $Endpoint_Data['reply_markup'] = $Keyboard;
    }

    $Endpoint_Get = file_get_contents($Endpoint_Url . '?' . http_build_query($Endpoint_Data));

    switch ($http_response_header['0'])
    {
        case 'HTTP/1.1 400 Bad Request':
        case 'HTTP/1.1 403 Forbidden':
            telegram_log('Copito', $Telegram_Chat, sprintf('Error de Telegram :: %1$s',$Endpoint_Url . '?' . http_build_query($Endpoint_Data)));
            return false;
    }

    $Data_Array = json_decode($Endpoint_Get,true);
    if(isset($Data_Array['result'])){
        if(isset($Data_Array['result']['message_id'])) return $Data_Array['result']['message_id'];
    }

    return true;
}
function Copito_Message_Delete($Telegram_Chat,$Telegram_Message){
    $Endpoint_Url   = 'https://api.telegram.org/bot' . telegram_option('token') . '/deleteMessage';
    $Endpoint_Data  = [
        'chat_id'       => $Telegram_Chat,
        'message_id'    => $Telegram_Message,
    ];
    file_get_contents($Endpoint_Url . '?' . http_build_query($Endpoint_Data));

    switch ($http_response_header['0'])
    {
        case 'HTTP/1.1 400 Bad Request':
        case 'HTTP/1.1 403 Forbidden':
            telegram_log('Copito', $Telegram_Chat, sprintf('Error de Telegram :: %1$s',$Endpoint_Url . '?' . http_build_query($Endpoint_Data)));
            return false;
            break;
    }
    return true;
}
function Copito_Member_Kick($Member_Id){
    $Telegram_Chat = COPITO_GROUP_ID;
    if(COPITO_OWNER == $Member_Id) return false;

    $Endpoint_Url = 'https://api.telegram.org/bot' . telegram_option('token') . '/kickChatMember';
    $Endpoint_Data  = [
        'chat_id'       => $Telegram_Chat,
        'user_id'       => $Member_Id,
        'until_date'    => time()+(60*5)+1,
    ];
    file_get_contents($Endpoint_Url . '?' . http_build_query($Endpoint_Data));

    switch ($http_response_header['0'])
    {
        case 'HTTP/1.1 400 Bad Request':
        case 'HTTP/1.1 403 Forbidden':
            telegram_log('Copito', $Telegram_Chat, sprintf('Error de Telegram :: %1$s',$Endpoint_Url . '?' . http_build_query($Endpoint_Data)));
            return false;
            break;
    }
    return true;
}
function Copito_Member_Ban($Member_Id,$Duration=0){
    $Telegram_Chat = COPITO_GROUP_ID;
    if(COPITO_OWNER == $Member_Id) return false;

    $Endpoint_Url = 'https://api.telegram.org/bot' . telegram_option('token') . '/kickChatMember';
    if($Duration)
    {
        $Endpoint_Data  = [
            'chat_id'       => $Telegram_Chat,
            'user_id'       => $Member_Id,
            'until_date'    => time()+$Duration,
        ];
    }
    else
    {
        $Endpoint_Data  = [
            'chat_id'       => $Telegram_Chat,
            'user_id'       => $Member_Id,
        ];
    }
    file_get_contents($Endpoint_Url . '?' . http_build_query($Endpoint_Data));

    switch ($http_response_header['0'])
    {
        case 'HTTP/1.1 400 Bad Request':
        case 'HTTP/1.1 403 Forbidden':
            telegram_log('Copito', $Telegram_Chat, sprintf('Error de Telegram :: %1$s',$Endpoint_Url . '?' . http_build_query($Endpoint_Data)));
            return false;
            break;
    }
    return true;
}
function Copito_Member_Unban($Member_Id){
    $Telegram_Chat = COPITO_GROUP_ID;
    if(COPITO_OWNER == $Member_Id) return false;

    $Endpoint_Url = 'https://api.telegram.org/bot' . telegram_option('token') . '/unbanChatMember';
    $Endpoint_Data  = [
        'chat_id'           => $Telegram_Chat,
        'user_id'           => $Member_Id,
        'only_if_banned'    => true,
    ];
    file_get_contents($Endpoint_Url . '?' . http_build_query($Endpoint_Data));

    switch ($http_response_header['0'])
    {
        case 'HTTP/1.1 400 Bad Request':
        case 'HTTP/1.1 403 Forbidden':
            telegram_log('Copito', $Telegram_Chat, sprintf('Error de Telegram :: %1$s',$Endpoint_Url . '?' . http_build_query($Endpoint_Data)));
            return false;
            break;
    }
    return true;
}
function Copito_Member_Mute($Member_Id,$Duration=0,$Remove_Restriction=false){
    $Telegram_Chat = COPITO_GROUP_ID;
    if(COPITO_OWNER == $Member_Id) return false;

    $Endpoint_Url   = 'https://api.telegram.org/bot' . telegram_option('token') . '/restrictChatMember';
    $Endpoint_Data  = [
        'chat_id'               => $Telegram_Chat,
        'user_id'               => $Member_Id,
        'permissions'           => json_encode([
            'can_send_messages'         => $Remove_Restriction?true:false,
            'can_send_media_messages'   => $Remove_Restriction?true:false,
            'can_send_polls'            => $Remove_Restriction?true:false,
            'can_send_other_messages'   => $Remove_Restriction?true:false,
            'can_add_web_page_previews' => $Remove_Restriction?true:false,
        ]),
        'until_date' => time()+$Duration
    ];
    file_get_contents($Endpoint_Url . '?' . http_build_query($Endpoint_Data));

    switch ($http_response_header['0'])
    {
        case 'HTTP/1.1 400 Bad Request':
        case 'HTTP/1.1 403 Forbidden':
            telegram_log('Copito', $Telegram_Chat, sprintf('Error de Telegram :: %1$s',$Endpoint_Url . '?' . http_build_query($Endpoint_Data)));
            return false;
            break;
    }
    return true;
}
function Copito_Member_Moderation($Member_Id,$Status_Name='REMOVE'){
    $Telegram_Chat = COPITO_GROUP_ID;
    if(COPITO_OWNER == $Member_Id) return false;

    $Endpoint_Url   = 'https://api.telegram.org/bot' . telegram_option('token') . '/promoteChatMember';
    $Endpoint_Data  = [
        'chat_id'               => $Telegram_Chat,
        'user_id'               => $Member_Id,
        'can_change_info'       => false,
        'can_invite_users'      => false,
        'can_pin_messages'      => false,
        'can_delete_messages'   => false,
        'can_restrict_members'  => false,
        'can_promote_members'   => false,
    ];
    $Moderation_Title = '';

    switch ($Status_Name)
    {
        case 'JUNIOR':
            $Endpoint_Data['can_delete_messages']  = true;
            $Moderation_Title = 'Mod. Junior';
            break;
        case 'SENIOR':
            $Endpoint_Data['can_delete_messages']  = true;
            $Endpoint_Data['can_restrict_members'] = true;
            $Moderation_Title = 'Mod. Senior';
            break;
        case 'MEM':
            $Endpoint_Data['can_delete_messages']  = true;
            $Endpoint_Data['can_restrict_members'] = true;
            $Endpoint_Data['can_invite_users']     = true;
            $Moderation_Title = 'Mod. MemCtl';
            break;
    }

    file_get_contents($Endpoint_Url . '?' . http_build_query($Endpoint_Data));

    if($Moderation_Title)
    {
        $Endpoint_Url   = 'https://api.telegram.org/bot' . telegram_option('token') . '/setChatAdministratorCustomTitle';
        $Endpoint_Data  = [
            'chat_id'               => $Telegram_Chat,
            'user_id'               => $Member_Id,
            'custom_title'          => $Moderation_Title,
        ];

        file_get_contents($Endpoint_Url . '?' . http_build_query($Endpoint_Data));
    }

    switch ($http_response_header['0'])
    {
        case 'HTTP/1.1 400 Bad Request':
        case 'HTTP/1.1 403 Forbidden':
            telegram_log('Copito', $Telegram_Chat, sprintf('Error de Telegram :: %1$s',$Endpoint_Url . '?' . http_build_query($Endpoint_Data)));
            return false;
            break;
    }
    return true;
}
function Copito_Member_ModerationTitles($Member_Id,$Status_Name='REMOVE'){
    $Telegram_Chat = COPITO_GROUP_ID;
    if(COPITO_OWNER == $Member_Id) return false;

    $Moderation_Title = '';

    switch ($Status_Name)
    {
        case 'JUNIOR':
            $Moderation_Title = 'Mod. Junior';
            break;
        case 'SENIOR':
            $Moderation_Title = 'Mod. Senior';
            break;
        case 'MEM':
            $Moderation_Title = 'Mod. MemCtl';
            break;
    }

    $Endpoint_Url   = 'https://api.telegram.org/bot' . telegram_option('token') . '/setChatAdministratorCustomTitle';
    $Endpoint_Data  = [
        'chat_id'               => $Telegram_Chat,
        'user_id'               => $Member_Id,
        'custom_title'          => $Moderation_Title,
    ];

    file_get_contents($Endpoint_Url . '?' . http_build_query($Endpoint_Data));


    switch ($http_response_header['0'])
    {
        case 'HTTP/1.1 400 Bad Request':
        case 'HTTP/1.1 403 Forbidden':
            telegram_log('Copito', $Telegram_Chat, sprintf('Error de Telegram :: %1$s',$Endpoint_Url . '?' . http_build_query($Endpoint_Data)));
            return false;
            break;
    }
    return true;
}
function Copito_Member_Moderators(){
    $Telegram_Chat = COPITO_GROUP_ID;

    $Endpoint_Url   = 'https://api.telegram.org/bot' . telegram_option('token') . '/getChatAdministrators';
    $Endpoint_Data  = [
        'chat_id'               => $Telegram_Chat,
    ];
    $Endpoint_Get   = file_get_contents($Endpoint_Url . '?' . http_build_query($Endpoint_Data));

    switch ($http_response_header['0'])
    {
        case 'HTTP/1.1 400 Bad Request':
        case 'HTTP/1.1 403 Forbidden':
            telegram_log('Copito', $Telegram_Chat, sprintf('Error de Telegram :: %1$s',$Endpoint_Url . '?' . http_build_query($Endpoint_Data)));
            return false;
            break;
    }

    $Data_Array = json_decode($Endpoint_Get,true);

    if(isset($Data_Array['result']))
    {
        $List = [];

        foreach ($Data_Array['result'] as $M){
            $List[] = [
                'ID'    => $M['user']['id'],
                'WPID'    => telegram_getid($M['user']['id']),
                'Is_Junior'    => $M['custom_title']==='Mod. Junior'?true:false,
                'Is_Senior'    => $M['custom_title']==='Mod. Senior'?true:false,
                'Is_MemCtl'    => $M['custom_title']==='Mod. MemCtl'?true:false,
            ];
        }

        return $List;
    }
    return [];
}
function Copito_Member_Status($Member_Id,$ForceUpdate=false){
    $Telegram_Chat = COPITO_GROUP_ID;

    $Is_Updated = get_post_meta(telegram_getid($Member_Id),'telegram_status',true);
    if(!$Is_Updated || $ForceUpdate)
    {
        $Endpoint_Url   = 'https://api.telegram.org/bot' . telegram_option('token') . '/getChatMember';
        $Endpoint_Data  = [
            'chat_id'               => $Telegram_Chat,
            'user_id'               => $Member_Id,
        ];
        $Endpoint_Get   = file_get_contents($Endpoint_Url . '?' . http_build_query($Endpoint_Data));

        switch ($http_response_header['0'])
        {
            case 'HTTP/1.1 400 Bad Request':
            case 'HTTP/1.1 403 Forbidden':
                telegram_log('Copito', $Telegram_Chat, sprintf('Error de Telegram :: %1$s',$Endpoint_Url . '?' . http_build_query($Endpoint_Data)));
                return false;
        }

        $Data_Array = json_decode($Endpoint_Get,true);

        if(isset($Data_Array['result']))
        {
            $Creator = false;
            if(isset($Data_Array['result']['status'])){
                if($Data_Array['result']['status']=='creator') $Creator = true;
            }
            update_post_meta(telegram_getid($Member_Id),'telegram_status',json_encode($Status = [
                'ID'        => isset($Data_Array['result']['user']['id'])?$Data_Array['result']['user']['id']:false,
                'FirstName' => isset($Data_Array['result']['user']['first_name'])?$Data_Array['result']['user']['first_name']:false,
                'LastName'  => isset($Data_Array['result']['user']['last_name'])?$Data_Array['result']['user']['last_name']:false,
                'Is_Junior' => $Creator?true:(isset($Data_Array['result']['can_delete_messages'])?$Data_Array['result']['can_delete_messages']:false),
                'Is_Senior' => $Creator?true:(isset($Data_Array['result']['can_restrict_members'])?$Data_Array['result']['can_restrict_members']:false),
                'Is_MemCtl' => $Creator?true:(isset($Data_Array['result']['can_invite_users'])?$Data_Array['result']['can_invite_users']:false),
                'Title'     => isset($Data_Array['result']['custom_title'])?$Data_Array['result']['custom_title']:'',
                'Creator'   => $Creator,
            ]));
            return $Status;
        }
        return false;
    }
    else
    {
        return array_merge([
            'ID'        => 0,
            'FirstName' => "",
            'LastName'  => "",
            'Is_Junior' => false,
            'Is_Senior' => false,
            'Is_MemCtl' => false,
            'Title'     => '',
            'Creator'   => false,
        ],json_decode($Is_Updated,true));
    }
}
function Copito_Member_Query2ID($Query_Name){
    $ID = false;
    if(is_numeric($Query_Name) && telegram_getid($Query_Name))
    {
        $ID = $Query_Name;
    }
    else
    {
        if(strpos($Query_Name,'@')!==false)
        {
            $Query = new WP_Query([
                'post_type' => 'telegram_subscribers',
                'meta_query' => [
                    [
                        'key' => 'telegram_username',
                        'value' => str_replace('@','',$Query_Name)
                    ]
                ]

            ]);
        }
        else
        {
            list($NameFirst,$NameLast) = explode("_",$Query_Name,2);

            if($NameFirst && $NameLast){
                $Query = new WP_Query([
                    'post_type' => 'telegram_subscribers',
                    'meta_query' => [
                        [
                            'key' => 'telegram_first_name',
                            'value' => $NameFirst
                        ],
                        [
                            'key' => 'telegram_last_name',
                            'value' => $NameLast
                        ]
                    ]

                ]);
            }
            else {
                $Query = new WP_Query([
                    'post_type' => 'telegram_subscribers',
                    'meta_query' => [
                        [
                            'key' => 'telegram_first_name',
                            'value' => $NameFirst
                        ]
                    ]

                ]);
            }
        }
        if ($Query->have_posts())
        {
            while ($Query->have_posts())
            {
                $Query->the_post();
                $ID = get_the_title();
            }
        }
    }
    return (int)$ID;
}
function Copito_Files_Download($File_Id)
{
    $Endpoint_Url   = 'https://api.telegram.org/bot' . telegram_option('token') . '/getFile';
    $Endpoint_Data  = [
        'file_id'               => $File_Id,
    ];
    $Endpoint_Get   = file_get_contents($Endpoint_Url . '?' . http_build_query($Endpoint_Data));

    switch ($http_response_header['0'])
    {
        case 'HTTP/1.1 400 Bad Request':
        case 'HTTP/1.1 403 Forbidden':
            telegram_log('Copito', 'file', sprintf('Error de Telegram :: %1$s',$Endpoint_Url . '?' . http_build_query($Endpoint_Data)));
            return false;
    }


    $Data_Array = json_decode($Endpoint_Get,true);

    if(isset($Data_Array['result']['file_path']))
    {
        $File_Path = $Data_Array['result']['file_path'];

        $Endpoint_Url   = 'https://api.telegram.org/file/bot' . telegram_option('token') . '/' . $File_Path;
        $Endpoint_Get   = file_get_contents($Endpoint_Url);
        if($Endpoint_Get)
        {
            return $Endpoint_Get;
        }
    }

    return false;
}
function Copito_Profile($Member_Id)
{
    $Profile_Path = WP_CONTENT_DIR.'/uploads/kui_system/telegram_profiles/'.$Member_Id.'.jpg';
    $Profile_Time = file_exists($Profile_Path)?filemtime($Profile_Path):0;
    $Profile_LifeTime = 3600*2;

    if($Profile_Time+$Profile_LifeTime>time()) return;

    $ch = curl_init();
    $optArray = array(
        CURLOPT_URL => 'https://api.telegram.org/bot' . telegram_option('token') . '/getUserProfilePhotos?user_id='.$Member_Id.'&limit=1',
        CURLOPT_RETURNTRANSFER => true
    );
    curl_setopt_array($ch, $optArray);
    $result = curl_exec($ch);
    curl_close($ch);

    $user_profile = json_decode($result,true);
    if(isset($user_profile['result']['photos'][0][0]['file_id']))
    {
        $file_id = $user_profile['result']['photos'][0][0]['file_id'];

        $ch = curl_init();
        $optArray = array(
            CURLOPT_URL => 'https://api.telegram.org/bot' . telegram_option('token') . '/getFile?file_id='.$file_id,
            CURLOPT_RETURNTRANSFER => true
        );
        curl_setopt_array($ch, $optArray);
        $result = curl_exec($ch);
        curl_close($ch);

        $file = json_decode($result,true);
        if(isset($file['result']['file_path']))
        {
            $file_path = $file['result']['file_path'];

            $ch = curl_init();
            $optArray = array(
                CURLOPT_URL => 'https://api.telegram.org/file/bot' . telegram_option('token') . '/'.$file_path,
                CURLOPT_RETURNTRANSFER => true
            );
            curl_setopt_array($ch, $optArray);
            $result = curl_exec($ch);
            curl_close($ch);

            $file_contents = $result;
            file_put_contents($Profile_Path,$file_contents);
        }
    }

    if(!file_exists($Profile_Path))
    {
        copy(WP_CONTENT_DIR."/uploads/2020/07/avatar.jpg",$Profile_Path);
    }

    Copito_Roles_Enroll($Member_Id);
}
function Copito_Roles_Enroll($Member_Id)
{
    $Old_Member_Status = Copito_Member_Status($Member_Id);
    $New_Member_Status = Copito_Member_Status($Member_Id,true);

    if($New_Member_Status['Creator']===true){
        return;
    }

    if($Old_Member_Status && $New_Member_Status)
    {
        $Command_Subject_ID     = Copito_Member_Query2ID($Member_Id);
        $Command_Subject_WP_ID  = telegram_getid(Copito_Member_Query2ID($Member_Id));
        $Command_Subject_Name   = get_post_meta($Command_Subject_WP_ID,'telegram_first_name',true);
        $Command_Subject_Last   = get_post_meta($Command_Subject_WP_ID,'telegram_last_name',true);
        $Message_User_Name_All  = trim($Command_Subject_Name.' '.$Command_Subject_Last);
        $Rendered_User          = "[$Message_User_Name_All](tg://user?id=$Command_Subject_ID)";
        $Reenroll_dispatch      = false;

        // Was MemCtl
        if($Old_Member_Status['Is_MemCtl']==true && $New_Member_Status['Is_MemCtl']==false)
        {
            Copito_Member_Moderation($Member_Id,'MEM');
            Copito_Message_Send(COPITO_GROUP_ID,sprintf(Copito_i18l_Reenroll_Refresh,'SYS Cronjob','roleset mem '.$Command_Subject_ID,'Administrador Mem'));
            $Reenroll_dispatch = true;
        }
        // Was Senior
        elseif($Old_Member_Status['Is_Senior']==true && $New_Member_Status['Is_Senior']==false)
        {
            Copito_Member_Moderation($Member_Id,'SENIOR');
            Copito_Message_Send(COPITO_GROUP_ID,sprintf(Copito_i18l_Reenroll_Refresh,'SYS Cronjob','roleset senior '.$Command_Subject_ID,'Moderador Sénior'));
            $Reenroll_dispatch = true;
        }
        // Was Junior
        elseif($Old_Member_Status['Is_Junior']==true && $New_Member_Status['Is_Junior']==false)
        {
            Copito_Member_Moderation($Member_Id,'JUNIOR');
            Copito_Message_Send(COPITO_GROUP_ID,sprintf(Copito_i18l_Reenroll_Refresh,'SYS Cronjob','roleset junior '.$Command_Subject_ID,'','Moderador Junior'));
            $Reenroll_dispatch = true;
        }

        // Title Probes
        if($New_Member_Status['Title']!='Mod. MemCtl' && $Old_Member_Status['Title']=='Mod. MemCtl' && $Old_Member_Status['Is_MemCtl']==true){
            Copito_Member_ModerationTitles($Member_Id,'MEM');
            Copito_Message_Send(COPITO_GROUP_ID,sprintf(Copito_i18l_Reenroll_Probes,'SYS Cronjob','roleset mem '.$Command_Subject_ID,$Rendered_User,'Mod. MemCtl'));
            $Reenroll_dispatch = true;
        }

        elseif($New_Member_Status['Title']!='Mod. Senior' && $Old_Member_Status['Title']=='Mod. Senior' && $Old_Member_Status['Is_Senior']==true){
            Copito_Member_ModerationTitles($Member_Id,'SENIOR');
            Copito_Message_Send(COPITO_GROUP_ID,sprintf(Copito_i18l_Reenroll_Probes,'SYS Cronjob','roleset senior '.$Command_Subject_ID,$Rendered_User,'Mod. Senior'));
            $Reenroll_dispatch = true;
        }

        elseif($New_Member_Status['Title']!='Mod. Junior' && $Old_Member_Status['Title']=='Mod. Junior' && $Old_Member_Status['Is_Junior']==true){
            Copito_Member_ModerationTitles($Member_Id,'JUNIOR');
            Copito_Message_Send(COPITO_GROUP_ID,sprintf(Copito_i18l_Reenroll_Probes,'SYS Cronjob','roleset junior '.$Command_Subject_ID,$Rendered_User,'Mod. Junior'));
            $Reenroll_dispatch = true;
        }

        if($Reenroll_dispatch){
            update_post_meta($Command_Subject_WP_ID,'telegram_status_reenroll',time()+3600*2);
        }


        Copito_Member_Status($Member_Id,true);
    }
}
function Copito_Badword_Filter($word,$filter_word,$is_strict=false)
{
    // Mayúsculas -> Minúsculas
    $word = strtolower($word);
    $filter_word = strtolower($filter_word);

    // Limpiar letras repetidas (aaaa -> a)
    $word = preg_replace("/([a-z])(\\1+)/i", '\\1', $word);

    // Quitar Tildes
    $word = str_replace(['á','é','í','ó','ú','à','è','ì','ò','ù'],['a','e','i','o','u','a','e','i','o','u'],$word);
    $word = str_replace(['ä','ë','ï','ö','ü','â','ê','î','ô','û'],['a','e','i','o','u','a','e','i','o','u'],$word);

    // Quitar Símbolos problemáticos (BAD SYMBOLS)
    $word = str_replace(['¿','?',':','"','\'','-'],['','','','','',''],$word);

    // Quitar ... , .
    $word = preg_replace('/^(.*)(\.\.\.)$/is','$1',$word);
    $word = preg_replace('/^(.*)(\.)$/is','$1',$word);
    $word = preg_replace('/^(.*)(\,)$/is','$1',$word);

    // Crear variantes
    $variants = [
        $word,
        preg_replace('/^(.*)(o)$/is','$1a',$word),
        preg_replace('/^(.*)(a)$/is','$1o',$word),
        preg_replace('/^(.*)(o)$/is','$1as',$word),
        preg_replace('/^(.*)(a)$/is','$1os',$word),
        $word.'s',
        str_replace('#','a',$word),
        str_replace('#','e',$word),
        str_replace('#','i',$word),
        str_replace('#','o',$word),
        str_replace('#','u',$word),
        str_replace('*','a',$word),
        str_replace('*','e',$word),
        str_replace('*','i',$word),
        str_replace('*','o',$word),
        str_replace('*','u',$word),
    ];
    $variants = array_unique($variants);

    foreach($variants as $variant)
    {
        if(strlen($variant)<=2) continue;
        if(is_numeric($variant)) continue;
        if(!preg_match("/[a-z]/i", $variant)) continue;

        if($variant==$filter_word)
            return true;

        if(strlen($variant)<=3) continue;

        $variant = preg_replace('/([^a-z])/is','.?',$variant);
        if(substr_count($variant,'.')>3) continue;

        preg_match('/\b('.$variant.')\b/',$filter_word,$matches);
        if(is_array($matches) && count($matches)>0)
            return true;

        if($is_strict)
        {
            preg_match('/'.$filter_word.'/im',$variant,$matches2);
            if(is_array($matches2) && count($matches2)>0)
                return true;
        }
    }
    return false;
}
function Copito_Captcha_StringGen($input, $strength = 10){
    $input_length = strlen($input);
    $random_string = '';
    for($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
    return $random_string;
}
function Copito_Captcha_Generate(){
    $permitted_chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ';

    $image = imagecreatetruecolor(280, 100);
    imageantialias($image, true);

    $colors = [];

    $red = rand(125, 175);
    $green = rand(125, 175);
    $blue = rand(125, 175);

    for($i = 0; $i < 5; $i++) {
        $colors[] = imagecolorallocate($image, $red - 20*$i, $green - 20*$i, $blue - 20*$i);
    }

    imagefill($image, 0, 0, $colors[0]);

    for($i = 0; $i < 10; $i++) {
        imagesetthickness($image, rand(2, 10));
        $line_color = $colors[rand(1, 4)];
        imagerectangle($image, rand(-10, 280), rand(-10, 10), rand(-10, 280), rand(40, 110), $line_color);
    }

    $black = imagecolorallocate($image, 0, 0, 0);
    $white = imagecolorallocate($image, 255, 255, 255);
    $textcolors = [$black, $white];

    $fonts = [COPITO_PLUGIN.'/fonts/Acme.ttf',COPITO_PLUGIN.'/fonts/Ubuntu.ttf',COPITO_PLUGIN.'/fonts/Merriweather.ttf',COPITO_PLUGIN.'/fonts/PlayfairDisplay.ttf'];

    $string_length = 6;
    $captcha_string = Copito_Captcha_StringGen($permitted_chars, $string_length);

    for($i = 0; $i < $string_length; $i++) {
        $letter_space = 230/$string_length;
        $initial = 20;

        imagettftext($image, 56, rand(-15, 15), $initial + $i*$letter_space, rand(25, 45)+50, $textcolors[rand(0, 1)], $fonts[array_rand($fonts)], $captcha_string[$i]);
    }

    ob_start();
    imagepng($image);
    $image_data = ob_get_clean();

    file_put_contents($filepath = WP_CONTENT_DIR."/uploads/copito/captcha/$captcha_string.png",$image_data);

    imagedestroy($image);

    return $captcha_string;
}
function Copito_Subjects_User($Query_String=false){
    $Query_User_ID = Copito_Member_Query2ID($Query_String);

    if($Query_User_ID){
        $Query_WP_ID = telegram_getid(Copito_Member_Query2ID($Query_String));

        if(!$Query_WP_ID){
            return false;
        }

        $Queried_User_First = get_post_meta($Query_WP_ID,'telegram_first_name',true);
        $Queried_User_Last  = get_post_meta($Query_WP_ID,'telegram_last_name',true);
        $Queried_User_All   = trim("$Queried_User_First $Queried_User_Last");

        return [
            'Telegram_ID'       => $Query_User_ID,
            'Wordpress_ID'      => $Query_WP_ID,
            'Display_Name'      => $Queried_User_All,
            'Display_Rendered'  => "[$Queried_User_All](tg://user?id=$Query_User_ID)",
            'Telegram_Roles'    => Copito_Member_Status($Query_User_ID)
        ];
    }

    return false;
}
function Copito_RoleSet_Check_Senior($Query_User_ID){
    global $Role_Is_Moderator_Junior;

    $Subject_Status = Copito_Member_Status($Query_User_ID);

    return !($Subject_Status &&
        (
            (isset($Subject_Status['Is_MemCtl']) && $Subject_Status['Is_MemCtl'])
            || ($Role_Is_Moderator_Junior && isset($Subject_Status['Is_Senior']) && $Subject_Status['Is_Senior'])
        )
    );
}
function Copito_RoleSet_Check_Junior($Query_User_ID){
    global $Role_Is_Moderator_Junior;

    $Subject_Status = Copito_Member_Status($Query_User_ID);

    return !($Subject_Status &&
        (
            (isset($Subject_Status['Is_MemCtl']) && $Subject_Status['Is_MemCtl'])
            || ($Role_Is_Moderator_Junior && isset($Subject_Status['Is_Senior']) && $Subject_Status['Is_Senior'])
        )
    );
}

/*
 * RUNTIME DE COPITO
 *
 * Función principal que habilita Copito, así como todas sus funciones y comandos. El runtime principal se enccargará
 * de procesar los Arrays en formato JSON devueltos por telegram y de calcular los roles de cada uno de sus usuarios,
 * así como las funciones encargadas de actualizar los datos de usuario, foto de perfil y nombres.
 *
 * El callback se encargará de ejecutar aquellas funciones ejecutadas por los botones inline del chat, en este caso,
 * de los botones para el Captcha y los de votación para el Viernes de Escritorio. Adicionalmente, se pueden añadir más
 * acciones en el Callback.
 */
function Copito_RunTime($Message_Data){
    /*
     * Procesar funciones devueltas por la ejecución de botones en el keyboard inline del chat.
     */
    if(defined('COPITO_IS_CALLBACK') && !defined('COPITO_IS_FROM_CHANNEL')){
        Copito_Callback($Message_Data);
        return;
    }

    /*
     * Variables de mensaje. Se obtiene el el ID del mensaje actual "wp_telegram_last_id" y el ID del usuario que ha
     * enviado el mensaje. Estos datos también están disponibles en $Message_Data pero se sigue utilizando el get_option
     * para ofrecer compatibilidad a codigos anteriores a la incorporación de $Message_Data.
     */
    $Message_ID                 = $Message_Data['message']['message_id'];
    $Message_User_ID            = (isset($Message_Data['message']['from']['id']))?$Message_Data['message']['from']['id']:0;


    if($Message_ID===0) {
        return;
    }

    /*
     * Si por alguna razón, el mensaje no es enviado por ningún usuario, la ejecución del código será detenida.
     * (Algo que no debería de ocurrir nunca XD)
     */
    if($Message_User_ID===0) {
        return;
    }

    /*
     * Comprobar el Chat del Mensaje.
     */
    $Telegram_Chat              = $Message_Data['message']['chat']['id'];
    if($Telegram_Chat===0) {
        return;
    }

    /*
     * Todos los mensajes recibidos por Copito deberían de estar enviados por un usuario previamente insertado en la
     * tabla de usuarios de Wordpress (custom post type: telegram_subscribers). En caso de no existir el correspondiente
     * usuario, la ejecución del código debería de ser detenida.
     */
    $Message_WP_ID              = telegram_getid($Message_User_ID);
    if(!$Message_WP_ID) {
        return;
    }

    /*
     * Definición de variables para el procesado del mensaje. Nombre de usuario a mostrar, roles del usuario por defecto
     * y otras variables de checkeo.
     */
    $Message_User_Name_First    = (isset($Message_Data['message']['from']['first_name']))?$Message_Data['message']['from']['first_name']:'null';
    $Message_User_Name_Last     = (isset($Message_Data['message']['from']['last_name']))?$Message_Data['message']['from']['last_name']:'';
    $Message_User_Name_All      = trim("$Message_User_Name_First $Message_User_Name_Last");
    $Message_User_Status        = Copito_Member_Status($Message_User_ID);
    $Message_Text               = isset($Message_Data['message']['text'])?$Message_Data['message']['text']:$Message_Data['message']['caption'];
    $Message_Date               = isset($Message_Data['message']['date'])?$Message_Data['message']['date']:0;
    $Role_Is_Moderator_Junior   = false;
    $Role_Is_Moderator_Senior   = false;
    $Role_Is_Moderator_MemCtl   = false;
    $Is_Image                   = (isset($Message_Data['message']['photo']))?true:false;
    $Is_File                    = (isset($Message_Data['message']['document']))?true:false;
    $Is_Reply                   = (isset($Message_Data['message']['reply_to_message']))?true:false;
    $Is_Edition                 = (isset($Message_Data['edited_message']['edit_date']) && $Message_Data['edited_message']['edit_date']>0)?true:false;
    $Reply_ID                   = isset($Message_Data['message']['reply_to_message']['from']['id'])?$Message_Data['message']['reply_to_message']['from']['id']:false;
    $Rendered_User              = "[$Message_User_Name_All](tg://user?id=$Message_User_ID)";

    $Message_Text               = trim($Message_Text);

    if(empty($Message_Text)){
        return;
    }

    /*
     * Procesar roles actuales del usuario que envia le mensaje. Estos roles son obtenidos por primera vez a través de
     * "Copito_Profile" y se actualiza automaticamente cada 24horas. Los datos son almacenados en caché.
     */
    if($Message_User_Status) {
        if(isset($Message_User_Status['Is_Junior']) && $Message_User_Status['Is_Junior']) {
            $Role_Is_Moderator_Junior = true;
        }
        if(isset($Message_User_Status['Is_Senior']) && $Message_User_Status['Is_Senior']) {
            $Role_Is_Moderator_Junior = true;
            $Role_Is_Moderator_Senior = true;
        }
        if(isset($Message_User_Status['Is_MemCtl']) && $Message_User_Status['Is_MemCtl']) {
            $Role_Is_Moderator_Junior = true;
            $Role_Is_Moderator_Senior = true;
            $Role_Is_Moderator_MemCtl = true;
        }
    }

    /*
     * Refrescar Variables de Canal
     *
     */
    if(defined('COPITO_IS_FROM_CHANNEL')){
        $Message_User_Name_First    = (isset($Message_Data['message']['sender_chat']['title']))?$Message_Data['message']['sender_chat']['title']:'null';
        $Message_User_Name_Last     = '';
        $Message_User_Name_All      = $Message_User_Name_First;
        $Message_User_Status        = [
            'Is_Junior'             => false,
            'Is_Senior'             => false,
            'Is_MemCtl'             => false,
        ];
        $Rendered_User              = "[$Message_User_Name_All](tg://user?id=$Message_User_ID)";
    }

    /*
     * Actualizar datos de usuario. Los datos a actualizar incluye foto de perfil, nombre de usuario (first and last
     * names), username @ y roles dentro del grupo.
     */
    Copito_Profile($Message_User_ID);

    $LastUpdate = (int)get_post_meta($Message_WP_ID,'telegram_status_reenroll',true);
    if($LastUpdate+600<time()){
        update_post_meta($Message_WP_ID,'telegram_status_reenroll',time());
        Copito_Roles_Enroll($Message_User_ID);
    }

    /*
     * Carga dinámica de plugins COP.*.php de Copito.
     */
    foreach (glob(WP_CONTENT_DIR."/plugins/KUI3/Copito/COP.*.php") as $Plugin_Path) {
        require_once $Plugin_Path;
    }

    //telegram_log('COPITO',"$Telegram_Chat - $Message_User_Name_All","$Message_Text");
}
function Copito_Callback($Message_Data){
    /*
     * Carga de variables anteriores.
     */
    $Telegram_Chat              = isset($Message_Data['message']['chat']['id'])?$Message_Data['message']['chat']['id']:0;
    $Message_User_ID            = isset($Message_Data['callback_query']['from']['id'])?$Message_Data['callback_query']['from']['id']:0;
    $Message_WP_ID              = telegram_getid($Message_User_ID);
    $Message_User_Name_First    = (isset($Message_Data['callback_query']['from']['first_name']))?$Message_Data['callback_query']['from']['first_name']:'';
    $Message_User_Name_Last     = (isset($Message_Data['callback_query']['from']['last_name']))?$Message_Data['callback_query']['from']['last_name']:'';
    $Message_User_Name_All      = trim("$Message_User_Name_First $Message_User_Name_Last");
    $Query_Data                 = (isset($Message_Data['callback_query']['data']))?$Message_Data['callback_query']['data']:false;
    $Rendered_User              = "[$Message_User_Name_All](tg://user?id=$Message_User_ID)";

    /*
     * Comprobaciones iniciales.
     */
    if($Telegram_Chat===0) {
        return;
    }

    if($Message_User_ID===0) {
        return;
    }
    if(!$Message_WP_ID) {
        return;
    }

    if(!$Query_Data) {
        return;
    }

    /*
     * Carga del controlador KUI
     */
    $KUI3 = new KUI_REST([]);

    //telegram_log('CALLBACK',"$Telegram_Chat - $Message_User_Name_All",json_encode($Message_Data));

    if(preg_match('/^Captcha_Refresh$/is',$Query_Data)){
        telegram_log('Captcha',$Telegram_Chat,'REFRESH');
        $Captcha_Status = get_post_meta($Message_WP_ID,'copito_captcha_status',true)?true:false;
        $Captcha_Config_Time  = get_option('wp_copito_captcha_config_time')?:1800;
        if($Captcha_Status)
        {
            telegram_log('Captcha',$Telegram_Chat,'STATUS OK');
            telegram_log('Captcha',$Telegram_Chat,'Internal ID '.$Message_WP_ID);
            //$Captcha_Image = get_post_meta($Message_WP_ID,'copito_captcha_first_img',true);
            $Captcha_Message = get_post_meta($Message_WP_ID,'copito_captcha_first_msg',true);
            if($Captcha_Message){
                //Copito_Message_Delete($Telegram_Chat,$Captcha_Image);
                Copito_Message_Delete($Telegram_Chat,$Captcha_Message);
                $Captcha_Msgs   = get_post_meta($Message_WP_ID,'copito_captcha_msgs',true);

                $Captcha_Pharse = Copito_Captcha_Generate();
                //$Img_Id = telegram_sendphoto($Telegram_Chat,'',COPITO_PUBLIC."/captcha/$Captcha_Pharse.png");
                telegram_log('Captcha',$Telegram_Chat,'REFRESH ON');
                $Send_Id = Copito_Message_Image(
                    $Telegram_Chat,
                    COPITO_PUBLIC."/captcha/$Captcha_Pharse.png",
                    "📛 *CaptchaCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nSe ha solicitado un CAPTCHA nuevo.\n\nEscribe el texto del nuevo CAPTCHA para poder ingresar en el grupo. *Tienes ".round($Captcha_Config_Time/60)." min. para resolver*",
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
        }
    }

    if(preg_match('/^Viernes_Upvote_ID_(.*)$/',$Query_Data,$Matches))
    {
        $Command_P_ID = (int)$Matches[1];

        $Last = get_option('wp_copito_lastfriday_msg');
        Copito_Message_Delete(COPITO_GROUP_ID,$Last);

        if(get_post_status($Command_P_ID)!==false)
        {
            if(get_post_type($Command_P_ID)=='viernesdeescritorio')
            {
                $Scorers = get_post_meta($Command_P_ID,'scorers',true);
                $ScoreList = explode(';',$Scorers);

                if(in_array($Message_User_ID,$ScoreList))
                {
                    $Snd_Id = telegram_sendmessage($Telegram_Chat,"🎨 *ViernesCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nYa has votado esta publicación.",false,true,true);
                    update_option('wp_copito_lastfriday_msg',$Snd_Id);
                }
                elseif(get_post_meta($Command_P_ID,'telegram_user_id',true)==$Message_User_ID)
                {
                    $Snd_Id = telegram_sendmessage($Telegram_Chat,"🎨 *ViernesCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nNo puedes votar tu propia publicación.",false,true,true);
                    update_option('wp_copito_lastfriday_msg',$Snd_Id);
                }
                else
                {
                    $Score = get_post_meta($Command_P_ID,'score',true);
                    update_post_meta($Command_P_ID,'score',$Score+1);
                    update_post_meta($Command_P_ID,'vtimestamp',time());
                    update_post_meta($Command_P_ID,'scorers',$Scorers.';'.$Message_User_ID);

                    $Original = get_post_meta($Command_P_ID,'message_id',true);

                    $Autor_KUI_ID       = (int)get_post_meta($Command_P_ID,'kui_user_id',true)?:0;
                    $Autor_Telegram_ID  = (int)get_post_meta($Command_P_ID,'telegram_user_id',true)?:0;

                    $Author_Display_Name = $KUI3->Displays_Users_Name($Autor_KUI_ID,$Autor_Telegram_ID);
                    $Author_Display_URL  = $KUI3->Displays_Users_URL($Autor_KUI_ID,$Autor_Telegram_ID);
                    $Author_Display_Rendered = "[$Author_Display_Name]($Author_Display_URL)";

                    $Snd_Id = Copito_Message_Image($Telegram_Chat,get_the_post_thumbnail_url($Command_P_ID),"🎨 *ViernesCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\n[$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID) ha hecho un upvote *+* *1* 🖌 al escritorio de $Author_Display_Rendered.\n\n*Autor:* $Author_Display_Rendered\n*Fecha:* ".get_the_date('d.m.Y H:i:s',$Command_P_ID)."\n*Puntuación:* ".($Score+1)." votos.",[
                        "inline_keyboard" => [
                            [
                                [
                                    "text" => "Votar +1",
                                    "callback_data" => "Viernes_Upvote_ID_{$Command_P_ID}"
                                ],
                            ],
                            [
                                [
                                    "text" => "Ver Escritorio",
                                    "url" => "tg://privatepost?channel=".COPITO_GROUP_ID_PRIV."&post=$Original"
                                ],
                                [
                                    "text" => "Ver en la Web",
                                    "url" => get_the_permalink($Command_P_ID)
                                ]
                            ]
                        ]
                    ],$Original);
                }
            }
        }
    }
    elseif(preg_match('/^Viernes_Upvote_(.*)_(.*)$/',$Query_Data,$Matches))
    {
        $Command_Week = $Matches[1];
        $Command_User_Id = $Matches[2];

        $Last = get_option('wp_copito_lastfriday_msg');
        Copito_Message_Delete(COPITO_GROUP_ID,$Last);

        $Query = new WP_Query([
            'post_type'     => 'viernesdeescritorio',
            'meta_query'    => [
                [
                    'key'       => 'week',
                    'value'     => $Command_Week,
                    'meta_compare' => '=',
                    'type' => 'NUMERIC'
                ],
                [
                    'key'       => 'telegram_user_id',
                    'value'     => $Command_User_Id,
                    'meta_compare' => '=',
                    'type' => 'NUMERIC'
                ]
            ]
        ]);
        if ($Query->have_posts())
        {
            while ($Query->have_posts())
            {
                $Query->the_post();

                if(get_post_meta(get_the_ID(),'telegram_user_id',true)==$Message_User_ID)
                {
                    $Snd_Id = telegram_sendmessage($Telegram_Chat,"🎨 *ViernesCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nNo puedes votar tu propia publicación.",false,true,true);
                    update_option('wp_copito_lastfriday_msg',$Snd_Id);
                    break;
                }

                $Scorers = get_post_meta(get_the_ID(),'scorers',true);
                $ScoreList = explode(';',$Scorers);

                if(in_array($Message_User_ID,$ScoreList))
                {
                    $Snd_Id = telegram_sendmessage($Telegram_Chat,"🎨 *ViernesCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nYa has votado esta publicación.",false,true,true);
                    update_option('wp_copito_lastfriday_msg',$Snd_Id);
                    break;
                }

                $Score = get_post_meta(get_the_ID(),'score',true);
                update_post_meta(get_the_ID(),'score',$Score+1);

                update_post_meta(get_the_ID(),'vtimestamp',time());

                update_post_meta(get_the_ID(),'scorers',$Scorers.';'.$Message_User_ID);

                $Autor_ID = get_post_meta(get_the_ID(),'telegram_user_id',true);
                $Internal_ID = telegram_getid($Autor_ID);

                if(!$Internal_ID) break;

                $Autor_Name = get_post_meta($Internal_ID,'telegram_first_name',true);
                $Autor_Last = get_post_meta($Internal_ID,'telegram_last_name',true);

                $Week = get_post_meta(get_the_ID(),'week',true);
                $Original = get_post_meta(get_the_ID(),'message_id',true);

                $Snd_Id = Copito_Message_Send($Telegram_Chat,"🎨 *ViernesCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nUpvote *+* *1* 🖌 \n\n*Autor:* [$Autor_Name $Autor_Last](tg://user?id=$Autor_ID)\n*Fecha:* ".get_the_date('d.m.Y H:i:s')."\n*Puntuación:* ".($Score+1)." votos.",[
                    "inline_keyboard" => [
                        [
                            [
                                "text" => "Votar +1",
                                "callback_data" => "Viernes_Upvote_{$Week}_{$Autor_ID}"
                            ],
                            [
                                "text" => "Ver en la Web",
                                "url" => get_the_permalink()
                            ],
                            [
                                "text" => "Ver perfil",
                                "url" => 'https://karlaperezyt.com/telegram/miembros/'.$Autor_ID
                            ]
                        ]
                    ]
                ],$Original);
                update_option('wp_copito_lastfriday_msg',$Snd_Id);

                break;
            }
        }
        else
        {
            $Snd_Id = telegram_sendmessage($Telegram_Chat,"🎨 *ViernesCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nNo se ha encontrado la publicación.",false,true,true);
            update_option('wp_copito_lastfriday_msg',$Snd_Id);
        }
    }
}


/*
 * OTRAS FUNCIONES
 *
 * Funciones utilizadas por los plugins de Copito. Estas funciones deberían de estar en el archivo PHP correspondiente
 * a cada plugin. Así que, resta pendiente un TODO para moverlas de lugar.
 */


function Copito_Captcha_Cleaner($Captcha_Config_Enabled,$Telegram_Chat)
{
    if($Captcha_Config_Enabled)
    {
        $Query = new WP_Query([
            'post_type' => 'telegram_subscribers',
            'meta_query'    => [
                [
                    'key'       => 'copito_captcha_status',
                    'value'     => 1,
                    'meta_compare' => '=',
                    'type' => 'NUMERIC'
                ]
            ]
        ]);
        if ($Query->have_posts())
        {
            while ($Query->have_posts())
            {
                $Query->the_post();

                $Captcha_Time = get_post_meta(get_the_ID(),'copito_captcha_timestamp',true);
                $Captcha_Config_Time  = get_option('wp_copito_captcha_config_time',true)?:1800;

                if($Captcha_Time && $Captcha_Time+$Captcha_Config_Time<time())
                {
                    $Messages = explode(';',get_post_meta(get_the_ID(),'copito_captcha_msgs',true));
                    if($Messages)
                    {
                        foreach ($Messages as $i)
                        {
                            if (!$i) continue;
                            Copito_Message_Delete(COPITO_GROUP_ID, $i);
                        }
                    }
                    delete_post_meta(get_the_ID(),'copito_captcha_msgs');
                    delete_post_meta(get_the_ID(),'copito_captcha_status');
                    delete_post_meta(get_the_ID(),'copito_captcha_timestamp');
                    delete_post_meta(get_the_ID(),'copito_captcha_pharse');
                    delete_post_meta(get_the_ID(),'copito_captcha_first_msg');
                    delete_post_meta(get_the_ID(),'copito_captcha_count');

                    $Captcha_User_ID   = (int)get_the_title(get_the_ID());
                    $Captcha_User_Name = get_post_meta(get_the_ID(),'telegram_first_name',true);
                    $Captcha_User_Last = get_post_meta(get_the_ID(),'telegram_last_name',true);


                    Copito_Member_Mute(get_the_title());
                    telegram_sendmessage($Telegram_Chat,"📛 *CaptchaCTL* (Copito's Cronjob)\n\nCAPTCHA expirado (se ha acabado el tiempo). Usuario BLOQUEADO.\n\n*Usuario:* [$Captcha_User_Name $Captcha_User_Last](tg://user?id=$Captcha_User_ID)\n\n*Motivo:* No ha completado el Captcha.");

                }
            }
        }
    }
}

function Copito_Feedly_Cronjob()
{
    $Last_Sequence  = get_option('wp_copito_rss');
    $Feed_Sources   = explode(';',get_option('wp_copito_rss_sources'));
    $Feed_LastDate  = json_decode(get_option('wp_copito_rss_time')?:[],true);

    if($Last_Sequence!=date('d') && date('H')>=20)
    {
        shuffle($Feed_Sources);

        for($Carry = 0;$Carry < count($Feed_Sources);$Carry++)
        {
            $Feed_Url = $Feed_Sources[$Carry];
            $Parser = simplexml_load_file($Feed_Url);

            // Normal Try
            $Feed_Title = $Parser->channel->item[0]->title;
            $Feed_Link = $Parser->channel->item[0]->link;
            $Feed_Description = $Parser->channel->item[0]->description;
            $Feed_Image = false;
            $Feed_Date = strtotime($Parser->channel->item[0]->pubDate);

            // YT Try
            if(!$Feed_Title && preg_match('/youtube\.com/is',$Feed_Url))
            {
                $Feed_Title = $Parser->entry[0]->title;
                $Feed_Link = $Parser->entry[0]->link->attributes()['href'];
                $Feed_Description = $Parser->entry[0]->children('media', true)->group->children('media', true)->description;
                $Feed_Image = (string)$Parser->entry[0]->children('media', true)->group->children('media', true)->thumbnail->attributes()['url'];
                $Feed_Date = strtotime($Parser->entry[0]->published);
                $Feed_Image = str_replace('hqdefault','maxresdefault',$Feed_Image);
            }

            if(!$Feed_Title || !$Feed_Link || !$Feed_Description) continue;

            if(!isset($Feed_LastDate[md5($Feed_Url)]))
            {
                $Feed_LastDate[md5($Feed_Url)] = 0;
            }

            if($Feed_Date>$Feed_LastDate[md5($Feed_Url)])
            {
                $Feed_Description = strip_tags($Feed_Description);
                $Feed_Description = preg_replace('#(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))#','',$Feed_Description);

                if(strlen($Feed_Description)>200)
                {
                    $Feed_Description = substr($Feed_Description,0,200).'...';
                }

                if($Feed_Image)
                {
                    Copito_Message_Image(COPITO_GROUP_ID,$Feed_Image,"🗞 *FeedlyCTL* (`Copito's Cronjob`)\n\n*$Feed_Title*\n\n$Feed_Description",[
                        "inline_keyboard" => [
                            [
                                [
                                    "text" => "Ver publicación",
                                    "url" => "$Feed_Link"
                                ]
                            ]
                        ]
                    ]);
                }
                else
                {
                    telegram_sendmessage(COPITO_GROUP_ID,"🗞 *FeedlyCTL* (`Copito's Cronjob`)\n\n*$Feed_Title*\n\n$Feed_Description\n\n$Feed_Link",[
                        "inline_keyboard" => [
                            [
                                [
                                    "text" => "Ver publicación",
                                    "url" => "$Feed_Link"
                                ]
                            ]
                        ]
                    ]);
                }

                $Feed_LastDate[md5($Feed_Url)] = $Feed_Date;

                break;
            }
        }

        update_option('wp_copito_rss_time',json_encode($Feed_LastDate));

        update_option('wp_copito_rss',date('d'));
    }
}

function Copito_Pet_Status($Telegram_Chat,$Message_User_ID,$Message_User_Name_First,$Message_User_Name_Last)
{
    $Copito_Loves = get_option('wp_copito_pet_loves')?:0;
    $Copito_Hunga = get_option('wp_copito_pet_hunga')?:0;

    $Str_Loves = "*Hugiiii:* ($Copito_Loves/5) ";
    $Str_Hunga = "*Hunga:* ($Copito_Hunga/5) ";

    $Text = [];

    $Text[] = "🐋 *CopitoCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n";

    $Text[] = "🐳🐳 Daaaaaaaaaaaaaaaa 🐳🐳\n";

    for($i=0;$i<5;$i++)
    {
        if($Copito_Loves>$i)
        {
            $Str_Loves .= "❤ ";
        }
        else
        {
            $Str_Loves .= "🖤 ";
        }
    }
    for($i=0;$i<5;$i++)
    {
        if($Copito_Hunga>$i)
        {
            $Str_Hunga .= "🍣 ";
        }
        else
        {
            $Str_Hunga .= "🍽 ";
        }
    }

    $Text[] = $Str_Loves;
    $Text[] = $Str_Hunga;

    telegram_sendmessage($Telegram_Chat,implode("\n",$Text));
}
function Copito_Pet_Restores()
{
    $Copito_Loves = get_option('wp_copito_pet_loves')?:0;
    $Copito_Hunga = get_option('wp_copito_pet_hunga')?:0;

    $Restores_Rand = round(rand(1,8000));

    if($Restores_Rand<60)
    {
        $Restores_Type =  random_int(1,2);
        if($Restores_Type==1){
            update_option('wp_copito_pet_loves',$Copito_Loves-1<0?0:$Copito_Loves-1);
        } else {
            update_option('wp_copito_pet_hunga',$Copito_Hunga-1<0?0:$Copito_Hunga-1);
        }
    }
}
function Copito_Pet_Says($Telegram_Chat,$Force_Say=false,$Message_User_ID=false,$Message_User_Name_First=false,$Message_User_Name_Last=false)
{
    $Copito_Loves = get_option('wp_copito_pet_loves')?:0;
    $Copito_Hunga = get_option('wp_copito_pet_hunga')?:0;

    $Says_Rand = round(rand(1,2500));

    $Says_Suffix = $Force_Say?" [$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID)":"";

    if(($Copito_Hunga<=2 && $Says_Rand<50) || $Force_Say===2)
    {
        switch (random_int(1,40))
        {
            case 1:
                telegram_sendmessage($Telegram_Chat,trim(sprintf(Copito_Words_Hunga[random_int(0,count(Copito_Words_Hunga)-1)],'')));
                break;
            case 2:
                telegram_sendmessage($Telegram_Chat,trim(sprintf(Copito_Words_Hunga_Emoji[random_int(0,count(Copito_Words_Hunga_Emoji)-1)],'')));
                break;
            default:
                telegram_sendmessage($Telegram_Chat,trim(sprintf(Copito_Words_Hunga[random_int(0,count(Copito_Words_Hunga)-1)],Copito_Words_Hunga_Emoji[random_int(0,count(Copito_Words_Hunga_Emoji)-1)])));
                break;
        }
    }
    elseif($Says_Rand<50 || $Force_Say===true)
    {
        switch($Copito_Loves)
        {
            case 0:
            case 1:
                switch (random_int(1,10))
                {
                    case 1:
                        telegram_sendmessage($Telegram_Chat,trim(sprintf(Copito_Words_Tired[random_int(0,count(Copito_Words_Tired)-1)],$Says_Suffix)));
                        break;
                    case 2:
                        telegram_sendmessage($Telegram_Chat,trim(sprintf(Copito_Words_Tired_Emoji[random_int(0,count(Copito_Words_Tired_Emoji)-1)],$Says_Suffix)));
                        break;
                    default:
                        telegram_sendmessage($Telegram_Chat,trim(sprintf(Copito_Words_Tired[random_int(0,count(Copito_Words_Tired)-1)],$Says_Suffix.Copito_Words_Tired_Emoji[random_int(0,count(Copito_Words_Tired_Emoji)-1)])));
                        break;
                }
                break;
            case 2:
            case 3:
                switch (random_int(1,40))
                {
                    case 1:
                        telegram_sendmessage($Telegram_Chat,trim(sprintf(Copito_Words_Normal[random_int(0,count(Copito_Words_Normal)-1)],$Says_Suffix)));
                        break;
                    case 2:
                        telegram_sendmessage($Telegram_Chat,trim(sprintf(Copito_Words_Normal_Emoji[random_int(0,count(Copito_Words_Normal_Emoji)-1)],$Says_Suffix)));
                        break;
                    default:
                        telegram_sendmessage($Telegram_Chat,trim(sprintf(Copito_Words_Normal[random_int(0,count(Copito_Words_Normal)-1)],$Says_Suffix.Copito_Words_Normal_Emoji[random_int(0,count(Copito_Words_Normal_Emoji)-1)])));
                        break;
                }
                break;
            case 4:
            case 5:
                switch (random_int(1,40))
                {
                    case 1:
                        telegram_sendmessage($Telegram_Chat,trim(sprintf(Copito_Words_Loves[random_int(0,count(Copito_Words_Loves)-1)],$Says_Suffix)));
                        break;
                    case 2:
                        telegram_sendmessage($Telegram_Chat,trim(sprintf(Copito_Words_Loves_Emoji[random_int(0,count(Copito_Words_Loves_Emoji)-1)],$Says_Suffix)));
                        break;
                    default:
                        telegram_sendmessage($Telegram_Chat,trim(sprintf(Copito_Words_Loves[random_int(0,count(Copito_Words_Loves)-1)],$Says_Suffix.Copito_Words_Loves_Emoji[random_int(0,count(Copito_Words_Loves_Emoji)-1)])));
                        break;
                }
                break;
        }
    }
}
function Copito_Meme($Telegram_Chat,$Message_User_ID=false,$Message_User_Name_First=false,$Message_User_Name_Last=false)
{
    $NoFlood = 0;//get_transient( 'copito_flood' )?:0;

    $NoFlood = $NoFlood+1;

    if($NoFlood<=4) set_transient( 'copito_flood', $NoFlood, 1800);
    if($NoFlood>=4) telegram_sendmessage($Telegram_Chat,'Fluddy 😭');

    if($NoFlood<4)
    {
        $Copito_Loves = get_option('wp_copito_pet_loves')?:0;
        $Copito_Hunga = get_option('wp_copito_pet_hunga')?:0;

        if($Copito_Loves<=2 || $Copito_Hunga<=2)
        {
            Copito_Pet_Says($Telegram_Chat,2,$Message_User_ID,$Message_User_Name_First,$Message_User_Name_Last);
        }
        else
        {
            $Memes = [];
            foreach (glob(WP_CONTENT_DIR."/uploads/copito/memes/*") as $File_Path)
            {
                $File_Name = basename($File_Path);

                $Memes[] = COPITO_PUBLIC."/memes/$File_Name";
            }

            $Memes_Rand = random_int(0,count($Memes)-1);

            telegram_sendphoto($Telegram_Chat,'',$Memes[$Memes_Rand]);

            $Restores_Rand = random_int(1,3);

            if($Restores_Rand==1)
            {
                update_option('wp_copito_pet_hunga',$Copito_Hunga-1<0?0:$Copito_Hunga-1);
            }
            else
            {
                update_option('wp_copito_pet_loves',$Copito_Loves-1<0?0:$Copito_Loves-1);
            }
        }
    }
}
function Copito_Chiste($Telegram_Chat,$Message_User_ID=false,$Message_User_Name_First=false,$Message_User_Name_Last=false)
{
    $NoFlood = 0;//get_transient( 'copito_flood' )?:0;

    $NoFlood = $NoFlood+1;

    if($NoFlood<=4) set_transient( 'copito_flood', $NoFlood, 1800);
    if($NoFlood>=4) telegram_sendmessage($Telegram_Chat,'Duuu 😭');

    if($NoFlood<4)
    {
        $Copito_Loves = get_option('wp_copito_pet_loves')?:0;
        $Copito_Hunga = get_option('wp_copito_pet_hunga')?:0;

        if($Copito_Loves<=2 || $Copito_Hunga<=2)
        {
            Copito_Pet_Says($Telegram_Chat,2,$Message_User_ID,$Message_User_Name_First,$Message_User_Name_Last);
        }
        else
        {
            $Chistes_Data = explode("\n\n",file_get_contents(WP_CONTENT_DIR."/uploads/copito/chistes.txt"));


            $Chistes_Rand = random_int(0,count($Chistes_Data)-1);

            telegram_sendmessage($Telegram_Chat,$Chistes_Data[$Chistes_Rand]);

            $Restores_Rand = random_int(1,3);

            if($Restores_Rand==1)
            {
                update_option('wp_copito_pet_hunga',$Copito_Hunga-1<0?0:$Copito_Hunga-1);
            }
            else
            {
                update_option('wp_copito_pet_loves',$Copito_Loves-1<0?0:$Copito_Loves-1);
            }
        }
    }
}
function Copito_Random_Meme()
{
    $Says_Rand = round(rand(1,5000));

    if($Says_Rand<10)
    {
        $Rand_Type = random_int(1,2);
        if($Rand_Type==1)
        {
            Copito_Meme(COPITO_GROUP_ID);
        }
        else
        {
            Copito_Chiste(COPITO_GROUP_ID);
        }
    }
}

function Copito_Viernes_Service()
{
    $KUI3 = new KUI_REST([]);

    if((date('N')==5) || (date('N')==6 && date('H')<12))
    {
        $Last_Sequence = get_option('wp_copito_viernes');
        if($Last_Sequence!=date('W'))
        {
            update_option('wp_copito_viernes',date('W'));

            $Text = [];
            $Text[] = "*¡Es #ViernesDeEscritorio* 🥳!";
            $Text[] = "Empieza la sesión de escritorios dirigida por Copito en la cuál todos los miembros son invitados a participar.";
            $Text[] = "Publica un Screenshot de tu escritorio con el hashtag #ViernesDeEscritorio y cualquier comentario que quieras hacer para participar en el evento. Los demás podrán responder con un \"+1\" para realizar votaciones. También podrán publicar comentarios desde la página web dónde también será publicado el escritorio. Con el comando /viernes_de_escritorio podrás consultar la tabla de puntuaciones de cada semana.";
            $Text[] = "Dubi daaaaaaaaaaaa 💜";

            telegram_sendmessage(COPITO_GROUP_ID,implode("\n\n",$Text));
        }
    }
    elseif(date('N')==6 && date('H')>=12)
    {
        $Last_Sequence = get_option('wp_copito_viernes_end');
        if($Last_Sequence!=date('W'))
        {
            update_option('wp_copito_viernes_end',date('W'));

            $Query = new WP_Query([
                'post_type'  => 'viernesdeescritorio',
                'orderby'    => [
                    'Score_Clause' => 'DESC',
                    'Time_Clause' => 'ASC',
                ],
                'meta_query' => [
                    'Score_Clause' => [
                        'key' => 'score',
                        'compare' => 'EXISTS',
                        'type'          => 'NUMERIC'
                    ],
                    'Time_Clause' => [
                        'key' => 'vtimestamp',
                        'value' => '-1',
                        'compare' => '>=',
                        'type' => 'NUMERIC',
                    ],
                ],
                'date_query' => array(
                    [
                        'year'  => date('Y'),
                        'week' => date('W'),
                    ],
                ),
                'posts_per_page' => 10
            ]);
            if ($Query->have_posts())
            {
                $Scores = [];
                $Scores[] = "Se acabó el evento #ViernesDeEscritorio 😞 A continuación, se adjunta tabla de puntuaciones de esta semana 🥳🥳🥳.";
                $N = 1;
                while ($Query->have_posts())
                {
                    $Query->the_post();

                    $Score = get_post_meta(get_the_ID(),'score',true)?:'0';


                    $Autor_KUI_ID       = (int)get_post_meta(get_the_ID(),'kui_user_id',true)?:0;
                    $Autor_Telegram_ID  = (int)get_post_meta(get_the_ID(),'telegram_user_id',true)?:0;
                    $Desktop_Msg_ID     = (int)get_post_meta(get_the_ID(),'message_id',true)?:0;

                    $Author_Display_Name = $KUI3->Displays_Users_Name($Autor_KUI_ID,$Autor_Telegram_ID);
                    $Author_Display_URL  = $KUI3->Displays_Users_URL($Autor_KUI_ID,$Autor_Telegram_ID);
                    $Author_Display_Rendered = "[$Author_Display_Name]($Author_Display_URL)";

                    $Desktop_Rendered = "[Ver escritorio](tg://privatepost?channel=".COPITO_GROUP_ID_PRIV."&post=$Desktop_Msg_ID)";

                    $Scores[] = "$N. $Author_Display_Rendered: *$Score* voto".($Score==1?'':'s').". $Desktop_Rendered";

                    $N++;
                }

                $Scores[] = "\nGracias por participar 💜 Copito os abraza a todos los participantes 🤗.";

                telegram_sendmessage(COPITO_GROUP_ID,implode("\n\n",$Scores),false,true);
            }
        }
    }
}
