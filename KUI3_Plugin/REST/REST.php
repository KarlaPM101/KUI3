<?php
class KUI_REST
{
    /*
     * REST KUI3 API
     */

    private $REST_DATA = [
        'sso'  => '',
        'slug' => '',
    ];

    public function __construct($REST_DATA)
    {
        foreach ($REST_DATA as $k => $v) {
            if ($k == 'slug') {
                $this->REST_DATA[$k] = $v;
                continue;
            }
            $this->REST_DATA[$k] = sanitize_text_field($v);
        }
    }

    public function Internal_Call($Api_Route)
    {
        $Data = file_get_contents("https://karlaperezyt.com/wp-json/kui/$Api_Route");

        if ($Data) {
            return json_decode($Data, true);
        }

        return [];
    }

    /*
     * ARTICLES API
     *
     * --
     */

    private $Article_ID   = 0;
    private $Article_Slug = '';

    public function Articles_Exists()
    {
        return get_post_status($this->Article_ID);
    }

    public function Articles_Get()
    {
        if (!$this->REST_DATA['slug']) {
            wp_send_json_error(new WP_Error('article_not_defined', 'No se ha definido el SLUG del artículo'), 400);
        }

        $ArticleSearch = new WP_Query([
            'name'           => urldecode($this->REST_DATA['slug']),
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'post_type'      => ['post', 'videopost', 'noticia', 'colaboracion', 'evento', 'viernesdeescritorio', 'wiki_post'],
        ]);

        if (!$ArticleSearch->have_posts()) {
            wp_send_json_error(new WP_Error('article_not_found', 'No se ha encontrado el artículo', ['slug' => $this->REST_DATA['slug']]), 404);
        }

        $ArticleSearch->the_post();

        setup_postdata(get_the_ID());

        $this->Article_ID = (int) get_the_ID();

        return $this->Article_ID;
    }
    public function Articles_Get_ID()
    {
        return $this->Article_ID;
    }
    public function Articles_Get_Visits()
    {
        if (!$this->Articles_Exists()) {
            return false;
        }
        return number_format(get_post_meta($this->Article_ID, 'kui3_visits', true));
    }
    public function Articles_Get_Comments()
    {
        if (!$this->Articles_Exists()) {
            return false;
        }
        $ArticlesComments = new WP_Query([
            'post_type'  => ['kui_comment'],
            'order'      => 'ASC',
            'meta_query' => [
                [
                    'key'   => 'comment_article_id',
                    'value' => $this->Article_ID,
                ],
            ],
        ]);
        return number_format($ArticlesComments->post_count);
    }
    public function Articles_Get_Score()
    {
        if (!$this->Articles_Exists()) {
            return false;
        }
        if (get_post_type($this->Article_ID) !== 'viernesdeescritorio') {
            return false;
        }

        return number_format(get_post_meta($this->Article_ID, 'score', true));
    }
    public function Articles_Get_Array($gHead, $gContent = false, $gMeta = false, $gUpdates = false)
    {
        $ArticleArray = [];

        $ArticleArray['ID']   = $this->Article_ID;
        $ArticleArray['Type'] = get_post_type($this->Article_ID);

        if ($gHead === true) {
            $ArticleArray['Caption']    = get_the_title($this->Article_ID);
            $ArticleArray['Background'] = get_the_post_thumbnail_url($this->Article_ID, 'full');
            $ArticleArray['Excerpt']    = get_the_excerpt($this->Article_ID);
            $ArticleArray['Date']       = sprintf('%1$s a las %2$s UTC', get_post_time('d \d\e F \d\e\l Y', true, $this->Article_ID, true), get_post_time('H:i', true, $this->Article_ID));
            $ArticleArray['VdE']        = false;

            if ($SEOtitle = get_post_meta($this->Article_ID, '_yoast_wpseo_title', true)) {
                $Rendered                = wpseo_replace_vars($SEOtitle, get_post($this->Article_ID));
                $ArticleArray['Caption'] = substr($Rendered, 0, strpos($Rendered, ' - '));
            }

            if (get_post_type($this->Article_ID) === 'wiki_post') {
                $ArticleArray['Date'] = sprintf('%1$s a las %2$s UTC', get_post_modified_time('d \d\e F \d\e\l Y', true, $this->Article_ID, true), get_post_modified_time('H:i', true, $this->Article_ID));
            }
        }

        if ($gMeta === true) {
            $ArticleArray['Video']   = get_post_meta($this->Article_ID, 'youtube', true);
            $ArticleArray['Fuentes'] = get_post_meta($this->Article_ID, 'fuentes', true);

        }

        if ($gUpdates === true) {
            $ArticleArray['Visits']   = sprintf('%1$s %2$s.', $this->Articles_Get_Visits(), $this->Articles_Get_Visits() === 1 ? 'visita' : 'visitas');
            $ArticleArray['Comments'] = (int) $this->Articles_Get_Comments();
            $ArticleArray['Score']    = (int) $this->Articles_Get_Score();
        }

        if ($gContent === true) {
            if (get_post_type($this->Article_ID) === 'wiki_post' || get_post_meta($this->Article_ID, 'markdown', true)) {
                $ArticleArray['Content'] = apply_filters('the_content', KUI3_Do_Markdown(get_the_content(null, false, $this->Article_ID)));
            } else {
                $ArticleArray['Content'] = apply_filters('the_content', get_the_content(null, false, $this->Article_ID));
            }

            $this->Articles_Update_Visits();
        }

        if ($gContent === true && get_post_type($this->Article_ID) === 'viernesdeescritorio') {
            $ArticleArray['Background'] = get_the_post_thumbnail_url($this->Article_ID, 'full');
        }

        if ($gHead === true && get_post_type($this->Article_ID) === 'viernesdeescritorio') {
            $this->Displays_Set(get_post_meta($this->Article_ID, 'kui_user_id', true), get_post_meta($this->Article_ID, 'telegram_user_id', true));

            $ArticleArray['VdE'] = [];

            $ArticleArray['VdE']['Author_Name']  = $this->Displays_Users_Name();
            $ArticleArray['VdE']['Author_Photo'] = $this->Displays_Users_Photo();
            $ArticleArray['VdE']['Author_Type']  = $this->Displays_Users_Type();
            $ArticleArray['VdE']['Author_ID']    = $this->Displays_Users_ID();
            $ArticleArray['VdE']['Author_Url']   = $this->Displays_Users_URL();
            $ArticleArray['VdE']['Telegram']     = (int) $this->Displays_Users_Type() == 'TELEGRAM' ? $this->Displays_Telegram_ID : 0;
            $ArticleArray['VdE']['Score']        = (int) $this->Articles_Get_Score();
            $ArticleArray['VdE']['Year']         = (int) get_the_date('Y', $this->Article_ID);
            $ArticleArray['VdE']['Week']         = (int) get_the_date('W', $this->Article_ID);
            $ArticleArray['VdE']['Image']        = get_the_post_thumbnail_url($this->Article_ID, 'full');
            $ArticleArray['VdE']['Text']         = get_post_meta($this->Article_ID, 'message_text', true);
            $ArticleArray['VdE']['Url']          = get_the_permalink($this->Article_ID);
            $ArticleArray['VdE']['Status']       = [
                'CanVote' => $this->UserSession_LoggedIn,
                'IsVoted' => $this->Articles_Score_IsDone(),
            ];
        }

        if ($gHead === true && get_post_type($this->Article_ID) === 'wiki_post') {
            $Authors      = get_post_meta($this->Article_ID, 'authors', true);
            $Authors_List = [];
            $Authors_IDS  = [];
            if ($Authors) {
                $Authors = json_decode($Authors, true);

                foreach ($Authors as $K => $V) {
                    if (!$V) {
                        continue;
                    }

                    $V = (int) $V;
                    if (in_array($V, $Authors_IDS)) {
                        continue;
                    }

                    $Authors_List[$K] = [
                        'ID'    => $V,
                        'Name'  => $this->Users_Get_Name($V),
                        'Photo' => $this->Users_Get_Picture($V),
                        'Url'   => $this->Users_Get_URL($V),
                    ];
                    $Authors_IDS[] = $V;
                }
            }

            $Original = (int) get_post_meta($this->Article_ID, 'kui_user_id', true);

            if ($Original && !in_array($Original, $Authors_IDS)) {
                $Authors_List[$this->Article_ID] = [
                    'ID'    => $Original,
                    'Name'  => $this->Users_Get_Name($Original),
                    'Photo' => $this->Users_Get_Picture($Original),
                    'Url'   => $this->Users_Get_URL($Original),
                ];
                $Authors_IDS[] = $V;
            }

            $Authors_List = array_filter($Authors_List);
            $Authors_List = array_reverse($Authors_List, false);
            rsort($Authors_List);

            if ($Authors_List) {
                $ArticleArray['Authors'] = $Authors_List;
            }
        }
        $ArticleArray['Creator'] = (int) get_post_meta($this->Article_ID, 'creator', true);

        return $ArticleArray;
    }

