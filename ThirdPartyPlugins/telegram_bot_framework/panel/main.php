<?php
    echo '
    <div class="wrap">

<div class="wrap about__container">

    <div class="about__section is-feature has-accent-background-color">
        <h1>Telegram Bot & Channel</h1>
        <p>Version '.get_option('wp_telegram_version').'</p>
    </div>
    <nav class="about__header-navigation nav-tab-wrapper wp-clearfix" aria-label="Secondary menu">
        <a href="https://www.botpress.org/docs/" target="_blank" class="nav-tab">Need Help?</a>
        <a href="https://www.botpress.org/support/" target="_blank" class="nav-tab">Support Forum</a>
        <a href="https://www.botpress.org/donate/" target="_blank" class="nav-tab">Donate</a>
        <a href="https://wordpress.org/support/plugin/telegram-bot/reviews/" target="_blank" class="nav-tab">Rate  <span style="color:yellow;">★★★★★</span></a>
    </nav>

    <div class="about__section changelog">
        <div class="column">
            <div style="float:right;width:300px;">
                <div id="fb-root"></div>
                <script>(function(d, s, id) {
                  var js, fjs = d.getElementsByTagName(s)[0];
                  if (d.getElementById(id)) return;
                  js = d.createElement(s); js.id = id;
                  js.src = "//connect.facebook.net/it_IT/sdk.js#xfbml=1&version=v2.4&appId=251544361535439";
                  fjs.parentNode.insertBefore(js, fjs);
                }(document, \'script\', \'facebook-jssdk\'));</script>
                <div class="fb-page" data-href="https://www.facebook.com/WPGov" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true" data-show-posts="false"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/WPGov"><a href="https://www.facebook.com/WPGov">WPGov.it</a></blockquote></div></div>
            </div>
			<h2>Keep updated</h2>
			<p>
                Join our <a href="https://t.me/BotPressOrg" alt="BotPress.Org Telegram">Telegram channel</a> to keep updated with latest news!
            </p>
        </div>
        <div class="clear"></div>
    </div>

    
    <div class="about__section has-3-columns" style="text-align:center;">
        <div class="column has-subtle-background-color">
            <br>
            <a class="button button-primary button-hero load-customize hide-if-no-customize" href="admin.php?page=telegram_send">Send a Message</a>
		</div>
		<div class="column has-subtle-background-color">
            <p>Messages sent</p>
            <br>
            <h2>'.get_option('wp_telegram_dispatches').'</h2>
        </div>
        <div class="column has-subtle-background-color">
            <p>Bot subscribers</p>
            <br>
            <h2>'.wp_count_posts('telegram_subscribers')->publish.'</h2>
        </div>
    </div>
    <div class="about__section">
        <div class="column">
            <h2>Latest news</h2>';
    
    include_once( ABSPATH . WPINC . '/feed.php' );
    $rss = fetch_feed( 'https://botpress.org/feed/' );
    $maxitems = 0;
    if ( ! is_wp_error( $rss ) ) :
        $maxitems = $rss->get_item_quantity( 5 ); 
        $rss_items = $rss->get_items( 0, $maxitems );
    endif;
    if ( $maxitems == 0 ) {
        echo '<h3>'. __( 'Cannot reach the feed', 'telegram-bot' ).'</h3>';
    } else {
        foreach ( $rss_items as $item ) {
            echo '<h3>';
            echo '<a target="_blank" href="'.esc_url( $item->get_permalink() ).'">'.esc_html( $item->get_title() ).'</a>';
            echo '</h3>';
        }
    }

echo '</div>
    </div>
</div>';