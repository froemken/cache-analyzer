..  include:: /Includes.rst.txt


..  _configuration:

=============
Configuration
=============

Immediately after installing this extension, the hook is active and looks
for cache expression records to process.

Cache Expression Record
=======================

A cache expression record can only be created as administrator or
system maintainer on the Root Page (PID: 0).

Click :guilabel:`Web > List` module, choose `root page (PID 0)` and create a
new record of type `Cache Expression` (table: `tx_cacheanalyzer_expression`).

..  confval:: Title
    :name: cache-expression-title
    :type: string
    :Default:

    A title for better identification in the list of other records of
    the same table.

..  confval:: Throw exception?
    :name: cache-expression-throw-exception
    :type: boolean
    :Default: false

    By default `cache_analyzer` will only analyze and protocol cache entries
    matching the requirements. If set to `true` a protocol will be
    written, but afterwards an exception will be thrown to prevent filling
    the caching framework with maybe invalid data.

..  confval:: Throw exception in frontend only?
    :name: cache-expression-throw-exception-fe-only
    :type: boolean
    :Default: true

    If `Throw Exception?` is active, it may happen that exceptions will also
    be thrown in backend. This may happen in the :guilabel:`Page` module where parts of
    content will be rendered for content element preview.

    To prevent throwing exceptions in backend this option must be `true`. If
    you have problems with cache in backend you have to set this option
    to `false`.

..  confval:: Expression
    :name: cache-expression-expression
    :type: string
    :Default:

    Enter the expression the cache entry has to match.

    If `Is regular expression?` is deactivated `cache_analyzer` will just
    compare a cache entry with :php:`mb_strpos`.

    If `Is regular expression?` is activated `cache_analyzer` will compare
    the cache entry using a regular expression. There is no need to add a delimiter
    like `/` in front/back of the expression. This will be done automatically.
    Further contained `/` will be quoted automatically, too.

    ..  note::

        In most cases the cache will be converted using :php:`json_encode`. So,
        if you are searching for something like `href=""` you should change
        your expression to `href=\"\"`.

..  confval:: Is regular expression?
    :name: cache-expression-is-regular-expression
    :type: bool
    :Default: false

    Activate to use regular expression syntax in field `Expression`.

..  confval:: Cache Configurations
    :name: cache-expression-cache-configurations
    :type: array
    :Default:

    TYPO3 comes with a lot of different cache configurations. If you
    know in with cache configuration the error happens you can select that
    config from the right list to the left list. If you are completely unsure
    feel free to move all cache configurations from right to left box.
