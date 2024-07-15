# TYPO3 Extension `cache_analyzer`

[![Latest Stable Version](https://poser.pugx.org/stefanfroemken/cache-analyzer/v/stable.svg)](https://packagist.org/packages/stefanfroemken/cache-analyzer)
[![TYPO3 13.2](https://img.shields.io/badge/TYPO3-13.2-green.svg)](https://get.typo3.org/version/13)
[![License](https://poser.pugx.org/stefanfroemken/cache-analyzer/license)](https://packagist.org/packages/stefanfroemken/cache-analyzer)
[![Total Downloads](https://poser.pugx.org/stefanfroemken/cache-analyzer/downloads.svg)](https://packagist.org/packages/stefanfroemken/cache-analyzer)
[![Monthly Downloads](https://poser.pugx.org/stefanfroemken/cache-analyzer/d/monthly)](https://packagist.org/packages/stefanfroemken/cache-analyzer)
![Build Status](https://github.com/froemken/cache-analyzer/actions/workflows/tests.yml/badge.svg)

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

## Installation

### Installation using Composer

The recommended way to install the extension is using Composer.

Run the following command within your Composer based TYPO3 project:

```
composer require stefanfroemken/cache-analyzer
```

#### Installation as extension from TYPO3 Extension Repository (TER)

Download and install `cache_analyzer` with the extension manager module.

### Minimal setup

1) Create a record of type "Cache Expression" on root page (PID: 0)
2) Enter fields `title`, `expression` and at lease one `cache configuration`
3) Clear Cache
4) Wait and let your website visitors requests your website
5) If an expression matches the log in `var/log` will be filled with additional environment variables

Have a look into the log to see, which request creates your log entry
