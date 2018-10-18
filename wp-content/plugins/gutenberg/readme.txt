=== Gutenberg ===
Contributors: matveb, joen, karmatosed
Requires at least: 4.9.8
Tested up to: 4.9
Stable tag: 3.9.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A new editing experience for WordPress is in the works, with the goal of making it easier than ever to make your words, pictures, and layout look just right. This is the beta plugin for the project.

== Description ==

Gutenberg is more than an editor. While the editor is the focus right now, the project will ultimately impact the entire publishing experience including customization (the next focus area).

<a href="https://wordpress.org/gutenberg">Discover more about the project</a>.

= Editing focus =

> The editor will create a new page- and post-building experience that makes writing rich posts effortless, and has “blocks” to make it easy what today might take shortcodes, custom HTML, or “mystery meat” embed discovery. — Matt Mullenweg

One thing that sets WordPress apart from other systems is that it allows you to create as rich a post layout as you can imagine -- but only if you know HTML and CSS and build your own custom theme. By thinking of the editor as a tool to let you write rich posts and create beautiful layouts, we can transform WordPress into something users _love_ WordPress, as opposed something they pick it because it's what everyone else uses.

Gutenberg looks at the editor as more than a content field, revisiting a layout that has been largely unchanged for almost a decade.This allows us to holistically design a modern editing experience and build a foundation for things to come.

Here's why we're looking at the whole editing screen, as opposed to just the content field:

1. The block unifies multiple interfaces. If we add that on top of the existing interface, it would _add_ complexity, as opposed to remove it.
2. By revisiting the interface, we can modernize the writing, editing, and publishing experience, with usability and simplicity in mind, benefitting both new and casual users.
3. When singular block interface takes center stage, it demonstrates a clear path forward for developers to create premium blocks, superior to both shortcodes and widgets.
4. Considering the whole interface lays a solid foundation for the next focus, full site customization.
5. Looking at the full editor screen also gives us the opportunity to drastically modernize the foundation, and take steps towards a more fluid and JavaScript powered future that fully leverages the WordPress REST API.

= Blocks =

Blocks are the unifying evolution of what is now covered, in different ways, by shortcodes, embeds, widgets, post formats, custom post types, theme options, meta-boxes, and other formatting elements. They embrace the breadth of functionality WordPress is capable of, with the clarity of a consistent user experience.

Imagine a custom “employee” block that a client can drag to an About page to automatically display a picture, name, and bio. A whole universe of plugins that all extend WordPress in the same way. Simplified menus and widgets. Users who can instantly understand and use WordPress  -- and 90% of plugins. This will allow you to easily compose beautiful posts like <a href="http://moc.co/sandbox/example-post/">this example</a>.

Check out the <a href="https://wordpress.org/gutenberg/handbook/reference/faq/">FAQ</a> for answers to the most common questions about the project.

= Compatibility =

Posts are backwards compatible, and shortcodes will still work. We are continuously exploring how highly-tailored metaboxes can be accommodated, and are looking at solutions ranging from a plugin to disable Gutenberg to automatically detecting whether to load Gutenberg or not. While we want to make sure the new editing experience from writing to publishing is user-friendly, we’re committed to finding  a good solution for highly-tailored existing sites.

= The stages of Gutenberg =

Gutenberg has three planned stages. The first, aimed for inclusion in WordPress 5.0, focuses on the post editing experience and the implementation of blocks. This initial phase focuses on a content-first approach. The use of blocks, as detailed above, allows you to focus on how your content will look without the distraction of other configuration options. This ultimately will help all users present their content in a way that is engaging, direct, and visual.

These foundational elements will pave the way for stages two and three, planned for the next year, to go beyond the post into page templates and ultimately, full site customization.

Gutenberg is a big change, and there will be ways to ensure that existing functionality (like shortcodes and meta-boxes) continue to work while allowing developers the time and paths to transition effectively. Ultimately, it will open new opportunities for plugin and theme developers to better serve users through a more engaging and visual experience that takes advantage of a toolset supported by core.

= Contributors =

Gutenberg is built by many contributors and volunteers. Please see the full list in <a href="https://github.com/WordPress/gutenberg/blob/master/CONTRIBUTORS.md">CONTRIBUTORS.md</a>.

== Frequently Asked Questions ==

= How can I send feedback or get help with a bug? =

We'd love to hear your bug reports, feature suggestions and any other feedback! Please head over to <a href="https://github.com/WordPress/gutenberg/issues">the GitHub issues page</a> to search for existing issues or open a new one. While we'll try to triage issues reported here on the plugin forum, you'll get a faster response (and reduce duplication of effort) by keeping everything centralized in the GitHub repository.

