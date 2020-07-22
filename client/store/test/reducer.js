/**
 * Internal dependencies
 */
import reducer from '../reducer';
import TYPES from '../action-types';

const defaultState = {
	activeItem: null,
	menus: {
		primary: [],
		secondary: [],
	},
};

describe( 'navigation reducer', () => {
	it( 'should return a default state', () => {
		const state = reducer( undefined, {} );
		expect( state ).toEqual( defaultState );
		expect( state ).not.toBe( defaultState );
	} );

	it( "should set a menu's items", () => {
		const state = reducer( defaultState, {
			type: TYPES.SET_MENU_ITEMS,
			menuId: 'primary',
			menuItems: [
				{
					id: 'menu-item-1',
					title: 'Menu Item 1',
				},
				{
					id: 'menu-item-2',
					title: 'Menu Item 2',
				},
			],
		} );

		expect( state.menus.primary.length ).toBe( 2 );
		expect( state.menus.primary[ 0 ].id ).toBe( 'menu-item-1' );
		expect( state.menus.primary[ 1 ].id ).toBe( 'menu-item-2' );
		expect( state.menus.secondary.length ).toBe( 0 );
	} );

	it( 'should set the active menu item', () => {
		const state = reducer( defaultState, {
			type: TYPES.SET_ACTIVE_ITEM,
			activeItem: 'test-active-item',
		} );

		expect( state.activeItem ).toBe( 'test-active-item' );
	} );
} );
