..  include:: /Includes.rst.txt


..  _introduction:

============
Introduction
============

..  _what-it-does:

What does it do?
================

With `cache_analyzer` you can analyze the data which will be written to
the Caching Framework. You can filter it by specific keywords or regular expressions.
You can also enrich the logged data with environment variables like GET, POST, a Backtrace and much more.
Also, you could throw exceptions to prevent writing invalid cache entries
into the Caching Framework.

The initial idea behind this was, that "sometimes" my website showed empty `href`
attributes of HTML `a` tags. I searched a very long time to find that error,
but without success. I then hooked into the Caching Framework to protocol
everything which matches `href=""`. A day later of logging data, I found the issue and saw
that the site was called with an invalid `L` parameter.

..  warning::

`cache_analyzer` is a debugging tool and should not be part of production
mode forever. As it hooks into the Caching Framework it may slow down your
site. So, please uninstall that extension after you have found your issue.