= How can I contribute? =

We’re calling this editor project "Gutenberg" because it's a big undertaking. We are working on it every day in GitHub, and we'd love your help building it.You’re also welcome to give feedback, the easiest is to join us in <a href="https://make.wordpress.org/chat/">our Slack channel</a>, `#core-editor`.

See also <a href="https://github.com/WordPress/gutenberg/blob/master/CONTRIBUTING.md">CONTRIBUTING.md</a>.

= Where can I read more about Gutenberg? =

- <a href="http://matiasventura.com/post/gutenberg-or-the-ship-of-theseus/">Gutenberg, or the Ship of Theseus</a>, with examples of what Gutenberg might do in the future
- <a href="https://make.wordpress.org/core/2017/01/17/editor-technical-overview/">Editor Technical Overview</a>
- <a href="https://wordpress.org/gutenberg/handbook/reference/design-principles/">Design Principles and block design best practices</a>
- <a href="https://github.com/Automattic/wp-post-grammar">WP Post Grammar Parser</a>
- <a href="https://make.wordpress.org/core/tag/gutenberg/">Development updates on make.wordpress.org</a>
- <a href="https://wordpress.org/gutenberg/handbook/">Documentation: Creating Blocks, Reference, and Guidelines</a>
- <a href="https://wordpress.org/gutenberg/handbook/reference/faq/">Additional frequently asked questions</a>


== Changelog ==

= Latest =

### New Features
* Add ability to change overlay color in Cover Image.
* Introduce new Font Size Picker with clear labels and size comparison.
* Introduce new RichText data structure to allow better manipulation of inline content.
* Add Pullquote style variation and color palette support.
* Add support for post locking when multiple authors interact with the editor.
* Add an alternative block appender when the container doesn’t support the default block (paragraph).
* Improve the UI and interactions around floats.
* Add option to skip PublishSidebar on publishing.
* Add support for shortcode embeds that enqueue scripts.
* Add a button to exit the Code Editor.
* Introduce a reusable ResizableBox component.
* Style nested `<ul>`s with circles instead of normal bullets.
* Show hierarchical terms sorted by name and allow them to be filterable through search. Hide the filter box if there are fewer than 8 terms.
* Improve messaging around invalid block detection.
* Use text color for links when a paragraph has a color applied.
* Allow extended usage of the controls API in resolvers within data layer.
* Ensure that a default block is inserted (and selected) when all other blocks are removed.
* Enhance the block parser to support multiple type, in accordance with JSON schema.
* Add a larger target area for resize drag handles.
* Add media button to classic block.
* Add control to toggle responsive mechanism on embed blocks.
* Update sidebar design to have a lighter feeling.
* Update resolvers in data layer to rely on controls instead of async generators.
* Set template validity on block reset.
* Remove dirty detection from Meta Boxes to reduce false positives of “unsaved changes”.
* Show “Publish: Immediately” for new drafts by inferring floating date.
* Add a slight transition to Full Screen mode.
* Improve spacing setup in Gallery Block.
* Remove additional side padding from blocks.
* Improve the reusable blocks “Export as JSON” link.
* Enforce a default block icon size to allow flex alignment and fix unaligned labels.
* Consider single unmodified default block as empty content.
* Only display URL input field when “Link To” is set for Image Block.
* Make backspace behavior consistent among Quote, Verse and Preformatted.
* Expose refresh method from Dropdown component.
* Omit style tags when pasting.
* Use best fitting embed aspect ratio if exact match doesn’t exist.
* Avoid dispatching undefined results in promise middleware.
* Change keyboard shortcut for removing a block to access + z.
* Replace the Full Screen mode “x” icon with a back arrow.
* Make drag handle visible on hover while in nested contexts.
* Pass the tab title to the TabPanel component for situations where it may need to be highlighted.
* Allow setting no alignment when a default alignment exists.
* Improve title and appender margin fix.
* Avoid focusing on the close button on modal and try a modal appear animation.
* Change the URL Input label to match Classic.
* Adjust media upload source for RichText.
* Handle edge cases in with-constrained-tabbing and add tests.
* Set a consistent input field width in media placeholders.
* Add a hover state to sidebar panel headers.
* Change settings title from “Writing” to “View”.
* Convert the “tools” menu group into internal plugin.
* Normalize data types and fix default implementation in parser.
* Cleanup CSS specificity issues on Button component for portability.
* Display error when attempting to embed URLs that can’t be embedded and don’t generate a fallback.
* Update some edit and save button labels and styles for consistency.
* Make “Manage Reusable Blocks” a link instead of an icon button.

