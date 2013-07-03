SET FOREIGN_KEY_CHECKS=0;

INSERT INTO `TABLE_PREFIX_pages` (`id`, `title`, `slug`, `breadcrumb`, `keywords`, `description`, `parent_id`, `layout_file`, `behavior_id`, `status_id`, `created_on`, `published_on`, `updated_on`, `created_by_id`, `updated_by_id`, `position`, `needs_login`) VALUES
(1, 'Home', '', 'Home', '', '', 0, 'normal', '', 100, '__DATE__', '__DATE__', '__DATE__', 1, 1, 0, 0),
(2, 'Page not found', 'page-not-found', 'Page not found', '', '', 1, '', 'page_not_found', 101, '__DATE__', '__DATE__', '__DATE__', 1, 1, 4, 2),
(3, 'About us', 'about-us', 'About us', '', '', 1, '', '', 100, '__DATE__', '__DATE__', '__DATE__', NULL, 1, 1, 2),
(4, 'RSS Feed', 'rss.xml', 'RSS Feed', '', '', 1, 'none', '', 101, '__DATE__', '__DATE__', '__DATE__', 1, 1, 3, 2),
(5, 'My first article', 'my-first-article', 'My first article', '', '', 8, '', '', 100, '__DATE__', '__DATE__', NULL, NULL, NULL, 1, 2),
(6, '%B %Y archive', 'b-y-archive', '%B %Y archive', '', '', 8, '', 'archive_month_index', 101, '__DATE__', '__DATE__', '__DATE__', 1, 1, 0, 2),
(7, 'My second article', 'my-second-article', 'My second article', '', '', 8, '', '', 100, '__DATE__', '__DATE__', NULL, NULL, NULL, 2, 2),
(8, 'Articles', 'articles', 'Articles', '', '', 1, '', 'archive', 100, '__DATE__', '__DATE__', '__DATE__', NULL, 1, 2, 2);

INSERT INTO `TABLE_PREFIX_page_parts` (`id`, `name`, `filter_id`, `content`, `content_html`, `page_id`, `is_protected`) VALUES
(2, 'body', 'redactor', '<h3>Sorry</h3>\n\n<p>This page not found<br>\n</p>\n', '<h3>Sorry</h3>\n\n<p>This page not found<br>\n</p>\n', 2, 0),
(3, 'body', 'redactor', '<p>This is my site. I live in this city ... I do some nice things, like this and that ...</p>', '<p>This is my site. I live in this city ... I do some nice things, like this and that ...</p>', 3, 0),
(6, 'body', 'redactor', '<p>My first test of my first article.</p>\n', '<p>My first test of my first article.</p>\n', 5, 0),
(7, 'body', 'redactor', '<p>This is my second article.</p>\n', '<p>This is my second article.</p>\n', 7, 0);

INSERT INTO `TABLE_PREFIX_page_roles` (`page_id`, `role_id`) VALUES
(1, 2),
(2, 2),
(3, 2),
(3, 3),
(3, 4),
(4, 2),
(4, 3),
(4, 4),
(5, 2),
(5, 3),
(5, 4),
(6, 2),
(6, 3),
(6, 4),
(7, 2),
(7, 3),
(7, 4),
(8, 2),
(8, 3),
(8, 4);

INSERT INTO `TABLE_PREFIX_roles` (`id`, `name`, `description`) VALUES
(1, 'login', 'Login privileges, granted after account confirmation'),
(2, 'administrator', 'Administrative user, has access to everything.'),
(3, 'developer', 'Developers role'),
(4, 'editor', '');

INSERT INTO `TABLE_PREFIX_roles_users` (`user_id`, `role_id`) VALUES
(1, 1),
(1, 2);

INSERT INTO `TABLE_PREFIX_settings` (`name`, `value`) VALUES
('site_title', '__SITE_NAME__'),
('default_locale', '__LANG__'),
('default_status_id', '100'),
('default_filter_id', 'redactor'),
('default_tab', 'page'),
('allow_html_title', 'off'),
('profiling', 'no'),
('date_format', 'd F Y'),
('debug', 'no'),
('find_similar', 'yes');

