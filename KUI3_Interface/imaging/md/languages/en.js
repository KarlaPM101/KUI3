(function(){
    var factory = function (exports) {
        var lang = {
            name : "en",
            description : "Open source online Markdown editor.",
            tocTitle    : "Tabla de Contenidos",
            toolbar : {
                undo             : "Deshacer(Ctrl+Z)",
                redo             : "Rehacer(Ctrl+Y)",
                bold             : "Negrita",
                del              : "Rallado",
                italic           : "Cursiva",
                quote            : "Bloque",
                ucwords          : "Words first letter convert to uppercase",
                uppercase        : "Selection text convert to uppercase",
                lowercase        : "Selection text convert to lowercase",
                h1               : "Encabezado 1",
                h2               : "Encabezado 2",
                h3               : "Encabezado 3",
                h4               : "Encabezado 4",
                h5               : "Encabezado 5",
                h6               : "Encabezado 6",
                "list-ul"        : "Lista desordenada",
                "list-ol"        : "Lista ordenada",
                hr               : "Linea",
                link             : "Enlace",
                "reference-link" : "Reference link",
                image            : "Imágen",
                code             : "Código",
                "preformatted-text" : "Código preformateado",
                "code-block"     : "Code block (Multi-languages)",
                table            : "Tabla",
                datetime         : "Datetime",
                emoji            : "Emoji",
                "html-entities"  : "HTML Entities",
                pagebreak        : "Page break",
                watch            : "Unwatch",
                unwatch          : "Watch",
                preview          : "Vista previa",
                fullscreen       : "Fullscreen (Press ESC exit)",
                clear            : "Clear",
                search           : "Search",
                help             : "Help",
                info             : "About " + exports.title
            },
            buttons : {
                enter  : "Aceptar",
                cancel : "Cancelar",
                close  : "Cerrar"
            },
            dialog : {
                link : {
                    title    : "Enlace",
                    url      : "Dirección",
                    urlTitle : "Título",
                    urlEmpty : "Error: Se requiere una dirección URL."
                },
                referenceLink : {
                    title    : "Referencia",
                    name     : "Nombre",
                    url      : "Address",
                    urlId    : "ID",
                    urlTitle : "Title",
                    nameEmpty: "Error: Reference name can't be empty.",
                    idEmpty  : "Error: Please fill in reference link id.",
                    urlEmpty : "Error: Please fill in reference link url address."
                },
                image : {
                    title    : "Imagen",
                    url      : "URL Imagen",
                    link     : "URL Enlace",
                    alt      : "Título",
                    uploadButton     : "Cargar",
                    imageURLEmpty    : "Error: Se requiere una dirección URL.",
                    uploadFileEmpty  : "Error: La imagen no puede estar vacía.",
                    formatNotAllowed : "Error: Sólo puedes subir imágenes. Formatos aceptados:"
                },
                preformattedText : {
                    title             : "Preformatted text / Codes", 
                    emptyAlert        : "Error: Please fill in the Preformatted text or content of the codes.",
                    placeholder       : "coding now...."
                },
                codeBlock : {
                    title             : "Code block",         
                    selectLabel       : "Languages: ",
                    selectDefaultText : "select a code language...",
                    otherLanguage     : "Other languages",
                    unselectedLanguageAlert : "Error: Please select the code language.",
                    codeEmptyAlert    : "Error: Please fill in the code content.",
                    placeholder       : "coding now...."
                },
                htmlEntities : {
                    title : "HTML Entities"
                },
                help : {
                    title : "Help"
                }
            }
        };
        
        exports.defaults.lang = lang;
    };
    
	// CommonJS/Node.js
	if (typeof require === "function" && typeof exports === "object" && typeof module === "object")
    { 
        module.exports = factory;
    }
	else if (typeof define === "function")  // AMD/CMD/Sea.js
    {
		if (define.amd) { // for Require.js

			define(["editormd"], function(editormd) {
                factory(editormd);
            });

		} else { // for Sea.js
			define(function(require) {
                var editormd = require("../editormd");
                factory(editormd);
            });
		}
	} 
	else
	{
        factory(window.editormd);
	}
    
})();