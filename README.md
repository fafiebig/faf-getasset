# DEPRECATED - DO NOT USE IT ANY LONGER!


# faf-getasset

Wordpress Plugin media proxy to privatize media urls.
Adds a private checkbox to media asset posts.
You must be logged in to see private checked media assets.

# Installation

* Unzip and upload the plugin to the **/wp-content/plugins/** directory
* Activate the plugin in WordPress
* Got to plugins page and use the optimization.

# Installation with composer

* Add the repo to your composer.json

```json

"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/fafiebig/faf-getasset.git"
    }
],

```

* require the package with composer

```shell

composer require fafiebig/faf-getasset 1.*

```

# Shortcode Usage

Shortcodes will create a link that forces a download of the file.

```shell

// for images
[download_image id="<imageId>" title="<link title>" link="<link name>" class="<css classes>" size="<image size>"]

// for other files
[download_attachment id="<attachmentId>" title="<link title>" link="<link name>" class="<css classes>"]

```
