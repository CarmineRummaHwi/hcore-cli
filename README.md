HCore/CLI installation
======================

HCore/CLI utilizes [Composer](https://getcomposer.org) to manage its
dependencies. So, before using HCore/CLI, make sure you have Composer
installed on your machine.

First, download the HCore/CLI using Composer:

``` {.language-php}
composer global require hcore/cli
```

Make sure to place Composer's system-wide vendor bin directory in your
`$PATH`{.language-php} so the laravel executable can be located by your
system. This directory exists in different locations based on your
operating system; however, some common locations include:

-   macOS and GNU / Linux Distributions:
    `$HOME/.composer/vendor/bin`{.language-php}
-   Windows:
    `%USERPROFILE%\AppData\Roaming\Composer\vendor\bin`{.language-php}