INSERT INTO `TABLE_PREFIX_plugins` (`id`, `title`, `settings`) VALUES
('archive', 'Archive', 'a:0:{}'),
('maintenance', 'Maintenance mode', 'a:0:{}'),
('page_not_found', 'Page not found', 'a:0:{}'),
('redactor', 'Redactor', 'a:0:{}'),
('slug_translit', 'Slug translit', 'a:0:{}'),
('tagsinput', 'jQuery Tags Input', 'a:0:{}');


INSERT INTO `TABLE_PREFIX_users` (`id`, `email`, `username`, `password`, `logins`, `last_login`) VALUES
(1, '__EMAIL__', '__USERNAME__', '__ADMIN_PASSWORD__', 0, 0);

INSERT INTO `TABLE_PREFIX_user_profiles` (`id`, `name`, `user_id`, `created_on`) VALUES
(1, 'Administrator', 1, '__DATE__');

INSERT INTO `TABLE_PREFIX_layout_blocks` (`layout_name`, `block`, `position`) VALUES
('none', 'body', 0),
('normal', 'body', 3),
('normal', 'bradcrumbs', 1),
('normal', 'extended', 4),
('normal', 'footer', 7),
('normal', 'header', 0),
('normal', 'recent', 6),
('normal', 'sidebar', 5),
('normal', 'top_banner', 2);

INSERT INTO `TABLE_PREFIX_page_widgets` (`page_id`, `widget_id`, `block`) VALUES
(1, 11, 'body'),
(1, 2, 'bradcrumbs'),
(1, 10, 'extended'),
(1, 3, 'footer'),
(1, 1, 'header'),
(1, 4, 'sidebar'),
(1, 5, 'top_banner'),
(2, 2, 'bradcrumbs'),
(2, 3, 'footer'),
(2, 1, 'header'),
(2, 9, 'recent'),
(2, 4, 'sidebar'),
(3, 2, 'bradcrumbs'),
(3, 3, 'footer'),
(3, 1, 'header'),
(3, 9, 'recent'),
(3, 4, 'sidebar'),
(4, 12, 'body'),
(5, 2, 'bradcrumbs'),
(5, 3, 'footer'),
(5, 1, 'header'),
(5, 9, 'recent'),
(5, 4, 'sidebar'),
(6, 7, 'body'),
(6, 2, 'bradcrumbs'),
(6, 3, 'footer'),
(6, 1, 'header'),
(6, 9, 'recent'),
(6, 4, 'sidebar'),
(7, 2, 'bradcrumbs'),
(7, 3, 'footer'),
(7, 1, 'header'),
(7, 9, 'recent'),
(7, 4, 'sidebar'),
(8, 6, 'body'),
(8, 2, 'bradcrumbs'),
(8, 3, 'footer'),
(8, 1, 'header'),
(8, 9, 'recent'),
(8, 4, 'sidebar');

