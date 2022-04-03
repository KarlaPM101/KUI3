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
/** @var bool       $Is_Edition */
/** @var bool       $Role_Is_Moderator_Junior */
/** @var bool       $Role_Is_Moderator_Senior */
/** @var bool       $Role_Is_Moderator_MemCtl */

if ( ! defined( 'ABSPATH' ) ) exit;

if(preg_match('/^\/copito(@.*)?$/is',$Message_Text,$Matches))
{
    Copito_Pet_Status($Telegram_Chat,$Message_User_ID,$Message_User_Name_First,$Message_User_Name_Last);
}
if(preg_match('/^\/saludar(@.*)?$/is',$Message_Text,$Matches) || preg_match('/copito/is',$Message_Text,$Matches))
{
    Copito_Pet_Says($Telegram_Chat,true,$Message_User_ID,$Message_User_Name_First,$Message_User_Name_Last);
}
if(preg_match('/^\/abrazar(@.*)?$/is',$Message_Text,$Matches))
{
    $Copito_Loves = get_option('wp_copito_pet_loves')?:0;

    $Copito_Loves++;

    if($Copito_Loves>5) $Copito_Loves = 5;

    //update_option('wp_copito_pet_loves',$Copito_Loves);
    update_option('wp_copito_pet_loves',5);

    $Text = [];

    $Text[] = "🐋 *CopitoCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n";

    $Text[] = "Hugiiiiii *+++* ❤";
    $Text[] = "";

    $Copito_Loves = get_option('wp_copito_pet_loves')?:0;
    $Copito_Hunga = get_option('wp_copito_pet_hunga')?:0;

    $Str_Loves = "*Hugiiii:* ($Copito_Loves/5) ";
    $Str_Hunga = "*Hunga:* ($Copito_Hunga/5) ";

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
if(preg_match('/^\/comida(@.*)?$/is',$Message_Text,$Matches))
{
    $Copito_Hunga = get_option('wp_copito_pet_hunga')?:0;

    $Copito_Hunga++;

    if($Copito_Hunga>5) $Copito_Hunga = 5;

    //update_option('wp_copito_pet_hunga',$Copito_Hunga);
    update_option('wp_copito_pet_hunga',5);

    $Text = [];

    $Text[] = "🐋 *CopitoCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n";

    $Text[] = "Hungaaa *+++* 🍣";
    $Text[] = "";

    $Copito_Loves = get_option('wp_copito_pet_loves')?:0;
    $Copito_Hunga = get_option('wp_copito_pet_hunga')?:0;

    $Str_Loves = "*Hugiiii:* ($Copito_Loves/5) ";
    $Str_Hunga = "*Hunga:* ($Copito_Hunga/5) ";

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

if(preg_match('/^\/meme(@.*)?$/is',$Message_Text,$Matches))
{
    Copito_Meme($Telegram_Chat,$Message_User_ID,$Message_User_Name_First,$Message_User_Name_Last);
}
if(preg_match('/^\/chiste(@.*)?$/is',$Message_Text,$Matches))
{
    Copito_Chiste($Telegram_Chat,$Message_User_ID,$Message_User_Name_First,$Message_User_Name_Last);
}

if(preg_match('/^\/dharma(@.*)?$/is',$Message_Text,$Matches) || ($Is_Reply && !$Is_Edition && (strpos(strtolower($Message_Text),'+1')!==false) && !isset($Message_Data['message']['reply_to_message']['caption'])))
{
    if(isset($Message_Data['message']['reply_to_message']['from']['id']))
    {
        $Command_Subject_ID = $Message_Data['message']['reply_to_message']['from']['id'];
        $Command_Subject_Name = $Message_Data['message']['reply_to_message']['from']['first_name'];
        $Command_Subject_Last = $Message_Data['message']['reply_to_message']['from']['last_name'];

        $Internal_ID = telegram_getid($Command_Subject_ID);

        if($Internal_ID)
        {
            if($Command_Subject_ID==$Message_User_ID)
            {
                telegram_sendmessage($Telegram_Chat,"🐋 *CopitoCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nNo puedes darte puntos Dharma a ti mismo.");
            }
            else
            {
                $Current_Karma = get_post_meta($Internal_ID,'copito_karma',true)?:0;
                $Current_Karma++;

                update_post_meta($Internal_ID,'copito_karma',$Current_Karma);

                $Text = [];

                $Text[] = "🐋 *CopitoCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n";

                $Text[] = "DHARMA *+* *1* 💐";

                $Text[] = "*Usuario:* [$Command_Subject_Name $Command_Subject_Last](tg://user?id=$Command_Subject_ID)";
                $Text[] = "*Dharma Total:* $Current_Karma puntos";

                telegram_sendmessage($Telegram_Chat,implode("\n",$Text));
            }
        }
    }
    else
    {
        $Query = new WP_Query([
            'post_type'  => 'telegram_subscribers',
            'orderby'    => 'meta_value_num',
            'order' => 'DESC',
            'meta_key'  => 'copito_karma',
            'posts_per_page' => 20
        ]);
        if ($Query->have_posts())
        {
            $Scores = [];
            $Scores[] = "🐋 *CopitoCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\n💐💐 Tabla de usuarios por DHARMA 💐💐";
            $N = 1;
            while ($Query->have_posts())
            {
                $Query->the_post();

                $Score = get_post_meta(get_the_ID(),'copito_karma',true)?:'0';

                $Autor_ID = get_the_title();
                $Internal_ID = telegram_getid($Autor_ID);

                if(!$Internal_ID) break;

                $Autor_Name = get_post_meta($Internal_ID,'telegram_first_name',true);
                $Autor_Last = get_post_meta($Internal_ID,'telegram_last_name',true);

                $Scores[] = "$N. ([$Autor_Name $Autor_Last](tg://user?id=$Autor_ID)): *$Score* puntos dharma";

                $N++;
            }

            telegram_sendmessage($Telegram_Chat,implode("\n\n",$Scores),false,true);
        }
    }
}

if(preg_match('/^\/perfil(@.*)?$/is',$Message_Text,$Matches))
{
    $Text = [];
    $Dates_Arr = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

    $Text[] = "🐋 *CopitoCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n";

    $Internal_ID = telegram_getid($Message_User_ID);

    $Profile_Friday_Score = 0;
    if($Internal_ID)
    {
        $Query_Viernes = new WP_Query([
            'post_type'     => 'viernesdeescritorio',
            'posts_per_page' => -1,
            'meta_query'    => [
                [
                    'key'       => 'telegram_user_id',
                    'value'     => (int)$Message_User_ID,
                    'meta_compare' => '=',
                    'type' => 'NUMERIC'
                ]
            ]
        ]);
        if ($Query_Viernes->have_posts())
        {
            while ($Query_Viernes->have_posts())
            {
                $Query_Viernes->the_post();
                $Profile_Friday_Score += get_post_meta(get_the_ID(),'score',true)?:0;
            }
        }

        $Profile_Name = get_post_meta($Internal_ID,'telegram_first_name',true);
        $Profile_Last = get_post_meta($Internal_ID,'telegram_last_name',true);
        $Profile_Display_Name = trim($Profile_Name.' '.$Profile_Last);
        $Profile_Display_Userid = get_post_meta($Internal_ID,'telegram_username',true)?'@'.get_post_meta($Internal_ID,'telegram_username',true):'sin definir';
        $Profile_Count = get_post_meta($Internal_ID,'telegram_custom',true)?:'0';
        $Profile_Date  = sprintf('%1$s de %2$s del %3$s',get_the_date('d',$Internal_ID),$Dates_Arr[get_the_date('n',$Internal_ID)-1],get_the_date('Y',$Internal_ID));
        $Profile_Last  = sprintf('%1$s de %2$s del %3$s',get_the_modified_date('d',$Internal_ID),$Dates_Arr[get_the_modified_date('n',$Internal_ID)-1],get_the_modified_date('Y',$Internal_ID));
        $Profile_Time  = sprintf('%1$s : %2$s : %3$s',get_the_modified_date('H',$Internal_ID),get_the_modified_date('i',$Internal_ID),get_the_modified_date('s',$Internal_ID));
        $Profile_Score  = get_post_meta($Internal_ID,'copito_karma',true)?:'0';
        $Profile_Status  = json_decode(get_post_meta($Internal_ID,'telegram_status',true)?:[],true);

        $Profile_Role = 'Usuario';
        if(isset($Profile_Status['Is_Junior']) && $Profile_Status['Is_Junior']) $Profile_Role = 'Moderador Junior';
        if(isset($Profile_Status['Is_Senior']) && $Profile_Status['Is_Senior']) $Profile_Role = 'Moderador Senior';
        if(isset($Profile_Status['Is_MemCtl']) && $Profile_Status['Is_MemCtl']) $Profile_Role = 'Administrador Mem';
        if($Message_User_ID==605772539) $Profile_Role = 'Creadora / YouTuber';

        if (file_exists(WP_CONTENT_DIR . '/uploads/telegram/' . $Message_User_ID . '.jpg'))
            $Profile_Display_Image = 'https://karlaperezyt.com/wp-content/uploads/telegram/' . $Message_User_ID . '.jpg';
        else
            $Profile_Display_Image = 'https://karlaperezyt.com/wp-content/uploads/2020/07/avatar.jpg';

        $Text[] = "*Nombre completo:* $Profile_Display_Name";
        $Text[] = "*Identificador:* $Profile_Display_Userid";
        $Text[] = "*Fecha de Registro:* $Profile_Date";
        $Text[] = "*Última conexión:* $Profile_Last $Profile_Time UTC";
        $Text[] = "*Nº de Mensajes:* ".number_format($Profile_Count);
        $Text[] = "*Puntos Dharma:* ".$Profile_Score.' '.($Profile_Score==1?'punto dharma':'puntos dharma');
        $Text[] = "*Puntos de Escritorio:* ".($Profile_Friday_Score?:0).' '.($Profile_Friday_Score==1?'punto':'puntos');
        $Text[] = "*Rol:* $Profile_Role";
    }
    else
    {
        $Text[] = 'No se ha encontrado el perfil del usuario solicitado.';
    }
    telegram_sendmessage($Telegram_Chat,implode("\n",$Text),[
        "inline_keyboard" => [
            [
                [
                    "text" => "Ver perfil en la Web",
                    "url" => 'https://karlaperezyt.com/telegram/miembros/'.$Message_User_ID
                ]
            ]
        ]
    ]);
}