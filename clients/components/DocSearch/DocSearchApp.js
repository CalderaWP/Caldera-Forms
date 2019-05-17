import React from 'react';
import  { Component } from 'react';
import DocSearch from './DocSearch';
import './App.css';

class DocSearchApp extends Component {

  render() {
    return (
      <div className="App container">
          <DocSearch
              apiRoot={this.props.apiRoot}
          />
      </div>
    );
  }
}

export default DocSearchApp;
