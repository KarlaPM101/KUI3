<?php
/** @var array      $Message_Data */
/** @var int        $Message_ID */
/** @var int        $Telegram_Chat */
/** @var string     $Message_Text */
/** @var int        $Message_User_ID */
/** @var string     $Message_User_Name_First */
/** @var string     $Message_User_Name_Last */
/** @var bool       $Is_File */
/** @var bool       $Is_Image */
/** @var bool       $Is_Reply */

function Copito_Viernes_Desktop($Telegram_Chat)
{
    $Query = new WP_Query([
        'post_type'      => 'viernesdeescritorio',
        'orderby'        => 'rand',
        'posts_per_page' => 1,
    ]);
    if($Query->have_posts())
    {
        while ($Query->have_posts())
        {
            $Query->the_post();

            $Desktop_URI = get_the_post_thumbnail_url(get_the_ID(),'large');
            $Desktop_User_ID = get_post_meta(get_the_ID(),'telegram_user_id',true);

            if(!$WP_User_ID = telegram_getid($Desktop_User_ID)) break;

            $Desktop_User_Name = get_post_meta($WP_User_ID,'telegram_first_name',true);
            $Desktop_User_Last = get_post_meta($WP_User_ID,'telegram_last_name',true);

            $Desktop_Score = get_post_meta(get_the_ID(),'score',true)?:'0';

            $Desktop_Week  = get_post_meta(get_the_ID(),'week',true);

            $Display_Name = trim("$Desktop_User_Name $Desktop_User_Last");

            $Post_Data = get_post(get_the_ID());
            $Post_Slug = $Post_Data->post_name;

            telegram_sendphoto($Telegram_Chat,'',$Desktop_URI);
            telegram_sendmessage($Telegram_Chat,"#Escritorio\n\n*Usuario:* [$Display_Name](tg://user?id=$Desktop_User_ID)\n*Puntos:* *$Desktop_Score puntos.*",
                [
                "inline_keyboard" => [
                    [
                        [
                            "text" => "Votar +1",
                            "callback_data" => "Viernes_Upvote_{$Desktop_Week}_{$Desktop_User_ID}"
                        ],
                        [
                            "text" => "Ver en la Web",
                            "url" => "https://karlaperezyt.com/viernesdeescritorio/$Post_Slug"
                        ]
                    ]
                ]
            ]);
        }
    }
}
function Copito_Viernes_RandomSay()
{
    $Says_Rand = round(rand(1,5500));
    if($Says_Rand<50)
    {
        Copito_Viernes_Desktop(COPITO_GROUP_ID);
    }
}


if ( ! defined( 'ABSPATH' ) ) exit;

Copito_Viernes_RandomSay();


