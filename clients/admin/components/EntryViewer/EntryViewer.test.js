import {EntryViewer} from "./EntryViewer";
import renderer from "react-test-renderer";
import React from 'react';
import {shallow} from 'enzyme';
import Enzyme from 'enzyme';
import Adapter from 'enzyme-adapter-react-16';

Enzyme.configure({adapter: new Adapter()});

const columns = [
	{key: 'id', name: 'ID'},
	{key: 'title', name: 'Title'},
	{key: 'count', name: 'Count'}
];
const valueOne = {
	id: 'one',
	value: 1
};

const rowOne = [
	{
		id: 1,
		title: 'One',
		count: 10,
		key: 1

	}
];
const rowTwo = [
	{
		id: 2,
		title: 'Two',
		count: 20,
		key: 2
	}
];

const rows = [rowOne, rowTwo];
const genericHandler = () => {};
describe('Entry FormAdminMainView component', () => {
	it.skip('matches snapshot', () => {

		expect(
			renderer.create(
				<EntryViewer
					columns={columns}
					rows={rows}
					onPageNav={genericHandler}
				/>
			).toJSON()
		).toMatchSnapshot();

	});

	it('Column ids are set ', () => {
		const component = shallow(
			<EntryViewer
				columns={[
					{key: 'a'},
					{key: 'b'}
				]}
				onPageNav={genericHandler}
				rows={rows}
			/>
		);
		component.instance().setColumnIds();
		expect(
			component.state('columnIds')
		).toEqual(['a', 'b'])

	});

	it('Knows if a row column is valid', () => {
		const component = shallow(
			<EntryViewer
				columns={[
					{key: 'a', name: 'ID'},
					{key: 'b', name: 'Title'},
				]}
				onPageNav={genericHandler}
				rows={rows}
			/>
		);

		component.instance().setColumnIds();

		expect(
			component.instance().rowHasColumn('b')
		).toBe(true);


		expect(
			component.instance().rowHasColumn('fld3000')
		).toBe(false);
	});

	it('Knows if a row column is invalid', () => {
		const component = shallow(
			<EntryViewer
				columns={[
					{key: 'a', name: 'ID'},
					{key: 'b', name: 'Title'},
				]}
				rows={rows}
				onPageNav={genericHandler}
			/>
		);

		component.instance().setColumnIds();

		expect(
			component.instance().rowHasColumn('fld3000')
		).toBe(false);
	});



	describe( 'Pagination', () => {
		describe( 'page counts', () => {
			it('Defaults state.page to 1', () => {
				const component = shallow(
					<EntryViewer
						columns={columns}
						rows={rows}
						onPageNav={genericHandler}
					/>
				);
				expect(
					component.state('page')
				).toEqual(1);
			});

			it('Sets state.page when passes as prop', () => {
				const component = shallow(
					<EntryViewer
						columns={columns}
						rows={rows}
						page={5}
						onPageNav={genericHandler}
					/>
				);
				expect(
					component.state('page')
				).toEqual(5);
			});

			it('increases state.page at onNext', () => {
				const component = shallow(
					<EntryViewer
						columns={columns}
						rows={rows}
						onPageNav={genericHandler}
					/>
				);
				component.instance().onNext();
				expect(
					component.state('page')
				).toEqual(2);
			});

			it('increases state.page at onNext', () => {
				const component = shallow(
					<EntryViewer
						columns={columns}
						rows={rows}
						onPageNav={genericHandler}
					/>
				);
				component.instance().onNext();
				expect(
					component.state('page')
				).toEqual(2);
			});


			it('decreases state.page at onNext', () => {
				const component = shallow(
					<EntryViewer
						columns={columns}
						rows={rows}
						page={2}
						onPageNav={genericHandler}
					/>
				);
				component.instance().onPrevious();
				expect(
					component.state('page')
				).toEqual(1);
			});
		});

		describe( 'Clicking navigation buttons', () => {
			it.skip( 'Clicks back', () => {
				let pageUpdate = {}
				const component = shallow(
					<EntryViewer
						columns={columns}
						rows={rows}
						page={2}
						onPageNav={(next,previous) => {
							pageUpdate = {
								next,
								previous
							}
						}}

					/>
				);
				component.find( '.' + EntryViewer.classNames.prevNav ).simulate( 'click')
				expect(
					component.state('page')
				).toEqual(1);
				expect(
					pageUpdate
				).toEqual({
					next: 1,
					previous: 2
				});
			});
			it.skip( 'Clicks forward', () => {
				let pageUpdate = {}
				const component = shallow(
					<EntryViewer
						columns={columns}
						rows={rows}
						page={2}
						onPageNav={(next,previous) => {
							pageUpdate = {
								next,
								previous
							}
						}}
					/>
				);
				component.find( '.' + EntryViewer.classNames.nextNav ).simulate( 'click')
				expect(
					component.state('page')
				).toEqual(1);
				expect(
					pageUpdate
				).toEqual({
					next: 3,
					previous: 2
				});
			});

		});


		describe( 'Disabling & enabling page nav', () => {
			it('Disables previous on page 1', () => {
				const component = shallow(
					<EntryViewer
						columns={columns}
						rows={rows}
						page={1}
						onPageNav={genericHandler}
					/>
				);
				expect(
					component.instance().showPreviousNav()
				).toEqual(true);
			});
			it('Does not disable previous on page 2', () => {
				const component = shallow(
					<EntryViewer
						columns={columns}
						rows={rows}
						page={2}
					/>
				);
				expect(
					component.instance().showPreviousNav()
				).toEqual(false);
			});
			it('Disables next on last page', () => {
				const component = shallow(
					<EntryViewer
						columns={columns}
						rows={rows}
						page={2}
						totalPages={2}
						onPageNav={genericHandler}
					/>
				);
				expect(
					component.instance().showNextNav()
				).toEqual(true);
			});
			it('Does not disable next on page 2nd of 3 pages', () => {
				const component = shallow(
					<EntryViewer
						columns={columns}
						rows={rows}
						page={2}
						totalPages={3}
						onPageNav={genericHandler}
					/>
				);
				expect(
					component.instance().showPreviousNav()
				).toEqual(false);
			});

		});


	});


	it('gets rows', () => {
		const component = shallow(
			<EntryViewer
				columns={columns}
				rows={rows}
				page={2}
				onPageNav={genericHandler}
			/>
		);
		expect(
			component.instance().rowGetter(0)
		).toEqual(rowOne);
		expect(
			component.instance().rowGetter(1)
		).toEqual(rowTwo);
	});


});