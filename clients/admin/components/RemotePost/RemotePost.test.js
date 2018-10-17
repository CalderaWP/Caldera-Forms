import React from 'react';
import renderer from "react-test-renderer";
import {mount} from 'enzyme';
import Enzyme from 'enzyme';
import Adapter from 'enzyme-adapter-react-16';
import {RemotePost} from "./RemotePost";
Enzyme.configure({adapter: new Adapter()});


const post = {
	id: 42,
	title : {
		rendered: 'Hello To Roy'
	},
	excerpt: {
		rendered: '<p>Hi R| Read more...</p>'
	},
	content: {
		rendered: '<p>Hi Roy</p>'
	},
	href: 'https://calderaforms.com/hi-roy'
};
describe( 'Remote post  Component', () => {

	it( 'Matches snapshot', () => {
		const component = renderer.create(
			<RemotePost
				post={post}

			/>
		);
		expect( component.toJSON() ).toMatchSnapshot();
	});

	it( 'Matches snapshot with class names', () => {
		const component = renderer.create(
			<RemotePost
				post={post}
				className={'remote-post-for-help'}
			/>
		);
		expect( component.toJSON() ).toMatchSnapshot();
	});

	it( 'Matches snapshot with read more text', () => {
		const component = renderer.create(
			<RemotePost
				post={post}
				className={'remote-post-for-help'}
				readMore={'Hi Roy - Read More'}
			/>
		);
		expect( component.toJSON() ).toMatchSnapshot();
	});

	it( 'Matches snapshot with button class name', () => {
		const component = renderer.create(
			<RemotePost
				post={post}
				className={'remote-post-for-help'}
				buttonClassName={'roys'}
			/>
		);
		expect( component.toJSON() ).toMatchSnapshot();
	});
});
