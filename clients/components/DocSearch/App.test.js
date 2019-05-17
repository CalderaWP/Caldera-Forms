import React from 'react';
import ReactDOM from 'react-dom';
import DocSearchApp from './DocSearchApp';

it('renders without crashing', () => {
  const div = document.createElement('div');
  ReactDOM.render(<DocSearchApp />, div);
});