// SUBIR VIERNES DE ESCRITORIO
if(($Is_Image || $Is_File) && isset($Message_Data['message']['caption']) && (strpos(strtolower($Message_Data['message']['caption']),'#viernesdeescritorio')!==false || strpos(strtolower($Message_Data['message']['caption']),'#escritorio')!==false || strpos(strtolower($Message_Data['message']['caption']),'#viernesescritorio')!==false || strpos(strtolower($Message_Data['message']['caption']),'#viernesdeescritorios')!==false))
{
    if((date('N')==5) || (date('N')==6 && date('H')<12))
    {
        if($Is_Image)
        {
            $Photos_Sizes = count($Message_Data['message']['photo'])-1;
        }
        else
        {
            $Photos_Sizes = count($Message_Data['message']['document']);
        }

        if(($Is_Image && isset($Message_Data['message']['photo'][$Photos_Sizes])) || ($Is_File && isset($Message_Data['message']['document'])))
        {
            if($Is_File)
            {
                $Photo_Info = $Message_Data['message']['document'];
            }
            else
            {
                $Photo_Info = $Message_Data['message']['photo'][$Photos_Sizes];
            }
            $Photo_ID = $Photo_Info['file_id'];
            $Photo_Data = Copito_Files_Download($Photo_ID);
            if(($Is_File && ($Photo_Info['mime_type']!='image/png' && $Photo_Info['mime_type']!='image/jpg' && $Photo_Info['mime_type']!='image/jpeg')))
            {
                $Photo_Data = false;
                telegram_sendmessage($Telegram_Chat,"🎨 *ViernesCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nLa imagen debe de ser PNG o JPG.");
            }
            if($Photo_Data)
            {
                $Week = date('W');
                $Year = date('Y');
                $filepath = WP_CONTENT_DIR."/uploads/kui_system/desktops/{$Year}/{$Week}_{$Message_User_ID}.png";
                if(file_exists($filepath))
                {
                    telegram_sendmessage($Telegram_Chat,"🎨 *ViernesCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nYa has publicado una imagen esta semana.");
                }
                else
                {
                    $PostNew = wp_insert_post([
                        'post_title'    => sprintf('Viernes de Escritorio #%1$s por %2$s',$Week,trim($Message_User_Name_First.' '.$Message_User_Name_Last)),
                        'post_content'  => sprintf('🖌 #ViernesDeEscritorio - Escritorio de %1$s',trim($Message_User_Name_First.' '.$Message_User_Name_Last)),
                        'post_type'     => 'viernesdeescritorio',
                        'post_status'   => 'publish',
                        'post_author'   => 1,
                    ]);
                    update_post_meta($PostNew, 'year',$Year);
                    update_post_meta($PostNew, 'week',$Week);
                    update_post_meta($PostNew, 'message_id',$Message_ID);
                    update_post_meta($PostNew, 'message_text',$Message_Data['message']['caption']);
                    update_post_meta($PostNew, 'telegram_user_id',$Message_User_ID);
                    update_post_meta($PostNew, 'score',0);
                    update_post_meta($PostNew, 'vtimestamp',0);

                    file_put_contents($FilePath = WP_CONTENT_DIR."/uploads/kui_system/desktops/{$Year}/{$Week}_{$Message_User_ID}.png",$Photo_Data);

                    $MimeType = wp_check_filetype($FilePath, null);
                    $Attachment = [
                        'post_mime_type'    => $MimeType['type'],
                        'post_parent'       => $PostNew,
                        'post_title'        => sanitize_file_name($FilePath),
                        'post_content'      => '',
                        'post_status'       => 'inherit'
                    ];
                    $Attachment_ID = wp_insert_attachment($Attachment,$FilePath,$PostNew );
                    set_post_thumbnail($PostNew,$Attachment_ID );

                    if (!function_exists('wp_crop_image')) {
                        include(ABSPATH.'wp-admin/includes/image.php');
                    }
                    wp_generate_attachment_metadata($Attachment_ID,$FilePath);

                    $Folders_ID = FileBird\Model\Folder::newOrGet('ViernesDeEscritorio',FileBird\Model\Folder::newOrGet('KUI3 System',0));
                    FileBird\Model\Folder::setFoldersForPosts($Attachment_ID,$Folders_ID);

                    $PostData = get_post($PostNew);
                    $PostSlug = $PostData->post_name;

                    $MessageSent = Copito_Message_Image($Telegram_Chat,get_the_post_thumbnail_url($PostNew,'full'),"🎨 *ViernesCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nSe ha publicado el escritorio.\nResponde \"+1\" para votar.",
                        [
                            "inline_keyboard" => [
                                [
                                    [
                                        "text" => "Votar +1",
                                        "callback_data" => "Viernes_Upvote_ID_{$PostNew}"
                                    ],

                                ],[
                                    [
                                        "text" => "Ver Escritorio",
                                        "url" => "tg://privatepost?channel=".COPITO_GROUP_ID_PRIV."&post=$Message_ID"
                                    ],
                                    [
                                        "text" => "Ver en la Web",
                                        "url" => "https://karlaperezyt.com/viernesdeescritorio/$PostSlug"
                                    ]
                                ]
                            ]
                        ],$Message_ID);
                    update_post_meta($PostNew, 'message_reference',$MessageSent);
                }
            }
        }
    }
    else
    {
        telegram_sendmessage($Telegram_Chat,"🎨 *ViernesCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\n*¿Quieres participar en el #ViernesDeEscritorio?* Publica tu escritorio el viernes con el hastag #ViernesDeEscritorio y tu escritorio será publicado en la web y los usuarios podrán comentar (desde la web) y votar positivamente (respondiendo \"+1\") tu escritorio de GNU/Linux.");
    }
}

