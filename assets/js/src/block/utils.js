/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';

/**
 * Returns whether the current user can edit the given entity.
 *
 * @param {string} kind     Entity kind.
 * @param {string} name     Entity name.
 * @param {string} recordId Record's id.
 */
export function useCanEditEntity( kind, name, recordId ) {
	return useSelect(
		( select ) =>
			select('core').canUserEditEntityRecord( kind, name, recordId ),
		[ kind, name, recordId ]
	);
}