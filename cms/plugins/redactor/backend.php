<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );


Assets::js('redactor.ru', PLUGINS_URL . $plugin->id() . '/vendors/redactor/ru.js', 'jquery');
Assets::js('redactor.min', PLUGINS_URL . $plugin->id() . '/vendors/redactor/redactor.min.js', 'jquery');
Assets::js('redactor', ADMIN_RESOURCES . 'js/redactor.js', 'global');

Assets::css('redator', PLUGINS_URL . $plugin->id() . '/vendors/redactor/redactor.css');

Filter::add('redactor');