// UPVOTES
if($Is_Reply && (strpos(strtolower($Message_Text),'+1')!==false || preg_match('/^\/upvote(@.*)?$/is',$Message_Text,$Matches))!==false)
{
    $Reply_Data = $Message_Data['message']['reply_to_message'];

    if(isset($Reply_Data['message_id']) && isset($Message_Data['message']['reply_to_message']['caption']))
    {
        Copito_Message_Delete(COPITO_GROUP_ID,$Message_ID);

        $Reply_Message_ID = $Reply_Data['message_id'];

        $Query = new WP_Query([
            'post_type'     => 'viernesdeescritorio',
            'meta_query'    => [
                [
                    'key'       => 'message_id',
                    'value'     => $Reply_Message_ID,
                    'meta_compare' => '=',
                    'type' => 'NUMERIC'
                ]
            ]
        ]);
        if(!$Query->have_posts())
        {
            $Query = new WP_Query([
                'post_type'     => 'viernesdeescritorio',
                'meta_query'    => [
                    [
                        'key'       => 'message_reference',
                        'value'     => $Reply_Message_ID,
                        'meta_compare' => '=',
                        'type' => 'NUMERIC'
                    ]
                ]
            ]);
        }

        $Last = get_option('wp_copito_lastfriday_msg');
        Copito_Message_Delete(COPITO_GROUP_ID,$Last);

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
                    $Snd_Id =  telegram_sendmessage($Telegram_Chat,"🎨 *ViernesCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nYa has votado esta publicación.",false,true,true);
                    update_option('wp_copito_lastfriday_msg',$Snd_Id);
                    break;
                }

                $Week = get_post_meta(get_the_ID(),'week',true);

                $Score = get_post_meta(get_the_ID(),'score',true);
                update_post_meta(get_the_ID(),'score',$Score+1);

                update_post_meta(get_the_ID(),'vtimestamp',time());

                update_post_meta(get_the_ID(),'scorers',$Scorers.';'.$Message_User_ID);

                $Autor_ID = get_post_meta(get_the_ID(),'telegram_user_id',true);
                $Internal_ID = telegram_getid($Autor_ID);

                if(!$Internal_ID) break;

                $Autor_Name = get_post_meta($Internal_ID,'telegram_first_name',true);
                $Autor_Last = get_post_meta($Internal_ID,'telegram_last_name',true);

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
            $Snd_Id = telegram_sendmessage($Telegram_Chat,"🎨 *ViernesCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nNo ha sido posible realizar la votación. Escritorio no encontrado.",false,true,true);
            update_option('wp_copito_lastfriday_msg',$Snd_Id);
        }
    }
}





