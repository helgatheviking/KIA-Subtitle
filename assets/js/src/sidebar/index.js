/**
 * External Dependencies
 */
import { TextControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { registerPlugin } from '@wordpress/plugins';
import { __ } from "@wordpress/i18n";

const SubtitlePanel = (props) => {

    // If we aren't editing a post, quit early.
    const editPost = useSelect( 'core/edit-post');

    if ( ! editPost ) {
        return null;
    }

    // For single post editing, get the current post type.
    const { getCurrentPostType } = useSelect('core/editor');

    const postType = getCurrentPostType();

    // Get the subtitle out of the meta.
    const [meta, setMeta] = useEntityProp('postType', postType, 'meta');
    const subtitle = meta?.kia_subtitle || '';

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

