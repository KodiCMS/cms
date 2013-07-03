<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

Assets::js('jquery.tagsinput', PLUGINS_URL . $plugin->id() . '/vendors/jquery-tags-input/jquery.tagsinput.min.js', 'jquery');
Assets::js('tagsinput', ADMIN_RESOURCES . 'js/tagsinput.js', 'global');
Assets::css('jquery.tagsinput', PLUGINS_URL . $plugin->id() . '/vendors/jquery-tags-input/jquery.tagsinput.css', 'global');