// TABLA DE PUNTUACIONES
if(preg_match('/^\/viernes_de_escritorio(@.*)?$/is',$Message_Text,$Matches) || preg_match('/^\/podium(@.*)?$/is',$Message_Text,$Matches))
{
    $KUI3 = new KUI_REST([]);

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
        'posts_per_page' => 100
    ]);
    if ($Query->have_posts())
    {
        $Scores = [];
        $Scores[] = "🎨 *ViernesCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nTabla de puntuaciones para esta semana.\n";
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

        telegram_sendmessage($Telegram_Chat,implode("\n\n",$Scores),[
            "inline_keyboard" => [
                [
                    [
                        "text" => "Ver Escritorios",
                        "url" => 'https://karlaperezyt.com/viernesdeescritorio/'.date('W').'/'.date('Y').''
                    ]
                ]
            ]
        ],true);
    }
    else
    {
        telegram_sendmessage($Telegram_Chat,"🎨 *ViernesCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\n *Aún no se han publicado escritorios esta semana.* \n\n*¿Quieres participar en el #ViernesDeEscritorio?* Publica tu escritorio el viernes con el hastag #ViernesDeEscritorio y tu escritorio será publicado en la web y los usuarios podrán comentar (desde la web) y votar positivamente (respondiendo \"+1\") tu escritorio de GNU/Linux.");
    }
}

// VOTO ESPECIFICO
if(preg_match('/^\/upvote ([0-9]{2})_([0-9]{1,})$/is',$Message_Text,$Matches))
{
    $Command_Week = $Matches[1];
    $Command_User_Id = $Matches[2];

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

    $Last = get_option('wp_copito_lastfriday_msg');
    Copito_Message_Delete(COPITO_GROUP_ID,$Last);

    Copito_Message_Delete(COPITO_GROUP_ID,$Message_ID);

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

// MY DESKTOP
if(preg_match('/^\/escritorio (.*)$/is',$Message_Text,$Matches)){
    $UserQuery_Str  = $Matches[1];
    $UserQuery_Data = Copito_Member_Query2ID($UserQuery_Str);

    $QueryLast = new WP_Query([
        'post_type' => 'viernesdeescritorio',
        'meta_query' => [
            [
                'key' => 'telegram_user_id',
                'value' => (int)$UserQuery_Data
            ]
        ],
        'posts_per_page' => 1
    ]);

    if($QueryLast->have_posts()){
        while ($QueryLast->have_posts()){
            $QueryLast->the_post();

            $Desktop_Author = (int)get_post_meta(get_the_ID(),'telegram_user_id',true);

            $KUI3 = new KUI_REST([]);

            $Desktop_Author_Name = $KUI3->Displays_Users_Name(0,$Desktop_Author);
            $Desktop_Author_Rendered = "[$Desktop_Author_Name](tg://user?id=$Desktop_Author)";


            $Desktop_ID = get_the_ID();
            $Desktop_Message = get_post_meta(get_the_ID(),'message_text',true);
            $Desktop_Message_Filtered = str_replace('#','',$Desktop_Message);
            $Desktop_Message_Reference = get_post_meta(get_the_ID(),'message_id',true);


            $MessageSent = Copito_Message_Image($Telegram_Chat,get_the_post_thumbnail_url($Desktop_ID,'large'),"🎨 *ViernesCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\n*Autor*: $Desktop_Author_Rendered\n\n$Desktop_Message_Filtered",
                [
                    "inline_keyboard" => [
                        [
                            [
                                "text" => "Votar +1",
                                "callback_data" => "Viernes_Upvote_ID_{$Desktop_ID}"
                            ],

                        ],[
                            [
                                "text" => "Ver Escritorio",
                                "url" => "tg://privatepost?channel=".COPITO_GROUP_ID_PRIV."&post=$Desktop_Message_Reference"
                            ],
                            [
                                "text" => "Ver en la Web",
                                "url" => get_the_permalink(get_the_ID())
                            ]
                        ]
                    ]
                ],$Desktop_Message_Reference);
        }
    }
    else {
        Copito_Message_Send($Telegram_Chat,"🎨 *ViernesCTL* ([$Message_User_Name_First $Message_User_Name_Last](tg://user?id=$Message_User_ID))\n\nNo se han encontrado escritorios.");
    }




}
elseif(preg_match('/^\/escritorio(@.*)?$/is',$Message_Text,$Matches))
{
    Copito_Viernes_Desktop($Telegram_Chat);
}

