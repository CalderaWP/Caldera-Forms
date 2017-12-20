## Development Notes:
0) Read section below on app URL
1) CF pro requires PHP5.5 or later.
3) Plugin only loads if CF_PRO_VER is not defined. This is useful because:
* You can define that constant earlier to disable CF Pro.
* We probably will add a version check on CF_PRO_VER there so that the CF API client can exist as a separate library for development and latest version will be used.
4) Inside of Caldera Forms Pro the following PHP code rules apply:
* Use the PSR-4ish class autoloader, not Caldera_Forms_Autoloader, which doesn't support nested dirs.
    * Class `container` is in namespace `calderawp\calderaforms\pro` and stored in namespace root (/classes)  in file `container.php`.
    * Class `settings` is in namespace`calderawp\calderaforms\pro\api\local` and is stored in `/classes/api/local` in file settings.php
* Use proper array syntax `[]` not `array()`
* Continue using snake_case function and class names, because WordPress.
5) Translation text domain is "caldera-forms-pro" 
6) Make a point to represent errors by throwing an Exception using `calderawp\calderaforms\pro\exceptions\Exception`
    * This is important because Josh intends to build error logging into that.
    * You can use the `to_wp_error` method to convert it into a `WP_Error` object
7) Assets are generated using WebPack. See below.
8) Composer is used for PHP dependencies. Currently vendor is committed, which makes Josh sad.

### App URL
The URL for Caldera Forms pro is defined in the constant `CF_PRO_APP_URL` and then filtered using the `caldera_forms_pro_app_url` filter. Make sure to use one of those to match your local development environment.

Always use the function `caldera_forms_pro_app_url()` to get app URL. This will be helpful beacuse:
* You can use it to run against a local version of app.
* In the future we will ship this with the real app URL there.
* In the future we will have a staging server we will want to test against.
* We may choose to deliver separate installs of this app for an enterprise service tier with different URLs. 

### WebPack
Boilerplate is egosist/vuepack

READ These docs for more information on boilerplate - https://github.com/egoist/vuepack/tree/master/docs


* Install`npm install` then install `npm run dev` to build for development. 
* In development, use `npm run dev` for a one time build or `npm run watch` to use a watch -- live refresh is wonky, but script rebuild works.
* Before committing, run `npm run build` to build assets for production.

### Structure
- build
This is the webpack config.
- classes
This is the PHP classes.
- client
This is the VueJS app.
- dist
This is where Webpack outputs to and is loaded by browser, etc.
- static
This is probably not needed.
- node_modules
This is gitignored 
- vendor
This is not gitignored
cf-pro.php
This is the plugin file, it just checks if PHP and such is correct, then includes bootstrap-cf-pro.php
bootstrap-cf-pro.php
This makes the plugin go.

#### VueJS Application Structure
This is used for the admin page and for the "Pro" tab in the form editor. It will become more important over time. Some of this infrastructure should go into Caldera Forms core.
 
Basic concepts:
* index.js makes app go.
* All components share state managed using Vuex - /client/store/index.js
    * Communication between components via props should be minimal.
    * Components should never communicate up, without using state as intermediary.
    * My use of Vuex is not optimal right now and still verbose, but if you stick to using getters, setters and mutations (client/state/getters.js etc )-- all uses of mapState should proably get removed -- things will stay forward-compatible.
* Main views -- admin screen and tab are encapsulated in views.
    * Views are stored in /client/views
    * A view should have minimal logic
    * Components should be view-agnostic. Always assume component will be reused later, in a different view.
* Components should be grouped by use.
    * For example, account edit and account display account components are stored in /client/components/Account
    * Elements component group is a work in progress (don't use yet, except Status) set of form elements -- inputs, labels, etc -- to compose components out of.
* Both views use Status component to display a "main alert". You can reuse inside of components.
    * Documentation for updating main alert is in that component's readme.
    * Documentation for component inside of another component is in that component's readme.
* Router isn't used, yet.

    
 /client
  index.js
    Bootstraps app.
  - components
    All components that are used to compose views
    
 