INSERT INTO `TABLE_PREFIX_widgets` (`id`, `type`, `template`, `name`, `description`, `created_on`, `code`) VALUES
(1, 'page_menu', 'header', 'Header menu', '', '2013-03-22 18:38:11', 'O:22:"Model_Widget_Page_Menu":13:{s:7:"exclude";a:3:{i:0;s:1:"6";i:1;s:1:"4";i:2;s:1:"2";}s:2:"id";s:2:"10";s:4:"type";s:9:"page_menu";s:4:"name";s:11:"Header menu";s:11:"description";s:0:"";s:6:"header";s:0:"";s:8:"template";s:6:"header";s:15:"template_params";a:0:{}s:5:"block";N;s:7:"caching";b:0;s:14:"cache_lifetime";i:0;s:9:"throw_404";b:0;s:8:"\0*\0_data";a:3:{s:15:"match_all_paths";i:0;s:7:"page_id";s:1:"1";s:8:"continue";s:0:"";}}'),
(2, 'page_breadcrumbs', 'bradcrumbs', 'Breadcrumbs', '', '2013-03-22 19:45:02', 'O:29:"Model_Widget_Page_Breadcrumbs":18:{s:7:"exclude";a:0:{}s:2:"id";s:1:"2";s:4:"type";s:16:"page_breadcrumbs";s:4:"name";s:11:"Breadcrumbs";s:11:"description";s:0:"";s:6:"header";s:0:"";s:8:"template";s:10:"bradcrumbs";s:16:"backend_template";N;s:17:"frontend_template";N;s:12:"use_template";b:1;s:15:"template_params";a:0:{}s:5:"block";N;s:6:"crumbs";b:0;s:7:"caching";b:0;s:14:"cache_lifetime";i:0;s:9:"throw_404";b:0;s:7:"\0*\0_ctx";O:7:"Context":9:{s:8:"\0*\0_page";N;s:11:"\0*\0_request";N;s:12:"\0*\0_response";N;s:10:"\0*\0_crumbs";N;s:11:"\0*\0_widgets";a:0:{}s:14:"\0*\0_widget_ids";N;s:10:"\0*\0_blocks";a:0:{}s:10:"\0*\0_params";a:0:{}s:14:"\0*\0_injections";a:0:{}}s:8:"\0*\0_data";a:1:{s:8:"continue";s:0:"";}}'),
(3, 'html', 'footer', 'Footer', '', '2013-03-22 20:29:27', 'O:17:"Model_Widget_HTML":12:{s:2:"id";s:2:"12";s:4:"type";s:4:"html";s:4:"name";s:6:"Footer";s:11:"description";s:0:"";s:6:"header";s:0:"";s:8:"template";s:6:"footer";s:15:"template_params";a:0:{}s:5:"block";N;s:7:"caching";b:0;s:14:"cache_lifetime";i:0;s:9:"throw_404";b:0;s:8:"\0*\0_data";a:1:{s:8:"continue";s:0:"";}}'),
(4, 'html', 'sidebar', 'Sidebar', '', '2013-03-22 20:41:25', 'O:17:"Model_Widget_HTML":12:{s:2:"id";s:2:"13";s:4:"type";s:4:"html";s:4:"name";s:7:"Sidebar";s:11:"description";s:0:"";s:6:"header";s:0:"";s:8:"template";s:7:"sidebar";s:15:"template_params";a:0:{}s:5:"block";N;s:7:"caching";b:0;s:14:"cache_lifetime";i:0;s:9:"throw_404";b:0;s:8:"\0*\0_data";a:1:{s:8:"continue";s:0:"";}}'),
(5, 'html', 'top_banner', 'Top banner', '', '2013-03-22 20:50:41', 'O:17:"Model_Widget_HTML":12:{s:2:"id";s:2:"14";s:4:"type";s:4:"html";s:4:"name";s:10:"Top banner";s:11:"description";s:0:"";s:6:"header";s:0:"";s:8:"template";s:10:"top_banner";s:15:"template_params";a:0:{}s:5:"block";N;s:7:"caching";b:0;s:14:"cache_lifetime";i:0;s:9:"throw_404";b:0;s:8:"\0*\0_data";a:1:{s:8:"continue";s:0:"";}}'),
(6, 'archive_month', 'archive-by-month', 'Archive by month', '', '2013-03-26 10:19:46', 'O:26:"Model_Widget_Archive_Month":17:{s:16:"backend_template";s:7:"archive";s:17:"frontend_template";s:7:"archive";s:2:"id";s:1:"6";s:4:"type";s:13:"archive_month";s:4:"name";s:16:"Archive by month";s:11:"description";s:0:"";s:6:"header";s:16:"Archive by month";s:8:"template";s:16:"archive-by-month";s:12:"use_template";b:1;s:15:"template_params";a:0:{}s:5:"block";N;s:6:"crumbs";b:0;s:7:"caching";b:1;s:14:"cache_lifetime";i:2629744;s:9:"throw_404";b:0;s:7:"\0*\0_ctx";O:7:"Context":9:{s:8:"\0*\0_page";N;s:11:"\0*\0_request";N;s:12:"\0*\0_response";N;s:10:"\0*\0_crumbs";N;s:11:"\0*\0_widgets";a:0:{}s:14:"\0*\0_widget_ids";N;s:10:"\0*\0_blocks";a:0:{}s:10:"\0*\0_params";a:0:{}s:14:"\0*\0_injections";a:0:{}}s:8:"\0*\0_data";a:2:{s:7:"page_id";s:1:"8";s:8:"continue";s:0:"";}}'),
(7, 'archive_hl', 'archive-headline', 'Archive headline', '', '2013-03-26 10:23:47', 'O:23:"Model_Widget_Archive_HL":17:{s:17:"frontend_template";s:10:"page_pages";s:8:"\0*\0_data";a:3:{s:11:"list_offset";i:0;s:9:"list_size";i:10;s:8:"continue";s:0:"";}s:2:"id";s:1:"7";s:4:"type";s:10:"archive_hl";s:4:"name";s:16:"Archive headline";s:11:"description";s:0:"";s:6:"header";s:0:"";s:8:"template";s:16:"archive-headline";s:16:"backend_template";N;s:12:"use_template";b:1;s:15:"template_params";a:0:{}s:5:"block";N;s:6:"crumbs";b:0;s:7:"caching";b:1;s:14:"cache_lifetime";i:2629744;s:9:"throw_404";b:0;s:7:"\0*\0_ctx";O:7:"Context":9:{s:8:"\0*\0_page";N;s:11:"\0*\0_request";N;s:12:"\0*\0_response";N;s:10:"\0*\0_crumbs";N;s:11:"\0*\0_widgets";a:0:{}s:14:"\0*\0_widget_ids";N;s:10:"\0*\0_blocks";a:0:{}s:10:"\0*\0_params";a:0:{}s:14:"\0*\0_injections";a:0:{}}}'),
(8, 'page_pages', NULL, 'Recent entries', '', '2013-03-26 10:24:43', 'O:23:"Model_Widget_Page_Pages":17:{s:8:"\0*\0_data";a:2:{s:11:"list_offset";i:0;s:9:"list_size";i:10;}s:2:"id";N;s:4:"type";s:10:"page_pages";s:4:"name";s:14:"Recent entries";s:11:"description";s:0:"";s:6:"header";N;s:8:"template";N;s:16:"backend_template";N;s:17:"frontend_template";N;s:12:"use_template";b:1;s:15:"template_params";a:0:{}s:5:"block";N;s:6:"crumbs";b:0;s:7:"caching";b:0;s:14:"cache_lifetime";i:2629744;s:9:"throw_404";b:0;s:7:"\0*\0_ctx";N;}'),
(9, 'page_pages', 'recent-entries', 'Recent entries', '', '2013-03-26 10:24:43', 'O:23:"Model_Widget_Page_Pages":17:{s:8:"\0*\0_data";a:4:{s:11:"list_offset";i:0;s:9:"list_size";i:5;s:7:"page_id";s:1:"8";s:8:"continue";s:0:"";}s:2:"id";s:1:"9";s:4:"type";s:10:"page_pages";s:4:"name";s:14:"Recent entries";s:11:"description";s:0:"";s:6:"header";s:14:"Recent entries";s:8:"template";s:14:"recent-entries";s:16:"backend_template";N;s:17:"frontend_template";N;s:12:"use_template";b:1;s:15:"template_params";a:0:{}s:5:"block";N;s:6:"crumbs";b:0;s:7:"caching";b:1;s:14:"cache_lifetime";i:3600;s:9:"throw_404";b:0;s:7:"\0*\0_ctx";O:7:"Context":9:{s:8:"\0*\0_page";N;s:11:"\0*\0_request";N;s:12:"\0*\0_response";N;s:10:"\0*\0_crumbs";N;s:11:"\0*\0_widgets";a:0:{}s:14:"\0*\0_widget_ids";N;s:10:"\0*\0_blocks";a:0:{}s:10:"\0*\0_params";a:0:{}s:14:"\0*\0_injections";a:0:{}}}'),
(10, 'page_pages', 'recent-entries', 'Recent entries index page', '', '2013-03-26 10:27:49', 'O:23:"Model_Widget_Page_Pages":17:{s:8:"\0*\0_data";a:4:{s:11:"list_offset";i:1;s:9:"list_size";i:4;s:7:"page_id";s:1:"8";s:8:"continue";s:0:"";}s:2:"id";s:2:"10";s:4:"type";s:10:"page_pages";s:4:"name";s:25:"Recent entries index page";s:11:"description";s:0:"";s:6:"header";s:0:"";s:8:"template";s:14:"recent-entries";s:16:"backend_template";N;s:17:"frontend_template";N;s:12:"use_template";b:1;s:15:"template_params";a:0:{}s:5:"block";N;s:6:"crumbs";b:0;s:7:"caching";b:1;s:14:"cache_lifetime";i:3600;s:9:"throw_404";b:0;s:7:"\0*\0_ctx";O:7:"Context":9:{s:8:"\0*\0_page";N;s:11:"\0*\0_request";N;s:12:"\0*\0_response";N;s:10:"\0*\0_crumbs";N;s:11:"\0*\0_widgets";a:0:{}s:14:"\0*\0_widget_ids";N;s:10:"\0*\0_blocks";a:0:{}s:10:"\0*\0_params";a:0:{}s:14:"\0*\0_injections";a:0:{}}}'),
(11, 'page_pages', 'last-entry', 'last entry index page', '', '2013-03-26 10:28:58', 'O:23:"Model_Widget_Page_Pages":17:{s:8:"\0*\0_data";a:4:{s:11:"list_offset";i:0;s:9:"list_size";i:1;s:7:"page_id";s:1:"8";s:8:"continue";s:0:"";}s:2:"id";s:2:"11";s:4:"type";s:10:"page_pages";s:4:"name";s:21:"last entry index page";s:11:"description";s:0:"";s:6:"header";s:0:"";s:8:"template";s:10:"last-entry";s:16:"backend_template";N;s:17:"frontend_template";N;s:12:"use_template";b:1;s:15:"template_params";a:0:{}s:5:"block";N;s:6:"crumbs";b:0;s:7:"caching";b:1;s:14:"cache_lifetime";i:3600;s:9:"throw_404";b:0;s:7:"\0*\0_ctx";O:7:"Context":9:{s:8:"\0*\0_page";N;s:11:"\0*\0_request";N;s:12:"\0*\0_response";N;s:10:"\0*\0_crumbs";N;s:11:"\0*\0_widgets";a:0:{}s:14:"\0*\0_widget_ids";N;s:10:"\0*\0_blocks";a:0:{}s:10:"\0*\0_params";a:0:{}s:14:"\0*\0_injections";a:0:{}}}'),
(12, 'page_pages', 'recent-entries-rss', 'Recent entries RSS', '', '2013-03-26 10:44:33', 'O:23:"Model_Widget_Page_Pages":17:{s:8:"\0*\0_data";a:4:{s:11:"list_offset";i:0;s:9:"list_size";i:10;s:7:"page_id";s:1:"8";s:8:"continue";s:0:"";}s:2:"id";s:2:"12";s:4:"type";s:10:"page_pages";s:4:"name";s:18:"Recent entries RSS";s:11:"description";s:0:"";s:6:"header";s:0:"";s:8:"template";s:18:"recent-entries-rss";s:16:"backend_template";N;s:17:"frontend_template";N;s:12:"use_template";b:1;s:15:"template_params";a:0:{}s:5:"block";N;s:6:"crumbs";b:0;s:7:"caching";b:1;s:14:"cache_lifetime";i:3600;s:9:"throw_404";b:0;s:7:"\0*\0_ctx";O:7:"Context":9:{s:8:"\0*\0_page";N;s:11:"\0*\0_request";N;s:12:"\0*\0_response";N;s:10:"\0*\0_crumbs";N;s:11:"\0*\0_widgets";a:0:{}s:14:"\0*\0_widget_ids";N;s:10:"\0*\0_blocks";a:0:{}s:10:"\0*\0_params";a:0:{}s:14:"\0*\0_injections";a:0:{}}}');

SET FOREIGN_KEY_CHECKS=1;