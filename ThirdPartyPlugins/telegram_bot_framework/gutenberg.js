( function( plugins, editPost, element, components, data, compose ) {
 
	const el = element.createElement;
 
	const { Fragment } = element;
	const { registerPlugin } = plugins;
	const { PluginSidebar, PluginSidebarMoreMenuItem } = editPost;
	const { PanelBody, TextareaControl, CheckboxControl, SelectControl } = components;
	const { withSelect, withDispatch } = data;
 
    const telegramIcon = wp.element.createElement(
        'img', 
        { 
            src: "https://karlaperezyt.com/wp-content/uploads/2021/04/icons8-aplicacion-telegrama-24.png",
            width: "24px",
            height: "24px",
        },
        null
    );
 
    var MetaCheckboxControl = compose.compose(
        withDispatch( function( dispatch, props ) {
            return {
                setMetaValue: function( metaValue ) {
                    dispatch( 'core/editor' ).editPost(
                        { meta: { [ props.metaKey ]: metaValue } }
                    );
                }
            }
        } ),
        withSelect( function( select, props ) {
            return {
                metaValue: select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ props.metaKey ],
            }
        } ) )( function( props ) {
            return el( CheckboxControl, {
                label: props.label,
                checked: props.metaValue,
                onChange: function( content ) {
                    props.setMetaValue( content );
                },
            });
        }
    );

    var MetaTextareaControl = compose.compose(
        withDispatch( function( dispatch, props ) {
            return {
                setMetaValue: function( metaValue ) {
                    dispatch( 'core/editor' ).editPost(
                        { meta: { [ props.metaKey ]: metaValue } }
                    );
                }
            }
        } ),
        withSelect( function( select, props ) {
            return {
                metaValue: ( select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ props.metaKey ] ? select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ props.metaKey ] : TelegramBotParams.template ),
            }
        } ) )( function( props ) {
            return el( TextareaControl, {
                label: props.label,
                value: props.metaValue,
                onChange: function( content ) {
                    props.setMetaValue( content );
                },
            });
        }
    );

    var MetaSelectControl = compose.compose(
        withDispatch( function( dispatch, props ) {
            return {
                setMetaValue: function( metaValue ) {
                    dispatch( 'core/editor' ).editPost(
                        { meta: { [ props.metaKey ]: metaValue } }
                    );
                }
            }
        } ),
        withSelect( function( select, props ) {
            return {
                metaValue: ( select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ props.metaKey ] ? select( 'core/editor' ).getEditedPostAttribute( 'meta' )[ props.metaKey ] : TelegramBotParams.target  ),
            }
        } ) )( function( props ) {
            return el( SelectControl, {
                label: props.label,
                value: props.metaValue,
                options: [
                    { value: 0, label: 'Users, Groups, Channel' },
                    { value: 1, label: 'Users' },
                    { value: 2, label: 'Groups' },
                    { value: 3, label: 'Users, Groups' },
                    { value: 4, label: 'Channel' }
                ],
                onChange: function( content ) {
                    console.log(content);
                    props.setMetaValue( content );
                },
            });
        }
    );

	registerPlugin( 'telegram-bot-plugin', {
        render: function() {
            return el( Fragment, {},
                el( PluginSidebarMoreMenuItem,
                    {
                        target: 'telegram-bot',
                        icon: telegramIcon,
                    },
                    'Telegram'
                ),
                el( PluginSidebar,
                    {
                        name: 'telegram-bot',
                        icon: telegramIcon,
                        title: 'Telegram',
                    },
                    el(
                        PanelBody, {},
                        el(
                            MetaCheckboxControl,
                            {
                                id: 'telegram_m_send',
                                metaKey: 'telegram_tosend',
                                label: 'Send to Telegram'
                            }
                        )
                    ),
                    el(
                        PanelBody, {},
                        el(
                            MetaTextareaControl,
                            {
                                id: 'telegram_m_send_content',
                                metaKey: 'telegram_tosend_message',
                                label: 'Message',
                            }
                        )
                    ),
                    el(
                        PanelBody, {},
                        el(
                            MetaSelectControl,
                            {
                                id: 'telegram_m_send_target',
                                metaKey: 'telegram_tosend_target',
                                label : 'Target'
                            }
                            
                        ),
                    )
                )
            );
        }
    } );
 
} )(
	window.wp.plugins,
	window.wp.editPost,
	window.wp.element,
	window.wp.components,
	window.wp.data,
	window.wp.compose
);