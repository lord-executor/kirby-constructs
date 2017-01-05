# Kirby Constructs

The goal of the Constructs plugin is similar to that of the [Kirby Modules Plugin](https://github.com/getkirby-plugins/modules-plugin),
but it takes things a step further.

_Module_ from the modules plugin leave some questions open, like:
* Where do I put my custom PHP code that doesn't belong in the template file?
* How can I prepare data for my template (controller logic)?
* How can I make a module or a group of modules reusable?

This is where Constructs come in. A Construct is a set of _Components_ (that would be _modules_ in Kirby Modules Plugin
terminology) together with assets, custom code and various other things that make up a logical unit. Each Construct
lives in its own directory and their locations can be configured to encourage reuse - for example, a Construct can
easily be added as a Git submodule.

The implementation allows templates, snippets and blueprints defined by Constructs to be easily overwritten with a
customized implementation which makes it easy to _tweak_ certain aspects of a Construct on a per-site basis.

## Features

What you can do with Constructs:
* It allows you to define _Components_. A component consists of a blueprint, an optional controller and an optional
template that are all defined in one directory.
* Group one or more Component into a Construct which can (but doesn't have to be) managed as a Git submodule. This
allows logical separation and reuse of Constructs.
* Add custom snippets to your Constructs.
* Add global field definitions to your Constructs.
* Add assets (JS, CSS, images, ...) to your Constructs that are made accessible through the
`assets/constructs/(:any)/(:all)` route, analogous to plugin assets.
* You can organize your code into PHP classes in the construct's `src` directory which will be autoloaded with PSR-4
conventions.
* All templates and snippets that come with a construct can be overwritten by placing a properly named alternative
file in your normal Kirby `snippets` or `templates` directory.

## Installation
### Using the KirbyCLI
```bash
kirby plugin:install lord-executor/kirby-constructs
```

### Manually
Get the plugin files by downloading a ZIP archive of the source from https://github.com/lord-executor/kirby-constructs or by cloning the repository with
```bash
git clone https://github.com/lord-executor/kirby-constructs.git
```
Then, copy all the files into your Kirby installation's site/plugins directory under a newly created constructs directory.

Note that the plugin directory must be named constructs and it must directly contain the constructs.php file.

### Configuration

* `constructs.dirs` - defaults to `['site/constructs']` \
  An array of paths, relative to the Kirby root, where each construct can be found. If you want to have multiple
  constructs, you have to list all their paths in this variable.

## Creating a Construct

## ToDo
* sample construct repository
* custom model for components
