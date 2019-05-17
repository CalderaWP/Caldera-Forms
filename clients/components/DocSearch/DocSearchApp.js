import React from 'react';
import  { Component } from 'react';
import DocSearch from './DocSearch';
import './App.scss';

class DocSearchApp extends Component {

  render() {
    return (
      <div id="cf-doc-search-app">
          <DocSearch
              apiRoot={this.props.apiRoot}
          />
      </div>
    );
  }
}

export default DocSearchApp;
