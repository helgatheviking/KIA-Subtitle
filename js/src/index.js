import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel } from '@wordpress/edit-post';
import { __ } from "@wordpress/i18n";
import { PanelBody, TextControl } from "@wordpress/components";
import { withSelect, withDispatch } from "@wordpress/data";

let SubtitleMetaField = (props) => {
    return (
        <TextControl 
            value={props.text_metafield}
            label={__("Subtitle", "kia-subtitle")}
            onChange={(value) => props.onMetaFieldChange(value)}
        />
    )
}

SubtitleMetaField = withSelect(
    (select) => {
        return {
            text_metafield: select('core/editor').getEditedPostAttribute('meta')['kia_subtitle']
        }
    }
)(SubtitleMetaField);

SubtitleMetaField = withDispatch(
    (dispatch) => {
        return {
            onMetaFieldChange: (value) => {
                dispatch('core/editor').editPost(
                    { meta: { kia_subtitle: value } }
                );
            }
        }
    }
)(SubtitleMetaField);

const PluginDocumentSettingPanelDemo = (props) => (
	<PluginDocumentSettingPanel
		name="kia-subtitle-panel"
		title={__("Subtitle", "kia-subtitle")}
		className="kia-subtitle-panel"
	>
		<SubtitleMetaField />
	</PluginDocumentSettingPanel>
);

registerPlugin( 'plugin-document-setting-panel-demo', {
	render: PluginDocumentSettingPanelDemo,
	icon: 'edit',
} );