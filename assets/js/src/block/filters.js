/**
 * WordPress dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';

const addSubtitleBlockClass = createHigherOrderComponent( ( BlockListBlock ) => {
    return ( props ) => {

        const { name, attributes } = props;

        if ( name != 'kia/post-subtitle' ) {
            return <BlockListBlock { ...props }/>;
        }

        // Add 'subtitle' to wrapper classes.
        return <BlockListBlock { ...props } className='subtitle' />;
    };
}, 'addSubtitleBlockClass' );

addFilter(
	'editor.BlockListBlock',
	'kia-subtitle/subtitle-block-class',
	addSubtitleBlockClass
);