    public function Articles_Score_IsDone()
    {
        if (!$this->Articles_Exists()) {
            return false;
        }

        if (!$this->UserSession_LoggedIn) {
            return false;
        }

        $Score_Users = explode(';', get_post_meta($this->Article_ID, 'scorers', true));

        return in_array('K' . $this->UserSession_UserID, $Score_Users);
    }
    public function Articles_Update_Visits()
    {
        if (!$this->Articles_Exists()) {
            return;
        }

        update_post_meta($this->Article_ID, 'kui3_visits', get_post_meta($this->Article_ID, 'kui3_visits', true) + 1);
    }

    public function Articles_Looping()
    {
        if (!$this->REST_DATA['slug']) {
            wp_send_json_error(new WP_Error('article_not_defined', 'No se ha definido el SLUG del artículo'), 400);
        }

        $Articles_Array = [];

        $Articles_Query = new WP_Query([
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ]);

        if ($Articles_Query->have_posts()) {
            while ($Articles_Query->have_posts()) {
                $Articles_Query->the_post();
                $Current_ID                  = get_the_ID();
                $Current_Data                = get_post($Current_ID);
                $Articles_Array[$Current_ID] = $Current_Data->post_name;
                if ($Current_Data->post_parent > 0) {
                    $Current_Parent              = get_post($Current_Data->post_parent);
                    $Articles_Array[$Current_ID] = $Current_Parent->post_name . '/' . $Current_Data->post_name;
                }
            }
        }

        $Article_Slug = urldecode($this->REST_DATA['slug']);
        $Article_Slug = sanitize_text_field($Article_Slug);

        if (substr($Article_Slug, 0, 1) === '/') {
            $Article_Slug = substr($Article_Slug, 1);
        }

        if (substr($Article_Slug, -1, 1) === '/') {
            $Article_Slug = substr($Article_Slug, 0, strlen($Article_Slug) - 1);
        }

        $Article_Search = (int) array_search($Article_Slug, $Articles_Array);

        if (!$Article_Search) {
            wp_send_json_error(new WP_Error('article_not_found', 'No se ha encontrado el artículo', ['query' => $Article_Slug]), 404);
        }

        setup_postdata((int) $Article_Search);

        $this->Article_ID = (int) $Article_Search;

        return $this->Article_ID;
    }

