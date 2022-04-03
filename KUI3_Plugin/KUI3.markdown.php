<?php
define('KUI_MARKDOWN_BLOCK',0);
define('KUI_MARKDOWN_CITE',1);
define('KUI_MARKDOWN_CODE',2);
define('KUI_MARKDOWN_TABLE',3);
define('KUI_MARKDOWN_FORMTABLE',4);
define('KUI_MARKDOWN_CODEP', 5);

function KUI3_Do_Markdown($Content){
    $Content = strip_tags($Content);
    $Content = str_replace("\r", "", $Content);

    $E = explode("\n", $Content);
    $E[] = "";
    $E[-1] = "";
    $Count = count($E);

    $Mardown_Sangria = 0;
    $Mardown_Block = KUI_MARKDOWN_BLOCK;
    $Mardown_L_Queue = [];
    $Mardown_Dictionary = [];
    $Mardown_MultiDictionary = [];

    for ($Line = 0; $Line < $Count; $Line++) {
        if (!isset($E[$Line])) {
            continue;
        }

        $Current = $E[$Line];
        $Prev = isset($E[$Line - 1]) ? $E[$Line - 1] : "";
        $Next = isset($E[$Line + 1]) ? $E[$Line + 1] : "";

        if ($Line == -1 || $Line == $Count) {
            continue;
        }


        $E[$Line] = "";

        ## REINICIAR LISTAS
        if (!preg_match('/^[-*+]\s(.*)$/', $Current)
            && !preg_match('/^\s\s\s\s[-*+]\s(.*)$/', $Current)
            && !preg_match('/^\s\s\s\s\s\s\s\s[-*+]\s(.*)$/', $Current)

            && !preg_match('/^[0-9]{1,5}\.\s(.*)$/', $Current)
            && !preg_match('/^\s\s\s\s[0-9]{1,5}\.\s(.*)$/', $Current)
            && !preg_match('/^\s\s\s\s\s\s\s\s[0-9]{1,5}\.\s(.*)$/', $Current)
        ) {
            while ($Mardown_L_Queue) {
                $Get = array_pop($Mardown_L_Queue);
                $E[$Line] .= "</" . strtolower($Get) . ">";
                $Mardown_Sangria--;
            }
            $Mardown_Sangria = 0;
        }

        ## REINICIAR CODE A
        if ($Mardown_Block === KUI_MARKDOWN_CODEP && !preg_match('/^\s\s\s\s(.*)$/', $Current)
        ) {
            $Mardown_Block = KUI_MARKDOWN_BLOCK;
            $E[$Line] .= "</pre>";
            continue;
        }

        ## REINICIAR BLOQUE
        if ($Mardown_Block === KUI_MARKDOWN_CITE && ((substr($Current, 0, 1) !== '>' && substr($Current, 0, 4) !== '&gt;') || !$Current)) {
            $E[$Line] .= "</blockquote>";
            $Mardown_Block = KUI_MARKDOWN_BLOCK;
            continue;
        }

        ## REINICIAR TABLAS ANIDADAS
        if ($Mardown_Block === KUI_MARKDOWN_TABLE && (preg_match('/^[-]{1,5}$/', $Current, $M) || !$Current)) {
            $Mardown_Block = KUI_MARKDOWN_BLOCK;
            $E[$Line] .= "</div></div>";
            continue;
        }

        ## REINICIAR TABLAS
        if ($Mardown_Block === KUI_MARKDOWN_FORMTABLE && (substr(trim($Current), 0, 1) != '|' || !$Current)) {
            $Mardown_Block = KUI_MARKDOWN_BLOCK;
            $E[$Line] .= "</table>";
            continue;
        }

        if (!$Current) {
            continue;
        }

        /*
         * BLOQUE DE CÓDIGOS
         */
        if (trim($Current) === "~~~") {
            if ($Mardown_Block === KUI_MARKDOWN_CODE) {
                $Mardown_Block = KUI_MARKDOWN_BLOCK;
                $E[$Line] .= "</pre>";
            } else {
                $Mardown_Block = KUI_MARKDOWN_CODE;
                $E[$Line] .= "<pre>";
            }
            continue;
        }

        /*
         * TABLAS ANIDADAS
         */
        if (preg_match('/^\-\-\s(.*)\s\-\-$/', $Current, $M)) {
            if ($Mardown_Block === KUI_MARKDOWN_TABLE) {
                $E[$Line] .= "</div></div>";
            }

            $Mardown_Block = KUI_MARKDOWN_TABLE;

            $E[$Line] .= "<div class='KUI3_WikiAbstractionTable'><div class='KUI3_Element_SubCaption'>$M[1]</div><div class='KUI3_Element_TableOfKeyValue'>";
            continue;
        } elseif ($Mardown_Block === KUI_MARKDOWN_TABLE && (preg_match('/^[-]{1,5}$/', $Current, $M) || !$Current)) {
            $Mardown_Block = KUI_MARKDOWN_BLOCK;
            $E[$Line] .= "</div></div>";
            continue;
        } elseif ($Mardown_Block === KUI_MARKDOWN_TABLE) {
            if (preg_match('/(.*)\s:\s(.*)/', $Current, $M)) {
                $E[$Line] .= "<div class='Row'><p>$M[1]</p><p>$M[2]</p></div>";
            } else {
                $E[$Line] .= "<div class='Row'><p class='Expand'>$Current</p></div>";
            }
        }

        /*
         * TABLAS
         */
        if (substr(trim($Current), 0, 1) == '|') {
            if ($Mardown_Block !== KUI_MARKDOWN_FORMTABLE) {
                $Mardown_Block = KUI_MARKDOWN_FORMTABLE;
                $E[$Line] = "<table class='KUI3_Element_FormalTable'>";

                $Table_Header = -1;
                $Table_Header_Line = -1;
                $Table_Aligns = [];
                $Table_K_StartOn = -1;
            }

            $Unset = false;

            $E[$Line] .= "<tr>";

            $Tabs = explode("|", $Current);
            foreach ($Tabs as $Tib) {
                $Table_Aligns[] = 0;
            }
            foreach ($Tabs as $K => $Tabs_I) {
                $Tabs_I = trim($Tabs_I);
                $Tabs_Txt = $Tabs_I;

                if (!$Tabs_I) {
                    if ($Table_K_StartOn == -1) {
                        continue;
                    } else {
                        if ($K < $Table_K_StartOn) {
                            continue;
                        } else {
                            $E[$Line] .= "<td>&nbsp;</td>";
                            continue;
                        }
                    }
                    continue;
                } else if ($Table_K_StartOn === -1) {
                    $Table_K_StartOn = $K;
                }

                $Tabs_I = preg_replace("/[\s\t]/", "", $Tabs_I);

                if (preg_match('/^[\-\:]*$/', $Tabs_I)) {
                    if (isset($E[$Line - 1]) && $Table_Header === -1) {
                        $Table_Header = $E[$Line - 1];
                    }

                    if (preg_match('/^\:\-*\:$/', $Tabs_I)) {
                        $Table_Aligns[$K] = 3;
                    } elseif (preg_match('/^\:\-*$/', $Tabs_I)) {
                        $Table_Aligns[$K] = 1;
                    } elseif (preg_match('/^\-*\:$/', $Tabs_I)) {
                        $Table_Aligns[$K] = 2;
                    }

                    $Unset = true;
                }

                $Table_Aligns_This = '';
                switch ($Table_Aligns[$K]) {
                    case 1:
                        $Table_Aligns_This = ' style="text-align:left;"';
                        break;
                    case 2:
                        $Table_Aligns_This = ' style="text-align:right;"';
                        break;
                    case 3:
                        $Table_Aligns_This = ' style="text-align:center;"';
                        break;
                }

                if ($Table_Header !== -1 && $Table_Header_Line === -1) {
                    $Table_Header_Line = $Line - 1;

                    $E[$Table_Header_Line] = str_replace('<td>', '<th>', $E[$Table_Header_Line]);
                }


                $E[$Line] .= "<td{$Table_Aligns_This}>{$Tabs_Txt}</td>";
            }


            $E[$Line] .= "</tr>";

            if ($Unset === true) {
                unset($E[$Line]);
            }

            //continue;
        }


        if ($Mardown_Block === KUI_MARKDOWN_CODE) {
            if (trim($Next) === "~~~") {
                $E[$Line] = $Current;
            } else {
                $E[$Line] = "$Current<br>";
            }
            continue;
        }

        /*
         * EJECUCIONES POR LÍNEA
         */
        if ($Mardown_Block !== KUI_MARKDOWN_TABLE && $Mardown_Block !== KUI_MARKDOWN_FORMTABLE) {
            /*
             * SUBTÍTULO
             */
            if (preg_match('/^###(.*)$/', $Current, $M)) {
                $M[1] = trim(str_replace('#','',$M[1]));
                $E[$Line] = "<h3>$M[1]</h3>";
            } /*
             * TÍTULO
             */
            elseif (preg_match('/^##(.*)$/', $Current, $M) || preg_match('/^#(.*)$/', $Current, $M)) {
                $M[1] = trim(str_replace('#','',$M[1]));
                $E[$Line] = "<h2>$M[1]</h2>";
            }
            /*
             * LISTAS DESORDENADAS
             */
            elseif (preg_match('/^[-*+]\s(.*)$/', $Current, $M)) {
                $Original = $M[1];

                if ($Mardown_Sangria === 0) {
                    $Mardown_L_Queue[] = 'UL';

                    $Current = "<ul>";
                } else {
                    $Current = "";
                }
                $Mardown_Sangria = 1;

                $Current .= "<li>$Original</li>";

                if (!$Next) {
                    $Get = array_pop($Mardown_L_Queue);
                    $Current .= "</" . strtolower($Get) . ">";

                    $Mardown_Sangria = 0;
                }

                $E[$Line] = $Current;
            } elseif (preg_match('/^\s\s\s\s[-*+]\s(.*)$/', $Current, $M)) {
                $Original = $M[1];

                //if($Mardown_Sangria===0){
                //unset($E[$Line]);
                //}
                if ($Mardown_Sangria === 1) {
                    $Mardown_L_Queue[] = 'UL';

                    $Current = "<ul>";
                } else {
                    $Current = "";
                }
                $Mardown_Sangria = 2;

                $Current .= "<li>$Original</li>";

                if (!$Next) {
                    $Get = array_pop($Mardown_L_Queue);
                    $Current .= "</" . strtolower($Get) . ">";

                    $Mardown_Sangria = 0;
                } elseif (preg_match('/^-\s(.*)$/', $Next)) {
                    $Get = array_pop($Mardown_L_Queue);
                    $Current .= "</" . strtolower($Get) . ">";

                    $Mardown_Sangria = 1;
                }

                $E[$Line] = $Current;
            } elseif (preg_match('/^\s\s\s\s\s\s\s\s[-*+]\s(.*)$/', $Current, $M)) {
                $Original = $M[1];

                //if($Mardown_Sangria===0 || $Mardown_Sangria===1){
                //unset($E[$Line]);
                //}
                if ($Mardown_Sangria === 2) {
                    $Mardown_L_Queue[] = 'UL';

                    $Current = "<ul>";
                } else {
                    $Current = "";
                }

                $Mardown_Sangria = 3;

                $Current .= "<li>$Original</li>";

                if (!$Next) {
                    $Get = array_pop($Mardown_L_Queue);
                    $Current .= "</" . strtolower($Get) . ">";


                    $Mardown_Sangria = 0;
                } elseif (preg_match('/^\s\s\s\s-\s(.*)$/', $Next)) {
                    $Get = array_pop($Mardown_L_Queue);
                    $Current .= "</" . strtolower($Get) . ">";

                    $Mardown_Sangria = 2;
                } elseif (preg_match('/^-\s(.*)$/', $Next)) {
                    $Get = array_pop($Mardown_L_Queue);
                    $Current .= "</" . strtolower($Get) . ">";

                    $Mardown_Sangria = 1;
                }

                $E[$Line] = $Current;
            }
            /*
             * LISTAS ORDENADAS
             */
            elseif (preg_match('/^[0-9]{1,5}\.\s(.*)$/', $Current, $M)) {
                $Original = $M[1];

                if ($Mardown_Sangria === 0) {
                    $Mardown_L_Queue[] = 'OL';

                    $Current = "<ol>";
                } else {
                    $Current = "";
                }
                $Mardown_Sangria = 1;

                $Current .= "<li>$Original</li>";

                if (!$Next) {
                    $Get = array_pop($Mardown_L_Queue);
                    $Current .= "</" . strtolower($Get) . ">";

                    $Mardown_Sangria = 0;
                }

                $E[$Line] = $Current;
            } elseif (preg_match('/^\s\s\s\s[0-9]{1,5}\.\s(.*)$/', $Current, $M)) {
                $Original = $M[1];

                //if($Mardown_Sangria===0){
                //unset($E[$Line]);
                //}
                if ($Mardown_Sangria === 1) {
                    $Mardown_L_Queue[] = 'OL';

                    $Current = "<ol>";
                } else {
                    $Current = "";
                }
                $Mardown_Sangria = 2;

                $Current .= "<li>$Original</li>";

                if (!$Next) {
                    $Get = array_pop($Mardown_L_Queue);
                    $Current .= "</" . strtolower($Get) . ">";

                    $Mardown_Sangria = 0;
                } elseif (preg_match('/^-\s(.*)$/', $Next)) {
                    $Get = array_pop($Mardown_L_Queue);
                    $Current .= "</" . strtolower($Get) . ">";

                    $Mardown_Sangria = 1;
                }

                $E[$Line] = $Current;
            } elseif (preg_match('/^\s\s\s\s\s\s\s\s[0-9]{1,5}\.\s(.*)$/', $Current, $M)) {
                $Original = $M[1];

                //if($Mardown_Sangria===0 || $Mardown_Sangria===1){
                //unset($E[$Line]);
                //}
                if ($Mardown_Sangria === 2) {
                    $Mardown_L_Queue[] = 'OL';

                    $Current = "<ol>";
                } else {
                    $Current = "";
                }

                $Mardown_Sangria = 3;

                $Current .= "<li>$Original</li>";

                if (!$Next) {
                    $Get = array_pop($Mardown_L_Queue);
                    $Current .= "</" . strtolower($Get) . ">";


                    $Mardown_Sangria = 0;
                } elseif (preg_match('/^\s\s\s\s-\s(.*)$/', $Next)) {
                    $Get = array_pop($Mardown_L_Queue);
                    $Current .= "</" . strtolower($Get) . ">";

                    $Mardown_Sangria = 2;
                } elseif (preg_match('/^-\s(.*)$/', $Next)) {
                    $Get = array_pop($Mardown_L_Queue);
                    $Current .= "</" . strtolower($Get) . ">";

                    $Mardown_Sangria = 1;
                }

                $E[$Line] = $Current;
            }
            /*
             * CODIGO P
             */
            elseif (preg_match('/^\s\s\s\s([^0-9*-+]{1})(.*)$/', $Current, $M)) {
                if($Mardown_Block!==KUI_MARKDOWN_CODEP){
                    $Mardown_Block = KUI_MARKDOWN_CODEP;
                    $Current = "<pre>";
                }
                else {
                    $Current = '';
                }
                $Original = $M[1].$M[2];
                $Current .= "<p>$Original</p>";

                $E[$Line] = $Current;
            }
            /*
             * LINEA HORIZONTAL
             */
            elseif (preg_match('/^[*_-]{3}$/', $Current, $M)) {
                $E[$Line] = "<hr>";
            } /*
             * BLOQUE DE CITACIÓN
             */
            elseif (preg_match('/^(>|\&gt;)(.*)$/', $Current, $M)) {
                $Original = $M[2];
                $Original_Explode = explode(' - ', $Original);

                if (count($Original_Explode) > 1) {
                    $Author = array_pop($Original_Explode);
                    $Original = implode(' - ', $Original_Explode);

                    if ($Mardown_Block !== KUI_MARKDOWN_CITE) {
                        $Mardown_L_Queue[] = 'blockquote';

                        $Current = "<blockquote>";
                    } else {
                        $Current = "";
                    }
                    $Mardown_Block = KUI_MARKDOWN_CITE;

                    $Current .= "<p>$Original</p><cite>$Author</cite>";

                    if (!$Next) {
                        $Current .= "</blockquote>";

                        $Mardown_Block = KUI_MARKDOWN_BLOCK;
                    }

                    $E[$Line] = $Current;
                } else {
                    if ($Mardown_Block !== KUI_MARKDOWN_CITE) {
                        $Mardown_L_Queue[] = 'blockquote';

                        $Current = "<blockquote>";
                    } else {
                        $Current = "";
                    }
                    $Mardown_Block = KUI_MARKDOWN_CITE;

                    $Current .= "<p>$Original</p>";

                    if (!$Next) {
                        $Current .= "</blockquote>";

                        $Mardown_Block = KUI_MARKDOWN_BLOCK;
                    }

                    $E[$Line] = $Current;
                }
            } /*
             * TEXTO PREFORMATEADO
             */
            /*
             * DICCIONARIO DE REFERENCIAS
             */
            elseif (preg_match('/^\[(.*)]:\s([^\s]*)(\s"(.*)")?$/', $Current, $M)) {
                unset($E[$Line]);

                $Mardown_Dictionary[$M[1]] = [
                    $M[2],
                    isset($M[3]) && $M[3] ? $M[4] : false
                ];

                continue;
            } /*
             * PÁRRAFO
             */
            else {
                if($Current){
                    $E[$Line] = "<p>$Current</p>";
                }
            }
        }


        /*
         * FORMATEO ESTÁNDARD
         */
        $Current_Spaced = " $E[$Line] ";
        $Current_Spaced = str_replace([')('], [') ('], $Current_Spaced);

        $MatchWord = "([\s,\.;:-_()])";

        ## NEGRITA Y CURSIVA
        ##
        ## ***negrita y cursiva***
        ## ___negrita y cursiva___
        ##
        $Current_Spaced = preg_replace("~{$MatchWord}[*_]{3}([^*_]*)[*_]{3}{$MatchWord}~", '$1<strong><em>$2</em></strong>$3', $Current_Spaced);

        ## NEGRITA
        ##
        ## **negrita**
        ## __negrita__
        ##
        $Current_Spaced = preg_replace("~{$MatchWord}[*_]{2}([^*_]*)[*_]{2}{$MatchWord}~", '$1<strong>$2</strong>$3', $Current_Spaced);

        ## CURSIVA
        ##
        ## *cursiva*
        ## _cursiva_
        ##
        $Current_Spaced = preg_replace("~{$MatchWord}[*_]([^*_]*)[*_]{$MatchWord}~", '$1<em>$2</em>$3', $Current_Spaced);

        ## PRE
        ##
        ## `pre`
        ##
        $Current_Spaced = preg_replace("~{$MatchWord}[`]([^`]*)[`]{$MatchWord}~", '$1<span class="Code">$2</span>$3', $Current_Spaced);

        // ENLACES AUTOMÁTICOS
        $Current_Spaced = preg_replace("|([^\(])(http[s]?[^\s<>]*)([^\)])|", '$1<a target="_blank" href="$2">$2</a>$3', $Current_Spaced);

        ## IMAGEN CON ENLACE
        ##
        ## [![absa](https://www.tablesgenerator.com/static/img/logo.png "absa")](https://www.tablesgenerator.com/markdown_tables "absa")
        ##
        $Current_Spaced = preg_replace_callback("~\[(!)*\[([^\]]+)?\]\(([^\)]+?)(?: \"([\w\s]+)\")*\)\]\(([^\)]+?)(?: \"([\w\s]+)\")*\)~", function ($M) {
            if (!$M[4]) {
                $M[4] = $M[6];
            }
            if (!$M[6]) {
                $M[4] = $M[2];
            }

            return "<a target='_blank' href='$M[5]' title='$M[6]'><img class='KUI3_Element_ImageMarked' src='$M[3]' alt='$M[4]'></a><p class='KUI3_Element_ImageLegend'>$M[4]</p>";
        }, $Current_Spaced);


        ## IMÁGENES & ENLACES
        ##
        ## ![]()
        ##
        preg_match_all("~{$MatchWord}(!)*\[([^\]]+)?\]\(([^\)]+?)(?: \"([\w\s]+)\")*\){$MatchWord}~", $Current_Spaced, $M, PREG_SET_ORDER);
        foreach ($M as $set) {
            if (isset($set[5]) && $set[5]) {
                $set[3] = $set[5];
            }
            $title = isset($set[5]) && $set[5] ? " title=\"{$set[5]}\"" : '';
            if ($set[2]) {
                $Current_Spaced = str_replace($set[0], "$set[1]<img class='KUI3_Element_ImageMarked' src=\"{$set[4]}\"$title alt=\"{$set[3]}\"/>" . ($set[3] ? "<p class='KUI3_Element_ImageLegend'>$set[3]</p>" : "") . "$set[6]", $Current_Spaced);
            } else {
                $Current_Spaced = str_replace($set[0], "$set[1]<a target='_blank' href=\"{$set[4]}\"$title>{$set[3]}</a>$set[6]", $Current_Spaced);
            }
        }

        $E[$Line] = trim($Current_Spaced);
    }

    /*
     * FASE 2
     */
    for ($Line = 0; $Line < $Count; $Line++) {
        if (!isset($E[$Line])) {
            continue;
        }

        $E[$Line] = " $E[$Line] ";
        $MatchWord = "([\s,.;:-_()])";

        /*
         * IMÁGENES (DICCIONARIO)
         *
         * Tipo #1:  ![Nombre -sin usar-][Referencia]
         */
        $E[$Line] = preg_replace_callback("~{$MatchWord}(!)*\[(.*)]\[(.*)]{$MatchWord}~", function ($M) use ($Mardown_Dictionary) {
            if (isset($Mardown_Dictionary[$M[4]])) {
                list($Src, $Alt) = $Mardown_Dictionary[$M[4]];
                return "$M[1]<a target='_blank' href='$Src'><img class='KUI3_Element_ImageMarked' src='$Src' alt='$Alt'></a>" . ($Alt ? "<p class='KUI3_Element_ImageLegend'>$Alt</p>" : "") . "$M[5]";
            }
            return "";
        }, $E[$Line]);

        /*
         * ENLACES (DICCIONARIO)
         *
         * Tipo #1:  [Nombre -sin usar-][Referencia]
         */
        $E[$Line] = preg_replace_callback("/{$MatchWord}\[(.*)]\[(.*)]{$MatchWord}/", function ($M) use ($Mardown_Dictionary) {
            if (isset($Mardown_Dictionary[$M[3]])) {
                list($Src, $Alt) = $Mardown_Dictionary[$M[3]];
                return "$M[1]<a href='$Src' title='$Alt' target='_blank'>$M[2]</a>$M[4]";
            }
            return "";
        }, $E[$Line]);

        /*
         * REFERENCIAS INTERNAS A OTROS POSTS
         *
         * Tipo #1:  [Título del Post]
         */
        $E[$Line] = preg_replace_callback("~{$MatchWord}\[(.*?)]{$MatchWord}~s", function ($M) use ($Mardown_Dictionary) {
            $Post_Query = get_page_by_title($M[2], OBJECT, 'wiki_post');
            if ($Post_Query) {
                $Post_ID = $Post_Query->ID;
                $Permalink = get_the_permalink($Post_ID);
                return "$M[1]<a data-goto='$Permalink'>$M[2]</a>$M[3]";
            }
            return "$M[1]<span style='color:red;'>$M[2]</span>$M[3]";
        }, $E[$Line]);


        $E[$Line] = trim($E[$Line]);
    }

    $doc = new DOMDocument();
    $doc->substituteEntities = false;
    $content = mb_convert_encoding(implode($E), 'html-entities', 'utf-8');
    @$doc->loadHTML($content);
    $E = $doc->saveHTML();

    $E = str_replace('&amp;bsol;','\\',$E);

    return $E;
}
