# Important notice

Pixel and Tonic recently unveiled Craft 2.5, currently in public beta and due to release December 1st 2015. Among a slew of great additions, this update adds _Customizable Element Indexes_ to Craft – basically making the DashCols plugin redundant.  

The native Customizable Element Indexes are awesome, and finally having this functionality in core is a great boon for Craft, its users and the community.  

DashCols 1.3 will be the final release for DashCols, and it will not receive any support, new features or bug fixes going forward. This repo will stay up indefinetely, though.  

Thanks a lot for all the support and interest in this plugin!  

[Read more about Craft 2.5 on Pixel & Tonic's blog](http://pixelandtonic.com/blog/craft-2.5-beta)

– Mats

***

# DashCols Craft CMS Plugin v. 1.3

_Now with Users and Assets support!_

**The element index tables lists all your content, but let's face it – they're kind of sparse. DashCols makes it easy to add (almost) any custom field to your entry, asset, category and user control panel tables.**

![Screenshot of index table customized using DashCols](/source/demo/index.jpg?raw=true "Index table customized w/ DashCols")

In addition, DashCols will also:

* Enable you to show/hide _default columns_ (URI, section, expiry date etc.) and _element metadata_ (ID, author and last updated date ++)
* Enable you to _sort_ index tables on most columns
* Improve the responsiveness of your index tables

## Installation and setup

* Download & unzip
* Move the /dashcols folder to craft/plugins
* Install

After installing, visit _DashCols’_ CP section and use the built-in Field Layout Designer to add custom fields to entry, category, asset or user sources, and configure the output of default columns and/or element metadata.

Please note that not all FieldTypes are supported – look below for the complete list.

## Options

* Undercover mode: You want to keep DashCols running, but completely hide the nav tab and "Edit columns" button.

![CP section](/source/demo/cp.jpg?raw=true)

### Supported FieldTypes

* Assets
* Categories
* Checkboxes
* Color
* Date/time
* Dropdown
* Entries
* Lightswitch
* Multi-select
* Number
* Plain Text
* Position Select
* Radio Buttons
* Tags
* Users

### Supported Custom FieldTypes

* Address (Smart Map) by @lindseydiloreto
* Doxter by @selvinortiz
* Preparse Field by @aelvan

### Unsupported FieldTypes

* Matrix
* Rich Text
* Table

### …but what about my awesome _custom_ FieldType?

For now, any String based attribute will display more or less as-is (some formatting is applied to stuff like URLs, Hex color codes etc.), and string values are truncated to a maximum of 50 characters. More complex stuff needs to be built in; I’m currently exploring options for enabling users to easily add support for their own custom FieldTypes.

If you have a publicly available FieldType plugin you wish to see supported, please file a feature request!

### Roadmap

Look for the following in coming updates:

* Support for popular, custom FieldTypes
* Option to clear a layout w/ a single button

## Bugs, feature requests, support

Please file any bug reports or other requests at GitHub: [https://github.com/mmikkel/dashcols-craft/issues](https://github.com/mmikkel/dashcols-craft/issues)

Note that _DashCols_ is a hobby project – unfortunately I can’t make any promises regarding response time for any requests.

**Pull requests are very welcome!**

## Disclaimer

_DashCols_ is provided free of charge. The author is not responsible for any data loss or other problems resulting from the use of this plugin.

Please report any bugs, feature requests or other issues [here](https://github.com/mmikkel/dashcols-craft/issues). Note that _DashCols_ is a hobby project and I can offer no promises regarding response time, feature implementations or bug amendments.

### Changelog

#### 1.3

* Now supports editing columns for _Users_  – big thanks to Lindsey DiLoreto for the help!
* Now supports editing columns for _Assets_
* CP section redesigned
* Fixes issue with the _Edit columns_ button not always appearing
* Various small improvements and fixes

#### 1.2.5

* Fixed issue where numeric columns wouldn't render zeros. Fixes #24

#### 1.2.4

* Added support for Doxter
* Slightly improved support for complex custom fieldtypes in general
* Removed custom plugin name setting

#### 1.2.3

* SVG assets now display as thumbnails for Craft 2.4 builds (only where ImageMagick is installed)

#### 1.2.2

* Fixed bug where the _Structure_ sorting option was hidden

#### 1.2.1

* **Added Entry Type metadata column**

#### 1.2

* **Added sorting capabilities for FieldTypes of Boolean, String, Number or DateTime value**

#### 1.1.9

* **Added option to output element metadata (Updated Date, ID and Author) as columns**
* Fixed issue where string values "1" and "0" would always render as a Lightswitch attribute – thanks Fred, you're a champ.
* Fixed issue where string values starting with "#" would sometimes render as a Color attribute
* String values interpreted as external links will now open in a new tab
* Added setting for renaming _DashCols_

#### 1.1.8

* _Asset_ columns will now display total number of files (if more than 1)
* _Asset_ columns now display icon + filename for files.

#### 1.1.7

Fixed issue where layouts would not save if CSRF is enabled

#### 1.1.6

_Minor refactor_

#### 1.1.5

* Fixed issue where the Edit Columns button would not be added to non-managed index tables
* Date/Time columns now display date and/or time based on field settings

#### 1.1.4

* Added settings page and the _Undercover mode_ setting to hide the CP section and disable layout editing

#### 1.1.3

* Fixed issue w/ redirect on All entries layout save
* Edit template only displays relevant default fields
* Added footer to CP section
* Removed editing for individual Single sections

#### 1.1.2

* Fixed issue with Singles layout redirect on save

#### 1.1.1

* Hotfixed an annoying issue w/ move icons not being vertically centered in tall table rows

#### 1.1

* Added option to hide default fields (postDate, expiryDate, URI and section)
* (Hopefully) improved CP section sub nav
* DashCols now redirects to index table upon saving a layout
* Some minor CSS fixes here and there

#### 1.0

Initial public release.
