/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import {
	AlignmentControl,
	BlockControls,
	InspectorControls,
	useBlockProps,
	PlainText,
	HeadingLevelDropdown,
	useBlockEditingMode,
} from '@wordpress/block-editor';
import { ToggleControl, TextControl, PanelBody } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { createBlock, getDefaultBlockName } from '@wordpress/blocks';
import { useEntityProp } from '@wordpress/core-data';

/**
 * 
 * Internal dependencies
 */
import { useCanEditEntity } from './utils';
import './filters.js';

export default function Edit( {
	attributes: { level, textAlign, isLink, rel, linkTarget },
	setAttributes,
	context: { postType, postId, queryId },
	insertBlocksAfter,
} ) {
	const TagName = 'h' + level;
	const isDescendentOfQueryLoop = Number.isFinite( queryId );
	/**
	 * Hack: useCanEditEntity may trigger an OPTIONS request to the REST API via the canUser resolver.
	 * However, when the Post Title is a descendant of a Query Loop block, the title cannot be edited.
	 * In order to avoid these unnecessary requests, we call the hook without
	 * the proper data, resulting in returning early without making them.
	 */
	const userCanEdit = useCanEditEntity(
		'postType',
		! isDescendentOfQueryLoop && postType,
		postId
	);

    // Get the subtitle out of the meta.
    const [meta, setMeta] = useEntityProp('postType', postType, 'meta');

    const subtitle = meta?.kia_subtitle || '';

    const updateSubtitle = (newValue) => {
        setMeta({ ...meta, kia_subtitle: newValue });
    };
    
	const [ link ] = useEntityProp( 'postType', postType, 'link', postId );
	const onSplitAtEnd = () => {
		insertBlocksAfter( createBlock( getDefaultBlockName() ) );
	};
	const blockProps = useBlockProps( {
		className: classnames( {
			[ `has-text-align-${ textAlign }` ]: textAlign,
			'no-subtitle': isDescendentOfQueryLoop && ! subtitle.length,
		} ),
	} );
	const blockEditingMode = useBlockEditingMode();

	let titleElement = <TagName { ...blockProps }>{ __( 'Subtitle', 'kia-subtitle' ) }</TagName>;

	if ( postType && postId ) {
		titleElement = userCanEdit ? (
			<PlainText
				tagName={ TagName }
				placeholder={ __( 'No Subtitle', 'kia-subtitle' ) }
				value={ subtitle }
				onChange={ updateSubtitle }
				__experimentalVersion={ 2 }
				__unstableOnSplitAtEnd={ onSplitAtEnd }
				{ ...blockProps }
			/>
		) : (
			<TagName
				{ ...blockProps }
				dangerouslySetInnerHTML={ { __html: subtitle || __( 'No Subtitle', 'kia-subtitle' ) } }
			/>
		);
	}

	if ( isLink && postType && postId ) {
		titleElement = userCanEdit ? (
			<TagName { ...blockProps }>
				<PlainText
					tagName="a"
					href={ link }
					target={ linkTarget }
					rel={ rel }
					placeholder={ ! subtitle.length ? __( 'No Subtitle', 'kia-subtitle' ) : null }
					value={ subtitle }
					onChange={ updateSubtitle }
					__experimentalVersion={ 2 }
					__unstableOnSplitAtEnd={ onSplitAtEnd }
				/>
			</TagName>
		) : (
			<TagName { ...blockProps }>
				<a
					href={ link }
					target={ linkTarget }
					rel={ rel }
					onClick={ ( event ) => event.preventDefault() }
					dangerouslySetInnerHTML={ {
						__html: subtitle || __( 'No Subtitle', 'kia-subtitle' )
					} }
				/>
			</TagName>
		);
	}
	
	return (
		<>
			{ blockEditingMode === 'default' && (
				<>
					<BlockControls group="block">
						<HeadingLevelDropdown
							value={ level }
							onChange={ ( newLevel ) =>
								setAttributes( { level: newLevel } )
							}
						/>
						<AlignmentControl
							value={ textAlign }
							onChange={ ( nextAlign ) => {
								setAttributes( { textAlign: nextAlign } );
							} }
						/>
					</BlockControls>
					<InspectorControls>
						<PanelBody title={ __( 'Settings' ) }>
							<ToggleControl
								__nextHasNoMarginBottom
								label={ __( 'Make subtitle a link', 'kia-subtitle' ) }
								onChange={ () =>
									setAttributes( { isLink: ! isLink } )
								}
								checked={ isLink }
							/>
							{ isLink && (
								<>
									<ToggleControl
										__nextHasNoMarginBottom
										label={ __( 'Open in new tab' ) }
										onChange={ ( value ) =>
											setAttributes( {
												linkTarget: value
													? '_blank'
													: '_self',
											} )
										}
										checked={ linkTarget === '_blank' }
									/>
									<TextControl
										__nextHasNoMarginBottom
										label={ __( 'Link rel' ) }
										value={ rel }
										onChange={ ( newRel ) =>
											setAttributes( { rel: newRel } )
										}
									/>
								</>
							) }
						</PanelBody>
					</InspectorControls>
				</>
			) }
			{ titleElement }
		</>
	);
}
