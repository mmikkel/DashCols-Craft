# DashCols Craft CMS Plugin

DashCols makes it easy to add custom fields to element index tables.
That’s right, if you ever wanted to display a “featured image” or the like in your entry listings – fret no more.

## Installation and setup

* Download & unzip
* Move the /dashcols folder to craft/plugins
* Install

After installing, visit _DashCols’_ CP section. Using the built-in Field Layout designer, DashCols enables you to easily add almost any field to your Entry and Category index tables _(support for Users coming soon)_.

Please note that not all FieldTypes are supported – look below for the complete list.

### Supported FieldTypes

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

### Partially supported FieldTypes

* Assets _(only images, and just 1 per field)_

### Unsupported FieldTypes

* Matrix
* Rich Text
* Table

### …but what about my awesome custom FieldType?

For now, any String based attribute will display more or less as-is (some formatting is applied to stuff like URLs, Hex color codes etc.). More complex stuff needs to be built in; I’m currently exploring options for enabling users to easily add support for their own custom FieldTypes.

If you have a publicly available FieldType plugin you wish to see supported, please file a feature request!

### What's next?

Look for the following in coming updates:

* Option to hide default fields _(postDate, expiryDate and section)_
* Output of non-image Asset fields _(icon and/or filename, haven't decided yet)_
* Sortable fields
* Support for User tables

## Bugs, feature requests, support

Please file any bug reports or other requests at GitHub: [https://github.com/mmikkel/dashcols-craft/issues](https://github.com/mmikkel/dashcols-craft/issues)

Note that _DashCols_ is a hobby project – unfortunately I can’t make any promises regarding response time for any requests.

**Pull requests are very welcome!**

## Disclaimer

_DashCols_ is provided free of charge. The author is not responsible for any data loss or other problems resulting from the use of this plugin.

Please report any bugs, feature requests or other issues here. As _DashCols_ is a hobby project, no promises are made regarding response time, feature implementations or bug amendments.

## Changelog

### Version 1.0

Initial public release.