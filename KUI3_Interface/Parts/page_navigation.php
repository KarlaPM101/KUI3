<nav class="KUI3_SiteNav _Desktop">
    <div class="_NavHead">
        <div class="k_ac k_mb10">
            <img alt="Karla's Project" src="/wp-content/themes/karlasflex/imaging/assets/kui/profile_rnd.png">
            <span class="_w">Karla's</span><span class="_gg"> Project</span>
        </div>
    </div>
    <ul class="_NavList KUI3_Scroll mCustomScrollbar _mCS_1">
        <div id="mCSB_1" class="mCustomScrollBox mCS-light mCSB_vertical mCSB_inside" style="max-height: none;"
             tabindex="0">
            <div id="mCSB_1_container" class="mCSB_container" style="position:relative; top:0; left:0;" dir="ltr">
                <?php
                $KUI = new KUI_REST([]);
                $Navigation = $KUI->Internal_Call("menu");
                if($Navigation){
                    foreach ($Navigation as $Navigation_I){
                        ?>
                        <li>
                        <a href="<?php echo $Navigation_I['Url']; ?>">
                            <img alt="<?php echo $Navigation_I['Title']; ?>" src="<?php echo $Navigation_I['Icon']; ?>" class="mCS_img_loaded"><?php echo $Navigation_I['Title']; ?>
                        </a>

                        <?php
                        if(isset($Navigation_I['Menu']) && $Navigation_I['Menu']){
                            ?>
                            <ul class="Sub">
                                <?php
                                foreach ($Navigation_I['Menu'] as $Navigation_Sub_I){
                                    ?>
                                    <li>
                                        <a href="<?php echo $Navigation_Sub_I['Url']; ?>">
                                            <img alt="<?php echo $Navigation_Sub_I['Title']; ?>" src="<?php echo $Navigation_Sub_I['Icon']; ?>" class="mCS_img_loaded"><?php echo $Navigation_Sub_I['Title']; ?>
                                        </a>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                            </li>
                            <?php
                        }
                    }
                }
                ?>
            </div>
        </div>
    </ul>
    <ul class="Footer">
        <li><a href="/informacion/kui3">Karla KUI3</a></li>
        <li><a href="/informacion/privacidad">Privacidad</a></li>
    </ul>
</nav>