# Shared Components
This is a library of React components that can safely be shared by any React-driven UI, Gutenberg or not and are not specific to a client.


## Components
* `FormSelectorNoGutenberg` A react-bootstrap dependent selector for all saved forms.
* `CalderaHeader` A component to create the header markup of a page.
- Child props can be passed. They will be outputted inside of a `ul`. You must supply `li`.
* `PageBody` Wraps the content of an admin page in a consistent wrapper.
* `StatusIndicator` Conveys succesful green messages or red messages of failure.

### `StatusIndicator`
* Conveys successful green messages or red messages of failure.
* Relies on CSS in admin.css for style, which makes it look the same as existing similar components.
* Designed to work the same way as status indicator used in Pro UI, which is VueJS.

#### Examples
Success message:
```jsx harmony
<StatusIndicator 
    message={'Everything Is Sivan!'}
    show={true} 
    success={true}
/>
```

Error/ warning:

```jsx harmony
<StatusIndicator
    message={'Error!'}
    show={true}
    success={false}
/>

```