### Bug Fixes
* Fix issue with Enter and the Read More block.
* Fix menu item hover colors.
* Fix issue with editor styles and fullscreen mode.
* Fix popover link repositioning.
* Fix Space Block layout issues on small screens.
* Fix custom classNames for dynamic blocks.
* Fix spacing of post-publish close button in other languages.
* Fix Async Generator resolvers resolution.
* Fix issue with Spacer Block not being resizable when using unified toolbar and spotlight mode.
* Fix grammar.md manifest entry and update data docs.
* Fix issue with region focus on the header area on IE11.
* Fix reusable block broken button dimensions on IE11.
* Fix issues with dropping blocks after dragging when calculating new block index.
* Fix InnerBlock templates sync conditions to avoid a forced locking.
* Fix typo in @wordpress/api-fetch README.md.
* Fix regression with Button Block placeholder text.
* Fix dropzone issue in Edge (event.dataTransfer.types not being an array).
* Fix documentation for registerBlockStyle arguments and clarify getSaveElement filter.
* Fix raw transforms not working in Edge when pasting content.
* Fix a regression where wide images would cause horizontal scrollbars.
* Fix issue with gallery margin while typing a caption.
* Fix Block alignment CSS rules affecting nested blocks.
* Fix CSS issue with nested paragraph placeholder.
* Fix links in docs and add documentation for isPublishSidebarEnabled.
* Fix shortcode package dependencies.
* Fix overscroll issues locking scroll up on long pages.
* Fix reference to SVG component in docs.
* Fix Table Block header and body column misalignment.
* Fix an issue where inserting like breaks would throw an error.
* Fix regressions with placeholder text color (Cover Image, captions).
* Fix Editor Styles regression.
* Fix faulty Jed state after setLocaleData.
* Fix small line-height issue in editor style.
* Fix Pullquote margin regressions.
* Fix issues with File Block and new RichText structures.
* Fix Writing Flow E2E test.
* Fix issues with “tips” popup margins.
* Fix issue with mentions after rich text value merge.
* Fix clipping issue with Instagram embed.
* Fix ESNext example code.
* Fix usage of tabs / spaces in parser code.
* Fix Classic Block toolbar regression.
* Fix issues with Table Block alignments.
* Fix inserter misalignment regression.

### Other Changes
* Minor i18n fixes after deprecations were removed.
* Rename parameter from mapStateToProps to mapSelectToProps in withSelect.
* Rename AccessibleSVG to SVG and make it work with React Native.
* Change createObjectUrl to createBlobURL.
* Clean up Sass variables, comments, reduce complexity.
* Move Classic Block to packages.
* Move HTML Block into the blocks library package.
* Move embed scripts into the body in preview documents.
* Ensure that the return value of apiFetch is always a valid Promise object in Firefox.
* Allow negative numbers in order field for Page Attributes.
* Make sure the demo page loads without marking itself as having changes.
* Refactor MediaUpload, MediaPlaceholder, and mediaUpload to support arrays with multiple supported types.
* Add new icons to dashicons package.
* Add link to “add_theme_support” docs.
* Remove glob and just include necessary files.
* Remove unused isButton prop.
* Remove Vine embed.
* Replace length check with RichText.isEmpty in Image Block.
* Replace TinyMCE function to decode entities with existing Gutenberg package.
* Extract the edit-post module as a reusable package.
* Pass editor initial settings as direct argument.
* Pass feature image ID to media upload component.
* Pass all available properties in the media object.
* Replace element-closest with registered vendor script.
* Add new handbook introduction and docs about “blocks as the interface”.
* Add utils to the wp-data script dependencies.
* Disable alternate diff drivers in setup script.
* Clarify RichText.Content readme docs.
* Document `isDefault` option for block styles.
* Update Panel component documentation.
* Update full post content test fixtures.
* Add ESLint rule about not allowing string literals in IDs.
* Add a test for the new Code → Preformatted transform and use snapshots.
* Add E2E test to visit demo page and verify errors.
* Add E2E tests for list creation.
* Update Redux to the latest version.

### Mobile
* Add the React Native entry point to more packages.
* Need to define isRichTextValueEmpty for mobile.
* Have Travis run mobile tests that use the parent code.
* Wire onEnter to requestHTMLWithCursor command in RichText.
