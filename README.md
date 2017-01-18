# FFCMS 3
FFCMS - fast, flexibility content management system, based on MVC architecture. FFCMS contains many package to realise application building. 

[![Code Climate](https://codeclimate.com/github/phpffcms/ffcms/badges/gpa.svg)](https://codeclimate.com/github/phpffcms/ffcms)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phpffcms/ffcms/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phpffcms/ffcms/?branch=master)

REQUIREMENTS
------------
FFCMS require support php 5.5 or newest. FFCMS include 2 native engine:
  * [ffcms-core](https://github.com/phpffcms/ffcms-core)
  * [ffcms-console](https://github.com/phpffcms/ffcms-console)

INSTALLATION
------------
To deploy ffcms software you must have composer and php-cli:
```bash
composer global require "fxp/composer-asset-plugin:1.2.*"
composer create-project phpffcms/ffcms ./path/to/document_root 3.0.0 --keep-vcs --prefer-dist
composer update
php console.php main:install
```

If you want to use **developer version** you must use "master" branch from developer repository with stability "-dev":
```bash
composer global require "fxp/composer-asset-plugin:1.2.*"
composer create-project phpffcms/ffcms ./path/to/document_root --stability="dev" --keep-vcs --prefer-dist
composer update
php console.php main:install
```

Authors
------------
Owner: Pyatinskyi Mihail, Russian Federation.

Website: https://ffcms.org

License (MIT)
------------
```
The MIT License (MIT)

Copyright (c) 2013-2017 FFCMS, Mihail Pyatinskyi

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
