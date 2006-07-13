Readme
------

The SpamSpan module obfuscates email addresses to help prevent spambots from
collecting them.  It implements the technique at http://www.spamspan.com

The problem with most email address obfuscators is that they rely upon
JavaScript being enabled on the client side.  This makes the technique
inaccessible to people with screen readers.  SpamSpan however will produce
clickable links if JavaScript is enabled, and will show the email address as
<code>example [at] example [dot] com</code> if the browser does not support
JavaScript or if JavaScript is disabled.

Installation
---------

1. Create a directory in the Drupal modules/ directory called spamspan and copy
   all of the module's files into it. 

2. Go to 'administer > modules', and enable the spamspan.module

3. Go to 'administer > configuration > input formats' and enable the filter in
   the desired input formats.

4. (optional) Rearrange the filters on the input format's 'rearrange' tab to
   resolve conflicts with other filters.

5. (optional) Select the configure tab to set available options

Module Author
------------

Lawrence Akka <lakka@users.sourceforge.net>

Note:  The spamspan javascript file is from www.spamspan.com
