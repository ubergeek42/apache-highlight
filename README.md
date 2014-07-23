# apache-highlight

This is a php script that when combined with a simple .htaccess file displays source files with syntax highlighting, and provides a link to view the raw file.  Our primary use case has been to dump sample code into a directory for our students to browse and download.

It uses the powerful [Ace](http://ace.c9.io) editor for syntax highlighting.

## Usage
* Copy highlight.php to some "central" location, like `/scripts/highlight.php` on your webserver.
* Make sure that apache has the mod_actions module enabled, and that `AllowOverride FileInfo Indexes` is set for the directory you will be placing your .htaccess file.
* Place an .htaccess file in the directory you want to highlight files from. See the example below.

### .htaccess sample
```apacheconf
Options +Indexes
IndexOptions FancyIndexing HTMLTable

# Two options to select what will get highlighted by this:
# 1. Highlight everything in this directory tree
ForceType text/plain
Action text/plain /scripts/highlight.php

# 2. Highlight only certain file extensions
Action highlight-code /scripts/highlight.php
AddHandler highlight-code .py
AddHandler highlight-code .java
```

## Sample Screenshot
![Screenshot](https://raw.github.com/ubergeek42/apache-highlight/master/screenshot.png)


## Notes/Issues

Some users have had trouble with the PATH_TRANSLATED variable not working properly for them.  Please refer to Pull Request #1 for an alternative method which requires a little bit more effort but should work.
