/**
 * External Dependencies
 */
import { TextControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import { __ } from "@wordpress/i18n";
import { store as coreStore } from '@wordpress/core-data';

const editorStore = 'core/editor';

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

    return (
        <PluginDocumentSettingPanel
            name="kia-subtitle-panel"
            title={__("Subtitle", "kia-subtitle")}
            className="kia-subtitle-panel"
        >
            <TextControl
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

