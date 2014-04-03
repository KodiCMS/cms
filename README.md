# [KodiCMS] (http://www.kodicms.ru/)

# # [Channel on Youtube] (http://www.youtube.com/channel/UCgZ25N9C1F8uoTXZZK55mqQ)

http://www.kodicms.ru/forum.html

KodiCMS based off [Kohana framework] (http://kohanaframework.org/). Kohana -
framework for creating web applications. You can create your own modules
Plugins in full using the tools Kohana.

As a template used Backend interface [Twitter Bootstrap] (http://twitter.github.com/bootstrap/),
which enables you to not spend a lot of time to develop templates for new
sections.


# # Key features .

* Is used as the nucleus [Kohana framework] (http://kohanaframework.org/)
* Admin interface is based on [Twitter Bootstrap 2.3] (http://twitter.github.com/bootstrap/)
* An ideal platform for the launch of major projects
* Enhanced with plugins
* Use `Observer` to extend the basic functionality
* Unlimited level pages
* High speed
* Processing of erroneous URL. ( If a visitor erred URL, most likely he will not get a response : Page Not Found )
* Widgets
* Visual Editor
* Differentiation of users
* Integration with social services . networks
* Zip patterns, and events for notification pochovyh
* Easy installer
* API

# # DEMO / demo site
http://demo.kodicms.ru/

> backend
> http://demo.kodicms.ru/backend

> Login: demo
> Password: demodemo


# # Forum / Forum

http://www.kodicms.ru/forum.html

# # Requirements

* Apache server with. Htaccess or NGINX
* PHP 5.3.3 ( or newer )
* MySQL ( and access to data management )


# # Install / Installation

1. Create a clone of the repository or `https://github.com/butschster/kodicms.git`
[ download zip file ] (https://github.com/butschster/kodicms/zipball/master)
with the latest version .

2 . Place the files on your web- server.

> When you install the site is not in the root directory , you need to make in two places Amendments.
> The files :
> * `. Htaccess => RewriteBase / subfolder /`
> * `Cms \ app \ bootstrap.php` => `Kohana :: init (array ('base_url' => '/ subfolder /', ...));`

3 . Before installing, you must delete the file config.php, if it exists in the site root

4 . Open the home page through a browser. Process starts intsallyatsii system.

> If an error occurs ErrorException [ 2 ]: date () [function.date]: It is not
> Safe to rely on the system's timezone settings. You are required to use the
> Date.timezone setting or the date_default_timezone_set () function.
> ....
> In `cms / app / bootstrap.php` there is a line `date_default_timezone_set ('UTC')`,
> It must be uncommented .
> [Available time zones ] (http://www.php.net/manual/timezones)

5 . Fill in all required fields and click "Install."
6. After installation, you will find yourself on a login page , where it will
Set your username and password to login .


# # Installation via Cli ( Console )

> KodiCMS enables singles to install the console .
> To install the module is used `Minion`

1. Before installing, you must delete the file config.php, if it exists in the site root

2 . Go to the root folder kodicms

3 . execute `php index.php - task = install`.

> The full set can be viewed through the help `php index.php - task = install - help`


# # # Sample configuration for Nginx
`` `nginx
server {
  listen 127.0.0.1:80;
  server_name example.com www.example.com;

  # PublicRoot our site
  root / srv / http / example.com / public_html;
  index index.php;

  # Set the path to the logs
  # To do buffering access_log
  access_log / srv / http / example.com / logs / access.log main buffer = 50k;
  error_log / srv / http / example.com / logs / error.log;

  charset utf8;
  autoindex off;

  location / {
    if (!-f $ request_filename) {
      rewrite ^ / (. *) $ / index.php;
    }
  }

  # Connect handler php-fpm
  location ~ \. php $ {
    # This string , we specify
    # The current location can be used
    # Only for internal queries
    # Thereby prohibit the processing of all php files
    # For which no established location
    internal;

    # Php-fpm. Socket connection .
    fastcgi_pass unix :/ var / run / php-fpm / php-fpm.sock;
    # Or fastcgi_pass 127.0.0.1:9000;
    fastcgi_param KOHANA_ENV development;
    # Or fastcgi_param KOHANA_ENV production;
    fastcgi_index index.php;
    fastcgi_param DOCUMENT_ROOT / srv / http / oskmedia / public_html;
    fastcgi_param SCRIPT_FILENAME / srv / http / oskmedia / public_html $ fastcgi_script_name;
    include fastcgi_params;
  }

  # Block access to all hidden files
  # Such as . Htaccess,. Git,. Svn etc.
  location ~ / \. ht {
      deny all;
  }
}
`` `

# # # Example file . Htaccess for Apache
`` `apache
# Set environment
SetEnv KOHANA_ENV production
# SetEnv KOHANA_ENV development
SetEnv KOHANA_BASE /
SetEnv BASE_URL http://www.example.com

# Turn on URL rewriting
RewriteEngine On

# Installation directory
RewriteBase /

# Protect hidden files from being viewed
<Files .*>
  Order Deny, Allow
  Deny From All
</ Files>

# Protect application and system files from being viewed
RewriteRule ^ (?: Cms | layouts | public | snippets) \ b. * Index.php / $ 0 [L]

# Allow any files or directories that exist to be displayed directly
RewriteCond% {REQUEST_FILENAME}!-F
RewriteCond% {REQUEST_FILENAME}!-D

# Rewrite all other URLs to index.php / URL
RewriteRule. * Index.php / $ 0 [PT] # Set environment
```

# # Bug tracker

If you have any problems while using CMS, inform them on our
bug tracker .

https://github.com/butschster/kodicms/issues


# # Copyright and license
```
Copyright 2012 Buchnev Pavel <butschster@gmail.com>.

---

KodiCMS is free software: you can redistribute it and / or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

KodiCMS is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with KodiCMS. If not, see <http://www.gnu.org/licenses/>.

KodiCMS has made an exception to the GNU General Public License for plugins.
See exception.txt for details and the full text.
```
