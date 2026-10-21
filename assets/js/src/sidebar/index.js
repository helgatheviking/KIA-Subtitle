/**
 * External Dependencies
 */
import { TextControl } from '@wordpress/components';
import { PlainText, store as blockEditorStore } from '@wordpress/block-editor';
import { useDispatch, useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import { createPortal, useEffect, useState } from '@wordpress/element';
import { __ } from "@wordpress/i18n";
import { store as coreStore } from '@wordpress/core-data';

const editorStore = 'core/editor';

const TITLE_WRAPPER_SELECTOR = '.editor-visual-editor__post-title-wrapper, .edit-post-visual-editor__post-title-wrapper';
const SUBTITLE_WRAPPER_CLASS = 'kia-subtitle-editor-wrapper';

/**
 * Find the post title wrapper, which may live inside the editor canvas iframe.
 *
 * @return {Element|null}
 */
const findTitleWrapper = () => {
    const iframe = document.querySelector( 'iframe[name="editor-canvas"]' );
    const canvas = iframe?.contentDocument || document;
    return canvas.querySelector( TITLE_WRAPPER_SELECTOR );
};

/**
 * Read the post title's typography so the subtitle can match the theme's title styles.
 *
 * @param {Element} titleWrapper
 * @return {string} JSON encoded styles, so they can be cheaply compared.
 */
const getTitleStyles = ( titleWrapper ) => {
    const title = titleWrapper.querySelector( '.editor-post-title, h1' );

    if ( ! title ) {
        return '{}';
    }

    const computed = titleWrapper.ownerDocument.defaultView.getComputedStyle( title );

    return JSON.stringify( {
        fontFamily: computed.fontFamily,
        fontWeight: computed.fontWeight,
        fontStyle: computed.fontStyle,
        letterSpacing: computed.letterSpacing,
        textTransform: computed.textTransform,
        color: computed.color,
        // Subtitle is smaller than the title.
        fontSize: `calc(${ computed.fontSize } * 0.6)`,
    } );
};

/**
 * Gutenberg moves Tab out of the editor canvas entirely, so the subtitle would be skipped.
 * Instead, let Tab move from the title to the subtitle, and Shift+Tab back again.
 *
 * @param {KeyboardEvent} event
 */
const onTitleWrapperKeyDown = ( event ) => {
    if ( event.key !== 'Tab' || event.altKey || event.ctrlKey || event.metaKey ) {
        return;
    }

    const titleWrapper = event.currentTarget;
    const title = titleWrapper.querySelector( '.editor-post-title, h1' );
    const field = titleWrapper.querySelector( '.kia-subtitle-editor-field' );

    if ( ! title || ! field ) {
        return;
    }

    if ( ! event.shiftKey && title.contains( event.target ) ) {
        field.focus();
    } else if ( event.shiftKey && event.target === field ) {
        title.focus();
    } else {
        return;
    }

    event.preventDefault();
    event.stopPropagation();
};

/**
 * Keep track of a container element placed directly after the post title.
 *
 * The editor canvas can be (re)mounted at any time (switching to the code editor, toggling the template preview, etc.)
 * so poll for the title wrapper and (re)create the container when needed.
 *
 * @return {{container: Element|null, titleStyles: Object}}
 */
const useSubtitleContainer = () => {
    const [ container, setContainer ] = useState( null );
    const [ titleStyles, setTitleStyles ] = useState( '{}' );

    useEffect( () => {
        // Title wrappers we have attached the keyboard handler to.
        const listening = new Set();

        const sync = () => {
            const titleWrapper = findTitleWrapper();

            // Forget any title wrappers removed when the canvas re-rendered.
            listening.forEach( ( wrapper ) => ! wrapper.isConnected && listening.delete( wrapper ) );

            if ( ! titleWrapper ) {
                setContainer( null );
                return;
            }

            let el = titleWrapper.querySelector( '.' + SUBTITLE_WRAPPER_CLASS );

            if ( ! el ) {
                el = titleWrapper.ownerDocument.createElement( 'div' );
                el.className = SUBTITLE_WRAPPER_CLASS;
                titleWrapper.appendChild( el );
            }

            if ( ! listening.has( titleWrapper ) ) {
                // Capture phase, so this runs before the editor's own Tab handling.
                titleWrapper.addEventListener( 'keydown', onTitleWrapperKeyDown, true );
                listening.add( titleWrapper );
            }

            setContainer( el );
            setTitleStyles( getTitleStyles( titleWrapper ) );
        };

        sync();
        const interval = setInterval( sync, 500 );

        return () => {
            clearInterval( interval );
            listening.forEach( ( titleWrapper ) => titleWrapper.removeEventListener( 'keydown', onTitleWrapperKeyDown, true ) );
        };
    }, [] );

    return { container, titleStyles: JSON.parse( titleStyles ) };
};

/**
 * Styles for the subtitle field. Rendered inside the portal so they reach the editor canvas iframe.
 */
const SUBTITLE_EDITOR_CSS = `
.${ SUBTITLE_WRAPPER_CLASS } {
    margin-top: 0.5rem;
    margin-bottom: 3rem;
}
.${ SUBTITLE_WRAPPER_CLASS } .kia-subtitle-editor-field.block-editor-plain-text {
    display: block;
    width: 100%;
    margin: 0;
    padding: 0.25em 0;
    line-height: 1.3;
    resize: none;
    background: transparent;
    border: 0;
    border-bottom: 1px solid transparent;
    border-radius: 0;
    box-shadow: none;
    transition: border-color 0.1s ease-in-out;
}
.${ SUBTITLE_WRAPPER_CLASS } .kia-subtitle-editor-field.block-editor-plain-text:focus {
    /* Transparent rather than none, so the outline still shows in forced colors (high contrast) mode. */
    outline: 2px solid transparent;
    border-bottom: 1px solid var(--wp-admin-theme-color, #3858e9);
    box-shadow: 0 1px 0 0 var(--wp-admin-theme-color, #3858e9);
}
.${ SUBTITLE_WRAPPER_CLASS } .kia-subtitle-editor-field::placeholder {
    color: inherit;
    /* Keeps roughly 4.5:1 contrast for dark title colors on light backgrounds. */
    opacity: 0.62;
}
@media (prefers-reduced-motion: reduce) {
    .${ SUBTITLE_WRAPPER_CLASS } .kia-subtitle-editor-field.block-editor-plain-text {
        transition: none;
    }
}
`;

const SubtitlePanel = (props) => {

    // Is this post type viewable... In the editor this seems to mean we can edit it (false for `wp_template` which is a hint we're in the site editor).
    const { isVisible, postTypeSlug } = useSelect( ( select ) => {

        const postTypeSlug = select( editorStore ).getCurrentPostType();
		const postType = select( coreStore ).getPostType( postTypeSlug );

		return {
			isVisible: postType?.viewable || false,
			postTypeSlug: postTypeSlug || '', // We pass a default value so we can call useEntityProp without it choking on an undefined value.
		};
	}, [] );

    // Get/set the post meta using entity prop.
    const [meta, setMeta] = useEntityProp('postType', postTypeSlug, 'meta');

    const { container, titleStyles } = useSubtitleContainer();

    const { insertDefaultBlock } = useDispatch( blockEditorStore );

    // If we are in the site editor quit early.
    if ( ! isVisible || ! postTypeSlug ) {
        return null;
    }

    // Get the subtitle out of the meta.
    const subtitle = meta?.kia_subtitle || '';

    // Wrapper to update the subtitle in the meta.
    const updateSubtitle = (newValue) => {
        setMeta({ ...meta, kia_subtitle: newValue });
    };

    // Display the subtitle directly beneath the post title when possible.
    if ( container ) {
        return createPortal(
            <>
                <style>{ SUBTITLE_EDITOR_CSS }</style>
                <PlainText
                    className="wp-block-kia-post-subtitle kia-subtitle-editor-field"
                    aria-label={ __( 'Add subtitle', 'kia-subtitle' ) }
                    placeholder={ __( 'Add subtitle', 'kia-subtitle' ) }
                    value={ subtitle }
                    onChange={ updateSubtitle }
                    onKeyDown={ ( event ) => {
                        // Subtitles are a single line. Like the post title, Enter moves on to the content.
                        if ( event.key === 'Enter' && ! event.nativeEvent.isComposing ) {
                            event.preventDefault();
                            // The editor won't move focus into the new block while a text field outside it has focus.
                            event.target.blur();
                            insertDefaultBlock( undefined, undefined, 0 );
                        }
                    } }
                    style={ titleStyles }
                />
            </>,
            container
        );
    }

    // Otherwise fall back to the document settings sidebar.
    return (
        <PluginDocumentSettingPanel
            name="kia-subtitle-panel"
            title={__("Subtitle", "kia-subtitle")}
            className="kia-subtitle-panel"
        >
            <TextControl
                label={ __( 'Subtitle', 'kia-subtitle' ) }
                hideLabelFromVision
                value={subtitle}
                onChange={updateSubtitle}
            />
        </PluginDocumentSettingPanel>
    );
}

registerPlugin( 'kia-subtitle', {
	render: SubtitlePanel,
	icon: 'edit',
} );
