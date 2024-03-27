/**
 * WordPress dependencies
 */
import { useBlockProps } from '@wordpress/block-editor';
import { useEntityProp } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';

export default function Save( {attributes} ) {

    const blockProps = useBlockProps.save();
  
    const TagName = 'h' + attributes?.level || 'h3';

    // Get the subtitle out of the meta.
    const { getCurrentPostType } = useSelect('core/editor');
    console.debug('getCurrentPostType', getCurrentPostType() );


    /*
    if ( subtitle && attributes.isLink ) { 
        subtitle = <a href={ attributes.link } target={ attributes.linkTarget } rel={ attributes.rel }> { subtitle } </a>;
    }
    */

    return <p>WTF</p>;


console.debug('attributes', attributes );
console.debug('blockProps', blockProps  );
console.debug('TagName', TagName );
console.debug('content', subtitle   );
    

    return <TagName { ...blockProps }> { subtitle } </TagName>;

};