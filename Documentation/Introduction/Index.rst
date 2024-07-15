..  include:: /Includes.rst.txt


..  _introduction:

============
Introduction
============

..  _what-it-does:

What does it do?
================

With `cache_analyzer` you can analyze the data which will be written to
Caching Framework for specific keyword or regular expression to write
environment data like GET, POST, Backtrace and many more into log or, you also
can throw an exception to prevent writing invalid cache entries
into Caching Framework.

The idea in behind was, that "sometimes" my website prints empty `href`
attributes of HTML `a` tags. I searched a very long time to find that error,
but no success. I then hooked into the Caching Framework to protocol
everything which matches `href=""`. A day later I found the issue and saw
that the site was called with an invalid `L` parameter.

`cache_analyzer` is a debugging tool and should not be part of production
mode forever. As it hooks into the Caching Framework it may slow down your
site. So, please uninstall that extension, after you have found your issue.
