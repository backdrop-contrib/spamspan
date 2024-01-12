SpamSpan
======================

The SpamSpan module obfuscates email addresses to help prevent spambots from
collecting them.  It implements the technique at http://www.spamspan.com.

The problem with most email address obfuscators is that they rely upon
JavaScript being enabled on the client side.  This makes the technique
inaccessible to people with screen readers.  SpamSpan, however, will produce
clickable links if JavaScript is enabled, and will show the email address as
<code>example [at] example [dot] com</code> if the browser does not support
JavaScript or if JavaScript is disabled.


Installation
------------

- Install this module using the official Backdrop CMS instructions at
https://backdropcms.org/guide/modules.

- Visit the configuration page at Administration > Configuration > Content
Authoring > Text Editors and formats > SpamSpan
(admin/config/content/formats/spamspan) to test out SpamSpan.

- Then go to the Text Formats Configuration page (admin/config/content/formats)
and configure the desired input formats to enable the filter.

- Important: rearrange the filter processing order to resolve conflicts with
other filters.  To avoid problems, you should at least make sure that the
SpamSpan filter has a higher weighting (greater number) than the line break
filter which comes with Drupal ("Convert line breaks into HTML" should come
above SpamSpan in the list of Enabled filters).  If you use the HTML filter
("Limit allowed HTML tags"), you may need to make sure that `<span>` is one of
the allowed tags. Also, the URL filter ("Convert URLs into links") must come
after SpamSpan.

Issues
------

Bugs and Feature requests should be reported in the [Issue Queue](https://github.com/backdrop-contrib/foo-project/issues).

Current Maintainers
-------------------

- [Robert J. Lang](https://github.com/bugfolder).

Credits
-------

- Ported to Backdrop CMS by [Robert J. Lang](https://github.com/bugfolder).
- Originally written for Drupal by [lakka](https://www.drupal.org/u/lakka)
  and maintained by [vitalie](https://www.drupal.org/u/vitalie) and
  [peterx](https://www.drupal.org/u/peterx).
- Based on [SpamSpan](https://www.spamspan.com).

License
-------

This project is GPL v2 software.
See the LICENSE.txt file in this directory for complete text.

