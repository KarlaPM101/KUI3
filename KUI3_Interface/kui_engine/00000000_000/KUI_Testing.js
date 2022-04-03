$KUI = {
    System: {
        Start: function (){
            console.log('KUI v3.0');
            var Body = $('body');
            $('#MOUNT').remove();
            Body.append('<div id="KUI3"></div>');
            Body.css('overflow','hidden');
            $KUI.Template.MainContent = $('#KUI3');
            if($KUI.System.TestCompat.StorageSupport()===false)
            {
                $KUI.Template.MainContent.html('Load Failed: localStorage not enabled.');
                return;
            }
            if($KUI.System.TestCompat.CookieSupport()===false)
            {
                $KUI.Template.MainContent.html('Load Failed: cookie not enabled.');
                return;
            }
            $KUI.Template.ColorCtrl.ColorLoad();
            $KUI.Resources.DeferedLoad.JQueryPlugins();
            $KUI.Template.AddMobileClass();
            $KUI.UserSession.Assignnance();
            $KUI.Template.Header();
            $KUI.Template.Navigation();
            $KUI.Template.Body();
            $KUI.Template.AddMobileClass();
            $KUI.System.Triggers();
            $KUI.Location.Push();
            setInterval(function (){
                $KUI.Location.Push();
            },100);
            $KUI.Loaders.Append();
        },
        Triggers: function () {
            $('*[data-goto]').off('.linkset').on('click.linkset', $KUI.Location.Goto);
            $KUI.Resources.ScrollBars();
            $KUI.Forms.SetUpForms();
        },
        EndPoint: "https://karlaperezyt.com/wp-json/",
        TestCompat:{
            CookieSupport:function (){
                var cookieEnabled = navigator.cookieEnabled;
                if (!cookieEnabled){
                    document.cookie = "testcookie";
                    cookieEnabled = document.cookie.indexOf("testcookie")!==-1;
                }
                return cookieEnabled || showCookieFail();
            },
            StorageSupport:function (){
                try {
                    return 'sessionStorage' in window && window['sessionStorage'] !== null;
                } catch(e) {
                    return false;
                }
            }
        }
    },
    Resources:{
        DeferedLoad:{
            JQueryPlugins:function (){
                if($(window).width()>=800)
                {
                    //$('head').prepend('<script type="text/javascript" src="/wp-content/themes/karlasflex/js/jQuery_Scroll.js"></script>');
                }
            },
        },
        ScrollBars:function (){
            if($(window).width()>=800)
            {
                $(".KUI3_Scroll").mCustomScrollbar();
            }
        },
        Assets:{
            LoaderSVG:"/wp-content/themes/karlasflex/imaging/assets/kui/loader.svg",
            ErrorAlert:"/wp-content/themes/karlasflex/imaging/assets/icons/general/alert.png",
            DefUser:"/wp-content/themes/karlasflex/imaging/assets/kui/default_user",
        },
        RandomString:function (Length) {
            var Return           = '';
            var Charas       = 'abcdef0123456789';
            var CharasLen = Charas.length;
            for (var i=0;i<Length;i++)
            {
                Return += Charas.charAt(Math.floor(Math.random() * CharasLen));
            }
            return Return;
        },
        RandomGUID:function () {
            var A = $KUI.Resources.RandomString(8);
            var B = $KUI.Resources.RandomString(4);
            var C = $KUI.Resources.RandomString(4);
            var D = $KUI.Resources.RandomString(4);
            var E = $KUI.Resources.RandomString(12);
            return A+'-'+B+'-'+C+'-'+D+'-'+E;
        },
        Cookies:{
            Set:function(cname, cvalue, exdays) {
                const d = new Date();
                d.setTime(d.getTime() + (exdays*24*60*60*1000));
                let expires = "expires="+ d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            },
            Get:function(cname) {
                let name = cname + "=";
                let decodedCookie = decodeURIComponent(document.cookie);
                let ca = decodedCookie.split(';');
                for(let i = 0; i <ca.length; i++) {
                    let c = ca[i];
                    while (c.charAt(0) === ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) === 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            }
        }
    },
    Loaders:{
        Append:function (){
            $('div.KUI3_LoaderGeneral').remove();
            if($KUI.Loaders.Interval)
            {
                clearInterval($KUI.Loaders.Interval);
            }

            $('body').prepend('<div class="KUI3_LoaderGeneral"><span></span></div>');
            $KUI.Loaders.Animate();
            $KUI.Loaders.Interval = setInterval(function (){$KUI.Loaders.CheckLoader();},1000);
        },
        Animate:function (){
            var LoaderSpan = $('div.KUI3_LoaderGeneral>span');
            LoaderSpan.css('left','-'+parseInt($(window).width()*0.33)+'px');
            LoaderSpan.animate({
                left: "+="+parseInt($(window).width()+$(window).width()*0.33),
            }, 1500,function (){
                LoaderSpan.css('left','-'+parseInt($(window).width()*0.33)+'px');
            });
        },
        CheckLoader:function (){
            var Loaders = $('.KUI3_Loader').length;
            var LoaderSpan = $('div.KUI3_LoaderGeneral>span');
            if(Loaders>0 && (parseInt(LoaderSpan.css('left'))<0 || parseInt(LoaderSpan.css('left')>parseInt($(window).width()+$(window).width()*0.33))))
            {
                $KUI.Loaders.Animate();
            }
            if(Loaders<0)
            {
                LoaderSpan.stop();
                LoaderSpan.css('left','-'+parseInt($(window).width()*0.33)+'px');
            }
        },
        Interval:false
    },
    Template: {
        MainContent: null,
        IsMobile: false,
        AddMobileClass: function(){
            if($(window).width()<800)
            {
                $KUI.Template.IsMobile = true;
                $('#KUI3').addClass('_Mobile');
                $('header.KUI3_SiteHeader').addClass('_Mobile');
                $('nav.KUI3_SiteNav').addClass('_Mobile');
                $('div.KUI3_SiteBody').addClass('_Mobile');
            }
            else
            {
                $('header.KUI3_SiteHeader').addClass('_Desktop');
                $('nav.KUI3_SiteNav').addClass('_Desktop');
                $('div.KUI3_SiteBody').addClass('_Desktop');
            }
            setInterval(function (){
                var Reload = false;
                if($KUI.Template.IsMobile===true)
                {
                    if($(window).width()>800)
                    {
                        Reload = true;
                        $KUI.Template.IsMobile = false;
                    }
                }
                if($KUI.Template.IsMobile===false)
                {
                    if($(window).width()<800)
                    {
                        Reload = true;
                        $KUI.Template.IsMobile = true;
                    }
                }

                if(Reload===true)
                {
                    $('#KUI3').html('');
                    $KUI.Template.Header();
                    $KUI.Template.Navigation();
                    $KUI.Template.Body();
                    $KUI.Template.AddMobileClass();
                    $KUI.Loaders.Append();
                    $KUI.Location.CurrentUrl = false;
                    $KUI.System.Triggers();
                }
            },100);
        },
        Header: function(){
            if($KUI.Template.IsMobile===false)
            {
                $KUI.Template.MainContent.append('<header class="KUI3_SiteHeader"><div class="KUI3_SiteHeader_Caption"></div><div class="KUI3_SiteHeader_UserCard"></div></header>');
                $KUI.UserSession.Templates.UserCard();

                var CardHandler = $('.KUI3_SiteHeader_UserCard');
                var ThemeSelector = $('<ul class="KUI_ThemeSelector"></ul>').appendTo(CardHandler);
                var ThemeSelector_Day = $('<li class="Day"></li>').appendTo(ThemeSelector);
                var ThemeSelector_Night = $('<li class="Night"></li>').appendTo(ThemeSelector);

                ThemeSelector_Day.click(function (){
                    $KUI.Template.ColorCtrl.SetLight();
                });
                ThemeSelector_Night.click(function (){
                    $KUI.Template.ColorCtrl.SetDark();
                });
            }
            $KUI.Template.FontFamily.DeferedLoad();
        },
        Navigation: function(){
            if($KUI.Template.IsMobile===false)
            {
                var Navigation = $('<nav class="KUI3_SiteNav"><div class="_NavHead"></div><ul class="_NavList KUI3_Scroll"></ul></nav>').appendTo($KUI.Template.MainContent);
                $('<ul class="Footer"><li data-goto="/informacion/kui3">Karla KUI3</li><li data-goto="/informacion/privacidad">Privacidad</li></ul>').appendTo(Navigation);
            }
            else
            {
                $KUI.Template.MainContent.append('<nav class="KUI3_SiteNav"><div class="_Toggle"><img alt="Menu" src="/wp-content/themes/karlasflex/imaging/assets/kui/menu.svg"></div><div class="_NavHead"></div><ul class="_NavList"><li class="KUI3_SiteHeader_UserCard"></li></ul></nav>');
                $KUI.UserSession.Templates.UserCard();
                $('<ul class="Footer"><li data-goto="/informacion/kui3">Karla KUI3</li><li data-goto="/informacion/privacidad">Privacidad</li></ul>').appendTo($('ul._NavList'));
            }
            $KUI.Navigation.MakeHeader();
            $KUI.Navigation.NavigationLoad();
            $('nav.KUI3_SiteNav > div._Toggle').click(function (){
                var NL = $('nav.KUI3_SiteNav').find('ul._NavList').eq(0);
                var NP = NL.parents('nav').eq(0);
                var NB = $('#KUI3._Mobile');
                NL.toggle();

                if(NL.css('display')==='none'){
                    NP.css('bottom','auto');
                    NB.css('overflow-y','auto');
                }
                else {
                    NP.css('bottom','0px');
                    NB.css('overflow-y','hidden');
                }


            });

        },
        Body: function(){
            if($KUI.Template.IsMobile===true)
            {
                $KUI.Template.MainContent.append('<header class="KUI3_SiteHeader"><div class="KUI3_SiteHeader_Caption"></div></header><div class="KUI3_SiteBody"></div>');
            }
            else
            {
                $KUI.Template.MainContent.append('<div class="KUI3_SiteBody KUI3_Scroll"></div>');
            }
        },
        FontFamily: {
            DeferedLoad:function(){
                $('head').append('<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100;300;400;500;700;900&display=swap" rel="stylesheet"> ');
            }
        },
        Layouts:{
            Common:{
                Caption:false,
                Body:false,
                Prepare:function(){
                    $KUI.Template.Layouts.Common.Caption = $('div.KUI3_SiteHeader_Caption');
                    $KUI.Template.Layouts.Common.Body = $('div.KUI3_SiteBody');

                    $KUI.Template.Layouts.Common.Caption.html('');
                    $KUI.Template.Layouts.Common.Body.html('');

                    $('ul.KUI3_SiteHeader_CatSelector').each(function (){
                        $(this).remove();
                    });
                },
                SetTitle:function(Title){
                    $KUI.Template.Layouts.Common.Caption.append('<h1>'+Title+'</h1>');
                    document.title = Title+" - Karla's Project";
                },
                AddButton:function (Text,Function){
                    var ButtonContainer = $KUI.Template.Layouts.Common.Caption.find('ul').eq(0);
                    if(ButtonContainer.length<=0)
                    {
                        $KUI.Template.Layouts.Common.Caption.append('<ul class="_ButtonList"></ul>');
                        ButtonContainer = $KUI.Template.Layouts.Common.Caption.find('ul').eq(0);
                    }
                    var Button = $('<li></li>').appendTo(ButtonContainer);
                    var Link = $('<a>'+Text+'</a>').appendTo(Button);
                    if(typeof Function === 'function')
                    {
                        Link.click(Function);
                    }
                },
                AddSearch:function (){
                    var ButtonContainer = $KUI.Template.Layouts.Common.Caption.find('ul').eq(0);
                    if(ButtonContainer.length<=0)
                    {
                        $KUI.Template.Layouts.Common.Caption.append('<ul class="_ButtonList"></ul>');
                        ButtonContainer = $KUI.Template.Layouts.Common.Caption.find('ul').eq(0);
                    }
                    var Button = $('<li class="SearchBar"></li>').prependTo(ButtonContainer);
                    $('<input class="Searcher" type="text" placeholder="Buscar tutoriales"><a><img src="/wp-content/themes/karlasflex/imaging/assets/kui/search.svg" alt="Buscar"></a>').appendTo(Button);
                    if($('div.KUI3_SearcherResults').length<=0)
                    {
                        $('body').append('<div class="KUI3_SearcherResults"><ul class="KUI3_Scroll"></ul></div>');
                    }

                    $KUI.Template.Layouts.Common.Searcher.Prepare();
                },
                Searcher:{
                    DoSearch:function (Query){
                        var SearchHandler = $KUI.Template.Layouts.Common.Caption.find('ul').eq(0).find('li.SearchBar').eq(0);
                        var SearchResults = $('div.KUI3_SearcherResults').eq(0);
                        var SearchResultsUL = SearchResults.find('ul').eq(0);
                        var SearchResultsULMSB = SearchResults.find('.mCSB_container').eq(0);

                        if(SearchResultsULMSB.length<=0)
                        {
                            SearchResultsULMSB = SearchResultsUL;
                        }

                        var Offset = SearchHandler.offset();
                        SearchResultsUL.find('li').each(function (){
                            $(this).remove();
                        });
                        SearchResults.css('left',((Offset.left+250/2+10)-400/2)+'px');

                        $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint + "kui/searcher/"+Query,function (data){
                            if(!Query)
                            {
                                SearchResults.hide();
                                return;
                            }
                            SearchResults.show();
                            SearchResultsULMSB.html('');

                            $.each(data,function (i,v){
                                SearchResultsULMSB.append('<li><a data-goto="'+v['Link']+'"><img alt="'+v['Caption']+'" src="'+v['Image']+'">'+v['Caption']+'</a></li>');
                            });

                            $KUI.System.Triggers();
                        },function (){
                            SearchResultsULMSB.html('');
                            SearchResultsULMSB.append('<li class="_Empty">Sin resultados</li>');

                            $KUI.System.Triggers();
                        },1800);
                    },
                    Prepare:function (){
                        var SearchHandler = $KUI.Template.Layouts.Common.Caption.find('ul').eq(0).find('li.SearchBar').eq(0);
                        var SearchInput = SearchHandler.find('input').eq(0);
                        var SearchBtn = SearchHandler.find('a').eq(0);
                        var SearchContainer = $('div.KUI3_SearcherResults');

                        SearchInput.on('input', function() {
                            $KUI.Template.Layouts.Common.Searcher.DoSearch(SearchInput.val());
                        });
                        SearchInput.click(function (){
                            var Elements = SearchContainer.find('li').length;
                            if(Elements>0 && SearchInput.val())
                            {
                                SearchContainer.show();
                            }
                        });
                        SearchBtn.click(function (){
                            var Elements = SearchContainer.find('li').length;
                            if(Elements>0 && SearchInput.val())
                            {
                                SearchContainer.show();
                            }
                        });


                        $(window).click(function() {
                            SearchContainer.hide();
                        });
                        SearchContainer.click(function(event){
                            event.stopPropagation();
                        });
                        SearchInput.click(function(event){
                            event.stopPropagation();
                        });
                        SearchBtn.click(function(event){
                            event.stopPropagation();
                        });


                    }
                },
                Trailer:function (){
                    var MergeContainer = $('<div class="KUI3_BodyMerge _Gray"></div>').appendTo($KUI.Template.Layouts.Common.Body);
                    var TrailerGroup = $('<div class="KUI3_BodyGroup"></div>').appendTo(MergeContainer);
                    var DesktopGroup = $('<div class="KUI3_BodyGroup_Large"></div>').appendTo(TrailerGroup);
                    var NewsGroup = $('<div class="KUI3_BodyGroup_Small"></div>').appendTo(TrailerGroup);
                    var DesktopTitle = $('<h2 class="GroupTitle"><span class="_Text">Escritorios Destacados</span><span class="_TextDesc">GNU/Linux y Windows with 💗</span></h2>').appendTo(DesktopGroup);
                    $('<h2 class="GroupTitle"><span class="_Text">Noticias Linuxeras</span><span class="_TextDesc">Recién sacadas del horno 😋</span></h2>').appendTo(NewsGroup);

                    var DesktopBtns = $('<ul></ul>').appendTo(DesktopTitle);
                    var AddDesktopH = $('<li></li>').appendTo(DesktopBtns);
                    var AddDesktop = $('<button class="KUI3_Button">Publicar Escritorio</button>').appendTo(AddDesktopH);
                    AddDesktop.click(function (){
                        $KUI.Desktops.Modals.Upload();
                    });

                    var DesktopGroupContainer = $('<div class="KUI3_BodyGroup_Container _AsyncTrilerDesktopGroup"></div>').appendTo(DesktopGroup);
                    var NewsGroupContainer = $('<div class="KUI3_BodyGroup_Container _AsyncTrilerNewsGroup"></div>').appendTo(NewsGroup);
                    $KUI.Desktops.Templates.FrontList(DesktopGroupContainer);
                    $KUI.News.Templates.FrontList(NewsGroupContainer);
                },
                AddSelector:function (Options,DefaultText){
                    var ButtonContainer = $KUI.Template.Layouts.Common.Caption.find('ul').eq(0);
                    if(ButtonContainer.length<=0)
                    {
                        $KUI.Template.Layouts.Common.Caption.append('<ul class="_ButtonList"></ul>');
                        ButtonContainer = $KUI.Template.Layouts.Common.Caption.find('ul').eq(0);
                    }
                    var Button = $('<li class="Selector"><span>'+DefaultText+'</span></li>').appendTo(ButtonContainer);
                    var Buttons = $('<ul class="KUI3_SiteHeader_CatSelector"></ul>').appendTo($('body'));
                    $(document).click(function (){
                        Buttons.hide();
                        Button.removeClass('_Show');
                    });
                    if(DefaultText==='Categoría' || DefaultText==='Escritorios Destacados')
                    {
                        Button.css('min-width','250px');
                    }
                    Button.click(function (event){
                        event.stopPropagation();
                        Buttons.toggle();
                        if($(this).hasClass('_Show'))
                        {
                            $(this).removeClass('_Show');
                        }
                        else
                        {
                            $(this).addClass('_Show');
                        }
                        var Offset = Button.offset();
                        Buttons.css('left',((Offset.left+180/2+1)-180/2)+'px');
                        if(parseInt(Buttons.css('left'))+180>$(window).width())
                        {
                            Buttons.css('left','auto');
                            Buttons.css('right','10px');
                        }

                        if(DefaultText==='Categoría' || DefaultText==='Escritorios Destacados')
                        {
                            Buttons.css('left',((Offset.left+250/2+1)-250/2)+'px');
                            Buttons.css('min-width','250px');
                            if(parseInt(Buttons.css('left'))+250>$(window).width())
                            {
                                Buttons.css('left','auto');
                                Buttons.css('right','10px');
                            }
                        }
                    });
                    $.each(Options,function (i,v){
                        var Option = $('<li>'+v['Title']+'</li>').appendTo(Buttons);
                        Option.click(function (){
                            $KUI.Location.Goto(v['Goto']);
                        });
                        if(typeof v['Child'] === 'boolean' && v['Child'] === true)
                        {
                            Option.addClass('_Child');
                        }
                        var BlogDesc = $('span._Mnt_Blog_Desc').eq(0);
                        if(DefaultText==='Categoría' && v['Goto']==='/'+$KUI.Location.UrlFragments[1]+'/'+$KUI.Location.UrlFragments[2]+'/'+$KUI.Location.UrlFragments[3])
                        {
                            Button.find('span').eq(0).html(v['Title']);
                            BlogDesc.html('Tutoriales sobre '+v['Title']);
                        }
                        if(DefaultText==='Categoría' && v['Goto']==='/'+$KUI.Location.UrlFragments[1]+'/'+$KUI.Location.UrlFragments[2])
                        {
                            Button.find('span').eq(0).html(v['Title']);
                            BlogDesc.html('Tutoriales sobre '+v['Title']);
                        }
                        if(DefaultText==='Escritorios Destacados' && v['Goto']==='/'+$KUI.Location.UrlFragments[1]+'/'+$KUI.Location.UrlFragments[2]+'/'+$KUI.Location.UrlFragments[3])
                        {
                            Button.find('span').eq(0).html(v['Title']);
                            if($KUI.Location.UrlFragments[2]==='featured')
                            {
                                BlogDesc.html('Mejor valorados del '+$KUI.Location.UrlFragments[3]);
                            }
                            else
                            {
                                BlogDesc.html('Escritorios de la '+v['Title']);
                            }
                        }
                        if(DefaultText==='Escritorios Destacados' && v['Goto']==='/'+$KUI.Location.UrlFragments[1]+'/'+$KUI.Location.UrlFragments[2])
                        {
                            Button.find('span').eq(0).html(v['Title']);
                            if($KUI.Location.UrlFragments[2]==='featured')
                            {
                                BlogDesc.html('Mejor valorados de cada semana');
                            }
                            else
                            {
                                BlogDesc.html('Escritorios de la '+v['Title']);
                            }
                        }
                    });

                }
            },
            FrontPage:{
                MakeLayout:function(){
                    $KUI.Template.Layouts.Common.Prepare();
                    $KUI.Template.Layouts.Common.SetTitle('Página Principal');
                    $KUI.Template.Layouts.Common.AddSearch();
                    if($KUI.Template.IsMobile===false)
                    {
                        $KUI.Template.Layouts.Common.Trailer();
                    }
                    $KUI.Feedy.Printers.FeedyPrint();
                }
            },
            Blog:{
                MakeLayout:function (){
                    var BlogCategory = $KUI.Location.UrlFragments[2];
                    if(!BlogCategory)
                    {
                        BlogCategory = 'publicaciones';
                    }
                    var BlogSubCategory = $KUI.Location.UrlFragments[3];
                    if(BlogSubCategory)
                    {
                        BlogSubCategory = '/'+BlogSubCategory;
                    }
                    else
                    {
                        BlogSubCategory = '';
                    }
                    var TextCategory = 'Todas las entradas';
                    var TextSubCategory = 'Todas las temáticas';
                    switch ($KUI.Location.UrlFragments[2])
                    {
                        case 'articulos':TextCategory = 'Artículos';break;
                        case 'reviews':TextCategory = 'Reviews';break;
                        case 'noticias':TextCategory = 'Notícias';break;
                    }
                    switch ($KUI.Location.UrlFragments[3])
                    {
                        case 'linux':TextSubCategory = 'GNU/Linux';break;
                        case 'windows':TextSubCategory = 'Windows';break;
                    }

                    $KUI.Template.Layouts.Common.Prepare();
                    $KUI.Template.Layouts.Common.SetTitle('Blog');
                    $KUI.Template.Layouts.Common.AddSearch();
                    $KUI.Template.Layouts.Common.AddSelector([{
                        Title : "Todas las entradas",
                        Goto  : "/blog"
                    },{
                        Title : "Artículos",
                        Goto  : "/blog/articulos"+BlogSubCategory
                    },{
                        Title : "Reviews",
                        Goto  : "/blog/reviews"+BlogSubCategory
                    },{
                        Title : "Notícias",
                        Goto  : "/blog/noticias"+BlogSubCategory
                    }],TextCategory);
                    $KUI.Template.Layouts.Common.AddSelector([{
                        Title : "Todas las temáticas",
                        Goto  : "/blog"
                    },{
                        Title : "GNU/Linux",
                        Goto  : "/blog/"+BlogCategory+"/linux"
                    },{
                        Title : "Windows",
                        Goto  : "/blog/"+BlogCategory+"/windows"
                    }],TextSubCategory);
                    $KUI.Feedy.Printers.ListingPrint('Blog',1,BlogCategory,BlogSubCategory);
                }
            },
            Tutorials:{
                MakeLayout:function (){
                    $KUI.Template.Layouts.Common.Prepare();
                    $KUI.Template.Layouts.Common.SetTitle('Tutoriales & Vídeos');
                    $KUI.Template.Layouts.Common.AddSearch();
                    $KUI.Feedy.Printers.ListingPrint('VideoPost',1,$KUI.Location.UrlFragments[2],$KUI.Location.UrlFragments[3]);
                    $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+"kui/videolist",function (data){
                        if($KUI.Location.UrlFragments[1]==='videos')
                        {
                            $KUI.Template.Layouts.Common.AddSelector(data,'Categoría');
                        }
                    },function (){},3600*24);
                }
            },
            Desktops:{
                MakeLayout:function (){
                    $KUI.Template.Layouts.Common.Prepare();
                    $KUI.Template.Layouts.Common.SetTitle('#ViernesDeEscritorio');
                    $KUI.Feedy.Printers.ListingPrint('Desktops',1,$KUI.Location.UrlFragments[2]?$KUI.Location.UrlFragments[2]:'featured',$KUI.Location.UrlFragments[3],3,function (Container){
                        $('<h2 class="GroupTitle"><span class="_Text">Podium</span><span class="_TextDesc kEL_mb_10">Podium de participantes</span></h2>').appendTo(Container);
                        $KUI.StaticWidgets.DesktopPodium(Container);
                    });

                    $KUI.Template.Layouts.Common.AddButton('Publicar Escritorio',function (){
                        $KUI.Desktops.Modals.Upload();
                    })

                    $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+"kui/desktopweeks",function (data){
                        if($KUI.Location.UrlFragments[1]!=='viernesdeescritorio') return;
                        $KUI.Template.Layouts.Common.AddSelector(data,'Escritorios Destacados');
                        $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+"kui/desktopweeks?ShowYears=true",function (data){
                            if($KUI.Location.UrlFragments[1]!=='viernesdeescritorio') return;
                            $KUI.Template.Layouts.Common.AddSelector(data,''+(new Date().getFullYear())+'');
                        },function (){},3600*24);
                    },function (){},3600*24);
                }
            },
            Wiki:{
                MakeLayout:function (){
                    $KUI.Template.Layouts.Common.Prepare();
                    $KUI.Template.Layouts.Common.SetTitle('Wiki Linuxpedia (beta)');
                    $KUI.Template.Layouts.Common.AddButton('Crear Artículo',function (){
                        $KUI.Wiki.Add();
                    })
                    /*$KUI.Template.Layouts.Common.AddButton('Guia para GNU/Linux',function (){
                        $KUI.Location.Goto('/guia-linux');
                    })*/

                    $KUI.Feedy.Printers.WikiPrint();
                }
            },
            Users:{
                Profile:function (){
                    var User_ID = $KUI.Location.UrlFragments[2];
                    var User_Type = 'kui';

                    if($KUI.Location.UrlFragments[1]==='telegram' && $KUI.Location.UrlFragments[2]==='miembros' && $KUI.Location.UrlFragments[3]){
                        User_ID = $KUI.Location.UrlFragments[3];
                        User_Type = 'telegram';
                    }
                    $KUI.Template.Layouts.Common.Prepare();

                    $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+`kui/user/profile?ID=${User_ID}&Type=${User_Type}&sso=${$KUI.UserSession.CurrentSSO}`,function (data){
                        var Continue = false;
                        if(($KUI.Location.UrlFragments[1]==='telegram' && $KUI.Location.UrlFragments[2]==='miembros' && $KUI.Location.UrlFragments[3])
                            || ($KUI.Location.UrlFragments[1]==='usuarios' && $KUI.Location.UrlFragments[2])){
                            Continue = true;
                        }
                        if(Continue===false){
                            return;
                        }

                        if(!data['Description']){
                            data['Description'] = '^a';
                        }

                        $KUI.Template.Layouts.Common.SetTitle(`Usuario: ${data['Name']}`);

                        if(data['ItsMe']===true){
                            $KUI.Template.Layouts.Common.AddButton('Editar Perfil',function (){
                                $KUI.UserSession.Modals.ProfileEdit();
                            });
                            $KUI.Template.Layouts.Common.AddButton('Cambiar Foto',function (){
                                $KUI.UserSession.Modals.ProfilePhotoEdt();
                            });
                        }

                        var Container_Main      = $('<div class="KUI3_BodyMerge _FullHeight"></div>').appendTo($KUI.Template.Layouts.Common.Body);
                        var Container_Group     = $('<div class="KUI3_BodyGroup"></div>').appendTo(Container_Main);

                        var Group_Information   = $('<div class="KUI3_BodyGroup_Small"></div>').appendTo(Container_Group);
                        var Group_Desktops      = $('<div class="KUI3_BodyGroup_Large"></div>').appendTo(Container_Group);

                        var Title_Information   = $(`<h2 class="GroupTitle"><span class="_Text">Información sobre ${data['Name']}</span><span class="_TextDesc">${data['Phrase']}</span></h2><div class="__Container_Information"></div>`).appendTo(Group_Information);
                        var Title_Other         = $(`<h2 class="GroupTitle"><span class="_Text">Escritorios de ${data['Name']}</span><span class="_TextDesc">#ViernesDeEscritorio</span></h2><div class="__Container_Desktops"></div>`).appendTo(Group_Desktops);

                        var Content_Information = $('div.__Container_Information');
                        var Content_Desktops    = $('div.__Container_Desktops');

                        var Loader_Desktops     = $('<div class="KUI3_Loader"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>').appendTo(Content_Desktops);

                        /* INFORMACIÓN DE USUARIO */
                        $(`<div class="KUI3_Profiles_Image"><img alt="${data['Name']}" src="${data['Photo']}"></div>`).appendTo(Content_Information);
                        $(`<div class="KUI3_Profiles_Phrase"><p class="kEL_ac">${data['Phrase']}</p></div>`).appendTo(Content_Information);
                        $(`<div class="KUI3_Element_SubCaption">Datos Generales</div>`).appendTo(Content_Information);
                        var Table = $(`<div class="KUI3_Element_TableOfKeyValue"></div>`).appendTo(Content_Information);
                        $(`<div class="KUI3_Element_SubCaption">Biografía</div>`).appendTo(Content_Information);
                        $(`<div class="KUI3_Profiles_Bio"><p>${data['Description']}</p></div>`).appendTo(Content_Information);


                        $(`<div class="Row"><p>Nick</p><p>${data['Name']}</p></div>`).appendTo(Table);
                        $(`<div class="Row"><p>Registro</p><p>${data['Date']['Created']}</p></div>`).appendTo(Table);
                        $(`<div class="Row"><p>Últ. vez</p><p>${data['Date']['Modified']}</p></div>`).appendTo(Table);
                        if(data['Comments']===1){
                            $(`<div class="Row"><p>N.º Comentarios</p><p><strong>${data['Comments']}</strong> comentario</p></div>`).appendTo(Table);
                        } else {
                            $(`<div class="Row"><p>N.º Comentarios</p><p><strong>${data['Comments']}</strong> comentarios</p></div>`).appendTo(Table);
                        }

                        $(`<div class="KUI3_Element_SubCaption">Escritorios</div>`).appendTo(Content_Information);
                        var DeskTable = $(`<div class="KUI3_Element_TableOfKeyValue"></div>`).appendTo(Content_Information);
                        if(data['Desktops']['Count']===1){
                            $(`<div class="Row"><p>Publicados</p><p><strong>${data['Desktops']['Count']}</strong> escritorio.</p></div>`).appendTo(DeskTable);
                        } else {
                            $(`<div class="Row"><p>Publicados</p><p><strong>${data['Desktops']['Count']}</strong> escritorios.</p></div>`).appendTo(DeskTable);
                        }
                        if(data['Desktops']['Score']===1){
                            $(`<div class="Row"><p>Puntuación</p><p><strong>${data['Desktops']['Score']}</strong> punto.</p></div>`).appendTo(DeskTable);
                        } else {
                            $(`<div class="Row"><p>Puntuación</p><p><strong>${data['Desktops']['Score']}</strong> puntos.</p></div>`).appendTo(DeskTable);
                        }
                        var DeskPor = Math.round(data['Desktops']['Count']*100/data['Desktops']['Score']);
                        $(`<div class="Row"><p>Por. %</p><p><strong>${DeskPor}</strong> %.</p></div>`).appendTo(DeskTable);

                        if(data['Telegram']!==false){
                            $(`<div class="KUI3_Element_SubCaption">Telegram</div>`).appendTo(Content_Information);
                            $(`<div class="KUI3_Profiles_TelegramImage"><img alt="${data['Telegram']['Name']}" src="${data['Telegram']['Photo']}"><p>${data['Telegram']['Name']}</p></div>`).appendTo(Content_Information);
                            var TelegramTable = $(`<div class="KUI3_Element_TableOfKeyValue"></div>`).appendTo(Content_Information);
                            if(data['Messages']===1){
                                $(`<div class="Row"><p>N.º Mensajes</p><p><strong>${data['Messages']}</strong> mensaje</p></div>`).appendTo(TelegramTable);
                            } else {
                                $(`<div class="Row"><p>N.º Mensajes</p><p><strong>${data['Messages']}</strong> mensajes</p></div>`).appendTo(TelegramTable);
                            }
                            if(data['Karma']===1){
                                $(`<div class="Row"><p>Pts. Dharma</p><p><strong>${data['Karma']}</strong> punto</p></div>`).appendTo(TelegramTable);
                            } else {
                                $(`<div class="Row"><p>Pts. Dharma</p><p><strong>${data['Karma']}</strong> puntos</p></div>`).appendTo(TelegramTable);
                            }
                            if(data['Telegram']['Strikes']===1){
                                $(`<div class="Row"><p>Strikes</p><p><strong>${data['Telegram']['Strikes']}</strong> strike</p></div>`).appendTo(TelegramTable);
                            } else {
                                $(`<div class="Row"><p>Strikes</p><p><strong>${data['Telegram']['Strikes']}</strong> strikes</p></div>`).appendTo(TelegramTable);
                            }
                            var Role = 'Usuario';
                            if(data['Telegram']['Role']==='MEM'){
                                Role = 'Administrador';
                            } else if(data['Telegram']['Role']==='MOD'){
                                Role = 'Modedrador Sénior';
                            } else if(data['Telegram']['Role']==='JUNIOR'){
                                Role = 'Modedrador Junior';
                            }
                            $(`<div class="Row"><p>Rol</p><p><strong>${Role}</strong></p></div>`).appendTo(TelegramTable);
                        }

                        /* ESCRITORIOS */
                        $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+`kui/article/list?Type=viernesdeescritorio&UserId=${User_ID}&sso=${$KUI.UserSession.CurrentSSO}`,function (desk){
                            Loader_Desktops.remove();

                            var Desktops = 0;

                            var List_Container = $('<ul class="KUI3_Listings_Feedy"></ul>').appendTo(Content_Desktops);
                            var Columns_N = $KUI.Desktops.ColumnCalculate();

                            var Desktops_Columns = [];
                            for(ii=0;ii<Columns_N;ii++){
                                Desktops_Columns[ii] = $(`<ul class="Feedy_Column" style="width:calc(100% / ${Columns_N})"></ul>`).appendTo(List_Container);
                            }
                            var N = 0;

                            $.each(desk,function (i,v){
                                Desktops++;

                                var Item = $(`<li class="_Desktop KUI3_Element_Box"></li>`).appendTo(Desktops_Columns[N]);

                                $(`<img alt="${v['Display_Name']}" src="${v['Image']}">`).appendTo(Item);
                                $(`<h4>${v['Score']}</h4>`).appendTo(Item);
                                $(`<div class="_Date">Semana #${v['Week']} del ${v['Year']}</div>`).appendTo(Item);

                                Item.click(function (){
                                    $KUI.Location.Goto(v['Url']);
                                });

                                N++;
                                if(N>Columns_N){
                                    N = 0;
                                }
                            });
                            if(Desktops<=0){
                                $('<div class="KUI3_Element_Message">Este usuario no ha publicado escritorios.</div>').appendTo(Content_Desktops);
                            }
                        });
                    });
                }
            }
        },
        ColorCtrl:{
            ColorLoad:function (){
                switch ($KUI.Template.ColorCtrl.GetCurrent()){
                    case "KUI3_Dark":
                        $KUI.Template.ColorCtrl.SetDark();
                        break;
                    default:
                        $KUI.Template.ColorCtrl.SetLight();
                        break;
                }
            },
            GetCurrent:function (){
                return localStorage.getItem("KUI3_Theme_Color")==="KUI3_Dark"?"KUI3_Dark":"KUI3_Light";
            },
            SetLight:function (){
                var Body = $('body');

                localStorage.setItem("KUI3_Theme_Color","KUI3_Light");
                $KUI.Template.MainContent.addClass("KUI3_Light");
                $KUI.Template.MainContent.removeClass("KUI3_Dark");
                Body.removeClass("dark");
            },
            SetDark:function (){
                var Body = $('body');

                localStorage.setItem("KUI3_Theme_Color","KUI3_Dark");
                $KUI.Template.MainContent.removeClass("KUI3_Light");
                $KUI.Template.MainContent.addClass("KUI3_Dark");
                Body.addClass("dark");
                $KUI.Template.ColorCtrl.EditorColor = "dark";
                $KUI.Template.ColorCtrl.EditorSubColor = "monokai";
            },
            EditorColor:"default",
            EditorSubColor:"default",
        }
    },
    Navigation: {
        MakeHeader:function(){
            $('nav.KUI3_SiteNav').find('>div._NavHead').eq(0).append('<div class="k_ac k_mb10"><img alt="Karla\'s Project" src="/wp-content/themes/karlasflex/imaging/assets/kui/profile_rnd.png"><span class="_w">Karla\'s</span><span class="_gg"> Project</span></div>');
        },
        NavigationLoad:function(){
            var ListUL = $('nav.KUI3_SiteNav').find('ul._NavList').eq(0);
            $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+"kui/menu",function (data){
                var ListScroller = ListUL.find('div.mCSB_container').eq(0);
                if(ListScroller.length>0)
                {
                    ListUL = ListScroller;
                }

                $.each(data,function(i,v){
                    if(typeof v['KUI'] === 'boolean' && v['KUI']===true)
                    {
                        MenuItem = $('<li><a data-goto="'+v['Url']+'"><img alt="'+v['Title']+'" src="'+v['Icon']+'">'+v['Title']+'</a></li>').appendTo(ListUL);
                    }
                    else
                    {
                        MenuItem = $('<li><a href="'+v['Url']+'"><img alt="'+v['Title']+'" src="'+v['Icon']+'">'+v['Title']+'</a></li>').appendTo(ListUL);
                    }
                    if(v['Menu']!==false)
                    {
                        var SubMenu = $('<ul class="Sub"></ul>').appendTo(MenuItem);
                        $.each(v['Menu'],function (i,v){
                            if(typeof v['KUI'] === 'boolean' && v['KUI']===true)
                            {
                                $('<li><a data-goto="'+v['Url']+'"><img alt="'+v['Title']+'" src="'+v['Icon']+'">'+v['Title']+'</a></li>').appendTo(SubMenu);
                            }
                            else
                            {
                                $('<li><a href="'+v['Url']+'"><img alt="'+v['Title']+'" src="'+v['Icon']+'">'+v['Title']+'</a></li>').appendTo(SubMenu);
                            }

                        });
                    }
                });
            },function (){},3600*24);
        }
    },
    Location: {
        CurrentUrl: false,
        UrlFragments: false,
        Push: function(){
            if($KUI.Location.CurrentUrl === window.location.pathname) return;

            $KUI.Location.CurrentUrl = window.location.pathname;
            $KUI.Location.UrlFragments = window.location.pathname.split('/');

            if(($KUI.Location.UrlFragments[1]==='articulo'
                || $KUI.Location.UrlFragments[1]==='tutorial'
                || $KUI.Location.UrlFragments[1]==='noticia'
                || $KUI.Location.UrlFragments[1]==='colaboracion'
                || $KUI.Location.UrlFragments[1]==='evento'
                || $KUI.Location.UrlFragments[1]==='viernesdeescritorio'
                || $KUI.Location.UrlFragments[1]==='wikipost') && $KUI.Location.UrlFragments[2] && $KUI.Location.UrlFragments[2]!=='publicar' && !$KUI.Location.UrlFragments[3])
            {
                $KUI.Modals.OpenArticle($KUI.Location.UrlFragments[2]);
            }
            else
            {
                var Article = $('#ModalArticle');
                if(Article.length>0)
                {
                    $KUI.Modals.CloseAll();
                }

                if($KUI.Location.UrlFragments[1]==='blog')
                {
                    $KUI.Template.Layouts.Blog.MakeLayout();
                    document.title = "Blog - Karla's Project";
                }
                else if($KUI.Location.UrlFragments[1]==='videos')
                {
                    $KUI.Template.Layouts.Tutorials.MakeLayout();
                    document.title = "Tutoriales - Karla's Project";
                }
                else if($KUI.Location.UrlFragments[1]==='viernesdeescritorio')
                {
                    $KUI.Template.Layouts.Desktops.MakeLayout();
                    document.title = "Escritorios - Karla's Project";

                    if($KUI.Location.UrlFragments[2]==='publicar'){
                        $KUI.Desktops.Modals.Upload();
                    }
                }
                else if($KUI.Location.UrlFragments[1]==='wiki')
                {
                    $KUI.Template.Layouts.Wiki.MakeLayout();
                    var WikiCookie = $KUI.Resources.Cookies.Get('kui3_md_wiki');
                    document.title = "Linuxpedia - Karla's Project";
                    if(WikiCookie!=='true')
                    {
                        $KUI.Resources.Cookies.Set('kui3_md_wiki','true',60);
                        //$KUI.Modals.ModalText('¡Eh!','Esta página está en contrucción. Eso significa que algunos artículos están en proceso de ser redactados.','/wp-content/themes/karlasflex/imaging/assets/obj/tuxy.jpg');
                    }
                }
                else if($KUI.Location.UrlFragments[1]==='guia-linux')
                {
                    location.href = '/guia-linux';
                }
                else if(($KUI.Location.UrlFragments[1]==='usuarios' && $KUI.Location.UrlFragments[2])
                    || ($KUI.Location.UrlFragments[1]==='telegram' && $KUI.Location.UrlFragments[2]==='miembros' && $KUI.Location.UrlFragments[3]))
                {
                    $KUI.Template.Layouts.Users.Profile();
                    document.title = "Usuarios - Karla's Project";
                }
                else if(($KUI.Location.UrlFragments[1]===''))
                {
                    $KUI.Template.Layouts.FrontPage.MakeLayout();
                    document.title = "Karla's Project";
                }
                else
                {
                    $KUI.StaticPages.LoadPage(window.location.pathname);
                    document.title = "Karla's Project";
                }
            }


            if($KUI.Template.IsMobile===true)
            {
                var NL = $('nav.KUI3_SiteNav').find('ul._NavList').eq(0);
                var NP = NL.parents('nav').eq(0);
                var NB = $('#KUI3._Mobile');
                NL.hide();
                NP.css('bottom','auto');
                NB.css('overflow-y','auto');
            }

            $('div.KUI3_SearcherResults').eq(0).hide();
            $('ul.KUI3_CardButtons').eq(0).hide();

            $KUI.System.Triggers();
        },
        Goto: function(Url){
            $KUI.Modals.PreModalLocation = $KUI.Location.CurrentUrl;
            if (typeof (Url) == 'object' || !Url)
            {
                Url = $(this).attr('data-goto');
            }
            window.history.pushState(null,"", Url);
            $KUI.Location.Push();
        },
        Query:{
            Read:function (Key){
                var queryString = window.location.search;
                var Params = new URLSearchParams(queryString);
                return Params.get(Key);
            }
        }
    },
    Modals:{
        OpenArticle:function(Slug){
            var ContainerMain  = $('#ModalArticle');
            if(ContainerMain.length>0)
            {
                $KUI.Modals.CloseAll();
            }

            var NB = $('#KUI3._Mobile');
            NB.css('overflow-y','hidden');

            $KUI.Template.MainContent.append('<div id="ModalArticle"><div class="_ModalClose"></div><div class="KUI3_Article_Body"><div class="InnerBody KUI3_Scroll"><div class="Container KUI3_TableOfContents KUI3_ContentFormal"></div></div><div class="KUI3_Article_Aside KUI3_Scroll"></div></div></div>');
            $('#ModalArticle > div._ModalClose').eq(0).click(function (){
                $KUI.Modals.CloseAll();
                $KUI.Location.Goto($KUI.Modals.PreModalLocation);
            });

            $KUI.Articles.References.TableOfContentsTries = 0;
            $KUI.Articles.References.CurrentSlug = Slug;
            $KUI.Articles.References.CurrentID = false;

            ContainerMain = $(ContainerMain.selector);
            var ContainerBody  = ContainerMain.find('div.KUI3_Article_Body').eq(0);
            var ContainerInner = ContainerBody.find('>div.InnerBody').eq(0);
            var ContainerAside = ContainerMain.find('div.KUI3_Article_Aside').eq(0);

            $KUI.System.Triggers();

            ContainerMain.prepend('<h1><ul><button class="KUI3_Button _Color _ComntBtn">Comentarios</button></ul><span class="_ArticleTitle"><img class="KUI3_Loader" alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></span></h1>' + ($KUI.Template.IsMobile?'<abbr class="_PostMeta"></abbr>':''));
            ContainerInner.prepend('<div class="KUI3_Loader kEL_mt_15 kEL_mb_15"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>');

            var CommentsBtn = ContainerMain.find('button._ComntBtn').eq(0);
            CommentsBtn.click(function (){
                $KUI.Articles.Comments.DisplayComments();
            });

            if($KUI.Location.UrlFragments[1]==='viernesdeescritorio')
            {
                ContainerMain.addClass('ViernesDeEscritorio');
            }

            if(ContainerAside.find('div.mCSB_container').length>0)
            {
                $KUI.Articles.References.AsideContainer = ContainerAside.find('div.mCSB_container').eq(0);
            }
            else
            {
                $KUI.Articles.References.AsideContainer = ContainerAside;
            }

            $KUI.Articles.References.AsideContainer.append('<div class="KUI3_Loader"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>');

            $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint + "kui/article/byslug/" + Slug+"?sso="+$KUI.UserSession.CurrentSSO,function(data) {

                var Handler_ArticleTitle = ContainerMain.find('span._ArticleTitle').eq(0);
                var Handler_ArticleLoader = ContainerMain.find('h1 > img.KUI3_Loader').eq(0);
                var Handler_ArticleMeta  = ContainerMain.find('abbr._PostMeta').eq(0);
                var Handler_AsideLoader  = ContainerAside.find('div.KUI3_Loader').eq(0);

                Handler_ArticleLoader.remove();
                Handler_AsideLoader.remove();
                Handler_ArticleTitle.html(data['Caption']);
                Handler_ArticleMeta.html(data['Date']);

                $KUI.Articles.References.CurrentID = data['ID'];

                $KUI.Articles.Introduction(data['Caption'],data['Background'],data['Excerpt'],data['Date'],data['Visits']);

                document.title = data['Caption']+" - Karla's Project";

                if(data['Video'])
                {
                    $KUI.Articles.AttachVideo(data['Video']);
                }

                if(data['Type']!=='viernesdeescritorio')
                {
                    $KUI.Articles.Sharer();
                }

                if(data['Type']==='viernesdeescritorio')
                {
                    $KUI.Articles.FridayScorers(data['VdE']);
                }

                $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint + "kui/article/byslug/" + Slug + "/content",function(response){
                    var ContainerMain  = $('#ModalArticle');
                    var ContainerBody  = ContainerMain.find('div.KUI3_Article_Body').eq(0);
                    var ContainerInner = ContainerBody.find('>div.InnerBody').eq(0);
                    var Handler_ContentLoader  = ContainerInner.find('div.KUI3_Loader').eq(0);

                    Handler_ContentLoader.remove();

                    if(response['Type']==='viernesdeescritorio')
                    {
                        ContainerInner.find('div.Container').eq(0).append('<img alt="#ViernesDeEscritorio" src="'+response['Background']+'">');
                    }
                    else
                    {
                        ContainerInner.find('div.Container').eq(0).append(response['Content']);
                    }

                    $KUI.Articles.WikiAbstraction(ContainerInner.find('div.Container').eq(0),$KUI.Articles.References.AsideContainer);

                    if(data['Type']==='post' || data['Type']==='videopost' || data['Type']==='wiki_post')
                    {
                        $KUI.Articles.TableOfContents.Prepare();

                        if(data['Fuentes'])
                        {
                            $KUI.Articles.AttachCustom('Recursos Externos',data['Fuentes']);
                        }
                    }


                    if(data['Type']==='wiki_post'){
                        var Header = $('#ModalArticle').find('h1').eq(0).find('>ul').eq(0);
                        var RevBtnTxt = $KUI.Template.IsMobile?'&nbsp;&nbsp;&nbsp;':'Historial de Revisiones';
                        var RevBtn = $('<button class="KUI3_Button _HistWikiBtn">'+RevBtnTxt+'</button>').appendTo(Header);
                        RevBtn.click(function (){
                            if($KUI.UserSession.IsSigned){
                                $KUI.Wiki.Revisions(data['ID']);
                            }
                            else {
                                $KUI.Wiki.Messages.NoSession();
                            }
                        });
                        var EditBtnTxt = $KUI.Template.IsMobile?'&nbsp;&nbsp;&nbsp;':'Editar Artículo';
                        var EditBtn = $('<button class="KUI3_Button _EditWikiBtn">'+EditBtnTxt+'</button>').appendTo(Header);
                        EditBtn.click(function (){
                            if($KUI.UserSession.IsSigned){
                                $KUI.Modals.Editor.WikiEdit(data['ID']);
                            }
                            else {
                                $KUI.Wiki.Messages.NoSession();
                            }
                        });

                        if(data['Authors']){
                            $KUI.Articles.AttachCustom('Contribuidores','<ul class="Article_Editors NoList"></ul>');

                            var EditorsaUL = $('ul.Article_Editors').eq(0);
                            $.each(data['Authors'],function (i,v){
                                if(v['ID']===data['Creator']){
                                    $(`<li class="KUI3_Element_UserList"><a data-goto="${v['Url']}"><span class="Display"><img alt="${v['Name']}" src="${v['Photo']}">${v['Name']}</span><div class="Autor">Autor</div></a></li>`).appendTo(EditorsaUL);
                                }
                                else {
                                    $(`<li class="KUI3_Element_UserList"><a data-goto="${v['Url']}"><span class="Display"><img alt="${v['Name']}" src="${v['Photo']}">${v['Name']}</span></a></li>`).appendTo(EditorsaUL);
                                }
                            });
                        }
                    }

                    $KUI.System.Triggers();
                });
            });

            $KUI.System.Triggers();
        },
        CloseAll:function (){
            var Article = $('#ModalArticle');
            if(Article.length>0)
            {
                Article.remove();
                $KUI.Articles.Comments.CloseComments();
            }
            var NB = $('#KUI3._Mobile');
            NB.css('overflow-y','auto');
        },
        ModalInd:0,
        ModalOpen:function (Callback){
            $KUI.Modals.ModalInd++;
            var ModalHandler = $('<div class="KUI3_Modal" data-modal-id="'+$KUI.Modals.ModalInd+'"></div>').appendTo($('body'));
            ModalHandler.animate({
                'opacity' : '1'
            },500);
            var ModalContainer = $('<div class="KUI3_ModalContainer"></div>').prependTo(ModalHandler);
            if(typeof Callback === 'function')
            {
                Callback(ModalContainer,$KUI.Modals.ModalInd);
                $KUI.System.Triggers();
            }
        },
        ModalClose:function (){
            var Modal = $('div.KUI3_Modal[data-modal-id="'+$KUI.Modals.ModalInd+'"]');
            if(Modal.length>0)
            {
                Modal.animate({
                    'opacity' : '0'
                },500,function (){
                    Modal.remove();
                });

                $KUI.Modals.ModalInd--;
                $KUI.System.Triggers();
            }
        },
        ModalText:function (Title,Message,Image,Callback){
            $KUI.Modals.ModalOpen(function (Content){
                Content.width('400px');
                Content.append('<h1>'+Title+'</h1>');
                if(typeof Image === 'string')
                {
                    Content.append('<div class="kEL_ac kEL_mb_15"><img style="max-width: 100%;height: auto;" src="'+Image+'" alt="'+Title+'"></div>');
                }
                Content.append('<div class="KUI3_Element_SquareWhite">'+Message+'</div>');
                var Footer = $('<div class="KUI3_Footer"></div>').appendTo(Content);
                var BtnClose = $('<button class="KUI3_Button _Color">Cerrar</button>').appendTo(Footer);
                if(typeof Callback ==='function')
                {
                    BtnClose.click(function (){
                        Callback();
                    });
                }
                else
                {
                    BtnClose.click(function (){
                        $KUI.Modals.ModalClose();
                    });
                }
            });
        },
        PreModalLocation:'/',
        Editor:{
            Close:function (){
                $('#EditorWindow').remove();
            },
            WikiEdit:function (WikiID){
                var Window = $('<div id="EditorWindow"></div>').appendTo($('body'));
                var WindowBox = $('<div class="EditorBox"></div>').appendTo(Window);
                var WindowClose = $('<div class="_ModalClose"></div>').appendTo(WindowBox);
                var WindowWaiter = $('<div class="KUI3_Loader kEL_mt_15" style="width: 100%;"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>').appendTo(WindowBox);

                WindowClose.click(function (){
                    $KUI.Modals.Editor.Close();
                });

                $KUI.Requests.Get($KUI.System.EndPoint+`kui/wiki/editor?sso=${$KUI.UserSession.CurrentSSO}&wiki_id=${WikiID}`,function (data){
                    $KUI.Wiki.IsNew = false;
                    $KUI.Wiki.Edit(Window,WindowBox,WindowWaiter,data);
                });
            },
        },
        Templates:{
            Modal_Error_Code_Template:function (Title,Message,$Code){
                $KUI.Modals.ModalOpen(function (Content){
                    Content.width('400px');
                    Content.append('<h1>'+Title+'</h1>');
                    Content.append('<div class="kEL_ac kEL_mb_15"><img style="max-width: 100px;height: auto;" src="/wp-content/themes/karlasflex/imaging/assets/icons/svg/ghost.svg" alt="'+Title+'"></div>');
                    Content.append('<div class="KUI3_Element_SquareWhite">'+Message+'</div>');
                    switch ($Code){
                        case 400:
                            Content.append('<div class="KUI3_Element_SquarePre">Stop code: 400.<br>Message: Bad Request.</div>');
                            break;
                        case 401:
                            Content.append('<div class="KUI3_Element_SquarePre">Stop code: 401.<br>Message: Unauthorized.</div>');
                            break;
                        case 404:
                            Content.append('<div class="KUI3_Element_SquarePre">Stop code: 404.<br>Message: Not Found.</div>');
                            break;
                        case 429:
                            Content.append('<div class="KUI3_Element_SquarePre">Stop code: 429.<br>Message: Too Many Requests.</div>');
                            break;
                        case 500:
                            Content.append('<div class="KUI3_Element_SquarePre">Stop code: 500.<br>Message: Internal Server Error.</div>');
                            break;
                    }

                    var Footer = $('<div class="KUI3_Footer"></div>').appendTo(Content);
                    var BtnClose = $('<button class="KUI3_Button _Color">Cerrar</button>').appendTo(Footer);
                    BtnClose.click(function (){
                        $KUI.Modals.ModalClose();
                    });
                });
            },
            Modal_Error_Code:function (StopCode,$Other_Title,$Other_Message){
                switch (StopCode){
                    case 400:
                        $KUI.Modals.Templates.Modal_Error_Code_Template('Error en la petición','Petición inválida. Algunos parámetros no se han enviado correctamente.',400)
                        break;
                    case 401:
                        $KUI.Modals.Templates.Modal_Error_Code_Template('Permiso denegado','Para realizar esta acción necesitas haber iniciado sesión con una cuenta de usuario.',401)
                        break;
                    case 404:
                        $KUI.Modals.Templates.Modal_Error_Code_Template('¡Ouch!','El contenido que buscabas no se encuentra.',404)
                        break;
                    case 429:
                        $KUI.Modals.Templates.Modal_Error_Code_Template('Fluddy','¡Eh! ¡Estás haciendo flood/spam. Intenta calmarte.',429)
                        break;
                    default:
                        $KUI.Modals.ModalText($Other_Title,$Other_Message);
                        break;
                }
            }
        }
    },
    Articles:{
        References:{
            AsideContainer:false,
            VisitsCounter:false,
            CurrentID:false,
            CurrentSlug:false,
            VisitsInt:0,
            TableOfContentsTries:0,
            TableOfContentsHandler:false
        },
        TableOfContents:{
            Prepare:function (){

                var Target = $KUI.Articles.References.AsideContainer;

                var Content = '<figure class="KUI3_Element_Box" id="KUI3_TOC" style="display: none;"><h4 class="KUI3_Element_Box_Caption">Tabla de Contenidos</h4><ol class="KUI3_Element_TableOfContents"><img class="kEL_mt_10 kEL_mb_10 kEL_w60p" alt="Tabla de Contenidos" src="/wp-content/themes/karlasflex/imaging/assets/icons/svg/book.svg"></ol></figure>';


                if($KUI.Template.IsMobile===true)
                {
                    Target = $('#ModalArticle > div.KUI3_Article_Body > div.InnerBody > div.Container').eq(0);
                    Target.prepend(Content);
                }
                else
                {
                    Target.append(Content);
                }


                $KUI.Articles.References.TableOfContentsTries = 0;
                var MyTOC = $('#KUI3_TOC');
                var Table = $('div.KUI3_TableOfContents');
                if((MyTOC.length<0 || Table.length<0) || !MyTOC.find('ol.KUI3_Element_TableOfContents').eq(0).hasClass('_LoadComplete'))
                {
                    $KUI.Articles.References.TableOfContentsHandler = setInterval(function (){
                        if($KUI.Articles.References.TableOfContentsTries>15) return;
                        $KUI.Articles.TableOfContents.Update();
                        $KUI.Articles.References.TableOfContentsTries++;
                    },500);
                }
            },
            Update:function (){
                var TOCcont = $('#KUI3_TOC');
                var MyTOC = TOCcont.find('ol.KUI3_Element_TableOfContents').eq(0);
                var Table = $('div.KUI3_TableOfContents');

                if($('#ModalArticle').length<0)
                {
                    clearInterval($KUI.Articles.References.TableOfContentsHandler);
                    $KUI.Articles.References.TableOfContentsHandler = false;
                    return;
                }

                if(MyTOC.hasClass('_LoadComplete'))
                {
                    clearInterval($KUI.Articles.References.TableOfContentsHandler);
                    $KUI.Articles.References.TableOfContentsHandler = false;
                    return;
                }

                MyTOC.addClass('_LoadComplete');
                clearInterval($KUI.Articles.References.TableOfContentsHandler);
                $KUI.Articles.References.TableOfContentsHandler = false;

                var Count = 0;

                Table.find('h1, h2, h3').each(function (){
                    var Anchor = "<a id='" + $(this).html() + "'></a>";
                    $(this).before(Anchor);

                    MyTOC.append('<li><a href="#' + $(this).html() + '">' + $(this).html() + '</a></li>');

                    TOCcont.show();
                    Count++;
                });

                if(Count <= 0){
                    TOCcont.remove();
                }
            }
        },
        Introduction:function (Caption,Background,Text,Date,Visits){
            var Target = $KUI.Articles.References.AsideContainer;
            var Info = '<span><img class="kEL_mdm_16 kEL_mr_5" alt="Fecha" src="/wp-content/themes/karlasflex/imaging/assets/icons/general/date.png">'+Date+'</span><br><span class="__VisitsCounter"><img class="kEL_mdm_16 kEL_mr_5" alt="Visitas" src="/wp-content/themes/karlasflex/imaging/assets/icons/general/visits.png">'+Visits+'</span>';

            var MyBox = $('<figure class="KUI3_Element_Box"><h4 class="KUI3_Element_Box_Caption">Resumen</h4></figure>').appendTo(Target);
            $('<div class="kEL_h150 kEL_Cover kEL_Shd_b kEL_lnd_lat" style="background-image: url('+Background+')"></div>').appendTo(MyBox);
            $('<p class="kEL_fx_s kEL_mt_15">'+Info+'</p>').appendTo(MyBox);

            if(typeof Text === 'string' && Text)
            {
                $('<p class="kEL_lh15 kEL_mt_15 kEL_aj kEL_lh15 kEL_fx_s">'+Text+'</p>').appendTo(MyBox);
            }

            $KUI.Articles.References.VisitsInt = parseInt(Visits.replace('.',''));

            if($KUI.Articles.References.VisitsCounter!==false){
                clearInterval($KUI.Articles.References.VisitsCounter);
            }

            $KUI.Articles.References.VisitsCounter = setInterval(function (){
                var CounterSpan = $('span.__VisitsCounter');
                if(CounterSpan.length<=0)
                {
                    clearInterval($KUI.Articles.References.VisitsCounter);
                    $KUI.Articles.References.VisitsCounter = false;
                }

                $.ajax({
                    async: true,
                    dataType: "json",
                    url: $KUI.System.EndPoint + "kui/article/byslug/" + $KUI.Articles.References.CurrentSlug + "/refresh",
                    cache : false,
                    success:function(data)
                    {
                        var NewInfo = '<img class="kEL_mdm_16 kEL_mr_5" alt="Visitas" src="/wp-content/themes/karlasflex/imaging/assets/icons/general/visits.png">'+data['Visits']+'';

                        if($KUI.Articles.References.VisitsInt !== parseInt(data['Visits'].replace('.','')))
                        {
                            NewInfo = '<img class="kEL_mdm_16 kEL_mr_5" alt="Visitas" src="/wp-content/themes/karlasflex/imaging/assets/icons/general/visits.png"><strong class="kEL_fw_nn kEL_bc_r">'+data['Visits']+'</strong>';
                            $KUI.Articles.References.VisitsInt = parseInt(data['Visits'].replace('.',''));


                            setTimeout(function (){
                                var NewInfo = '<img class="kEL_mdm_16 kEL_mr_5" alt="Visitas" src="/wp-content/themes/karlasflex/imaging/assets/icons/general/visits.png">'+data['Visits']+'';
                                CounterSpan.html(NewInfo);
                            },500);
                        }
                        CounterSpan.html(NewInfo);

                        var Score = $('._DesktopScore');
                        if(Score.length>0)
                        {
                            Score.html(data['Score']);
                        }

                        var CommentsBtn = $('#ModalArticle').find('button._ComntBtn').eq(0);
                        CommentsBtn.html('Comentarios ('+data['Comments']+')');
                    }
                });


            },5000);
        },
        AttachVideo:function (VideoID){
            var Target = $KUI.Articles.References.AsideContainer;
            Target.append('<figure class="KUI3_Element_Box"><ul class="KUI3_Element_Box_MiniUL"><li><a target="_blank" href="https://youtu.be/'+VideoID+'"><img alt="youtube" src="/wp-content/themes/karlasflex/imaging/assets/icons/general/youtubemini.png">Ver en YouTube</a></li></ul><h4 class="KUI3_Element_Box_Caption">Vídeo Relacionado</h4><iframe style="height: 194px;" class="kEL_lnd_lat kEL_lnd_btn kEL_brd_0" src="https://www.youtube.com/embed/'+VideoID+'" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></figure>');
        },
        AttachCustom:function(Title,Content){
            var Target = $KUI.Articles.References.AsideContainer;
            if($KUI.Template.IsMobile===true)
            {
                Target = $('#ModalArticle > div.KUI3_Article_Body > div.InnerBody > div.Container').eq(0);
            }
            Target.append('<figure class="KUI3_Element_Box KUI3_ContentFormal"><h4 class="KUI3_Element_Box_Caption">'+Title+'</h4>'+Content+'</figure>');
        },
        Sharer:function(){
            var Sharer = {
                'facebook'  : '?share=facebook',
                'twitter'   : '?share=twitter',
                'telegram'  : '?share=telegram',
                'whatsapp'  : '?share=jetpack-whatsapp',
                'reddit'    : '?share=reddit',
                'tumblr'    : '?share=tumblr',
                'pinterest' : '?share=pinterest',
            }

            $('#ModalArticle > div.KUI3_Article_Body > div.InnerBody div.Container').prepend('<ul class="_Sharer"></ul>');
            $.each(Sharer,function (i,v){
                $('#ModalArticle ul._Sharer').append('<li><a target="_blank" href="'+v+'"><img alt="'+i+'" src="/wp-content/themes/karlasflex/imaging/assets/icons/social/'+i+'.png"></a></li>');
            });
        },
        Comments:{
            UpdateHandler:false,
            DisplayComments:function (){
                var CommentContainer = $('<div id="KUI3_Article_Comments"></div>').appendTo($('body'));
                var CommentTitle = $('<header>Comentarios<div class="_Close"></div></header>').appendTo(CommentContainer);
                var CommentList = $('<ul class="CommentList KUI3_Scroll"></ul>').appendTo(CommentContainer);
                var CommentBox = $('<div class="Publisher"></div>').appendTo(CommentContainer);

                CommentTitle.find('div._Close').eq(0).click(function (){
                    $KUI.Articles.Comments.CloseComments();
                });

                CommentList.prepend('<div class="KUI3_Loader kEL_mt_15"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>');

                $KUI.Articles.Comments.UpdateComments();

                $KUI.Articles.Comments.UpdateHandler = setInterval(function (){
                    $KUI.Articles.Comments.UpdateComments();
                },10000);
            },
            UpdateComments:function (){
                var CommentList = $('#KUI3_Article_Comments > ul.CommentList').eq(0);
                var CommentPublisher = $('#KUI3_Article_Comments > div.Publisher').eq(0);

                $KUI.Requests.Get($KUI.System.EndPoint+'kui/article/byslug/'+$KUI.Articles.References.CurrentSlug+'/comments?sso='+$KUI.UserSession.CurrentSSO,function (data){
                    var MCS = CommentList.find('div.mCSB_container').eq(0);

                    if(MCS.length>0)
                    {
                        CommentList = MCS;
                    }

                    CommentList.find('li').each(function (){
                        $(this).remove();
                    });

                    $('#KUI3_Article_Comments').find('div.KUI3_Loader').eq(0).remove();

                    var Comments = data['Comments'];
                    var UserData = data['User'];

                    var Count = 0;

                    $.each(Comments,function (i,v){
                        Count++;
                        CommentList.append('<li><div class="_Photo"><img alt="'+v['Name']+'" src="'+v['Photo']+'"></div><div class="_Content"><h4>'+v['Name']+'</h4><span>'+v['Data']+'</span><div class="KUI3_ContentFormal">'+v['Text']+'</div></div></li>');
                    });

                    if(Count===0)
                    {
                        CommentList.append('<li><div class="KUI3_Message kEL_w100 kEL_ac">Sin comentarios.</div></li>');
                    }

                    var CommentsBtn = $('#ModalArticle').find('button._ComntBtn').eq(0);
                    CommentsBtn.html('Comentarios ('+Count+')');

                    $KUI.System.Triggers();

                    if(!CommentPublisher.hasClass('_Ldl'))
                    {
                        CommentPublisher.addClass('_Ldl');

                        var Form = $('<div class="KUI3_Form"></div>').appendTo(CommentPublisher);

                        if(UserData['Signed']===false)
                        {
                            Form.append('<p>Necesitas iniciar sesión para comentar.</p>');
                        }
                        else
                        {
                            var SpanInput = $('<span><abbr>*</abbr>Texto:</span>').appendTo(Form);
                            var TextInput = $('<textarea name="Text" placeholder="Escribe tu comentario" style="height: 50px;" class="kEL_mb_15"></textarea>').appendTo(Form);
                            var SendBtn = $('<button class="KUI3_Button">Enviar</button>').appendTo(Form);

                            $KUI.System.Triggers();

                            SendBtn.click(function (){
                                if(SendBtn.attr('disabled')) return;
                                SendBtn.attr('disabled','true');

                                Form.find('span.FormError').each(function (){
                                    $(this).remove();
                                });
                                Form.find('.ColorRed').each(function (){
                                    $(this).removeClass('ColorRed');
                                });

                                var Errors = false;

                                if(!TextInput.val())
                                {
                                    SpanInput.append('<span class="FormError">El comentario no puede estar vacío.</span>');
                                    TextInput.addClass('ColorRed');
                                    Errors = true;
                                }
                                else if(TextInput.val().length<5)
                                {
                                    SpanInput.append('<span class="FormError">Un comentario muy corto. ¿No?</span>');
                                    TextInput.addClass('ColorRed');
                                    Errors = true;
                                }
                                else if(TextInput.val().length>512)
                                {
                                    SpanInput.append('<span class="FormError">Un comentario muy largo. ¿No?</span>');
                                    TextInput.addClass('ColorRed');
                                    Errors = true;
                                }

                                if(!Errors)
                                {
                                    $KUI.Requests.Post($KUI.System.EndPoint+'kui/article/byslug/'+$KUI.Articles.References.CurrentSlug+'/comments',function (data){
                                        $KUI.Articles.Comments.UpdateComments();
                                        SendBtn.removeAttr('disabled');
                                        TextInput.val('');
                                    },{
                                        sso : $KUI.UserSession.CurrentSSO,
                                        text : TextInput.val()
                                    },function (){
                                        $KUI.Modals.ModalOpen(function (Content){
                                            Content.width('400px');
                                            Content.append('<h1>Ouch</h1>');
                                            Content.append('<p>No se ha podido publicar el comentario.</p>');
                                            var Footer = $('<div class="KUI3_Footer"></div>').appendTo(Content);
                                            var BtnClose = $('<button class="KUI3_Button _Color">Cerrar</button>').appendTo(Footer);
                                            BtnClose.click(function (){
                                                $KUI.Modals.ModalClose();
                                            });
                                        });
                                    });
                                }
                                else
                                {
                                    SendBtn.removeAttr('disabled');
                                }
                            });
                        }
                    }
                });
            },
            CloseComments:function (){
                var CommentContainer = $('#KUI3_Article_Comments');
                CommentContainer.remove();
                clearInterval($KUI.Articles.Comments.UpdateHandler);
                $KUI.Articles.Comments.UpdateHandler = false;
            }
        },
        FridayScorers:function (FridayData){
            var Target = $KUI.Articles.References.AsideContainer;
            var MyBox = $('<figure class="KUI3_Element_Box KUI3_Element_FridayCard"><h4 class="KUI3_Element_Box_Caption">Usuario</h4></figure>').appendTo(Target);
            $('<img class="AuthorImage kEL_mt_15 kEL_block" alt="'+FridayData['Author_Name']+'" src="'+FridayData['Author_Photo']+'">').appendTo(MyBox);
            $('<h2 class="AuthorName">'+FridayData['Author_Name']+'</h2>').appendTo(MyBox);
            $('<p class="Description">'+FridayData['Text']+'</p>').appendTo(MyBox);
            var Box_Menu = $(`<ul class="KUI3_Element_Box_MiniUL"></ul>`).appendTo(MyBox);
            var Box_Menu_Li = $(`<li></li>`).appendTo(Box_Menu);
            var Box_Menu_A_Profile = $(`<a data-goto="${FridayData['Author_Url']}"><img alt="perfil" src="/wp-content/themes/karlasflex/imaging/assets/icons/general/user.png">Ver perfil de ${FridayData['Author_Name']}</a>`).appendTo(Box_Menu_Li);
            Box_Menu_A_Profile.click(function(){
                $KUI.Location.Goto(FridayData['Author_Url']);
            });

            var MyScoreBox = $('<figure class="KUI3_Element_Box KUI3_Element_FridayCard"><h4 class="KUI3_Element_Box_Caption">Puntuación</h4></figure>').appendTo(Target);
            $('<div class="Score _DesktopScore">'+FridayData['Score']+'</div>').appendTo(MyScoreBox);

            if(FridayData['Status']['CanVote']===false)
            {
                $('<div class="KUI3_Message" style="margin-top: 25px;">Necesitas iniciar sesión para poder votar.</div>').appendTo(MyScoreBox);
            }
            else if(FridayData['Status']['IsVoted']===true)
            {
                $('<div class="KUI3_Message" style="margin-top: 25px;">Ya has votado este escritorio.</div>').appendTo(MyScoreBox);
            }
            else
            {
                var Footer = $('<div class="KUI3_Element_Box_Footer kEL_ac kEL_mt_25" style="margin-top: 25px;"></div>').appendTo(MyScoreBox);
                var Button = $('<button class="KUI3_Button">Votar +1</button>').appendTo(Footer);
                Button.click(function (){
                    Button.attr('disabled','disabled');
                    $KUI.Requests.Post($KUI.System.EndPoint+"kui/desktops/upvote",function (data){
                        $('._DesktopScore').html(data['Score']);
                        Button.remove();
                        $KUI.Modals.ModalText('Votación realizada','¡Genial! Has realizado un <strong>upvote +1</strong>. Este escritorio ahora tiene <strong>'+data['Score']+' puntos</strong>.','/wp-content/themes/karlasflex/imaging/assets/backgrounds/desktopbrush.png');
                        sessionStorage.clear();
                    },{
                        sso : $KUI.UserSession.CurrentSSO,
                        desktop : $KUI.Articles.References.CurrentID
                    },function (){
                        Button.removeAttr('disabled');
                        $KUI.Modals.ModalText('Error al votar','No ha sido posible realizar la votación.');
                    })
                });
            }
        },
        WikiAbstraction:function (Container,Aside){
            if(Aside.length<=0 || Container.length<=0){
                return;
            }

            if($KUI.Template.IsMobile===true){
                return;
            }

            Container.find('div.KUI3_WikiAbstractionTable').each(function (){
                var Caption = $(this).find('.KUI3_Element_SubCaption').eq(0).html();
                var Inner   = $(this).find('.KUI3_Element_TableOfKeyValue').eq(0).html();

                var NewBox = $('<figure class="KUI3_Element_Box"></figure>').appendTo(Aside);
                var NewBox_Title = $('<h4 class="KUI3_Element_Box_Caption">'+Caption+'</h4>').appendTo(NewBox);

                if(Caption==='Fuentes'){
                    $('<div class="kEL_ac"><img src="/wp-content/themes/karlasflex/imaging/assets/icons/svg/fountain.svg" class="kEL_w60p kEL_mb_15"></div>').appendTo(NewBox);
                }

                var NewBox_Table = $('<div class="KUI3_Element_TableOfKeyValue"></div>').appendTo(NewBox);

                $(this).remove();

                NewBox_Table.html(Inner);
            });
        }
    },
    StaticPages:{
        LoadPage:function (PageSlug){
            $KUI.Template.Layouts.Common.Prepare();

            var PageContainers;
            if($KUI.Location.UrlFragments[1]==='blog')
            {
                PageContainers = $KUI.StaticPages.Layouts.BlogLayout();
            }
            else if($KUI.Location.UrlFragments[1]==='colaboraciones')
            {
                PageContainers = $KUI.StaticPages.Layouts.ColaborationsLayout();
            }
            else
            {
                PageContainers = $KUI.StaticPages.Layouts.Default();
            }
            var ContainerMain = PageContainers[0];
            var ContainerAside = PageContainers[1];

            $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+"kui/page/byslug/"+PageSlug,function (data){
                $KUI.Template.Layouts.Common.SetTitle(data['Caption']);

                var PageContainer = $('<div class="KUI3_BodyGroup_Container"></div>').appendTo(ContainerMain);

                var PageWidgets = data['Widgets'];

                if(PageWidgets!==false)
                {
                    $.each(PageWidgets,function (i,v){
                        eval("$KUI.StaticWidgets."+v+"(ContainerAside);");
                    })
                }
                else
                {
                    $KUI.StaticWidgets.BlogSuscribe(ContainerAside);
                }

                $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+"kui/page/byslug/"+PageSlug+"/content",function (subdata){
                    var Body = $('<div class="KUI3_ContentFormal">'+subdata['Content']+'</div>').appendTo(PageContainer);
                });
            },function (){},3600*24);
        },
        Layouts:{
            Default:function (){
                var PageBody = $KUI.Template.MainContent.find('div.KUI3_SiteBody').eq(0);
                var ScrollContainer = PageBody.find('div.mCSB_container').eq(0);
                if(ScrollContainer.length>0)
                {
                    PageBody = ScrollContainer;
                }

                var PageSubBody = $('<div class="KUI3_BodyMerge"></div>').appendTo(PageBody);
                var PageGroup = $('<div class="KUI3_BodyGroup _IsPage" style=""></div>').appendTo(PageSubBody);
                var PageContainer = $('<div class="KUI3_BodyGroup_Large _Strech"></div>').appendTo(PageGroup);
                var PageAside = $('<div class="KUI3_BodyGroup_Small _Aside"></div>').appendTo(PageGroup);
                return [PageContainer,PageAside];
            },
            BlogLayout:function (){
                return [false,false];
            },
            ColaborationsLayout:function (){
                return [false,false];
            }
        }
    },
    Cache:{
        AsyncronousRequest:function (RequestUrl,Callback,ErrorCallback,Duration=0){
            Duration = Duration*1000;
            var LocalJSON = sessionStorage.getItem('chc_async_rel20210825_'+RequestUrl);
            var Expiry    = parseInt(sessionStorage.getItem('chc_async_rel20210825_i_'+RequestUrl));
            var Now       = parseInt(Date.now());
            //LocalJSON = false;
            if(Duration!==0 && Now>Expiry)
            {
                LocalJSON = false;
            }
            if(LocalJSON)
            {
                Callback(JSON.parse(LocalJSON));
            }
            else
            {
                if(Duration!==0)
                {
                    sessionStorage.setItem('chc_async_rel20210825_i_'+RequestUrl,""+(Now+Duration)+"");
                }
                $.ajax({
                    async: true,
                    dataType: "json",
                    url: RequestUrl,
                    cache : true,
                    success:function (data){
                        sessionStorage.setItem('chc_async_rel20210825_'+RequestUrl, JSON.stringify(data));
                        Callback(data);
                    },
                    error:function (){
                        if (typeof ErrorCallback === 'function')
                        {
                            ErrorCallback();
                        }
                    }
                });
            }
        },
    },
    Requests: {
        Get:function (RequestUrl, Callback, ErrorCallback) {
            $.ajax({
                async: true,
                dataType: "json",
                url: RequestUrl,
                cache: false,
                success: function (data) {
                    localStorage.setItem('chc_async_' + RequestUrl, JSON.stringify(data));
                    Callback(data);
                },
                error: function () {
                    if (typeof ErrorCallback === 'function') {
                        ErrorCallback();
                    }
                }
            });
        },
        Post:function (RequestUrl, Callback,Variables, ErrorCallback) {
            $.ajax({
                async: true,
                dataType: "json",
                type:"POST",
                data:Variables,
                url: RequestUrl,
                cache: true,
                success: function (data) {
                    localStorage.setItem('chc_async_' + RequestUrl, JSON.stringify(data));
                    Callback(data);
                },
                error: function () {
                    if (typeof ErrorCallback === 'function') {
                        ErrorCallback();
                    }
                }
            });
        },
        Delete:function (RequestUrl, Callback,Variables, ErrorCallback) {
            $.ajax({
                async: true,
                dataType: "json",
                type:"DELETE",
                data:Variables,
                url: RequestUrl,
                cache: true,
                success: function (data) {
                    localStorage.setItem('chc_async_' + RequestUrl, JSON.stringify(data));
                    Callback(data);
                },
                error: function () {
                    if (typeof ErrorCallback === 'function') {
                        ErrorCallback();
                    }
                }
            });
        },
    },
    UserSession:{
        CurrentSSO:false,
        Assignnance:function (){
            var SSOCookie = $KUI.Resources.Cookies.Get('kui3_ssoticket');
            if(!SSOCookie)
            {
                SSOCookie = $KUI.Resources.RandomGUID();
                $KUI.Resources.Cookies.Set('kui3_ssoticket',SSOCookie,60);
            }
            $KUI.UserSession.CurrentSSO = SSOCookie;

            if(typeof $KUI.Location.Query.Read('kui_forgot') === 'string' && $KUI.Location.Query.Read('code'))
            {
                $KUI.UserSession.Modals.PasswordCreate();
            }

            if(typeof $KUI.Location.Query.Read('kui_subedt') === 'string' && $KUI.Location.Query.Read('subuuid'))
            {
                $KUI.SubscriptionManager.SubscriptionID = $KUI.Location.Query.Read('subuuid');
                $KUI.Requests.Get($KUI.System.EndPoint+"kui/subscription?subuuid="+$KUI.Location.Query.Read('subuuid'),function (data){
                    $KUI.UserSession.CurrentUser.IsDefined = true;
                    $KUI.UserSession.CurrentUser.SubActivated = true;
                    $KUI.UserSession.CurrentUser.SubEmail = data['Email'];
                    $KUI.UserSession.CurrentUser.SubInterest1 = data['Int1'];
                    $KUI.UserSession.CurrentUser.SubInterest2 = data['Int2'];
                    $KUI.UserSession.CurrentUser.SubInterest3 = data['Int3'];
                    $KUI.SubscriptionManager.SuscriptionModal();
                });
            }
            if(typeof $KUI.Location.Query.Read('kui_sub') === 'string')
            {
                $KUI.Modals.ModalText('Boletín actualizado','<p>Ya no recibirás más correos electrónicos. Tu boletín ha sido eliminado.</p>','/wp-content/themes/karlasflex/imaging/assets/icons/general/subscribe.png');
            }
            if(typeof $KUI.Location.Query.Read('kui_val') === 'string' && $KUI.Location.Query.Read('kui_val') === 'mail')
            {
                $KUI.Modals.ModalText('Verificación completada','<p>La verificación mediante correo electrónico ha sido completada.</p>','/wp-content/themes/karlasflex/imaging/assets/icons/general/subscribe.png');
            }
            if(typeof $KUI.Location.Query.Read('kui_val') === 'string' && $KUI.Location.Query.Read('kui_val') === 'user')
            {
                $KUI.Modals.ModalText('Verificación completada','<p>Tu cuenta de usuario ha sido verificada. ¡Gracias!</p>');
            }
        },
        IsSigned:false,
        Templates:{
            UserCard:function (){
                var CardHandler = $('.KUI3_SiteHeader_UserCard');
                CardHandler.append('<div class="KUI3_Loader"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>');
                $KUI.UserSession.IsSigned = false;
                $KUI.Requests.Get($KUI.System.EndPoint + 'kui/usersession/card?sso='+$KUI.UserSession.CurrentSSO,function (data){
                    $KUI.UserSession.CurrentUser.IsDefined = true;
                    CardHandler.find('div.KUI3_Loader').eq(0).remove();
                    var CardButtons = $('<ul class="KUI3_CardButtons"></ul>').appendTo($('body'));
                    var Photo;
                    if(data['Display_Photo'].length>0)
                    {
                        Photo = '/wp-content/uploads/kui_system/users_profiles/'+data['Display_Photo'];
                    }
                    else
                    {
                        Photo = $KUI.Resources.Assets.DefUser;
                    }
                    var CardProfile;
                    if($KUI.Template.IsMobile===true){
                        CardProfile = $('<div class="CardProfile"><img class="Profile" alt="'+data['Display_Name']+'" src="'+Photo+'.jpg"><ul class="KUI3_CardButtons"></ul></div>').appendTo(CardHandler);
                        CardButtons = CardProfile.find('ul.KUI3_CardButtons').eq(0);
                    }
                    else
                    {
                        CardProfile = $('<div class="CardProfile">Hola, hola, <a>'+data['Display_Name']+'<img class="Profile" alt="'+data['Display_Name']+'" src="'+Photo+'.jpg"></a></div>').appendTo(CardHandler);

                        var ABtn = CardProfile.find('>a').eq(0);
                        ABtn.click(function (){
                            CardButtons.toggle();
                        });
                        $(window).click(function() {
                            CardButtons.hide();
                        });
                        CardButtons.click(function(event){
                            event.stopPropagation();
                        });
                        ABtn.click(function(event){
                            event.stopPropagation();
                        });
                    }

                    if(data['Dektop_Published']===false)
                    {
                        $KUI.Desktops.CanPublish = true;
                    }

                    $KUI.UserSession.IsSigned = true;
                    $KUI.UserSession.CurrentUser.DisplayProfile = Photo+'.jpg';
                    $KUI.UserSession.CurrentUser.DisplayName = data['Display_Name'];
                    $KUI.UserSession.CurrentUser.SubActivated = data['Subscription']['IsSuscribed'];
                    $KUI.UserSession.CurrentUser.SubInterest1 = data['Subscription']['Interest1'];
                    $KUI.UserSession.CurrentUser.SubInterest2 = data['Subscription']['Interest2'];
                    $KUI.UserSession.CurrentUser.SubInterest3 = data['Subscription']['Interest3'];
                    $KUI.UserSession.CurrentUser.Email        = data['Email'];
                    $KUI.UserSession.CurrentUser.DisplayBio   = data['Display_Bio'];


                    var BtnProfile = $('<li><a>Ver mi Perfil</a></li>').appendTo(CardButtons);
                    var BtnPhoto = $('<li><a>Cambiar Foto</a></li>').appendTo(CardButtons);
                    $('<li class="HL"></li>').appendTo(CardButtons);
                    var BtnDesktop = $('<li><a>Publicar Escritorio</a></li>').appendTo(CardButtons);
                    $('<li class="HL"></li>').appendTo(CardButtons);
                    var BtnBoletin = $('<li><a>Gestionar Boletín</a></li>').appendTo(CardButtons);
                    var BtnConfig = $('<li><a>Preferencias</a></li>').appendTo(CardButtons);
                    $('<li class="HL"></li>').appendTo(CardButtons);
                    var BtnLogout = $('<li><a>Cerrar sesión</a></li>').appendTo(CardButtons);

                    BtnLogout.click(function (){
                        $KUI.UserSession.Modals.Logout();
                    });
                    BtnDesktop.click(function (){
                        $KUI.Desktops.Modals.Upload();
                    });
                    BtnBoletin.click(function (){
                        $KUI.SubscriptionManager.SuscriptionModal();
                    });
                    BtnProfile.click(function (){
                        $KUI.Location.Goto(`/usuarios/${data['ID']}`);
                    });
                    BtnPhoto.click(function (){
                        $KUI.UserSession.Modals.ProfilePhotoEdt();
                    });
                    BtnConfig.click(function (){
                        $KUI.UserSession.Modals.ProfileEdit();
                    });

                },function (){
                    $KUI.UserSession.CurrentUser.IsDefined = true;
                    CardHandler.find('div.KUI3_Loader').eq(0).remove();
                    var CardButtons = $('<ul class="CardMainButtons"></ul>').appendTo(CardHandler);
                    var BtnRegister = $('<li><a>Registro</a></li>').appendTo(CardButtons);
                    var BtnLogin = $('<li><a>Iniciar Sesión</a></li>').appendTo(CardButtons);

                    BtnRegister.click(function (){
                        $KUI.UserSession.Modals.Signup();
                    });
                    BtnLogin.click(function (){
                        $KUI.UserSession.Modals.Login();
                    });
                });
            },
        },
        Modals:{
            Signup:function (){
                $KUI.Modals.ModalOpen(function (Handler,I){
                    Handler.width('450px');
                    Handler.append('<h1>Registro de usuario</h1>');
                    Handler.append('<div class="KUI3_Element_BoxBackground" style="background-position-y: 41%;background-image: url(/wp-content/themes/karlasflex/imaging/assets/backgrounds/copito.jpg);"></div>');

                    var FormHandler = $('<div class="KUI3_Form"></div>').appendTo(Handler);
                    FormHandler.append('<span class="Caption _Is_Name"><abbr>*</abbr>Nombre o nickname:</span>');
                    FormHandler.append('<input type="text" placeholder="Escribe un nick o nombre" name="Username">');
                    FormHandler.append('<span class="Caption _Is_Mail"><abbr>*</abbr>Dirección email:</span>');
                    FormHandler.append('<input type="text" placeholder="ejemplo@dominio.com" name="Email">');
                    FormHandler.append('<span class="FormInfo">La dirección de email será privada en todo momento.</span>');
                    FormHandler.append('<span class="Caption _Is_Passwd"><abbr>*</abbr>Contraseña:</span>');
                    FormHandler.append('<div class="KUI3_Column"><div><input type="password" placeholder="Inventa una contraseña" name="Passwd1"></div><div><input type="password" placeholder="Repítela" name="Passwd2"></div></div>');
                    FormHandler.append('<span class="Caption _Is_GDPR"><abbr>*</abbr>Declaración de Privacidad:</span>');
                    FormHandler.append('<div class="KUI3_Forms_Checkbox"><div class="_Check"></div><div class="_Title">Acepto la <a target="_blank" href="https://karlaperezyt.com/informacion/privacidad/">Declaración de Privacidad</a></div><input name="GDPR" type="hidden"></div>');
                    var FormFooter = $('<div class="KUI3_Footer"></div>').appendTo(Handler);
                    var BtnClose = $('<button class="KUI3_Button">Cancelar</button>').appendTo(FormFooter);
                    var BtnSend = $('<button class="KUI3_Button _Color">Crear Usuario</button>').appendTo(FormFooter);
                    BtnClose.click(function (){
                        $KUI.Modals.ModalClose();
                    });
                    BtnSend.click(function (){
                        $KUI.UserSession.Actions.Signup(FormHandler,BtnSend);
                    });
                });
            },
            Login:function (){
                $KUI.Modals.ModalOpen(function (Handler,I){
                    Handler.width('450px');
                    Handler.append('<h1>Iniciar sesión</h1>');
                    Handler.append('<div class="KUI3_Element_BoxBackground" style="background-position-y: 41%;background-image: url(/wp-content/themes/karlasflex/imaging/assets/backgrounds/copito.jpg);"></div>');

                    var FormHandler = $('<div class="KUI3_Form"></div>').appendTo(Handler);
                    FormHandler.append('<span class="Caption _Is_Mail"><abbr>*</abbr>Dirección email:</span>');
                    FormHandler.append('<input type="text" placeholder="ejemplo@dominio.com" name="Email">');
                    FormHandler.append('<span class="Caption _Is_Passwd"><abbr>*</abbr>Contraseña:</span>');
                    FormHandler.append('<input type="password" placeholder="Escribe tú contraseña" name="Passwd1">');
                    FormHandler.append('<p><a class="kEL_fx_s kEL_mt_5 kEL_block _Btn_Forgot">He olvidado la contraseña</a></p>');

                    var Forgot = FormHandler.find('a._Btn_Forgot').eq(0);
                    Forgot.click(function (){
                        $KUI.UserSession.Modals.PasswordForgotten();
                    });

                    var FormFooter = $('<div class="KUI3_Footer"></div>').appendTo(Handler);
                    var BtnClose = $('<button class="KUI3_Button">Cancelar</button>').appendTo(FormFooter);
                    var BtnSend = $('<button class="KUI3_Button _Color">Iniciar sesión</button>').appendTo(FormFooter);
                    BtnClose.click(function (){
                        $KUI.Modals.ModalClose();
                    });
                    BtnSend.click(function (){
                        $KUI.UserSession.Actions.Login(FormHandler,BtnSend);
                    });
                });
            },
            Logout:function (){
                var CardHandler = $('.KUI3_SiteHeader_UserCard');
                $KUI.Modals.ModalOpen(function (Content){
                    Content.width('400px');
                    Content.append('<h1>Cerrar sesión</h1>');
                    Content.append('<p>Te dispones a cerrar la sesión de usuario. ¿Confirmar?</p>');
                    var Footer = $('<div class="KUI3_Footer"></div>').appendTo(Content);
                    var BtnClose = $('<button class="KUI3_Button">Cancelar</button>').appendTo(Footer);
                    BtnClose.click(function (){
                        $KUI.Modals.ModalClose();
                    });
                    var BtnAcept = $('<button class="KUI3_Button _Color">Aceptar</button>').appendTo(Footer);
                    BtnAcept.click(function (){
                        $KUI.UserSession.Actions.Logout();
                    });
                });
            },
            PasswordForgotten:function (){
                $KUI.Modals.ModalOpen(function (ForgotHandler,I){
                    ForgotHandler.width('400px');
                    ForgotHandler.append('<h1>Recuperar usuario</h1>');

                    var FormHandler = $('<div class="KUI3_Form"></div>').appendTo(ForgotHandler);
                    FormHandler.append('<span class="Caption _Is_Mail"><abbr>*</abbr>Dirección email:</span>');
                    FormHandler.append('<input type="text" placeholder="ejemplo@dominio.com" name="Email">');
                    FormHandler.append('<span class="FormInfo">Se te enviará un email con instrucciones para recuperar tu contraseña.</span>');

                    var FormFooter = $('<div class="KUI3_Footer"></div>').appendTo(ForgotHandler);
                    var BtnClose = $('<button class="KUI3_Button">Cancelar</button>').appendTo(FormFooter);
                    var BtnSend = $('<button class="KUI3_Button _Color">Enviar email</button>').appendTo(FormFooter);
                    BtnClose.click(function (){
                        $KUI.Modals.ModalClose();
                    });
                    BtnSend.click(function (){
                        $KUI.UserSession.Actions.PasswordForgotten(FormHandler,BtnSend);
                    });
                });
            },
            PasswordCreate:function (){
                $KUI.Modals.ModalOpen(function (Handler,I){
                    Handler.width('450px');
                    Handler.append('<h1>Crear contraseña</h1>');
                    Handler.append('<div class="KUI3_Element_BoxBackground" style="background-position-y: 41%;background-image: url(/wp-content/themes/karlasflex/imaging/assets/backgrounds/copito.jpg);"></div>');

                    var FormHandler = $('<div class="KUI3_Form"></div>').appendTo(Handler);
                    FormHandler.append('<span class="FormInfo">Ya casi estás. Simplemente inventate una contraseña nueva y procura no olvidarla otra vez.</span>');
                    FormHandler.append('<span class="Caption _Is_Passwd"><abbr>*</abbr>Contraseña:</span>');
                    FormHandler.append('<div class="KUI3_Column"><div><input type="password" placeholder="Inventa una contraseña" name="Passwd1"></div><div><input type="password" placeholder="Repítela" name="Passwd2"></div></div>');

                    var FormFooter = $('<div class="KUI3_Footer"></div>').appendTo(Handler);
                    var BtnClose = $('<button class="KUI3_Button">Cancelar</button>').appendTo(FormFooter);
                    var BtnSend = $('<button class="KUI3_Button _Color">Cambiar contraseña</button>').appendTo(FormFooter);
                    BtnClose.click(function (){
                        $KUI.Modals.ModalClose();
                        $KUI.Location.Goto('/');
                    });
                    BtnSend.click(function (){
                        $KUI.UserSession.Actions.PasswordCreate(FormHandler,BtnSend);
                    });
                });
            },
            ProfilePhotoEdt:function (){
                if($KUI.UserSession.IsSigned===false)
                {
                    $KUI.UserSession.Messages.NoSession();
                }
                else
                {
                    $KUI.Modals.ModalOpen(function (Handler,I){
                        Handler.width('450px');
                        Handler.append('<h1>Cambiar Foto de Perfil</h1>');


                        var FormHandler = $('<div class="KUI3_Form"></div>').appendTo(Handler);
                        FormHandler.append('<span class="Caption"><abbr>*</abbr>Imagen actual:</span>');
                        FormHandler.append(`<div class="KUI3_Profiles_Image"><img id="Pho" style="height:auto;" alt="${$KUI.UserSession.CurrentUser.DisplayName}" src="${$KUI.UserSession.CurrentUser.DisplayProfile}"></div>`);
                        FormHandler.append('<span class="Caption _Is_File"><abbr>*</abbr>Imagen:</span>');
                        FormHandler.append('<input type="file" placeholder="Selecciona una imagen" name="Desktop">');
                        FormHandler.append('<span class="FormInfo">La imagen deberá ser JPG, PNG o BMP. El tamaño no debe superar los 10 MB. La imagen de perfil será recortada a 640x640 píxeles (manteniendo una proporción del 1:1)</span>');
                        var FormFooter = $('<div class="KUI3_Footer"></div>').appendTo(Handler);
                        var BtnClose = $('<button class="KUI3_Button">Cancelar</button>').appendTo(FormFooter);
                        var BtnSend = $('<button class="KUI3_Button _Color">Cambiar</button>').appendTo(FormFooter);
                        BtnClose.click(function (){
                            $KUI.Modals.ModalClose();
                        });
                        BtnSend.click(function (){
                            $KUI.UserSession.Actions.PhotoUpload(FormHandler,BtnSend);
                        });
                        FormHandler.find('input[name="Desktop"]').eq(0).change(function(){
                            if (this.files && this.files[0]) {
                                var reader = new FileReader();

                                reader.onload = function (e) {
                                    $('#Pho').attr('src', e.target.result);
                                }

                                reader.readAsDataURL(this.files[0]);
                            }
                        });
                    });
                }
            },
            ProfileEdit:function (){
                $KUI.Modals.ModalOpen(function (Handler,I){
                    Handler.width('450px');
                    Handler.append('<h1>Configuración de perfil</h1>');

                    var FormHandler = $('<div class="KUI3_Form"></div>').appendTo(Handler);
                    FormHandler.append('<span class="Caption _Is_Name"><abbr>*</abbr>Nombre o nickname:</span>');
                    FormHandler.append('<input type="text" placeholder="Escribe un nick o nombre" name="Username" value="'+$KUI.UserSession.CurrentUser.DisplayName+'">');
                    FormHandler.append('<span class="Caption _Is_Mail"><abbr>*</abbr>Dirección email:</span>');
                    FormHandler.append('<input type="text" placeholder="ejemplo@dominio.com" name="Email" value="'+$KUI.UserSession.CurrentUser.Email+'">');
                    FormHandler.append('<span class="FormInfo">La dirección de email será privada en todo momento.</span>');
                    FormHandler.append('<span class="Caption _Is_Text"><abbr>*</abbr>Biografía:</span>');
                    FormHandler.append('<textarea style="height: 150px;" placeholder="Aquí puedes presentarte brevemente..." name="Text">'+$KUI.UserSession.CurrentUser.DisplayBio+'</textarea>');
                    var FormFooter = $('<div class="KUI3_Footer"></div>').appendTo(Handler);
                    var BtnClose = $('<button class="KUI3_Button">Cancelar</button>').appendTo(FormFooter);
                    var BtnSend = $('<button class="KUI3_Button _Color">Guardar perfil</button>').appendTo(FormFooter);
                    BtnClose.click(function (){
                        $KUI.Modals.ModalClose();
                    });
                    BtnSend.click(function (){
                        $KUI.UserSession.Actions.ProfileEdit(FormHandler,BtnSend);
                    });
                });
            }
        },
        Actions:{
            SessionReload:function (){
                var CardHandler = $('.KUI3_SiteHeader_UserCard');
                $KUI.Modals.ModalClose();
                CardHandler.html('');
                $KUI.UserSession.Templates.UserCard();
            },
            Signup:function (FormHandler,BtnSend){
                $(FormHandler).find('span.FormError').each(function (){
                    $(this).remove();
                });
                $(FormHandler).find('.ColorRed').each(function (){
                    $(this).removeClass('ColorRed');
                });

                if(BtnSend.attr('disabled')) return;
                BtnSend.attr('disabled','true');

                var Errors = false;

                var PasswordSpan = FormHandler.find('span._Is_Passwd').eq(0);
                var Password1 = FormHandler.find('input[name="Passwd1"]').eq(0);
                var Password2 = FormHandler.find('input[name="Passwd2"]').eq(0);
                if(!Password1.val()) {
                    Password1.addClass('ColorRed');
                    Password2.addClass('ColorRed');
                    PasswordSpan.append('<span class="FormError">La contraseña no puede estar vacía.</span>');
                    Errors = true;
                }
                else if(Password1.val()!==Password2.val()) {
                    Password1.addClass('ColorRed');
                    Password2.addClass('ColorRed');
                    PasswordSpan.append('<span class="FormError">Las contraseñas no coinciden.</span>');
                    Errors = true;
                }
                else if(Password1.val().length<5) {
                    Password1.addClass('ColorRed');
                    Password2.addClass('ColorRed');
                    PasswordSpan.append('<span class="FormError">La contraseña ha de tener, al menos, 5 carácteres.</span>');
                    Errors = true;
                }
                else if(Password1.val().length>56) {
                    Password1.addClass('ColorRed');
                    Password2.addClass('ColorRed');
                    PasswordSpan.append('<span class="FormError">La contraseña supera los 20 carácteres admitidos</span>');
                    Errors = true;
                }

                var NameSpan  = FormHandler.find('span._Is_Name').eq(0);
                var NameInput = FormHandler.find('input[name="Username"]').eq(0);
                if(!NameInput.val()) {
                    NameInput.addClass('ColorRed');
                    NameSpan.append('<span class="FormError">El nombre no puede estar vacío.</span>');
                    Errors = true;
                }
                else if(NameInput.val().length<5) {
                    NameInput.addClass('ColorRed');
                    NameSpan.append('<span class="FormError">El nombre ha de tener, al menos, 5 carácteres.</span>');
                    Errors = true;
                }
                else if(NameInput.val().length>56) {
                    NameInput.addClass('ColorRed');
                    NameSpan.append('<span class="FormError">El nombre supera los 20 carácteres admitidos.</span>');
                    Errors = true;
                }

                var MailSpan  = FormHandler.find('span._Is_Mail').eq(0);
                var MailInput = FormHandler.find('input[name="Email"]').eq(0);
                const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if(!MailInput.val()) {
                    MailInput.addClass('ColorRed');
                    MailSpan.append('<span class="FormError">El email no puede estar vacío.</span>');
                    Errors = true;
                }
                else if(MailInput.val().length<5) {
                    MailInput.addClass('ColorRed');
                    MailSpan.append('<span class="FormError">El email ha de tener, al menos, 5 carácteres.</span>');
                    Errors = true;
                }
                else if(MailInput.val().length>145) {
                    MailInput.addClass('ColorRed');
                    MailSpan.append('<span class="FormError">El email supera los 145 carácteres admitidos.</span>');
                    Errors = true;
                }
                else if(!re.test(String(MailInput.val()).toLowerCase()))
                {
                    MailInput.addClass('ColorRed');
                    MailSpan.append('<span class="FormError">El formato del email no es válido.</span>');
                    Errors = true;
                }

                var GDPR_Span  = FormHandler.find('span._Is_GDPR').eq(0);
                var GDPR_Input = FormHandler.find('input[name="GDPR"]').eq(0);
                if(!GDPR_Input.val() || parseInt(GDPR_Input.val())===0) {
                    GDPR_Input.addClass('ColorRed');
                    GDPR_Span.append('<span class="FormError">Debes aceptar la DdP.</span>');
                    Errors = true;
                }

                if(MailInput.val())
                {
                    $KUI.Requests.Get($KUI.System.EndPoint+"kui/usersession/mailcheck?email="+MailInput.val(),function (){
                        MailInput.addClass('ColorRed');
                        MailSpan.append('<span class="FormError">Ya existe un usuario con la dirección email indicada.</span>');
                        BtnSend.removeAttr('disabled');
                    },function (){
                        if(!Errors)
                        {
                            $KUI.Requests.Post($KUI.System.EndPoint+"kui/usersession/card",function (){
                                $KUI.UserSession.Messages.SignedUp();
                            },{
                                user_name : NameInput.val(),
                                user_mail : MailInput.val(),
                                user_pass : Password1.val(),
                                sso       : $KUI.UserSession.CurrentSSO
                            },function (){
                                BtnSend.removeAttr('disabled');
                                $KUI.UserSession.Messages.SignedErr(BtnSend);
                            });
                        }
                    });
                }
                else
                {
                    if(Errors)
                    {
                        BtnSend.removeAttr('disabled');
                    }
                }

                $KUI.System.Triggers();
            },
            Login:function (FormHandler,BtnSend){
                $(FormHandler).find('span.FormError').each(function (){
                    $(this).remove();
                });
                $(FormHandler).find('.ColorRed').each(function (){
                    $(this).removeClass('ColorRed');
                });

                if(BtnSend.attr('disabled')) return;
                BtnSend.attr('disabled','true');

                var Errors = false;

                var PasswordSpan = FormHandler.find('span._Is_Passwd').eq(0);
                var Password1 = FormHandler.find('input[name="Passwd1"]').eq(0);
                if(!Password1.val()) {
                    Password1.addClass('ColorRed');
                    PasswordSpan.append('<span class="FormError">La contraseña no puede estar vacía.</span>');
                    Errors = true;
                }

                var MailSpan  = FormHandler.find('span._Is_Mail').eq(0);
                var MailInput = FormHandler.find('input[name="Email"]').eq(0);
                const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if(!MailInput.val()) {
                    MailInput.addClass('ColorRed');
                    MailSpan.append('<span class="FormError">El email no puede estar vacío.</span>');
                    Errors = true;
                }
                else if(!re.test(String(MailInput.val()).toLowerCase()))
                {
                    MailInput.addClass('ColorRed');
                    MailSpan.append('<span class="FormError">El formato del email no es válido.</span>');
                    Errors = true;
                }

                if(MailInput.val())
                {
                    if(!Errors)
                    {
                        $KUI.UserSession.Assignnance();
                        $KUI.Requests.Post($KUI.System.EndPoint+"kui/usersession/login",function (){
                            $KUI.UserSession.Actions.SessionReload();
                        },{
                            user_mail : MailInput.val(),
                            user_pass : Password1.val(),
                            sso       : $KUI.UserSession.CurrentSSO
                        },function (){
                            BtnSend.removeAttr('disabled');
                            $KUI.UserSession.Messages.LoginErr(BtnSend);
                        });
                    }
                }
                else
                {
                    if(Errors)
                    {
                        BtnSend.removeAttr('disabled');
                    }
                }
                $KUI.System.Triggers();
            },
            Logout:function (){
                var CardHandler = $('.KUI3_SiteHeader_UserCard');
                $KUI.UserSession.CurrentSSO = false;
                $KUI.Resources.Cookies.Set('kui3_ssoticket','',60);
                $KUI.UserSession.Assignnance();
                $KUI.Modals.ModalClose();
                CardHandler.html('');
                $KUI.UserSession.Templates.UserCard();
            },
            PasswordCreate:function (FormHandler,BtnSend){
                $(FormHandler).find('span.FormError').each(function (){
                    $(this).remove();
                });
                $(FormHandler).find('.ColorRed').each(function (){
                    $(this).removeClass('ColorRed');
                });

                if(BtnSend.attr('disabled')) return;
                BtnSend.attr('disabled','true');

                var Errors = false;

                var PasswordSpan = FormHandler.find('span._Is_Passwd').eq(0);
                var Password1 = FormHandler.find('input[name="Passwd1"]').eq(0);
                var Password2 = FormHandler.find('input[name="Passwd2"]').eq(0);
                if(!Password1.val()) {
                    Password1.addClass('ColorRed');
                    Password2.addClass('ColorRed');
                    PasswordSpan.append('<span class="FormError">La contraseña no puede estar vacía.</span>');
                    Errors = true;
                }
                else if(Password1.val()!==Password2.val()) {
                    Password1.addClass('ColorRed');
                    Password2.addClass('ColorRed');
                    PasswordSpan.append('<span class="FormError">Las contraseñas no coinciden.</span>');
                    Errors = true;
                }
                else if(Password1.val().length<5) {
                    Password1.addClass('ColorRed');
                    Password2.addClass('ColorRed');
                    PasswordSpan.append('<span class="FormError">La contraseña ha de tener, al menos, 5 carácteres.</span>');
                    Errors = true;
                }
                else if(Password1.val().length>56) {
                    Password1.addClass('ColorRed');
                    Password2.addClass('ColorRed');
                    PasswordSpan.append('<span class="FormError">La contraseña supera los 20 carácteres admitidos</span>');
                    Errors = true;
                }

                if(!Errors)
                {
                    $KUI.Requests.Post($KUI.System.EndPoint+"kui/usersession/passwd",function (){
                        $KUI.Modals.ModalClose();
                        $KUI.UserSession.Messages.PassCreated();
                        var CardHandler = $('.KUI3_SiteHeader_UserCard');
                        CardHandler.html('');
                        $KUI.UserSession.Templates.UserCard();
                        $KUI.Location.Goto('/');
                    },{
                        user_pass : Password1.val(),
                        sso       : $KUI.UserSession.CurrentSSO,
                        code      : $KUI.Location.Query.Read('code')
                    },function (){
                        BtnSend.removeAttr('disabled');
                        $KUI.UserSession.Messages.PassCreatedErr();
                    });
                }
                else
                {
                    BtnSend.removeAttr('disabled');
                }

                $KUI.System.Triggers();
            },
            PasswordForgotten:function (FormHandler,BtnSend){
                $(FormHandler).find('span.FormError').each(function (){
                    $(this).remove();
                });
                $(FormHandler).find('.ColorRed').each(function (){
                    $(this).removeClass('ColorRed');
                });

                if(BtnSend.attr('disabled')) return;
                BtnSend.attr('disabled','true');

                var Errors = false;

                var MailSpan  = FormHandler.find('span._Is_Mail').eq(0);
                var MailInput = FormHandler.find('input[name="Email"]').eq(0);
                const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if(!MailInput.val()) {
                    MailInput.addClass('ColorRed');
                    MailSpan.append('<span class="FormError">El email no puede estar vacío.</span>');
                    Errors = true;
                }
                else if(!re.test(String(MailInput.val()).toLowerCase()))
                {
                    MailInput.addClass('ColorRed');
                    MailSpan.append('<span class="FormError">El formato del email no es válido.</span>');
                    Errors = true;
                }

                if(MailInput.val())
                {
                    if(!Errors)
                    {
                        $KUI.UserSession.Assignnance();
                        $KUI.Requests.Post($KUI.System.EndPoint+"kui/usersession/forgot",function (){
                            BtnSend.removeAttr('disabled');
                            $KUI.UserSession.Messages.PassForgott();
                        },{
                            user_mail : MailInput.val(),
                            sso       : $KUI.UserSession.CurrentSSO,
                        });
                    }
                }
                else
                {
                    if(Errors)
                    {
                        BtnSend.removeAttr('disabled');
                    }
                }

                $KUI.System.Triggers();
            },
            PhotoUpload:function (FormHandler,BtnSend){
                $(FormHandler).find('span.FormError').each(function (){
                    $(this).remove();
                });
                $(FormHandler).find('.ColorRed').each(function (){
                    $(this).removeClass('ColorRed');
                });

                if(BtnSend.attr('disabled')) return;
                BtnSend.attr('disabled','true');

                var Errors = false;

                var DesktopSpan  = FormHandler.find('span._Is_File').eq(0);
                var DesktopInput = FormHandler.find('input[name="Desktop"]').eq(0);
                if(DesktopInput.get(0).files.length===0){
                    DesktopSpan.append('<span class="FormError">Debes seleccionar un fichero.</span>');
                    Errors = true;
                }
                else if(DesktopInput.get(0).files[0].size>10000000){
                    DesktopSpan.append('<span class="FormError">El tamaño del fichero supera los 10 MB permitido.</span>');
                    Errors = true;
                }
                else if(DesktopInput.get(0).files[0].type!=='image/bmp' && DesktopInput.get(0).files[0].type!=='image/png' && DesktopInput.get(0).files[0].type!=='image/jpg' && DesktopInput.get(0).files[0].type!=='image/jpeg'){
                    DesktopSpan.append('<span class="FormError">El fichero ha de ser una imagen JPG, PNG o BMP.</span>');
                    Errors = true;
                }

                if(Errors===true)
                {
                    BtnSend.removeAttr('disabled');
                }
                else
                {
                    var formData = new FormData();
                    formData.append("sso", $KUI.UserSession.CurrentSSO);
                    formData.append("profile", DesktopInput.get(0).files[0]);

                    $.ajax({
                        url: $KUI.System.EndPoint+"kui/usersession/photo",
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(data){
                            $KUI.UserSession.CurrentUser.DisplayProfile = data['Image_Path'];
                            $KUI.UserSession.Actions.SessionReload();
                            sessionStorage.clear();
                            $KUI.Location.CurrentUrl = false;
                            $KUI.Location.Push();
                            $KUI.Modals.ModalClose();
                            $KUI.UserSession.Messages.UploadOk();
                        },
                        error:function (){
                            BtnSend.removeAttr('disabled');
                            $KUI.UserSession.Messages.UploadErr();
                        }
                    });
                }

                $KUI.System.Triggers();
            },
            ProfileEdit:function (FormHandler,BtnSend){
                $(FormHandler).find('span.FormError').each(function (){
                    $(this).remove();
                });
                $(FormHandler).find('.ColorRed').each(function (){
                    $(this).removeClass('ColorRed');
                });

                if(BtnSend.attr('disabled')) return;
                BtnSend.attr('disabled','true');

                var Errors = false;

                var TextSpan = FormHandler.find('span._Is_Text').eq(0);
                var TextInput = FormHandler.find('textarea[name="Text"]').eq(0);
                if(TextInput.val().length>512) {
                    TextInput.addClass('ColorRed');
                    TextSpan.append('<span class="FormError">La biografía supera los 512 carácteres admitidos</span>');
                    Errors = true;
                }

                var NameSpan  = FormHandler.find('span._Is_Name').eq(0);
                var NameInput = FormHandler.find('input[name="Username"]').eq(0);
                if(!NameInput.val()) {
                    NameInput.addClass('ColorRed');
                    NameSpan.append('<span class="FormError">El nombre no puede estar vacío.</span>');
                    Errors = true;
                }
                else if(NameInput.val().length<5) {
                    NameInput.addClass('ColorRed');
                    NameSpan.append('<span class="FormError">El nombre ha de tener, al menos, 5 carácteres.</span>');
                    Errors = true;
                }
                else if(NameInput.val().length>56) {
                    NameInput.addClass('ColorRed');
                    NameSpan.append('<span class="FormError">El nombre supera los 20 carácteres admitidos.</span>');
                    Errors = true;
                }

                var MailSpan  = FormHandler.find('span._Is_Mail').eq(0);
                var MailInput = FormHandler.find('input[name="Email"]').eq(0);
                const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if(!MailInput.val()) {
                    MailInput.addClass('ColorRed');
                    MailSpan.append('<span class="FormError">El email no puede estar vacío.</span>');
                    Errors = true;
                }
                else if(MailInput.val().length<5) {
                    MailInput.addClass('ColorRed');
                    MailSpan.append('<span class="FormError">El email ha de tener, al menos, 5 carácteres.</span>');
                    Errors = true;
                }
                else if(MailInput.val().length>145) {
                    MailInput.addClass('ColorRed');
                    MailSpan.append('<span class="FormError">El email supera los 145 carácteres admitidos.</span>');
                    Errors = true;
                }
                else if(!re.test(String(MailInput.val()).toLowerCase()))
                {
                    MailInput.addClass('ColorRed');
                    MailSpan.append('<span class="FormError">El formato del email no es válido.</span>');
                    Errors = true;
                }

                if(MailInput.val())
                {
                    var Continue = function (){
                        if(!Errors)
                        {
                            $KUI.Requests.Post($KUI.System.EndPoint+"kui/usersession/edit",function (d){
                                $KUI.UserSession.CurrentUser.DisplayName = NameInput;
                                $KUI.UserSession.CurrentUser.DisplayBio = TextInput;
                                $KUI.UserSession.CurrentUser.Email = MailInput;
                                $KUI.UserSession.Actions.SessionReload();
                                sessionStorage.clear();
                                $KUI.Location.CurrentUrl = false;
                                $KUI.Location.Push();
                                $KUI.Modals.ModalClose();
                                if(d['OK']==='MAIL'){
                                    $KUI.UserSession.Messages.ProfileChanged();
                                } else {
                                    $KUI.UserSession.Messages.ProfileEdit();
                                }
                            },{
                                user_name : NameInput.val(),
                                user_mail : MailInput.val(),
                                user_bio  : TextInput.val(),
                                sso       : $KUI.UserSession.CurrentSSO
                            },function (){
                                BtnSend.removeAttr('disabled');
                                $KUI.UserSession.Messages.ProfileErr();
                            });
                        }
                    };
                    if($KUI.UserSession.CurrentUser.Email !== MailInput.val()){
                        $KUI.Requests.Get($KUI.System.EndPoint+"kui/usersession/mailcheck?email="+MailInput.val(),function (){
                            MailInput.addClass('ColorRed');
                            MailSpan.append('<span class="FormError">Ya existe un usuario con la dirección email indicada.</span>');
                            BtnSend.removeAttr('disabled');
                        },function (){
                            Continue();
                        });
                    }
                    else
                    {
                        Continue();
                    }
                }
                else
                {
                    if(Errors)
                    {
                        BtnSend.removeAttr('disabled');
                    }
                }

                $KUI.System.Triggers();
            }
        },
        Messages:{
            LoginErr:function (){
                $KUI.Modals.ModalText('Error','<p>No se ha podido iniciar sesión. Revisa el email y la contraseña.</p><p>Si acabas de crear una cuenta de usuario, necesitarás activarla verificando tu email para poder iniciar sesión.</p>');
            },
            SignedErr:function (){
                $KUI.Modals.ModalText('Error','<p>No se ha podido crear el usuario. ¿El formulario contiene errores?</p>');
            },
            SignedUp:function (){
                $KUI.Modals.ModalText('¡Genial!','<p>El usuario ha sido creado. Revisa tu email, pues se te ha enviado un mensaje para verificar tú usuario.</p>',false,function (){
                    $KUI.Modals.ModalClose();
                    $KUI.Modals.ModalClose();
                });
            },
            PassCreated:function (){
                $KUI.Modals.ModalText('¡Genial!','<p>Tu contraseña ha sido recuperada. ¡Que bien!</p>');
            },
            PassCreatedErr:function (){
                $KUI.Modals.ModalText('Error','<p>No se ha podido recuperar el usuario.');
            },
            PassForgott:function (){
                $KUI.Modals.ModalText('¡Email enviado!','<p>Se ha enviado un email a tu bandeja de entrada para que intentes recuperar la contraseña.</p>');
            },
            NoSession:function (){
                $KUI.Modals.ModalText('No puedes hacer esto','Para continuar necesitas una cuenta de usuario e iniciar sesión en ella.','/wp-content/themes/karlasflex/imaging/assets/obj/penguins.png');
            },
            UploadErr:function (){
                $KUI.Modals.ModalText('Error al cambiar foto','¡Algo no ha ido bien! La foto de perfil no ha podido ser actualizada.','/wp-content/themes/karlasflex/imaging/assets/obj/penguins.png');

            },
            UploadOk:function (){
                $KUI.Modals.ModalText('La foto ha sido cambiada','¡Genial! Has actualizado tu foto de perfil. Esta será pública en los comentarios y en tu perfil de escritorios.');
            },
            ProfileEdit:function (d){
                $KUI.Modals.ModalText('Perfil editado','¡Genial! Has actualizado la información de tu perfil. Este será pública en los comentarios y en tu perfil de escritorios.');
                $KUI.UserSession.CurrentUser.DisplayProfile = d['Image_Path'];
                sessionStorage.clear();
                $KUI.Location.CurrentUrl = false;
                $KUI.Location.Push();
                $KUI.UserSession.Actions.SessionReload();
            },
            ProfileChanged:function (d){
                $KUI.Modals.ModalText('¡eh!','Al parecer has cambiado tu email. Se te ha enviado un email para verificar si es válido, por el momento, se te ha cerrado la sesión. En cuanto al resto de datos, han sido actualizados.');
                $KUI.UserSession.CurrentUser.DisplayProfile = d['Image_Path'];
                sessionStorage.clear();
                $KUI.Location.CurrentUrl = false;
                $KUI.Location.Push();
                $KUI.UserSession.Actions.SessionReload();
            },
            ProfileErr:function (){
                $KUI.Modals.ModalText('Perfil no editado','¡Vaya! Algo no ha ido bien al intentar modificar tu perfil.');
                $KUI.UserSession.CurrentUser.DisplayProfile = d['Image_Path'];
                sessionStorage.clear();
                $KUI.Location.CurrentUrl = false;
                $KUI.Location.Push();
                $KUI.UserSession.Actions.SessionReload();
            },
        },
        CurrentUser:{
            IsDefined:false,
            DisplayProfile:false,
            DisplayName:false,
            SubActivated:false,
            SubInterest1:false,
            SubInterest2:false,
            SubInterest3:false,
            SubEmail:false,
            Email:false,
            DisplayBio:false,
        }
    },
    Forms:{
        SetUpForms:function (){
            $KUI.Forms.Checkboxes();
        },
        Checkboxes:function (){
            $('div.KUI3_Forms_Checkbox').each(function (){
                var Status = false;
                var Container = $(this);
                var Checker = $(this).find('div._Check').eq(0);
                var Title   = $(this).find('div._Title').eq(0);
                var Input   = $(this).find('input').eq(0);

                if(Container.hasClass('_set')) return;
                Container.addClass('_set');

                if(parseInt(Input.val())===1 || Input.val()===true || Input.val()==='true'){
                    Status = true;
                }

                if(Status===true){
                    Checker.addClass('_Is_Checked');
                } else {
                    Checker.removeClass('_Is_Checked')
                }

                $(window).click(function() {
                    Container.removeClass('_Focus');
                });
                Container.click(function(event){
                    event.stopPropagation();
                    Container.addClass('_Focus');


                    Status = parseInt(Input.val()) === 1;

                    if(Status===true){
                        Checker.removeClass('_Is_Checked')
                        Input.val('0');
                    } else {
                        Checker.addClass('_Is_Checked');
                        Input.val('1');
                    }
                });
            });
        }
    },
    Desktops:{
        FeedPool:false,
        LocalStorage:{
            FeaturedImage:false
        },
        CanPublish:false,
        Templates:{
            FrontList:function (Container){
                var Loader = $('<div class="KUI3_Loader kEL_mt_15 kEL_mb_15"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>').appendTo(Container);
                var COL_COUNT = $KUI.Desktops.ColumnCalculate();

                $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+"kui/desktops/list?Featured=true&Limit="+COL_COUNT,function (data){
                    var List = $('<ul class="KUI3_Desktops_Trailer"></ul>').appendTo(Container);
                    Loader.remove();

                    if($KUI.Desktops.References.TrailerInterval)
                    {
                        clearInterval($KUI.Desktops.References.TrailerInterval);
                        $KUI.Desktops.References.TrailerInterval = false;
                    }

                    var W = List.width()/COL_COUNT;
                    var H = W*(9/16);

                    $.each(data,function (i,v){
                        if(i===0)
                        {
                            $KUI.Desktops.LocalStorage.FeaturedImage = v['Image'];
                        }

                        var LI = $('<li style="background-image: url('+v['Image']+');width:'+W+'px;height:'+H+'px;"></li>').appendTo(List);
                        var Score = $('<span class="_Score">'+v['Score']+'</span>').appendTo(LI);
                        var User = $('<span class="_Profile"><img alt="'+v['Display_Name']+'" src="'+v['Display_Photo']+'">'+v['Display_Name']+'</span>').appendTo(LI);
                        LI.click(function (){
                            $KUI.Location.Goto(v['Url']);
                        });
                    });

                    $KUI.System.Triggers();

                    $KUI.Desktops.References.TrailerInterval = setInterval(function (){
                        if($KUI.Desktops.References.WindowWidth===$(window).width()) return;
                        $KUI.Desktops.References.WindowWidth = $(window).width();
                        if(Container.length<=0)
                        {
                            clearInterval($KUI.Desktops.References.TrailerInterval);
                            $KUI.Desktops.References.TrailerInterval = false;
                        }

                        $('div._AsyncTrilerDesktopGroup').each(function (){$(this).html('');});
                        $KUI.Desktops.Templates.FrontList(Container);
                    },100);

                },function (){},3600*24);

            },
        },
        Modals:{
            Upload:function (){
                if($KUI.UserSession.IsSigned===false)
                {
                    $KUI.Desktops.Messages.NoSession();
                }
                else if($KUI.Desktops.CanPublish===false)
                {
                    $KUI.Desktops.Messages.NoUpload();
                }
                else
                {
                    $KUI.Modals.ModalOpen(function (Handler,I){
                        Handler.width('450px');
                        Handler.append('<h1>Subir Escritorio</h1>');
                        if($KUI.Desktops.LocalStorage.FeaturedImage!==false)
                        {
                            Handler.append('<div class="KUI3_Element_BoxBackground" style="background-image: url('+$KUI.Desktops.LocalStorage.FeaturedImage+');"></div>');
                        }

                        var FormHandler = $('<div class="KUI3_Form"></div>').appendTo(Handler);
                        FormHandler.append('<span class="Caption _Is_File"><abbr>*</abbr>Escritorio:</span>');
                        FormHandler.append('<input type="file" placeholder="Selecciona una imagen" name="Desktop">');
                        FormHandler.append('<span class="FormInfo">La imagen deberá ser JPG, PNG o BMP. El tamaño no debe superar los 10 MB.</span>');
                        FormHandler.append('<span class="Caption _Is_Text"><abbr>*</abbr>Mensaje:</span>');
                        FormHandler.append('<textarea style="height: 150px;" placeholder="Describe tu escritorio. P.E. Distribución, DE o WM, tema, iconos..." name="Text">');
                        FormHandler.append('<span class="Caption _Is_GDPR"><abbr>*</abbr>Declaración de Privacidad:</span>');
                        FormHandler.append('<div class="KUI3_Forms_Checkbox"><div class="_Check"></div><div class="_Title">Acepto la <a target="_blank" href="https://karlaperezyt.com/informacion/privacidad/">Declaración de Privacidad</a></div><input name="GDPR" type="hidden"></div>');
                        var FormFooter = $('<div class="KUI3_Footer"></div>').appendTo(Handler);
                        var BtnClose = $('<button class="KUI3_Button">Cancelar</button>').appendTo(FormFooter);
                        var BtnSend = $('<button class="KUI3_Button _Color">Subir escritorio</button>').appendTo(FormFooter);
                        BtnClose.click(function (){
                            $KUI.Modals.ModalClose();
                        });
                        BtnSend.click(function (){
                            $KUI.Desktops.Actions.Upload(FormHandler,BtnSend);
                        });
                    });
                }

            }
        },
        Actions:{
            Upload:function (FormHandler,BtnSend){
                $(FormHandler).find('span.FormError').each(function (){
                    $(this).remove();
                });
                $(FormHandler).find('.ColorRed').each(function (){
                    $(this).removeClass('ColorRed');
                });

                if(BtnSend.attr('disabled')) return;
                BtnSend.attr('disabled','true');

                var Errors = false;

                var TextSpan = FormHandler.find('span._Is_Text').eq(0);
                var TextInput = FormHandler.find('textarea[name="Text"]').eq(0);
                if(!TextInput.val()) {
                    TextInput.addClass('ColorRed');
                    TextSpan.append('<span class="FormError">El mensaje no puede estar vacío</span>');
                    Errors = true;
                }
                else if(TextInput.val().length<5) {
                    TextInput.addClass('ColorRed');
                    TextSpan.append('<span class="FormError">El mensaje ha de tener, al menos, 5 carácteres.</span>');
                    Errors = true;
                }
                else if(TextInput.val().length>512) {
                    TextInput.addClass('ColorRed');
                    TextSpan.append('<span class="FormError">El mensaje supera los 512 carácteres admitidos</span>');
                    Errors = true;
                }

                var DesktopSpan  = FormHandler.find('span._Is_File').eq(0);
                var DesktopInput = FormHandler.find('input[name="Desktop"]').eq(0);
                if(DesktopInput.get(0).files.length===0){
                    DesktopSpan.append('<span class="FormError">Debes seleccionar un fichero.</span>');
                    Errors = true;
                }
                else if(DesktopInput.get(0).files[0].size>10000000){
                    DesktopSpan.append('<span class="FormError">El tamaño del fichero supera los 10 MB permitido.</span>');
                    Errors = true;
                }
                else if(DesktopInput.get(0).files[0].type!=='image/bmp' && DesktopInput.get(0).files[0].type!=='image/png' && DesktopInput.get(0).files[0].type!=='image/jpg' && DesktopInput.get(0).files[0].type!=='image/jpeg'){
                    DesktopSpan.append('<span class="FormError">El fichero ha de ser una imagen JPG, PNG o BMP.</span>');
                    Errors = true;
                }

                var GDPR_Span  = FormHandler.find('span._Is_GDPR').eq(0);
                var GDPR_Input = FormHandler.find('input[name="GDPR"]').eq(0);
                if(!GDPR_Input.val() || parseInt(GDPR_Input.val())===0) {
                    GDPR_Input.addClass('ColorRed');
                    GDPR_Span.append('<span class="FormError">Debes aceptar la DdP.</span>');
                    Errors = true;
                }

                if(Errors===true)
                {
                    BtnSend.removeAttr('disabled');
                }
                else
                {
                    var formData = new FormData();
                    formData.append("sso", $KUI.UserSession.CurrentSSO);
                    formData.append("message", TextInput.val());
                    formData.append("desktop", DesktopInput.get(0).files[0]);

                    $.ajax({
                        url: $KUI.System.EndPoint+"kui/desktops/list",
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(data){
                            $KUI.Modals.ModalClose();
                            $KUI.Desktops.Messages.UploadOk(data);
                        },
                        error:function (){
                            BtnSend.removeAttr('disabled');
                            $KUI.Desktops.Messages.UploadErr();
                        }
                    });
                }

                $KUI.System.Triggers();
            }
        },
        Messages: {
            UploadErr:function (){
                $KUI.Modals.ModalOpen(function (Content){
                    Content.width('400px');
                    Content.append('<h1>Escritorio no publicado</h1>');
                    Content.append('<p>No se ha podido subir el escritorio</p><p>Puede que ya hayas subido un escritorio esta semana. Sólo puedes subir una imagen.</p><p>Si no lo has hecho aún, entonces comprueba que tu imagen cumple los requisitos: JPG, PNG o BMP de 10MB o menor.</p>');
                    var Footer = $('<div class="KUI3_Footer"></div>').appendTo(Content);
                    var BtnClose = $('<button class="KUI3_Button _Color">Cerrar</button>').appendTo(Footer);
                    BtnClose.click(function (){
                        $KUI.Modals.ModalClose();
                    });
                });
            },
            UploadOk:function (d){
                $KUI.Modals.ModalOpen(function (Content){
                    Content.width('400px');
                    Content.append('<h1>¡Fantástico!</h1>');
                    if(d['Mode']==='future')
                    {
                        Content.append('<p>Tu escritorio se ha subido, aunque no será publicado hasta el próximo <strong>viernes, '+d['d']+' de '+d['m']+' del '+d['Y']+'</strong>.</p>');
                    }
                    else
                    {
                        Content.append('<p>Se ha publicado tu escritorio en la web (y será compartido en el grupo de Telegram y en el grupo de Facebook).</p>');
                    }
                    var Footer = $('<div class="KUI3_Footer"></div>').appendTo(Content);
                    var BtnClose = $('<button class="KUI3_Button _Color">Cerrar</button>').appendTo(Footer);
                    BtnClose.click(function (){
                        $KUI.Modals.ModalClose();
                    });
                });
            },
            NoUpload:function (){
                $KUI.Modals.ModalOpen(function (Content){
                    Content.width('400px');
                    Content.append('<h1>No puedes publicar</h1>');
                    Content.append('<p>No se ha podido subir el escritorio</p><p>Ya has subido un escritorio esta semana. Sólo puedes subir una imagen.</p>');
                    var Footer = $('<div class="KUI3_Footer"></div>').appendTo(Content);
                    var BtnClose = $('<button class="KUI3_Button _Color">Cerrar</button>').appendTo(Footer);
                    BtnClose.click(function (){
                        $KUI.Modals.ModalClose();
                    });
                });
            },
            NoSession:function (){
                $KUI.Modals.ModalOpen(function (Content){
                    Content.width('400px');
                    Content.append('<h1>No puedes publicar</h1>');
                    Content.append('<p>Para publicar escritorios necesitas una cuenta de usuario, o bien, publicarlo desde el grupo de Telegram.</p>');
                    var Footer = $('<div class="KUI3_Footer"></div>').appendTo(Content);
                    var BtnClose = $('<button class="KUI3_Button _Color">Cerrar</button>').appendTo(Footer);
                    BtnClose.click(function (){
                        $KUI.Modals.ModalClose();
                    });
                });
            },
        },
        References: {
            TrailerInterval:false,
            WindowWidth:false
        },
        ColumnCalculate:function (){
            var WINWIDGT = $(window).width();
            var COL_COUNT = 4;
            if(WINWIDGT < 1324)
            {
                COL_COUNT = 1;
            }
            else if(WINWIDGT < 1500)
            {
                COL_COUNT = 2;
            }
            else if(WINWIDGT < 1850)
            {
                COL_COUNT = 3;
            }
            if(WINWIDGT<1150)
            {
                COL_COUNT++;
                COL_COUNT++;
                if(WINWIDGT<900)
                {
                    COL_COUNT = 2;
                }
            }
            return COL_COUNT;
        }
    },
    News:{
        FeedPool:false,
        Templates:{
            FrontList:function (Container){
                var Loader = $('<div class="KUI3_Loader kEL_mt_15 kEL_mb_15"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>').appendTo(Container);
                var N = Math.floor((Container.parents('div.KUI3_BodyGroup').eq(0).height()-48-9*2-5*2)/40);
                if($(window).width()<1150)
                {
                    N = 5;
                }
                var NN = 0;
                if(N<4)
                {
                    N = 4;
                }

                $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+"kui/article/list?Type=noticia&Limit=10",function (data){
                    var List = $('<ul class="KUI3_FrontNewsList"></ul>').appendTo(Container);
                    Loader.remove();

                    if($KUI.News.References.TrailerInterval)
                    {
                        clearInterval($KUI.News.References.TrailerInterval);
                        $KUI.News.References.TrailerInterval = false;
                    }

                    $.each(data,function (i,v){
                        NN++;
                        if(NN<=N)
                        {
                            var LI = $('<li></li>').appendTo(List);
                            var A = $('<a data-goto="'+v['Url']+'">'+v['Title']+'</a>').appendTo(LI);
                        }
                    });

                    $KUI.System.Triggers();

                    $KUI.News.References.TrailerInterval = setInterval(function (){
                        if($KUI.News.References.WindowWidth===$(window).width()) return;
                        $KUI.News.References.WindowWidth = $(window).width();
                        if(Container.length<=0)
                        {
                            clearInterval($KUI.News.References.TrailerInterval);
                            $KUI.News.References.TrailerInterval = false;
                        }

                        $('div._AsyncTrilerNewsGroup').each(function (){$(this).html('');});
                        $KUI.News.Templates.FrontList(Container);
                    },100);

                },function (){},3600*24);

            },
        },
        References:{
            TrailerInterval:false,
            WindowWidth:false,
        }
    },
    Feedy:{
        Widgets:{
            Article:function (Container,v){
                var LI = $('<li class="_Article KUI3_Element_Box"></li>').appendTo(Container);
                var Image = $('<img alt="'+v['Title']+'" src="'+v['Image']+'">').appendTo(LI);
                var Title = $('<h2>'+v['Title']+'</h2>').appendTo(LI);
                var Date = $('<div class="_Date">'+v['Date']['Date']+'</div>').appendTo(LI);
                var Excerpt = $('<div class="_Description">'+v['Excerpt']+'</div>').appendTo(LI);
                if(v['Chapters'].length>0)
                {
                    var Chapers = $('<ol class="_Chapters"></ol>').appendTo(LI);
                    $.each(v['Chapters'],function (ii,vv){
                        var Chara = $('<li>'+vv+'</li>').appendTo(Chapers);
                        Chara.click(function (event){
                            event.stopPropagation();
                            $KUI.Location.Goto(v['Url']+'#'+vv);
                        });
                    });
                }
                LI.click(function (){
                    $KUI.Location.Goto(v['Url']);
                });
            },
            YouTube:function (Container,v){
                var LI = $('<li class="_YouTube KUI3_Element_Box"></li>').appendTo(Container);
                var Header = $('<div class="_Header"></div>').appendTo(LI);
                $('<img alt="Karla" src="https://karlaperezyt.com/wp-content/themes/karlasflex/imaging/assets/kui/profile_rnd.png">').appendTo(Header);
                $('<p>¡Nuevo vídeo!</p>').appendTo(Header);
                $('<iframe class="Portrait" src="https://www.youtube.com/embed/'+v['Feedy']['VID']+'" allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>').appendTo(LI);
                LI.click(function (){
                    window.open('https://youtu.be/'+v['Feedy']['VID']);
                });
            },
            MemberShip:function (Container,v){
                var LI = $('<li class="_Member KUI3_Element_Box"></li>').appendTo(Container);
                var Pre = $('<div class="_Background"></div>').appendTo(LI);
                $('<div style="background-image: url('+v['Feedy']['Profile']+')"></div>').appendTo(Pre);
                var Header = $('<div class="_Header"></div>').appendTo(LI);
                $('<img alt="'+v['Feedy']['Name']+'" src="'+v['Feedy']['Profile']+'">').appendTo(Header);
                var P = $('<div class="_Text"></div>').appendTo(LI);
                $('<p>¡Hola '+v['Feedy']['Name']+'!</p>').appendTo(P);
                $('<p>Bienvenid@ a la membresía 😊</p>').appendTo(P);
                LI.click(function (){
                    window.open(v['Feedy']['URL']);
                });
            },
            Twitter:function (Container,v){
                var LI = $('<li class="_Twitter KUI3_Element_Box"></li>').appendTo(Container);
                var Excerpt = $('<div class="_Description">'+v['Feedy']['Text']+'</div>').appendTo(LI);
                LI.click(function (){
                    window.open(v['Feedy']['Url']?v['Feedy']['Url']:'https://twitter.com/KarlaPerezYT');
                });
            },
            Instagram:function (Container,v){
                var LI = $('<li class="_Instagram KUI3_Element_Box"></li>').appendTo(Container);
                var Image = $('<img alt="'+v['Feedy']['Text']+'" src="'+v['Feedy']['Image']+'">').appendTo(LI);
                var Excerpt = $('<div class="_Description">'+v['Feedy']['Text']+'</div>').appendTo(LI);
                LI.click(function (){
                    window.open(v['Feedy']['Image']);
                });
            },
            FridayBoard:function (Container,v){},
            Desktop:function (Container,v){
                var LI = $('<li class="_Desktop KUI3_Element_Box"></li>').appendTo(Container);
                var Image = $('<img alt="'+v['Display_Name']+'" src="'+v['Image']+'">').appendTo(LI);
                var Title = $('<h2><img alt="'+v['Display_Name']+'" src="'+v['Display_Photo']+'">'+v['Display_Name']+'</h2>').appendTo(LI);
                var Score = $('<h4>'+v['Score']+'</h4>').appendTo(LI);
                var Date = $('<div class="_Date">Semana #'+v['Week']+' del '+v['Year']+'</div>').appendTo(LI);
                LI.click(function (){
                    $KUI.Location.Goto(v['Url']);
                });
            },
            ExternalRSS:function (Container,v){
                var LI = $('<li class="_Twitter _RSSmod KUI3_Element_Box"></li>').appendTo(Container);
                var Excerpt = $(`<div class="_Favicon"><span>${v['Feedy']['SiteName']}</span><img alt="${v['Feedy']['SiteName']}" src="${v['Feedy']['SiteIcon']}"></div>`).appendTo(LI);
                var Excerpt = $('<div class="_Description">'+v['Feedy']['Text']+'</div>').appendTo(LI);
                LI.click(function (){
                    window.open(v['Feedy']['Url']);
                });
            },
        },
        Printers:{
            FeedyPrint:function (){
                var MergeContainer = $('<div class="KUI3_BodyMerge _FeedyFrontPage"></div>').appendTo($KUI.Template.Layouts.Common.Body);
                var TrailerGroup = $('<div class="KUI3_BodyGroup"></div>').appendTo(MergeContainer);
                var BlogGroup = $('<div class="KUI3_BodyGroup_Large"></div>').appendTo(TrailerGroup);
                var StuffGroup = $('<div class="KUI3_BodyGroup_Small"></div>').appendTo(TrailerGroup);
                var StuffGroup_YT = $('<div class="__YouTubeVideo"></div>').appendTo(StuffGroup);
                var StuffGroup_Other = $('<div class="__Other"></div>').appendTo(StuffGroup);
                var MainFeedy = $('<h2 class="GroupTitle"><span class="_Text">Contenido Reciente</span><span class="_TextDesc">Artículos, Tutoriales & Wikis</span></h2>').appendTo(BlogGroup);
                $('<h2 class="GroupTitle"><span class="_Text">¡Mi último vídeo!</span><span class="_TextDesc">¿Te lo vás a perder?</span></h2>').appendTo(StuffGroup_YT);
                $('<h2 class="GroupTitle"><span class="_Text">¡Más cosaaaaaaas!</span><span class="_TextDesc">¿Pensabas que eso era todo?</span></h2>').appendTo(StuffGroup_Other);
                var BlogBtns = $('<ul></ul>').appendTo(MainFeedy);
                var BlogBtnsLI = $('<li></li>').appendTo(BlogBtns);
                var BlogBtnsBTN = $('<button class="KUI3_Button">Visitar Blog</button>').appendTo(BlogBtnsLI);
                BlogBtnsBTN.click(function (){
                    $KUI.Location.Goto('/blog');
                });

                var Loader = $('<div class="KUI3_Loader kEL_mt_15 kEL_mb_15"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>').appendTo(BlogGroup);
                var NLoad = 30;
                if($(window).width()<1150)
                {
                    NLoad = 8;
                }
                $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+"kui/article/list?Type=post,videopost,wiki_post&Limit="+NLoad,function (data){
                    var List = $('<ul class="KUI3_Listings_Feedy"></ul>').appendTo(BlogGroup);
                    var WINWIDGT = $(window).width();
                    var COL_COUNT = 3;
                    if(WINWIDGT < 1172)
                    {
                        if(WINWIDGT<800)
                        {
                            COL_COUNT = 1;
                        } else if(WINWIDGT<1150)
                        {
                            COL_COUNT = 2;
                        }
                        else
                        {
                            COL_COUNT = 1;
                        }
                    }
                    else if(WINWIDGT < 1576)
                    {
                        COL_COUNT = 2;
                    }
                    var FEEDYS = [];
                    var FEEDYS_COLUMN = [
                        [],[],[],[]
                    ];
                    for(i=0;i<COL_COUNT;i++)
                    {
                        FEEDYS_COLUMN[i] = $('<ul style="width: calc(100% / '+COL_COUNT+')" class="Feedy_Column" data-ind="'+(i+1)+'"></ul>').appendTo(List);
                    }
                    Loader.remove();

                    if($KUI.Feedy.References.UpdateColumnsInterval)
                    {
                        clearInterval($KUI.Feedy.References.UpdateColumnsInterval);
                        $KUI.Feedy.References.UpdateColumnsInterval = false;
                    }
                    Selected_Column = 0;

                    $.each(data,function (i,v){
                        $KUI.Feedy.Widgets.Article(FEEDYS_COLUMN[Selected_Column],v);
                        Selected_Column++;
                        if(Selected_Column>=COL_COUNT)
                        {
                            Selected_Column = 0;
                        }
                    });

                    $KUI.System.Triggers();

                    $KUI.Feedy.References.WindowWidth = $(window).width();

                    $KUI.Feedy.References.UpdateColumnsInterval = setInterval(function (){
                        if($KUI.Feedy.References.WindowWidth===$(window).width()) return;
                        $KUI.Feedy.References.WindowWidth = $(window).width();
                        if(MergeContainer.length<=0)
                        {
                            clearInterval($KUI.Feedy.References.UpdateColumnsInterval);
                            $KUI.Feedy.References.UpdateColumnsInterval = false;
                        }

                        $('._FeedyFrontPage').each(function (){$(this).remove();});
                        $KUI.Feedy.Printers.FeedyPrint();
                    },100);

                },function (){},3600*5);

                var StuffVideo = $('<div class="KUI3_Loader kEL_mt_15 kEL_mb_15"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>').appendTo(StuffGroup_YT);
                $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+"kui/article/list?Type=feedy&Limit=1&FType=youtube",function (data){
                    var List = $('<ul class="KUI3_Listings_Feedy"></ul>').appendTo(StuffGroup_YT);
                    var Column = $('<ul style="width: 100%" class="Feedy_Column"></ul>').appendTo(List);
                    StuffVideo.remove();

                    $.each(data,function (i,v){
                        switch (v['Feedy']['Type'])
                        {
                            case 'youtube':
                                $KUI.Feedy.Widgets.YouTube(Column,v);
                                break;

                        }
                    });

                    $KUI.Feedy.PortraitAllocate(Column);

                    $KUI.System.Triggers();
                },function (){},3600*5);

                var NLoad2 = 20;
                if($(window).width()<1150)
                {
                    NLoad2 = 8;
                }
                var StuffLoader = $('<div class="KUI3_Loader kEL_mt_15 kEL_mb_15"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>').appendTo(StuffGroup_Other);
                $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+"kui/article/list?Type=feedy&Limit="+NLoad2,function (data){
                    var List = $('<ul class="KUI3_Listings_Feedy"></ul>').appendTo(StuffGroup_Other);
                    var Column = $('<ul style="width: 100%" class="Feedy_Column"></ul>').appendTo(List);
                    StuffLoader.remove();

                    $.each(data,function (i,v){
                        switch (v['Feedy']['Type'])
                        {
                            case 'youtube':
                                $KUI.Feedy.Widgets.YouTube(Column,v);
                                break;
                            case 'ytmember':
                                $KUI.Feedy.Widgets.MemberShip(Column,v);
                                break;
                            case 'instagram':
                                $KUI.Feedy.Widgets.Instagram(Column,v);
                                break;
                            case 'twitter':
                                $KUI.Feedy.Widgets.Twitter(Column,v);
                                break;
                            case 'friday_board':
                                $KUI.Feedy.Widgets.FridayBoard(Column,v);
                                break;
                            case 'externalrss':
                                $KUI.Feedy.Widgets.ExternalRSS(Column,v);
                                break;
                        }

                    });

                    $KUI.Feedy.PortraitAllocate(Column);

                    $KUI.System.Triggers();
                },function (){},3600*5);

            },
            ListingPrint:function (PrintType,PrintPage,PrintCategory,PrintSubCategory,DisplayColumns,FourthColumn=false) {
                var PrintTitle;
                var PrintDesc;
                var QueryType;
                var QueryRunner = false;
                var TextCategory = 'Todas las entradas';
                var TextSubCategory = 'GNU/Linux y Windows';
                if(typeof DisplayColumns !== 'number')
                {
                    DisplayColumns = 4;
                }
                switch ($KUI.Location.UrlFragments[2])
                {
                    case 'articulos':TextCategory = 'Artículos';break;
                    case 'reviews':TextCategory = 'Reviews';break;
                    case 'noticias':TextCategory = 'Notícias';break;
                }
                switch ($KUI.Location.UrlFragments[3])
                {
                    case 'linux':TextSubCategory = 'GNU/Linux';break;
                    case 'windows':TextSubCategory = 'Windows';break;
                }
                switch (PrintType)
                {
                    case 'Blog':
                        PrintTitle = 'Entradas del Blog';
                        PrintDesc = TextCategory+' sobre '+TextSubCategory;
                        QueryType = 'post';
                        break;
                    case 'VideoPost':
                        PrintTitle = 'Tutoriales & Vídeos';
                        PrintDesc = 'Todos los tutoriales y vídeos';
                        QueryType = 'videopost';
                        break;
                    case 'Desktops':
                        PrintTitle = 'Viernes de Escritorio';
                        PrintDesc = 'Mejor valorados por semana';
                        QueryType = 'viernesdeescritorio';
                        QueryRunner = 'desktops'
                        break;
                }

                var MergeContainer = $('<div class="KUI3_BodyMerge _CntFeedyGnr"></div>').appendTo($KUI.Template.Layouts.Common.Body);
                var TrailerGroup = $('<div class="KUI3_BodyGroup"></div>').appendTo(MergeContainer);
                var BlogGroup = $('<div class="KUI3_BodyGroup_All"></div>').appendTo(TrailerGroup);
                var MainFeedy = $('<h2 class="GroupTitle"><span class="_Text">'+PrintTitle+'</span><span class="_TextDesc _Mnt_Blog_Desc">'+PrintDesc+'</span></h2>').appendTo(BlogGroup);

                var Loader = $('<div class="KUI3_Loader kEL_mt_15 kEL_mb_15"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>').appendTo(BlogGroup);
                $KUI.Cache.AsyncronousRequest(
                    $KUI.System.EndPoint+"kui/article/list?Type="+QueryType+"&Limit=60&Page="+PrintPage+"&Category="+PrintCategory+"&SubCategory="+PrintSubCategory+"",
                    function (data){
                        var List = $('<ul class="KUI3_Listings_Feedy"></ul>').appendTo(BlogGroup);
                        var WINWIDGT = $(window).width();
                        var COL_COUNT = 4;
                        if(DisplayColumns===4)
                        {
                            COL_COUNT = 4;
                            if(WINWIDGT < 1172)
                            {
                                if(WINWIDGT<800)
                                {
                                    COL_COUNT = 1;
                                } else if(WINWIDGT<1150)
                                {
                                    COL_COUNT = 2;
                                }
                                else
                                {
                                    COL_COUNT = 2;
                                }
                            }
                            else if(WINWIDGT < 1576)
                            {
                                COL_COUNT =  3;
                            }
                        }
                        else
                        {
                            COL_COUNT = 3;
                            if(WINWIDGT < 1172)
                            {
                                if(WINWIDGT<800)
                                {
                                    COL_COUNT = 1;
                                } else if(WINWIDGT<1150)
                                {
                                    COL_COUNT = 2;
                                }
                                else
                                {
                                    COL_COUNT = 1;
                                }
                            }
                            else if(WINWIDGT < 1576)
                            {
                                COL_COUNT = 2;
                            }
                        }


                        var FEEDYS = [];
                        var FEEDYS_COLUMN = [
                            [],[],[],[]
                        ];
                        for(i=0;i<COL_COUNT;i++)
                        {
                            FEEDYS_COLUMN[i] = $('<ul style="width: calc(100% / '+COL_COUNT+')" class="Feedy_Column" data-ind="'+(i+1)+'"></ul>').appendTo(List);
                        }
                        Loader.remove();

                        if($KUI.Feedy.References.UpdateColumnsInterval)
                        {
                            clearInterval($KUI.Feedy.References.UpdateColumnsInterval);
                            $KUI.Feedy.References.UpdateColumnsInterval = false;
                        }
                        Selected_Column = 0;

                        $.each(data,function (i,v){
                            switch (QueryRunner)
                            {
                                case 'desktops':
                                    $KUI.Feedy.Widgets.Desktop(FEEDYS_COLUMN[Selected_Column],v);
                                    break;
                                default:
                                    $KUI.Feedy.Widgets.Article(FEEDYS_COLUMN[Selected_Column],v);

                            }

                            Selected_Column++;
                            if(Selected_Column>=COL_COUNT)
                            {
                                Selected_Column = 0;
                            }
                        });

                        $KUI.System.Triggers();

                        $KUI.Feedy.References.WindowWidth = $(window).width();

                        $KUI.Feedy.References.UpdateColumnsInterval = setInterval(function (){
                            if($KUI.Feedy.References.WindowWidth===$(window).width()) return;
                            $KUI.Feedy.References.WindowWidth = $(window).width();
                            if(MergeContainer.length<=0)
                            {
                                clearInterval($KUI.Feedy.References.UpdateColumnsInterval);
                                $KUI.Feedy.References.UpdateColumnsInterval = false;
                            }

                            $('._CntFeedyGnr').each(function (){$(this).remove();});
                            $KUI.Feedy.Printers.ListingPrint(PrintType,PrintPage,PrintCategory,PrintSubCategory,DisplayColumns,FourthColumn);
                        },100);

                        if(DisplayColumns===3 && typeof FourthColumn === 'function')
                        {
                            BlogGroup.removeClass('KUI3_BodyGroup_All');
                            BlogGroup.addClass('KUI3_BodyGroup_Large');
                            var ColumnContainer = $('<div class="KUI3_BodyGroup_Small"></div>').appendTo(TrailerGroup);
                            FourthColumn(ColumnContainer);
                        }

                    },
                    function (){},
                    3600*5
                );
            },
            WikiPrint:function (){
                var Container_Main          = $('<div class="KUI3_BodyMerge _FeedyFrontPage"></div>').appendTo($KUI.Template.Layouts.Common.Body);

                var Container_Main_HGroup   = $('<div class="KUI3_BodyGroup"></div>').appendTo(Container_Main);

                var Group_Main              = $('<div class="KUI3_BodyGroup_Large"></div>').appendTo(Container_Main_HGroup);
                var Group_Aside             = $('<div class="KUI3_BodyGroup_Small"></div>').appendTo(Container_Main_HGroup);

                var Group_Aside_Guide       = $('<div class="__YouTubeVideo"></div>').appendTo(Group_Aside);
                var Group_Aside_Widgets     = $('<div class="__Other"></div>').appendTo(Group_Aside);

                var Titles_Main             = $('<h2 class="GroupTitle"><span class="_Text">Conceptos sobre GNU/Linux</span>' +
                    '<span class="_TextDesc">Conceptos generales sobre GNU/Linux</span></h2><div class="__Container_Wk_Main"></div>').appendTo(Group_Main);
                var Titles_Main_Distros     = $('<h2 class="GroupTitle _WithHeight"><span class="_Text">Distribuciones</span>' +
                    '<span class="_TextDesc">Distribuciones principales de GNU/Linux</span></h2><div class="__Container_Wk_Distros"></div>').appendTo(Group_Main);
                var Titles_Main_Cats        = $('<h2 class="GroupTitle _WithHeight"><span class="_Text">Conceptos principales</span>' +
                    '<span class="_TextDesc">Ordenados por categorias</span></h2><div class="__Container_Wk_Cats"></div>').appendTo(Group_Main);
                var Titles_Main_TT          = $('<h2 class="GroupTitle _WithHeight"><span class="_Text">Tutoriales Wiki</span>' +
                    '<span class="_TextDesc">Tutoriales & Guías</span></h2><div class="__Container_Wk_TT"></div>').appendTo(Group_Main);
                var Titles_Main_CMD         = $('<h2 class="GroupTitle _WithHeight"><span class="_Text">Comandos</span>' +
                    '<span class="_TextDesc">Comandos principales del terminal</span></h2><div class="__Container_Wk_CMD"></div>').appendTo(Group_Main);
                var Titles_Guide            = $('<h2 class="GroupTitle"><span class="_Text">Historia sobre GNU/Linux</span>' +
                    '<span class="_TextDesc kEL_mb_10">Conoce cómo empezó todo</span></h2>').appendTo(Group_Aside_Guide);
                var Titles_Widgets          = $('<h2 class="GroupTitle _WithHeight"><span class="_Text">Boletín del Blog</span>' +
                    '<span class="_TextDesc kEL_mb_10">¿Quieres recibir artículos por email?</span></h2>').appendTo(Group_Aside_Widgets);

                $KUI.StaticWidgets.LinuxHistory(Group_Aside_Guide);
                $KUI.StaticWidgets.BlogSuscribe(Group_Aside_Widgets);

                var Loader_Main             = $('<div class="KUI3_Loader kEL_mt_15 kEL_mb_15"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>').insertAfter($('.__Container_Wk_Main'));
                var Loader_Cats             = $('<div class="KUI3_Loader kEL_mt_15 kEL_mb_15"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>').insertAfter($('.__Container_Wk_Cats'));
                var Loader_Distro           = $('<div class="KUI3_Loader kEL_mt_15 kEL_mb_15"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>').insertAfter($('.__Container_Wk_Distros'));
                var Loader_Command          = $('<div class="KUI3_Loader kEL_mt_15 kEL_mb_15"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>').insertAfter($('.__Container_Wk_CMD'));
                var Loader_Tutorial         = $('<div class="KUI3_Loader kEL_mt_15 kEL_mb_15"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>').insertAfter($('.__Container_Wk_TT'));

                if($KUI.Feedy.References.UpdateColumnsInterval){
                    clearInterval($KUI.Feedy.References.UpdateColumnsInterval);
                }

                $KUI.Feedy.References.UpdateColumnsInterval = false;
                $KUI.Feedy.References.WindowWidth           = 0;

                /* Categorias agrupadas */
                $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+"kui/wiki/categories",function (data){
                    var Container = $('div.__Container_Wk_Cats');
                    Container.html('');

                    Loader_Cats.remove();

                    var Container_List = $('<ul class="KUI3_Listings_WikiCat"></ul>').appendTo(Container);

                    var NI = 0;

                    $.each(data,function (i,v){
                        var Container_List_Item = $('<li class="KUI3_Element_Box"></li>').appendTo(Container_List);
                        $('<div class="Image Portrait" data-allocate="vertical" data-widescreen="false"><img data-w="600" data-h="400" alt="'+v['Title']+'" src="'+v['Image']+'"></div>').appendTo(Container_List_Item);
                        $('<h2>'+v['Title']+'</h2>').appendTo(Container_List_Item);
                        $('<div class="Description">'+v['Description']+'</div>').appendTo(Container_List_Item);

                        var Container_List_Sub = $('<ul class="Concepts_List"></ul>').appendTo(Container_List_Item);

                        $.each(v['Posts'],function (ii,vv){
                            var Container_Sub_Item = $('<li></li>').appendTo(Container_List_Sub);
                            $('<a data-goto="'+vv['Url']+'">'+vv['Title']+'</a>').appendTo(Container_Sub_Item);

                            NI++;
                        })
                    });

                    if(NI===0){
                        Container.html('<div class="KUI3_Message">Aún no hay artículos aquí.</div>');
                    }

                    $KUI.Feedy.PortraitAllocate(Container);
                    $KUI.System.Triggers();

                },function (){},3600*24*4);

                /* Conceptos GNU/Linux */
                $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+"kui/wiki/categories?term=683651432",function (data){
                    var Container = $('div.__Container_Wk_Main');
                    Container.html('');

                    Loader_Main.remove();

                    var Container_List = $('<ul class="KUI3_Listings_WikiCat_Mains"></ul>').appendTo(Container);
                    var Container_List_Col = [];
                    Container_List_Col[1] = $('<li class="Sub _InSide"></li>').appendTo(Container_List);
                    Container_List_Col[2] = $('<li class="Sub _InMiddle"></li>').appendTo(Container_List);
                    Container_List_Col[3] = $('<li class="Sub _InSide"></li>').appendTo(Container_List);
                    Container_List_Col[4] = $('<li class="Sub _InSide"></li>').appendTo(Container_List);

                    var NI = 0;

                    $.each(data,function (i,v){
                        var Container_I;
                        var Container_xW = 16;
                        var Container_xH = 9/2;
                        var Container_aA = "vertical";
                        if(i===0 || i===1){
                            Container_I = 1;
                        }
                        else if(i===2){
                            Container_I = 2;
                            Container_xW = 16;
                            Container_xH = 9;
                            Container_aA = "normal";
                        }
                        else if(i===3 || i===4){
                            Container_I = 3;
                        }
                        else {
                            Container_I = 4;
                        }

                        var Container_List_Item = $('<div class="Item KUI3_Element_Box"></div>').appendTo(Container_List_Col[Container_I]);
                        Container_List_Item.click(function (){
                            $KUI.Location.Goto(v['Url']);
                        });

                        if(Container_I!==4){
                            $('<div class="Image Portrait" data-allocate="vertical" data-fixheight="true" data-xw="'+Container_xW+'" data-xh="'+Container_xH+'"><img data-w="'+v['Image_W']+'" data-h="'+v['Image_H']+'" alt="'+v['Title']+'" src="'+v['Image']+'"></div>').appendTo(Container_List_Item);
                        }
                        $('<h2>'+v['Title']+'</h2>').appendTo(Container_List_Item);
                        if(Container_I!==4){
                            $('<div class="Description">'+v['Description']+'</div>').appendTo(Container_List_Item);
                        }

                        NI++;
                    });

                    if(NI===0){
                        Container.html('<div class="KUI3_Message">Aún no hay artículos aquí.</div>');
                    }

                    $KUI.System.Triggers();

                    var Update = function (){
                        if($KUI.Feedy.References.WindowWidth===$(window).width()) return;
                        $KUI.Feedy.References.WindowWidth = $(window).width();
                        if(Container.length<=0)
                        {
                            clearInterval($KUI.Feedy.References.UpdateColumnsInterval);
                            $KUI.Feedy.References.UpdateColumnsInterval = false;
                        }

                        $KUI.Feedy.PortraitAllocate(Container);

                        var LI_Middle = $('ul.KUI3_Listings_WikiCat_Mains li._InMiddle').eq(0);
                        var LI_Side   = $('ul.KUI3_Listings_WikiCat_Mains li._InSide').eq(0);
                        LI_Middle.height((Math.round(parseFloat(LI_Side.height())))+"px");
                        $KUI.System.Triggers();
                    };

                    Update();

                    $KUI.Feedy.References.UpdateColumnsInterval = setInterval(function (){Update();},100);
                },function (){},3600*24*4);

                /* Distribuciones */
                $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+"kui/wiki/categories?term=683651433",function (data){
                    var Container = $('div.__Container_Wk_Distros');
                    Container.html('');

                    Loader_Distro.remove();

                    var Container_List = $('<ul class="KUI3_Listings_WikiCat _ImageOnly"></ul>').appendTo(Container);

                    var NI = 0;

                    $.each(data,function (i,v){
                        var Container_List_Item = $('<li class="KUI3_Element_Box"></li>').appendTo(Container_List);
                        $('<div class="Image Portrait" data-allocate="vertical" data-widescreen="false"><img data-w="'+v['Image_W']+'" data-h="'+v['Image_H']+'" alt="'+v['Title']+'" src="'+v['Image']+'"></div>').appendTo(Container_List_Item);
                        $('<h2>'+v['Title']+'</h2>').appendTo(Container_List_Item);

                        Container_List_Item.click(function (){
                            $KUI.Location.Goto(v['Url']);
                        });

                        NI++;
                    });

                    if(NI===0){
                        Container.html('<div class="KUI3_Message">Aún no hay artículos aquí.</div>');
                    }

                    $KUI.Feedy.PortraitAllocate(Container);
                    $KUI.System.Triggers();

                },function (){},3600*24*4);

                /* Tutoriales */
                $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+"kui/wiki/categories?term=683651452&norder",function (data){
                    var Container = $('div.__Container_Wk_TT');
                    Container.html('');

                    Loader_Tutorial.remove();

                    var Categories = {};
                    var N = 0;

                    $.each(data,function (i,v){
                        var First_Letter = v['Title'].charAt(0).toUpperCase();

                        if(!Categories.hasOwnProperty(First_Letter)){
                            Categories[First_Letter] = {};
                        }

                        Categories[First_Letter][N] = v;

                        N++;
                    });

                    var Container_List = $('<ul class="KUI3_Listings_WikiCat WikiTutos"></ul>').appendTo(Container);

                    $.each(Categories,function (i,v){
                        var Container_List_Sub = $(`<li class="KUI3_Element_Box"></li>`).appendTo(Container_List);
                        var Container_List_Title = $(`<h2>${i}</h2>`).appendTo(Container_List_Sub);
                        var Container_List_Cont = $(`<ul class="List"></ul>`).appendTo(Container_List_Sub);


                        $.each(v,function (ii,vv){
                            var Container_Item = $(`<li class="Item"></li>`).appendTo(Container_List_Cont);

                            $(`<a data-goto="${vv['Url']}">${vv['Title']}</a>`).appendTo(Container_Item);
                        });
                    });

                    $KUI.System.Triggers();

                },function (){},3600*24*4);

                /* Comandos */
                $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+"kui/wiki/categories?term=683651436&norder",function (data){
                    var Container = $('div.__Container_Wk_CMD');
                    Container.html('');

                    Loader_Command.remove();

                    var Container_List = $('<ul class="KUI3_Listings_WikiCommand"></ul>').appendTo(Container);

                    var NI = 0;

                    $.each(data,function (i,v){
                        var Container_List_Item = $('<li class="KUI3_Element_Box"></li>').appendTo(Container_List);
                        $('<h2>'+v['Title']+'</h2>').appendTo(Container_List_Item);
                        $('<p>'+v['Description']+'</p>').appendTo(Container_List_Item);

                        Container_List_Item.click(function (){
                            $KUI.Location.Goto(v['Url']);
                        });

                        NI++;
                    });

                    if(NI===0){
                        Container.html('<div class="KUI3_Message">Aún no hay artículos aquí.</div>');
                    }

                    $KUI.System.Triggers();

                },function (){},3600*24*4);
            }
        },
        References:{
            UpdateColumnsInterval:false,
            WindowWidth:false
        },
        PortraitAllocate:function (Container){
            $(Container).find('.Portrait').each(function (){
                var Calculate_Portrait = true;
                var Calculate_Allocate = false;

                if($(this).attr('data-widescreen')==='false'){
                    Calculate_Portrait = false;
                }

                if($(this).attr('data-allocate')==='vertical'){
                    Calculate_Allocate = true;
                }

                if(Calculate_Portrait===true){
                    var xW = 16;
                    var xH = 9;

                    var dW = $(this).attr('data-xw');
                    if(typeof dW === 'string'){
                        xW = parseFloat(dW);
                    }

                    var dH = $(this).attr('data-xh');
                    if(typeof dH === 'string'){
                        xH = parseFloat(dH);
                    }

                    var W = $(this).width();
                    $(this).css('height',(W*(xH/xW))+"px");
                }

                if(Calculate_Allocate===true){
                    var I = $(this).find('img').eq(0);
                    IH = parseInt(I.attr('data-h'))/2;
                    CH = $(this).height()/2;
                    PW = ($(this).width()*(9/16))/2;
                    I.attr('data-hh',PW);
                    I.css('position','relative');
                    I.css('top',(-PW+CH)+"px");
                }
            });
            setTimeout(function (){
                $(Container).find('.Portrait').each(function (){
                    if($(this).attr('data-widescreen')!=='false' && $(this).attr('data-fixheight')==='true'){
                        var P = $(this).parents('div').eq(0);
                        var I = $(this).find('img').eq(0);
                        var PH = P.outerHeight();
                        var CH = $(this).outerHeight()+3;
                        P.attr('data-ph',P.outerHeight());
                        P.attr('data-ch',$(this).outerHeight());
                        if(CH<PH){
                            $(this).css('height',(PH)+"px");
                            if(I.length>0){
                                I.css('height',(PH)+"px");
                            }
                        }
                    }
                });
            },100);
        }
    },
    StaticWidgets:{
        BlogSuscribe:function (AsideContainer){
            var MyWidget = $('<div class="KUI3_Element_Box"></div>').appendTo(AsideContainer);
            $KUI.SubscriptionManager.SuscriptionForm(MyWidget,false);
        },
        KarlaProfile:function (AsideContainer){
            var SocialAssets = "/wp-content/themes/karlasflex/imaging/assets/icons/social/";
            var MyWidget = $('<div class="KUI3_Element_Box"></div>').appendTo(AsideContainer);
            var Caption = $('<h2 class="KUI3_Element_Box_Caption">Sobre Mí</h2>').appendTo(MyWidget);
            $('<img class="KUI3_Element_ImageCaption" alt="Karla Pérez" src="https://karlaperezyt.com/wp-content/uploads/2020/07/SAVE_20191217_083246.jpg">').appendTo(MyWidget);
            $('<div class="KUI3_Element_SubCaption">Datos Generales</div>').appendTo(MyWidget);
            var MyInfo = $('<div class="KUI3_Element_TableOfKeyValue"></div>').appendTo(MyWidget);
            $('<div class="Row"><p>Nombre</p><p>Carla Pérez</p></div>').appendTo(MyInfo);
            $('<div class="Row"><p>Nacimiento</p><p>18 de Julio 1995</p></div>').appendTo(MyInfo);
            $('<div class="Row"><p>Nacionalidad</p><p>🇪🇸 Española</p></div>').appendTo(MyInfo);
            $('<div class="Row"><p>Canal</p><p><a href="https://www.youtube.com/KarlasProject" target="_blank">Karla\'s Project</a></p></div>').appendTo(MyInfo);
            $('<div class="Row"><p>Temática</p><p>Software</p></div>').appendTo(MyInfo);
            $('<div class="Row"><p>Tipología</p><p>vlog, tutoriales</p></div>').appendTo(MyInfo);
            $('<div class="Row"><p>Inicios</p><p>15 de Mayo de 2017</p></div>').appendTo(MyInfo);
            $('<div class="KUI3_Element_SubCaption">Redes Sociales</div>').appendTo(MyWidget);
            var SocialNetworks = $('<ul class="KUI3_Element_ListOfImagesH"></ul>').appendTo(MyWidget);
            $('<li><a href="https://www.youtube.com/channel/UCgHXvTpaNOBCIDqCNhOxPkg" target="_blank"><img alt="Youtube" src="'+SocialAssets+'youtube.png"></a></li>').appendTo(SocialNetworks);
            $('<li><a href="https://twitter.com/KarlaPerezYT" target="_blank"><img alt="Twitter" src="'+SocialAssets+'twitter.png"></a></li>').appendTo(SocialNetworks);
            $('<li><a href="https://www.instagram.com/karlaperezyt/" target="_blank"><img alt="Instagram" src="'+SocialAssets+'instagram.png"></a></li>').appendTo(SocialNetworks);
            $('<li><a href="https://www.facebook.com/KarlaPerezYT" target="_blank"><img alt="Facebook" src="'+SocialAssets+'facebook.png"></a></li>').appendTo(SocialNetworks);
            $('<li><a href="mailto:contacto@karlaperezyt.com" target="_blank"><img alt="GMail" src="'+SocialAssets+'gmail.png"></a></li>').appendTo(SocialNetworks);
        },
        DesktopPodium:function (AsideContainer){
            var MyWidget = $('<div class="KUI3_Element_Box"></div>').appendTo(AsideContainer);
            $('<p class="kEL_ac kEL_mb_15"><img style="width: 100px;" alt="Podium" src="/wp-content/uploads/2021/03/podium.png"></p>').appendTo(MyWidget);
            var Loader = $('<div class="KUI3_Loader kEL_mt_15 kEL_mb_15"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>').appendTo(MyWidget);
            $KUI.Cache.AsyncronousRequest($KUI.System.EndPoint+"kui/desktops/podium",function (data){
                Loader.remove();
                var Podium = $('<ul class="KUI3_Element_PodiumUL"></ul>').appendTo(MyWidget);
                $.each(data,function (i,v){
                    $('<li class="KUI3_Element_UserList"><a data-goto="'+v['Profile']+'"><span class="Display"><img alt="'+v['Display_Name']+'" src="'+v['Display_Photo']+'">'+v['Display_Name']+'</span><div class="Score">'+v['Score']+'</div></a></li>').appendTo(Podium);
                });

                $KUI.System.Triggers();
            });
        },
        LinuxHistory:function (AsideContainer){
            var MyWidget = $('<div class="KUI3_Element_Box KUI3_Widget_LinuxHistory"></div>').appendTo(AsideContainer);
            $('<p class="Image"><img alt="Richard Stallman" src="/wp-content/uploads/2021/08/stallman.jpg"></p>').appendTo(MyWidget);
            $('<p class="IsP">Richard Stallman (que trabajaba en el MIT, Massachusetts Institute of Technology) se sintió indignado al comprobar que cada vez era más difícil conseguir el código fuente de los programas que utilizaba para adaptarlos a sus necesidades, tal como había hecho hasta entonces.</p>').appendTo(MyWidget);
            $('<p class="IsP">A partir de ese momento, Stallman decidió ser consecuente con sus ideales e iniciar un gran proyecto para intentar abrir otra vez el código fuente de los programas. </p>').appendTo(MyWidget);
            var BoxFooter = $('<div class="KUI3_Element_Box_Footer"></div>').appendTo(MyWidget);
            var BoxFooter_Button = $('<button class="KUI3_Button">Conoce la historia de GNU/Linux</button>').appendTo(BoxFooter);
            BoxFooter_Button.click(function (){
                $KUI.Location.Goto('/gnulinux-historia/')
            });
        }
    },
    SubscriptionManager:{
        SuscriptionForm:function (MyWidget,IsModal){
            var Caption;
            var BtnClass = '';
            if(IsModal===true)
            {
                $('<h1>Gestionar Boletín</h1>').appendTo(MyWidget);
                BtnClass = ' _Color';
            }
            else
            {
                $('<h2 class="KUI3_Element_Box_CaptionWithIcon"><img alt="Suscripción" src="/wp-content/themes/karlasflex/imaging/assets/icons/general/subscribe.png">Recibe nuevos artículos y vídeos por correo.</h2>').appendTo(MyWidget);
            }
            var Loader = $('<div class="KUI3_Loader"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>').appendTo(MyWidget);

            var CheckInterval = setInterval(function (){
                if(MyWidget.length<=0) clearInterval(CheckInterval);

                if($KUI.UserSession.CurrentUser.IsDefined)
                {
                    Loader.remove();

                    var SubForm = $('<div class="KUI3_Form"></div>').appendTo(MyWidget);

                    if($KUI.UserSession.IsSigned===true)
                    {
                        var MyUser = $('<div class="KUI3_Element_ImageCenteredRounded"><img alt="'+$KUI.UserSession.CurrentUser.DisplayName+'" src="'+$KUI.UserSession.CurrentUser.DisplayProfile+'"></div>').appendTo(SubForm);
                        $('<div class="KUI3_Element_TextAstrongCentered kEL_mt_5">'+$KUI.UserSession.CurrentUser.DisplayName+'</div>').appendTo(MyUser);
                    }
                    else
                    {
                        var InpEmailSpan = $('<span class="InputEmail"><abbr>*</abbr>Correo electrónico:</span>').appendTo(SubForm);
                        var InpEmailInput = false;
                        if($KUI.UserSession.CurrentUser.SubEmail!==false)
                        {
                            $('<div class="KUI3_Element_Input">'+$KUI.UserSession.CurrentUser.SubEmail+'</div>').appendTo(SubForm);
                        }
                        else
                        {
                            InpEmailInput = $('<input name="email" type="text" placeholder="Escribe tu email...">').appendTo(SubForm);
                        }
                    }

                    var InpThemesSpan = $('<span class="InputEmail"><abbr>*</abbr>Tipo de boletín:</span>').appendTo(SubForm);
                    var InpThemesInput1 = $('<div class="KUI3_Forms_Checkbox InputTheme1"><div class="_Check"></div><div class="_Title">Artículos y Vídeos sobre Linux</div><input name="Interest1" type="hidden"></div>').appendTo(SubForm);
                    var InpThemesInput2 = $('<div class="KUI3_Forms_Checkbox InputTheme2"><div class="_Check"></div><div class="_Title">Artículos y Vídeos sobre Windows</div><input name="Interest2" type="hidden"></div>').appendTo(SubForm);
                    var InpThemesInput3 = $('<div class="KUI3_Forms_Checkbox InputTheme3"><div class="_Check"></div><div class="_Title">Vídeos de YouTube</div><input name="Interest3" type="hidden"></div>').appendTo(SubForm);
                    var FormFooter;
                    if(IsModal===true)
                    {
                        FormFooter = $('<div class="KUI3_Footer"></div>').appendTo(MyWidget);
                    }
                    else
                    {
                        FormFooter = $('<div class="KUI3_Element_Box_Footer"></div>').appendTo(MyWidget);
                    }

                    var FormSubmit = $('<button class="KUI3_Button'+BtnClass+'">Suscribirme</button>').appendTo(FormFooter);

                    if($KUI.UserSession.CurrentUser.SubActivated===true)
                    {
                        if($KUI.UserSession.CurrentUser.SubInterest1===true)
                        {
                            InpThemesInput1.find('input').eq(0).val(true);
                        }
                        if($KUI.UserSession.CurrentUser.SubInterest2===true)
                        {
                            InpThemesInput2.find('input').eq(0).val(true);
                        }
                        if($KUI.UserSession.CurrentUser.SubInterest3===true)
                        {
                            InpThemesInput3.find('input').eq(0).val(true);
                        }

                        FormSubmit.html('Actualizar');
                        FormSubmit.click(function (){
                            SubForm.find('.ColorRed').each(function (){
                                $(this).removeClass('ColorRed');
                            });
                            SubForm.find('span.FormError').each(function (){
                                $(this).remove();
                            });

                            var Errors = false;

                            var Interest1 = !!InpThemesInput1.find('input').eq(0).val();
                            var Interest2 = !!InpThemesInput2.find('input').eq(0).val();
                            var Interest3 = !!InpThemesInput3.find('input').eq(0).val();
                            if(!Interest1 && !Interest2 && !Interest3)
                            {
                                InpThemesSpan.append('<span class="FormError">Debes marcar, por lo menos, un tipo de boletín.</span>');
                                Errors = true;
                            }

                            if(Errors===false)
                            {
                                FormSubmit.attr('disabled','disabled');
                                $KUI.Requests.Post($KUI.System.EndPoint+"kui/subscription",function (){
                                    FormSubmit.removeAttr('disabled');
                                    $KUI.Modals.ModalText('Boletín actualizado','<p>Tus listas de suscripciones han sido actualizadas en tu boletín electrónico.</p>','/wp-content/themes/karlasflex/imaging/assets/icons/general/subscribe.png');
                                },{
                                    sso : $KUI.UserSession.CurrentSSO,
                                    interest1 : InpThemesInput1.find('input').eq(0).val(),
                                    interest2 : InpThemesInput2.find('input').eq(0).val(),
                                    interest3 : InpThemesInput3.find('input').eq(0).val(),
                                    email : $KUI.UserSession.CurrentUser.SubEmail
                                },function (){
                                    FormSubmit.removeAttr('disabled');
                                    $KUI.Modals.ModalText('Error','<p>Se ha producido un error al actualizar el boletín.</p>','/wp-content/themes/karlasflex/imaging/assets/icons/general/subscribe.png');
                                });
                            }
                        });

                        var FormRemove;
                        if(IsModal===true)
                        {
                            FormRemove = $('<button class="KUI3_Button">Cancelar boletín</button>').prependTo(FormFooter);
                        }
                        else
                        {
                            FormRemove = $('<button class="KUI3_Button">Cancelar boletín</button>').appendTo(FormFooter);
                        }


                        FormRemove.click(function (){
                            FormRemove.attr('disabled','disabled');

                            $KUI.Requests.Delete($KUI.System.EndPoint+"kui/subscription",function (){
                                FormRemove.removeAttr('disabled');
                                $KUI.UserSession.CurrentUser.SubActivated = false;
                                $KUI.UserSession.CurrentUser.SubInterest1 = false;
                                $KUI.UserSession.CurrentUser.SubInterest2 = false;
                                $KUI.UserSession.CurrentUser.SubInterest3 = false;
                                if($KUI.SubscriptionManager.SubscriptionID)
                                {
                                    $KUI.Modals.ModalClose();
                                }
                                else
                                {
                                    MyWidget.html('');
                                    $KUI.SubscriptionManager.SuscriptionForm(MyWidget,IsModal);
                                }
                                $KUI.Modals.ModalText('Boletín eliminado','<p>Ya no recibirás más correos electrónicos. Tu boletín ha sido eliminado.</p>','/wp-content/themes/karlasflex/imaging/assets/icons/general/subscribe.png');
                            },{
                                sso : $KUI.UserSession.CurrentSSO,
                                subuuid : $KUI.SubscriptionManager.SubscriptionID
                            },function (){
                                FormRemove.removeAttr('disabled');
                                $KUI.Modals.ModalText('Error','<p>Se ha producido un error al eliminar el boletín.</p>','/wp-content/themes/karlasflex/imaging/assets/icons/general/subscribe.png');
                            });
                        });
                    }
                    else
                    {
                        if($KUI.UserSession.IsSigned)
                        {
                            FormSubmit.click(function (){
                                SubForm.find('.ColorRed').each(function (){
                                    $(this).removeClass('ColorRed');
                                });
                                SubForm.find('span.FormError').each(function (){
                                    $(this).remove();
                                });

                                var Errors = false;

                                var Interest1 = !!InpThemesInput1.find('input').eq(0).val();
                                var Interest2 = !!InpThemesInput2.find('input').eq(0).val();
                                var Interest3 = !!InpThemesInput3.find('input').eq(0).val();
                                if(!Interest1 && !Interest2 && !Interest3)
                                {
                                    InpThemesSpan.append('<span class="FormError">Debes marcar, por lo menos, un tipo de boletín.</span>');
                                    Errors = true;
                                }

                                if(Errors===false)
                                {
                                    FormSubmit.attr('disabled','disabled');
                                    $KUI.Requests.Post($KUI.System.EndPoint+"kui/subscription",function (){
                                        FormSubmit.removeAttr('disabled');
                                        $KUI.Modals.ModalText('Boletín activado','<p>¡Genial! Acabas de activar el boletín a través de correo electrónico.</p>','/wp-content/themes/karlasflex/imaging/assets/icons/general/subscribe.png');
                                        $KUI.UserSession.CurrentUser.SubActivated = true;
                                        $KUI.UserSession.CurrentUser.SubInterest1 = !!InpThemesInput1.find('input').eq(0).val();
                                        $KUI.UserSession.CurrentUser.SubInterest2 = !!InpThemesInput2.find('input').eq(0).val();
                                        $KUI.UserSession.CurrentUser.SubInterest3 = !!InpThemesInput3.find('input').eq(0).val();
                                        MyWidget.html('');
                                        $KUI.SubscriptionManager.SuscriptionForm(MyWidget,IsModal);
                                    },{
                                        sso : $KUI.UserSession.CurrentSSO,
                                        interest1 : InpThemesInput1.find('input').eq(0).val(),
                                        interest2 : InpThemesInput2.find('input').eq(0).val(),
                                        interest3 : InpThemesInput3.find('input').eq(0).val(),
                                    },function (){
                                        FormSubmit.removeAttr('disabled');
                                        $KUI.Modals.ModalText('Error','<p>Se ha producido un error al activar el boletín.</p>','/wp-content/themes/karlasflex/imaging/assets/icons/general/subscribe.png');
                                    });
                                }

                            });
                        }
                        else
                        {
                            FormSubmit.click(function (){
                                SubForm.find('.ColorRed').each(function (){
                                    $(this).removeClass('ColorRed');
                                });
                                SubForm.find('span.FormError').each(function (){
                                    $(this).remove();
                                });

                                var Errors = false;
                                const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                                if(!InpEmailInput.val()) {
                                    InpEmailInput.addClass('ColorRed');
                                    InpEmailSpan.append('<span class="FormError">El email no puede estar vacío.</span>');
                                    Errors = true;
                                }
                                else if(InpEmailInput.val().length<5) {
                                    InpEmailInput.addClass('ColorRed');
                                    InpEmailSpan.append('<span class="FormError">El email ha de tener, al menos, 5 carácteres.</span>');
                                    Errors = true;
                                }
                                else if(InpEmailInput.val().length>145) {
                                    InpEmailInput.addClass('ColorRed');
                                    InpEmailSpan.append('<span class="FormError">El email supera los 145 carácteres admitidos.</span>');
                                    Errors = true;
                                }
                                else if(!re.test(String(InpEmailInput.val()).toLowerCase()))
                                {
                                    InpEmailInput.addClass('ColorRed');
                                    InpEmailSpan.append('<span class="FormError">El formato del email no es válido.</span>');
                                    Errors = true;
                                }

                                var Interest1 = !!InpThemesInput1.find('input').eq(0).val();
                                var Interest2 = !!InpThemesInput2.find('input').eq(0).val();
                                var Interest3 = !!InpThemesInput3.find('input').eq(0).val();
                                if(!Interest1 && !Interest2 && !Interest3)
                                {
                                    InpThemesSpan.append('<span class="FormError">Debes marcar, por lo menos, un tipo de boletín.</span>');
                                    Errors = true;
                                }

                                if(Errors===false)
                                {
                                    FormSubmit.attr('disabled','disabled');
                                    $KUI.Requests.Post($KUI.System.EndPoint+"kui/subscription",function (){
                                        $KUI.Modals.ModalText('Boletín activado','<p>¡Genial! Acabas de activar el boletín a través de correo electrónico.</p><div class="KUI3_Message">Necesitarás confirmar tu boletín a través del enlace que acabas de recibir por correo electrónico.</div>','/wp-content/themes/karlasflex/imaging/assets/icons/general/subscribe.png');
                                        FormSubmit.removeAttr('disabled');
                                    },{
                                        sso : $KUI.UserSession.CurrentSSO,
                                        interest1 : InpThemesInput1.find('input').eq(0).val(),
                                        interest2 : InpThemesInput2.find('input').eq(0).val(),
                                        interest3 : InpThemesInput3.find('input').eq(0).val(),
                                        email: InpEmailInput.val()
                                    },function (){
                                        FormSubmit.removeAttr('disabled');
                                        $KUI.Modals.ModalText('Error','<p>Se ha producido un error al activar el boletín.</p>','/wp-content/themes/karlasflex/imaging/assets/icons/general/subscribe.png');
                                    });
                                }
                            });
                        }
                    }

                    if(IsModal===true)
                    {
                        var ModalClose = $('<button class="KUI3_Button">Cerrar</button>').prependTo(FormFooter);
                        ModalClose.click(function (){
                            $KUI.Modals.ModalClose();
                        });
                    }

                    $KUI.System.Triggers();

                    clearInterval(CheckInterval);
                }
            },500);
        },
        SuscriptionModal:function (){
            $KUI.Modals.ModalOpen(function (ModalContainer){
                ModalContainer.css('width','450px');
                $KUI.SubscriptionManager.SuscriptionForm(ModalContainer,true);
            });
        },
        SubscriptionID:false,
    },
    Wiki:{
        Messages: {
            NoSession:function (){
                $KUI.Modals.ModalOpen(function (Content){
                    Content.width('400px');
                    Content.append('<h1>No puedes hacer esto</h1>');
                    Content.append('<p>Para poder editar artículos o visualizar revisiones necesitas tener una cuenta de usuario.</p>');
                    var Footer = $('<div class="KUI3_Footer"></div>').appendTo(Content);
                    var BtnClose = $('<button class="KUI3_Button _Color">Cerrar</button>').appendTo(Footer);
                    BtnClose.click(function (){
                        $KUI.Modals.ModalClose();
                    });
                });
            },
        },
        Revisions:function (WikiID){
            var RevisionBox     = $('<div id="KUI3_Article_Comments"></div>').appendTo($('body'));
            var RevisionTitle   = $('<header>Revisiones<div class="_Close"></div></header>').appendTo(RevisionBox);
            var RevisionList    = $('<ul class="RevisionList KUI3_Scroll"></ul>').appendTo(RevisionBox);
            var RevisionLoader  = $('<div class="KUI3_Loader kEL_mt_15"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>').appendTo(RevisionBox);

            RevisionTitle.find('div._Close').eq(0).click(function (){
                $KUI.Articles.Comments.CloseComments();
            });

            $KUI.Requests.Get($KUI.System.EndPoint+`kui/wiki/revisions?wiki_id=${WikiID}`,function (data){
                if(RevisionBox.length<=0){
                    return;
                }

                RevisionLoader.remove();

                var Count = 0;

                $.each(data,function (i,v){
                    if(v['Author']==false){
                        return;
                    }
                    var Item = $(`<li><div class="Profile"><img src="${v['Author']['Photo']}" alt="${v['Author']['Name']}"><span>${v['Author']['Name']}</span></div></li>`).appendTo(RevisionList);
                    $(`<abbr>${v['Date']}</abbr>`).appendTo(Item);
                    var Open = $(`<a>Ver</a>`).appendTo(Item);
                    Open.click(function (){
                        $KUI.Articles.Comments.CloseComments();

                        $KUI.Wiki.Modals.RevisionOverview({
                            User_Name        : v['Author']['Name'],
                            User_Image       : v['Author']['Photo'],
                            Revision_ID      : v['ID'],
                            Revision_Date    : v['Date'],
                            Revision_Comment : v['Comment'],
                            Revision_Add     : v['m_Add'],
                            Revision_Mod     : v['m_Mod'],
                            Revision_Del     : v['m_Del'],
                            Revision_Txt     : v['m'],
                        });
                    });
                    Count++;
                });

                if(!Count || Count<=0){
                    RevisionList.remove();
                    $('<div class="KUI3_Message">No hay revisiones. Cuando alguien edite el artículo original, aquí aparecerá el historial de modificaciones realizadas por cada usuario.</div>').appendTo(RevisionBox);
                }

                $KUI.System.Triggers();
            });
        },
        Edit:function (Window,WindowBox,WindowWaiter,data){
            if(Window.length<=0){
                return;
            }

            WindowWaiter.remove();

            var WindowTitle     = $(`<h2>${data['Title']}</h2>`).appendTo(WindowBox);
            var ColumnLeft      = $('<div class="ColumnLeft"></div>').appendTo(WindowBox);
            var ColumnRight     = $('<div class="ColumnRight"></div>').appendTo(WindowBox);
            var WindowFooter    = $('<div class="KUI3_Element_Box_Footer"></div>').appendTo(ColumnLeft);
            var SaveBtn         = $('<button class="KUI3_Button">Guardar</button>').appendTo(WindowFooter);

            var FormBox         = $('<div class="KUI3_Form"></div>').appendTo(ColumnLeft);

            var TitleSpan       = $('<span><abbr>*</abbr>Concepto:</span>').appendTo(FormBox);
            var TitleField      = $('<input type="text" placeholder="Ej. Linux" name="Title">').appendTo(FormBox);

            var SearchItSpan    = $('<span><abbr>*</abbr>Título:</span>').appendTo(FormBox);
            var SearchItField   = $('<input type="text" placeholder="Escribe un título" name="SEOtitle">').appendTo(FormBox);

            var CatSpan         = $('<span><abbr>*</abbr>Categoría:</span>').appendTo(FormBox);
            var CatField        = $('<select placeholder="Selecciona una categoría" name="Cat"></select>').appendTo(FormBox);

            var ScreenSpan      = $('<span><abbr>*</abbr>Miniatura:</span>').appendTo(FormBox);
            var ScreenPrev      = $('<div class="Preview"></div>').appendTo(FormBox);
            var ScreenField     = $('<input type="file" placeholder="Escoge una imagen" name="File">').appendTo(FormBox);
            if(data['Image']){
                $(`<img id="Pho" src="${data['Image']}" alt="Miniatura">`).appendTo(ScreenPrev);
            }
            ScreenField.change(function(){
                if (this.files && this.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        var ImgH = $('#Pho');
                        if(ImgH.length<=0){
                            var New = $('<img id="Pho">').appendTo(ScreenPrev)
                            New.attr('src', e.target.result);
                            New.attr('alt',"Miniatura");
                        }
                        else {
                            ImgH.attr('src', e.target.result);
                        }
                    }

                    reader.readAsDataURL(this.files[0]);
                }
            });

            var DescSpan        = $('<span><abbr>*</abbr>Resumen:</span>').appendTo(FormBox);
            var DescField       = $('<textarea style="height: 80px;" placeholder="Escribe un resumen (2 o 3 líneas)" name="Desc">').appendTo(FormBox);

            $KUI.Requests.Get($KUI.System.EndPoint+"kui/wiki/categories?all=1",function (cats){
                if(CatField.length<0){
                    return;
                }

                $.each(cats,function (i,v){
                    var Opt = $(`<option value="${v['ID']}">${v['Title']}</option>`).appendTo(CatField);
                    if(v['ID']===data['Category']){
                        Opt.attr('default','true');
                        CatField.val(v['ID']);
                    }
                })
            });

            var Editor = $('<div id="WikiEditor"><textarea></textarea></div>').appendTo(ColumnRight);

            Editor.find('textarea').val(data['Content']);
            TitleField.val(data['Title']);
            DescField.val(data['Excerpt']);
            SearchItField.val(data['SEOTitle']);

            var EditorMD = editormd("WikiEditor",{
                width: "100%",
                height: "calc(100vh - 145px)",
                fontSize: "8pt",
                autoHeight: false,
                watch : false,
                path : '/wp-content/themes/karlasflex/imaging/md/lib/',
                toolbarIcons : function() {
                    return ["bold","italic", "quote", "|", "h2", "h3" , "|","list-ul", "list-ol", "table", "hr", "|", "image", "link", "|", "code", "preformatted-text", "|", "watch"]
                },
                lang : {
                    name: "en"
                },
                theme : $KUI.Template.ColorCtrl.EditorColor,
                editorTheme : $KUI.Template.ColorCtrl.EditorSubColor
            });

            if($KUI.Wiki.IsNew===true){
                SaveBtn.click(function (){
                    $KUI.Wiki.Actions.Save(WindowBox,SaveBtn,EditorMD,{
                        'TitleField'    : TitleField,
                        'TitleSpan'     : TitleSpan,
                        'SearchItField' : SearchItField,
                        'SearchItSpan'  : SearchItSpan,
                        'DescField'     : DescField,
                        'SearchItSpan'  : SearchItSpan,
                        'ComField'      : false,
                        'ComSpan'       : false,
                        'ScreenField'   : ScreenField,
                        'ScreenSpan'    : ScreenSpan,
                        'CatField'      : CatField,
                        'CatSpan'       : CatSpan,
                    },data);
                });
            }
            else {
                SaveBtn.click(function (){
                    $KUI.Modals.ModalOpen(function (ModalContainer){
                        ModalContainer.css('width','400px');

                        $(`<h1>Comentarios de edición</h1>`).appendTo(ModalContainer);
                        var SecFormBox         = $('<div class="KUI3_Form"></div>').appendTo(ModalContainer);
                        var ComSpan        = $('<span><abbr>*</abbr>Motivo de edición:</span>').appendTo(SecFormBox);
                        var ComField       = $('<textarea style="height: 80px;" placeholder="Especificar motivo para llevar a cabo la edición. Si es una corrección ortografica, especificar dicho motivo aquí." name="Desc">').appendTo(SecFormBox);

                        var Sec_Footer           = $(`<div class="KUI3_Footer"></div>`).appendTo(ModalContainer);
                        var Sec_Footer_Close    = $(`<button class="KUI3_Button">Cancelar</button>`).appendTo(Sec_Footer);
                        var Sec_Footer_Save    = $(`<button class="KUI3_Button _Color">Guardar</button>`).appendTo(Sec_Footer);

                        Sec_Footer_Close.click(function (){
                            $KUI.Modals.ModalClose();
                        });

                        Sec_Footer_Save.click(function (){
                            $KUI.Wiki.Actions.Save(WindowBox,Sec_Footer_Save,EditorMD,{
                                'TitleField'    : TitleField,
                                'TitleSpan'     : TitleSpan,
                                'SearchItField' : SearchItField,
                                'SearchItSpan'  : SearchItSpan,
                                'DescField'     : DescField,
                                'SearchItSpan'  : SearchItSpan,
                                'ComField'      : ComField,
                                'ComSpan'       : ComSpan,
                                'ScreenField'   : ScreenField,
                                'ScreenSpan'    : ScreenSpan,
                                'CatField'      : CatField,
                                'CatSpan'       : CatSpan,
                            },data);
                        });
                    });
                });
            }

            $KUI.System.Triggers();
        },
        IsNew:false,
        Add:function (){
            $KUI.Wiki.IsNew = true;
            if($KUI.UserSession.IsSigned===true){
                var Window = $('<div id="EditorWindow"></div>').appendTo($('body'));
                var WindowBox = $('<div class="EditorBox"></div>').appendTo(Window);
                var WindowClose = $('<div class="_ModalClose"></div>').appendTo(WindowBox);
                var WindowWaiter = $('<div class="KUI3_Loader"><img alt="..." src="'+$KUI.Resources.Assets.LoaderSVG+'"></div>').appendTo(WindowBox);

                WindowClose.click(function (){
                    $KUI.Modals.Editor.Close();
                });

                $KUI.Wiki.Edit(Window,WindowBox,WindowWaiter,{
                    "ID"       : 0,
                    "Title"    : "Nuevo artículo",
                    "SEOTitle" : "",
                    "Excerpt"  : "",
                    "Content"  : "",
                    "Image"    : false,
                    "Revision" : false,
                });
            }
            else {
                $KUI.Wiki.Messages.NoSession();
            }
        },
        Modals:{
            RevisionOverview:function (d){
                $KUI.Modals.ModalOpen(function (ModalContainer){
                    ModalContainer.css('width','400px');

                    $(`<h1>Información de Revisión</h1>`).appendTo(ModalContainer);

                    var Revision_Overview_Box       = $(`<div class="KUI_Revisions_Overview"></div>`).appendTo(ModalContainer);
                    var Revision_Overview_User      = $(`<div class="UserCard"><img alt="${d['User_Name']}" src="${d['User_Image']}"></div>`).appendTo(Revision_Overview_Box);
                    var Revision_Overview_Data      = $(`<div class="Data"></div>`).appendTo(Revision_Overview_Box);


                    $(`<div class="Title">Revisión publicada por</div>`).appendTo(Revision_Overview_Data);
                    $(`<div class="Name">${d['User_Name']}</div>`).appendTo(Revision_Overview_Data);
                    $(`<div class="Date">${d['Revision_Date']}</div>`).appendTo(Revision_Overview_Data);
                    if(!d['Revision_Comment']){
                        d['Revision_Comment'] = '<em>Sin comentarios.</em>';
                    }
                    $(`<div class="Modified">
                        <p>${d['Revision_Comment']}</p>
                        <span class="Gnd">${d['Revision_Add']}</span>
                        <span class="Red">${d['Revision_Del']}</span>
                    </div>`).appendTo(Revision_Overview_Data);

                    var Revision_Overview_Footer    = $(`<div class="Footer"></div>`).appendTo(Revision_Overview_Data);
                    var Revision_Footer_Edit        = $(`<button>Editar</button>`).appendTo(Revision_Overview_Footer);
                    var Revision_Footer_Diff        = $(`<button>Diff</button>`).appendTo(Revision_Overview_Footer);
                    var Revision_Footer_Cancel      = $(`<button>Cancelar</button>`).appendTo(Revision_Overview_Footer);

                    Revision_Footer_Edit.click(function (){
                        $KUI.Modals.Editor.WikiEdit(d['Revision_ID']);
                    });
                    Revision_Footer_Diff.click(function (){
                        $KUI.Modals.ModalOpen(function (SubModalContainer){
                            SubModalContainer.css('max-width','1100px');
                            SubModalContainer.addClass('Expanded');

                            $(`<h1>Visualizar cambios: Revisión de ${d['User_Name']} @ ${d['Revision_Date']}</h1>`).appendTo(SubModalContainer);
                            var Diff_PrintBox       = $(`<div class="KUI_Revisions_TextPrinted KUI3_ContentFormal KUI3_Scroll"></div>`).appendTo(SubModalContainer);
                            var Diff_Footer         = $(`<div class="KUI3_Footer"></div>`).appendTo(SubModalContainer);
                            var Diff_FooterClose    = $(`<button class="KUI3_Button _Color">Cerrar</button>`).appendTo(Diff_Footer);
                            Diff_FooterClose.click(function (){
                                $KUI.Modals.ModalClose();
                            });

                            Diff_PrintBox.html(d['Revision_Txt']);
                        });
                    });
                    Revision_Footer_Cancel.click(function (){
                        $KUI.Modals.ModalClose();
                    });
                });
            }
        },
        Actions:{
            Save:function (WindowBox,Sec_Footer_Save,EditorMD,FormDatas,data){
                TitleField      = FormDatas['TitleField'];
                TitleSpan       = FormDatas['TitleSpan'];
                SearchItField   = FormDatas['SearchItField'];
                SearchItSpan    = FormDatas['SearchItSpan'];
                DescField       = FormDatas['DescField'];
                DescSpan        = FormDatas['SearchItSpan'];
                ComField        = FormDatas['ComField'];
                ComSpan         = FormDatas['ComSpan'];
                ScreenField     = FormDatas['ScreenField'];
                ScreenSpan      = FormDatas['ScreenSpan'];
                CatField        = FormDatas['CatField'];
                CatSpan         = FormDatas['CatSpan'];

                $(WindowBox).find('span.FormError').each(function (){
                    $(this).remove();
                });
                $(WindowBox).find('.ColorRed').each(function (){
                    $(this).removeClass('ColorRed');
                });

                if(Sec_Footer_Save.attr('disabled')) return;
                Sec_Footer_Save.attr('disabled','true');

                var Errors = false;

                var Field_Content = EditorMD.getMarkdown();

                if(!Field_Content) {
                    Errors = true;
                    $KUI.Modals.ModalText("Error al guardar","El contenido no puede estar vacío.");
                }

                if(!TitleField.val()){
                    TitleField.addClass('ColorRed');
                    TitleSpan.append('<span class="FormError">El concepto no puede estar vacío.</span>');
                    Errors = true;
                }
                else if(TitleField.val().length>30){
                    TitleField.addClass('ColorRed');
                    TitleSpan.append('<span class="FormError">El concepto es demasiado largo. Máx. 30 carácteres.</span>');
                    Errors = true;
                }
                else if(TitleField.val().length<3){
                    TitleField.addClass('ColorRed');
                    TitleSpan.append('<span class="FormError">El concepto es demasiado corto. Min. 3 carácteres.</span>');
                    Errors = true;
                }

                if(!SearchItField.val()){
                    SearchItField.addClass('ColorRed');
                    SearchItSpan.append('<span class="FormError">El título no puede estar vacío.</span>');
                    Errors = true;
                }
                else if(SearchItField.val().length>125){
                    SearchItField.addClass('ColorRed');
                    SearchItSpan.append('<span class="FormError">El título es demasiado largo. Máx. 125 carácteres.</span>');
                    Errors = true;
                }
                else if(SearchItField.val().length<3){
                    SearchItField.addClass('ColorRed');
                    SearchItSpan.append('<span class="FormError">El título es demasiado corto. Min. 3 carácteres.</span>');
                    Errors = true;
                }

                if(!DescField.val()){
                    DescField.addClass('ColorRed');
                    DescSpan.append('<span class="FormError">La descripción no puede estar vacía.</span>');
                    Errors = true;
                }
                else if(DescField.val().length>256){
                    DescField.addClass('ColorRed');
                    DescSpan.append('<span class="FormError">La descripción es demasiado larga. Máx. 256 carácteres.</span>');
                    Errors = true;
                }
                else if(DescField.val().length<3){
                    DescField.addClass('ColorRed');
                    DescSpan.append('<span class="FormError">La descripción es demasiado corta. Min. 3 carácteres.</span>');
                    Errors = true;
                }

                if($KUI.Wiki.IsNew===false){
                    if(!ComField.val()){
                        ComField.addClass('ColorRed');
                        ComSpan.append('<span class="FormError">El motivo no puede estar vacío.</span>');
                        Errors = true;
                    }
                    else if(ComField.val().length>256){
                        ComField.addClass('ColorRed');
                        ComSpan.append('<span class="FormError">El motivo es demasiado largo. Máx. 256 carácteres.</span>');
                        Errors = true;
                    }
                    else if(ComField.val().length<3){
                        ComField.addClass('ColorRed');
                        ComSpan.append('<span class="FormError">El motivo es demasiado corto. Min. 3 carácteres.</span>');
                        Errors = true;
                    }
                }


                if(!data['Image'] && ScreenField.get(0).files.length===0){
                    ScreenSpan.append('<span class="FormError">Debes seleccionar un fichero para la miniatura.</span>');
                    Errors = true;
                }
                else if(ScreenField.get(0).files.length!==0 && ScreenField.get(0).files[0].size>10000000){
                    ScreenSpan.append('<span class="FormError">El tamaño de la miniatura supera los 10 MB permitidos.</span>');
                    Errors = true;
                }
                else if(ScreenField.get(0).files.length!==0 && ScreenField.get(0).files[0].type!=='image/bmp' && ScreenField.get(0).files[0].type!=='image/png' && ScreenField.get(0).files[0].type!=='image/jpg' && ScreenField.get(0).files[0].type!=='image/jpeg'){
                    ScreenSpan.append('<span class="FormError">La miniatura ha de ser una imagen JPG, PNG o BMP.</span>');
                    Errors = true;
                }

                if(Errors===false){
                    var formData = new FormData();
                    formData.append("sso", $KUI.UserSession.CurrentSSO);
                    formData.append("wiki_id", data['ID']);
                    formData.append("title", TitleField.val());
                    formData.append("seotitle", SearchItField.val());
                    formData.append("content", Field_Content);
                    formData.append("excerpt", DescField.val());
                    formData.append("category", CatField.val());
                    if($KUI.Wiki.IsNew===false){
                        formData.append("comment", ComField.val());
                    }
                    if(data['Revision']!==false){
                        formData.append("revision", data['Revision']['ID']);
                    }
                    if(ScreenField.get(0).files.length===0){

                    }
                    else {
                        formData.append("image", ScreenField.get(0).files[0]);
                    }

                    $.ajax({
                        url: $KUI.System.EndPoint+"kui/wiki/editor",
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(data){
                            if($KUI.Wiki.IsNew===true){
                                $('#EditorWindow').remove();
                                $KUI.Modals.ModalText("¡Bien!","El artículo ha sido creado.");
                            }
                            else {
                                $KUI.Modals.ModalClose();
                                $KUI.Modals.ModalText("¡Bien!","El artículo ha sido actualizado.");
                            }
                            sessionStorage.clear();

                            $KUI.Location.CurrentUrl = false;
                            $KUI.Location.Push();

                            if($KUI.Wiki.IsNew===true){
                                $KUI.Location.Goto(data['Url']);
                            }

                        },
                        error:function (obj,str,cod){
                            Sec_Footer_Save.removeAttr('disabled');
                            $KUI.Modals.Templates.Modal_Error_Code(obj.status,'Error al guardar','No ha sido posible guardar el artículo.');
                        },
                    });
                }
                else {
                    Sec_Footer_Save.removeAttr('disabled');
                }
            }
        }
    }
};