    /*
     * USERSESSION API
     *
     * --
     */

    private $UserSession_LoggedIn  = false;
    private $UserSession_SSOTicket = '';
    private $UserSession_UserID    = 0;

    public function UserSession_Authenticate()
    {
        $this->UserSession_SSOTicket = $this->REST_DATA['sso'];

        if ($this->REST_DATA['sso']) {
            if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/s', $this->REST_DATA['sso'])) {
                wp_send_json_error(new WP_Error('sso_ticket_invalid', 'Ticket SSO inválido', ['sso' => $this->REST_DATA['sso']]), 400);
            }
        }

        $UserQuery = new WP_Query([
            'post_type'  => 'kui_user',
            'meta_query' => [
                [
                    'key'   => 'sso_ticket',
                    'value' => $this->REST_DATA['sso'],
                ],
            ],
        ]);

        if (!$UserQuery->have_posts()) {
            return false;
        }

        $UserQuery->the_post();

        $this->UserSession_UserID = get_the_ID();

        if (get_post_meta($this->UserSession_UserID, 'activation_status', true) != '1') {
            return false;
        }

        $this->UserSession_LoggedIn = true;

        return true;
    }
    public function UserSession_CurrentTicket()
    {
        return $this->UserSession_SSOTicket;
    }
    public function UserSession_CurrentUser()
    {
        return $this->UserSession_UserID;
    }
    public function UserSession_IsLogged()
    {
        return $this->UserSession_LoggedIn;
    }
    public function UserSession_Requiered()
    {
        if ($this->UserSession_IsLogged() === false) {
            wp_send_json_error(new WP_Error('user_requiered', 'Requiere iniciar sesión.', ['sso' => $this->UserSession_SSOTicket]), 401);
        }
        return true;
    }

    public function Users_Exists($Queried_User_ID = false)
    {
        if ($Queried_User_ID === false) {
            $Queried_User_ID = $this->UserSession_UserID;
        }

        if (get_post_status($Queried_User_ID) === false) {
            return false;
        }

        if (get_post_type($Queried_User_ID) !== 'kui_user') {
            return false;
        }

        return true;
    }
    public function Users_Get_Picture($Queried_User_ID = false)
    {
        if ($Queried_User_ID === false) {
            $Queried_User_ID = $this->UserSession_UserID;
        }

        $User_Photo = get_post_meta($Queried_User_ID, 'user_display_photo', true);

        if (file_exists(WP_CONTENT_DIR . "/uploads/kui_system/users_profiles/{$User_Photo}.jpg")) {
            return "/wp-content/uploads/kui_system/users_profiles/{$User_Photo}.jpg";
        }

        return '/wp-content/themes/karlasflex/imaging/assets/kui/default_user.jpg';
    }
    public function Users_Get_Name($Queried_User_ID = false)
    {
        if ($Queried_User_ID === false) {
            $Queried_User_ID = $this->UserSession_UserID;
        }

        $User_Display = get_post_meta($Queried_User_ID, 'user_display_name', true);

        return $User_Display ?: 'Copito';
    }
    public function Users_Get_URL($Queried_User_ID = false)
    {
        return "https://karlaperezyt.com/usuarios/$Queried_User_ID";
    }

    public function Users_Get_Array($Queried_User_ID = false, $gHead = false)
    {
        if ($Queried_User_ID === false) {
            $Queried_User_ID = $this->UserSession_UserID;
        }

        $UserArray = [];

    }

    public function Telegram_Get_Picture($Queried_User_ID)
    {
        if (get_post_status($Queried_User_ID) !== false
            && get_post_type($Queried_User_ID) === 'telegram_subscribers') {
            $Query = get_post($Queried_User_ID);

            if (telegram_getid($Query->post_name) === $Queried_User_ID) {
                $Queried_User_ID = $Query->post_name;
            }
        }

        if (file_exists(WP_CONTENT_DIR . "/uploads/kui_system/telegram_profiles/{$Queried_User_ID}.jpg")) {
            return "/wp-content/uploads/kui_system/telegram_profiles/{$Queried_User_ID}.jpg";
        }

        return '/wp-content/themes/karlasflex/imaging/assets/kui/default_user.jpg';
    }
    public function Telegram_Get_Name($Queried_User_ID)
    {
        $User_Display = trim(implode(' ', [get_post_meta($Queried_User_ID, 'telegram_first_name', true), get_post_meta($Queried_User_ID, 'telegram_last_name', true)]));

        return $User_Display ?: 'Copito';
    }
    public function Telegram_Get_URL($Queried_User_ID)
    {
        if (get_post_status($Queried_User_ID) !== false
            && get_post_type($Queried_User_ID) === 'telegram_subscribers') {
            $Query = get_post($Queried_User_ID);

            if (telegram_getid($Query->post_name) === $Queried_User_ID) {
                $Queried_User_ID = $Query->post_name;
            }
        }

        return "https://karlaperezyt.com/telegram/miembros/$Queried_User_ID";
    }

    private $Displays_KUI_ID      = false;
    private $Displays_Telegram_ID = false;

    public function Displays_Set($KUI_ID, $Telegram_ID)
    {
        $this->Displays_KUI_ID      = (int) $KUI_ID;
        $this->Displays_Telegram_ID = (int) $Telegram_ID;
    }
    public function Displays_Users_ID($KUI_ID = false, $Telegram_ID = false)
    {
        if ($KUI_ID === false) {
            $KUI_ID = $this->Displays_KUI_ID;
        }
        if ($Telegram_ID === false) {
            $Telegram_ID = $this->Displays_Telegram_ID;
        }

        if (is_int($KUI_ID) && $KUI_ID !== 0 && get_post_status($KUI_ID) !== false) {
            return $KUI_ID;
        } else if (is_int($Telegram_ID) && $Telegram_ID !== 0 && get_post_status(telegram_getid($Telegram_ID)) !== false) {
            return $Telegram_ID;
        } else {
            return 0;
        }
    }
    public function Displays_Users_Type($KUI_ID = false, $Telegram_ID = false)
    {
        if ($KUI_ID === false) {
            $KUI_ID = $this->Displays_KUI_ID;
        }
        if ($Telegram_ID === false) {
            $Telegram_ID = $this->Displays_Telegram_ID;
        }

        if (is_int($KUI_ID) && $KUI_ID !== 0 && get_post_status($KUI_ID) !== false) {
            return 'KUI';
        } else if (is_int($Telegram_ID) && $Telegram_ID !== 0 && get_post_status(telegram_getid($Telegram_ID)) !== false) {
            return 'TELEGRAM';
        } else {
            return false;
        }
    }
    public function Displays_Users_Photo($KUI_ID = false, $Telegram_ID = false)
    {
        if ($KUI_ID === false) {
            $KUI_ID = $this->Displays_KUI_ID;
        }
        if ($Telegram_ID === false) {
            $Telegram_ID = $this->Displays_Telegram_ID;
        }

        if (is_int($KUI_ID) && $KUI_ID !== 0 && get_post_status($KUI_ID) !== false) {
            return $this->Users_Get_Picture($KUI_ID);
        } else if (is_int($Telegram_ID) && $Telegram_ID !== 0 && get_post_status(telegram_getid($Telegram_ID)) !== false) {
            return $this->Telegram_Get_Picture($Telegram_ID);
        } else {
            return '/wp-content/themes/karlasflex/imaging/assets/kui/default_user.jpg';
        }
    }
    public function Displays_Users_Name($KUI_ID = false, $Telegram_ID = false)
    {
        if ($KUI_ID === false) {
            $KUI_ID = $this->Displays_KUI_ID;
        }
        if ($Telegram_ID === false) {
            $Telegram_ID = $this->Displays_Telegram_ID;
        }

        if (is_int($KUI_ID) && $KUI_ID !== 0 && get_post_status($KUI_ID) !== false) {
            return $this->Users_Get_Name($KUI_ID);
        } else if (is_int($Telegram_ID) && $Telegram_ID !== 0 && get_post_status(telegram_getid($Telegram_ID)) !== false) {
            return $this->Telegram_Get_Name(telegram_getid($Telegram_ID));
        } else {
            return 'Copito';
        }
    }
    public function Displays_Users_URL($KUI_ID = false, $Telegram_ID = false)
    {
        if ($KUI_ID === false) {
            $KUI_ID = $this->Displays_KUI_ID;
        }
        if ($Telegram_ID === false) {
            $Telegram_ID = $this->Displays_Telegram_ID;
        }

        if (is_int($KUI_ID) && $KUI_ID !== 0 && get_post_status($KUI_ID) !== false) {
            return "https://karlaperezyt.com/usuarios/{$KUI_ID}";
        } else if (is_int($Telegram_ID) && $Telegram_ID !== 0 && get_post_status(telegram_getid($Telegram_ID)) !== false) {
            return "https://karlaperezyt.com/telegram/miembros/{$Telegram_ID}";
        } else {
            return false;
        }
    }
    public function Displays_Users_Mention($KUI_ID = false, $Telegram_ID = false)
    {
        if ($KUI_ID === false) {
            $KUI_ID = $this->Displays_KUI_ID;
        }
        if ($Telegram_ID === false) {
            $Telegram_ID = $this->Displays_Telegram_ID;
        }

        $Get_Name = $this->Displays_Users_Name($KUI_ID, $Telegram_ID);
        $Get_URL  = $this->Displays_Users_URL($KUI_ID, $Telegram_ID);
        $Get_Tel  = "tg://user?id=$Telegram_ID";
        if (is_int($KUI_ID) && $KUI_ID !== 0 && get_post_status($KUI_ID) !== false) {
            return "[$Get_Name]($Get_URL)";
        } else if (is_int($Telegram_ID) && $Telegram_ID !== 0 && get_post_status(telegram_getid($Telegram_ID)) !== false) {
            return "[$Get_Name]($Get_Tel)";
        } else {
            return false;
        }
    }

    public function Floody_Dispatch($Flood_Type, $Max_Tries = 4, $Max_Time_Interval = 300, $Block_Time_Interval = 3600 * 24)
    {
        if (!$this->UserSession_IsLogged()) {
            return;
        }

        if (!$this->Users_Exists()) {
            return;
        }

        $User_Flood = get_post_meta($this->UserSession_CurrentUser(), 'floody', true);

        if ($User_Flood) {
            $User_Flood = json_decode($User_Flood, true);
        } else {
            $User_Flood = [];
        }

        if (!isset($User_Flood[$Flood_Type])) {
            $User_Flood[$Flood_Type] = [0, 0];
        }

        list($Current_Tries, $Current_Time) = $User_Flood[$Flood_Type];

        $Doing_Flood = false;

        // Flood control activated
        if ($Current_Time + $Max_Time_Interval > time()) {
            // Is Flooding
            if ($Current_Tries > $Max_Tries) {
                $Doing_Flood = true;
            } else {
                $Doing_Flood = false;
                $Current_Tries++;
            }
        }
        // Flood expired
        else {
            $Current_Tries = 0;
        }
        $Current_Time = time();

        if ($Doing_Flood === true && $Block_Time_Interval > 0) {
            $Current_Time = time() + $Block_Time_Interval;
        }

        $User_Flood[$Flood_Type] = [
            (int) $Current_Tries,
            (int) $Current_Time,
        ];

        update_post_meta($this->UserSession_CurrentUser(), 'floody', json_encode($User_Flood));

        if ($Doing_Flood === true) {
            wp_send_json_error(new WP_Error('is_doing_flood', 'Acción bloqueada por flood/spam. Por favor, intenta hacer las cosas más despacio.<br>Se ha establecido un bloqueo de <strong>' . ($Block_Time_Interval / 3600) . ' hora(s)</strong>.'), 429);
        }

        return;
    }
}
