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

Obviously (at least I hope that is obvious), you could do all of this and much more in a regular plugin if you wanted
to. The point of this plugin is to provide a common basis and layer of abstraction so that you _don't_ have to do this
in every plugin. With Constructs, you can get straight to the point of your application, building blueprints and
templates and do it in a way that is reusable.

To get an idea of what could be done with Constructs, checkout the [Kirby Constructs Sample](https://github.com/lord-executor/kirby-constructs-sample)
on Github.

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
  An array of paths, relative to the Kirby root, where constructs will be searched. Each construct has to be in it's
  own subdirectory with a `settings.yml` file in it (see [Creating a Construct]).

## Creating a Construct
Creating a Construct is easy; all you really need is a directory with a `settings.yml` file in it. Then it becomes a
question of what you would like to do with your construct.

With the default path, your construct folder structure should look something like this:

```
site/constructs/
  first/
    assets/
      ...
    components/
      first.component-one/
        first.componet-one.yml (blueprint)
        first.componet-one.php (controller)
        first.componet-one.html.php (template)
      first.component-two/
        first.component-two.yml (blueprint)
    fields/
      ...
    src/
      ...
    init.php
    settings.yml (required)
  second/
    assets/
      ...
    components/
      ...
    fields/
      ...
    src/
      ...
    settings.yml (required)
```

### settings.yml

```
name: sample-construct
rootNamespace: SampleConstruct
pageModel: Constructs\ComponentPage
nesting: children
initFile: init.php
```

* **name** (required) \
  The internal name of the construct. While this _can_ be different from the directory name it resides in, the best
  practice is to make the name match the directory and use all lowercase and dashes.
* **rootNamespace** (required if you have a `src` directory) \
  The root namespace for the Construct's `src` directory.
* **pageModel** (defaults to the Kirby `Constructs\ComponentPage` class)
  All components in this construct will use the model class (fully qualified class name) specified here. `ComponentPage`
  derives directly from Kirby's `Page` class, but it adds some additional features. By setting this to `Page` you can
  create components that are backed by the regular Kirby page model.
* **nesting** (defaults to `children`)
  Controls the nesting structure of components within a page. See [Components as Building Blocks] for more details.
* **initFile** (defaults to `init.php`)
  Path (relative to the Construct directory) to the initialization PHP file. Similar to plugins, your Constructs _can_
  contain their own Kirby initialization code like adding field methods, etc. If the init file exists, it is executed
  directly after the Construct has been registered.

### Components
**First, a word on naming**: Due to the way that Kirby blueprints work, every Component name has to be unique and
_should not_ contain special characters that are not allowed in file names. To make the names unique, you can use a
multi-part hierarchical identifier with a dot (`.`) character as separator - e.g. `my-bundle.my-component`. 

To add a component, you first need a `components` directory in your Construct directory - see the folder structure above.

Next, you need a directory for each Component. The name of the directory has to correspond to the Component name. The
only _required_ thing about a component is the blueprint, the controller and template files are optional and the usual
Kirby rules apply here if they are omitted. For all three, the file name has to be the Component name followed by the
appropriate extension for the contents as defined here:
```
my-component.yml (blueprint)
my-component.php (controller)
my-component.html.php (template)
```

### Global Fields
Global field definitions work just like they do in [vanilla Kirby](https://getkirby.com/docs/panel/blueprints/global-field-definitions),
except that you can place them in the `fields` directory of your Construct instead.

### Snippets
Adding snippets to your Construct is again fairly trivial. Just add the snippet file to the `snippets` directory of
your Construct, but note that to avoid conflicts with snippets defined elsewhere, to include the snippet you will have
to use the _virtual_ path which is determined like this: 
```
constructs/{constructName}/{pathToSnippet}/{snippetName}
```

So, if your snippet lies in `site/constructs/my-construct/snippets/special/snip.php`, then you can use the snippet like
this
```php
<?php snippet('constructs/my-construct/special/snip') ?>
```

### Classes
If your construct needs some logic or services or whatever kids nowadays call that kind of thing, then you'll probably
want to put that stuff in some PHP classes that you can then use from your Component controllers and blueprints or
in field / page methods or models. With Constructs you can just put those classes into your Construct's `src`
directory and as long as you follow [PSR-4](http://www.php-fig.org/psr/psr-4/) conventions in combination with the
`rootNamespace` defined in the `settings.yml` all of your classes will be autoloaded for you.

### Assets
Very much in line with the route that Kirby provides for [plugin assets](https://getkirby.com/docs/developer-guide/plugins/assets),
Construct assets can be accessed through the following route.
```
http://{domain}/assets/constructs/{constructName}/{optionalSubfolder}/{filename}
```

This of course assumes that you have placed all your assets in the Construct's `assets` directory.

For example, if your Construct has an asset in the path `site/constructs/my-construct/assets/js/main.js`, then you can
attach this script to your template with
```php
<?php echo js('assets/constructs/my-construct/js/main.js'); ?>
```

## Components as Building Blocks
You _can_ create Components that act like any other Kirby page if you so desire, but the design goal of Components was
to also allow components to be _parts_ of a page very similar to what the [Kirby Modules Plugin](https://github.com/getkirby-plugins/modules-plugin)
tries to do.

You can make pages extremely flexible if you _outsource_ some of the building blocks of your pages into smaller
elements - you could call them Components - and then compose the page by adding different Components to different pages.
Of course you can already do some of that with structured fields and other patterns, but the Component style of
implementation makes reuse a lot easier - particularly for more complex sites.

The Constructs plugin tries to not make too many (most likely invalid) assumptions about the way you want to structure
your pages or how you want to use Components, but it provides some (hopefully) helpful functions that can be used to
accomplish a wide variety of things.

To get an idea of _how_ Constructs can be used to build pages, check out the [Kirby Constructs Sample](https://github.com/lord-executor/kirby-constructs-sample)
on Github.

## Overriding Constructs
While Constructs are meant to be reusable, there are always going to be situations where the default is just not up to
the task. Maybe you need to tweak the markup, or add some custom logic to the controller, or ... whatever. In any case
_some_ of the things you might want to customize can be accomplished with overrides without touching the original
Construct code at all.

### Blueprints, Controllers, Templates and Global Fields
Just create a file with the same name as the Component in your site's blueprints, controllers and / or templates
directory, but **watch the file ending change** for templates from `*.html.php` in the Component directory to
`*.php` in the site's template directory.

Once the file is in place, it should automatically take precedence over the one provided by the Construct.

### Snippets
Again, this is just a matter of placing the modified file in the site's snippets directory making sure the _path_ in
the snippets directory matches the _virtual path_ of the snippet in the Construct.
```
constructs/{constructName}/{pathToSnippet}/{snippetName}
```

### Construct Configuration
You can also override the Construct configuration in the `settings.yml` file using Kirby's configuration. To do this,
you can set each settings key individually according to the following Kirby config key naming convention.
```
constructs.{constructName}.{settingsKey}
```

So, to override the init file of `my-construct`, you could do something like this:
```php
c::set('constructs.my-construct.initFile', '/path/to/custom/initFile.php');
```

**Note** that most changes you can make here will inevitably **break** the construct. There are however _some_ cases where
this could be useful, for example if you are trying to override the Construct initialization with your own code.

# ToDo
* Integration with Kirby JSON API plugin
* Simple form of dependency injection (?)
