# [KodiCMS](http://www.kodicms.ru/)

KodiCMS основана на базе [Kohana framework](http://kohanaframework.org/). Kohana - 
фреймворк для создания web приложений. Вы можете создавать собственные модули, 
плагины в полном объеме используя инструменты Kohana.

В качестве шаблона Backend интерфейса используется [Twitter Bootstrap](http://twitter.github.com/bootstrap/),
который позволит вам не тратить много времени на разработку шаблонов для новых
разделов.


## Ключевые особенности.

* В качестве ядра используется [Kohana framework](http://kohanaframework.org/)
* Admin интерфейс построен на базе [Twitter Bootstrap](http://twitter.github.com/bootstrap/)
* Идеальная платформа для старта крупных проектов
* Расширение при помощи плагинов
* Использование `Observer` для расширения базового функционала
* Неограниченный уровень страниц
* Высокая скорость работы
* Обработка ошибочных URL. (Если посетитель допустил ошибку URL, скорее всего он не получит в ответ: Страница не найдена)
* Удобный инсталлятор

## DEMO
http://demo.kodicms.ru/

> backend
> http://demo.kodicms.ru/backend

> Login: demo
> Password: demodemo


## Скриншоты

http://www.kodicms.ru/screenshots.html


## Требования

* Apache server with .htaccess либо NGINX
* PHP 5.3.3 (или более новая)
* MySQL (и доступ к управлению данными)


## Установка

1. Создайте клон репозитория `https://github.com/butschster/kodicms.git` или 
[скачайте zip архив](https://github.com/butschster/kodicms/zipball/master)
с последней версией.

2. Разместите файлы на вашем web-сервере.

> При установке сайта не в корневую директорию, необходимо в двух местах внести изменеия.
> В файлах:
> * `.htaccess => RewriteBase /subfolder/`
> * `cms\app\bootstrap.php` => `Kohana::init( array( 'base_url' => '/subfolder/', ... ) );`

3. Перед установкой необходимо удалить файл config.php, если он имеется в корне сайта

4. Откройте главную страницу через браузер. Запустится процесс интсалляции системы.

> Если возникла ошибка ErrorException [ 2 ]: date() [function.date]: It is not 
> safe to rely on the system's timezone settings. You are required to use the 
> date.timezone setting or the date_default_timezone_set() function.
> ....
> В `cms/app/bootstrap.php` есть строка `date_default_timezone_set( 'UTC' )`, 
> необходимо ее разкомментировать.
> [Доступные временные зоны](http://www.php.net/manual/timezones)

5. Заполните все необходимые поля и нажмите кнопку "Установить". 
6. После установки системы вы окажетесь на странице авторизации, где будет 
указан ваш логин и пароль для входа в систему.


## Установка через Cli (Консоль)

> KodiCMS позоляет установить систему через консоль.
> Для установки используется модуль `Minion`

1. Перед установкой необходимо удалить файл config.php, если он имеется в корне сайта

2. Перейти в корень папки kodicms

3. выполнить команду `php index.php --task=install`. 

> Полный набор параметров можно посмотреть через help `php index.php --task=install --help`


### Пример конфигурации для Nginx
```nginx
server{
  listen 127.0.0.1:80;
  server_name   example.com www.example.com;

  # PublicRoot нашего сайта
  root          /srv/http/example.com/public_html;
  index         index.php;

  # Устанавливаем пути к логам
  # Для access_log делаем буферизацию
  access_log    /srv/http/example.com/logs/access.log main buffer=50k;
  error_log     /srv/http/example.com/logs/error.log;

  charset       utf8;
  autoindex     off;

  location / {
    if (!-f $request_filename) {
      rewrite ^/(.*)$ /index.php;
    }
  }

  # Подключаем обработчик php-fpm
  location ~ \.php$ {
    # Этой строкой мы указываем,
    # что текущий location можно использовать
    # только для внутренних запросов
    # Тем самым запрещаем обработку всех php файлов,
    # для которых не создан location
    internal;

    # php-fpm. Подключение через сокет.
    fastcgi_pass   unix:/var/run/php-fpm/php-fpm.sock;
    # или fastcgi_pass   127.0.0.1:9000;
    fastcgi_param   KOHANA_ENV development;
    # или fastcgi_param   KOHANA_ENV production;
    fastcgi_index  index.php;
    fastcgi_param  DOCUMENT_ROOT  /srv/http/oskmedia/public_html;
    fastcgi_param  SCRIPT_FILENAME  /srv/http/oskmedia/public_html$fastcgi_script_name;
    include fastcgi_params;
  }

  # Блокируем доступ для всех скрытых файлов,
  # таких как .htaccess, .git, .svn и т.д.
  location ~ /\.ht {
      deny all;
  }
}
```

### Пример файла .htaccess для Apache
```apache
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
  Order Deny,Allow
  Deny From All
</Files>

# Protect application and system files from being viewed
RewriteRule ^(?:cms|layouts|public|snippets)\b.* index.php/$0 [L]

# Allow any files or directories that exist to be displayed directly
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Rewrite all other URLs to index.php/URL
RewriteRule .* index.php/$0 [PT]# Set environment
```

## Bug tracker

Если у вас возникли проблемы во время использования CMS, сообщайте их на наш
баг трекер.

https://github.com/butschster/kodicms/issues

## Changelog

### 5.14.0

* Переделана сортировка страниц
* Переделан модуль Plugins
* Редактор шаблонов и сниппетов рястягивается на высоту экрана
* Ace обзавелся двумя комбинациями клавиш: CTRL+F - на весь экран, CTRL+S - сохранение
* В плагин Yandex Metrika добавлены дополнительные настройки
* Переделаны настройки плагина Less compiler
* В настройках сайта добавлено поле "Описание сайта" и заменен ключ "Заголовок сайта" на `site_title`
* Другие улучшения ядра

### 5.5.0

* Удален из поставки плагин CodeMirror (Теперь для подсветки используется Ace)
* Доработан JS API добавления фильтров(редакторов) в систему
* Изменен роут для доступа к системному API 
* JS файлового менеджера elfiner вынесен в папку модуля



## Copyright and license
```
Copyright 2012 Buchnev Pavel <butschster@gmail.com>.

---

KodiCMS is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

KodiCMS is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with KodiCMS.  If not, see <http://www.gnu.org/licenses/>.

KodiCMS has made an exception to the GNU General Public License for plugins.
See exception.txt for details and the full text.
```