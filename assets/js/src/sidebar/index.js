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
    const { getCurrentPostType } = useSelect('core/editor');

    const postType = getCurrentPostType();

    // If no post type is selected (for example, if in Site Editor), return null.
    if ( ! postType ) {
        return null;
    }   

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

