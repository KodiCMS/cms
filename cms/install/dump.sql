SET FOREIGN_KEY_CHECKS=0;

INSERT INTO `TABLE_PREFIX_pages` (`id`, `title`, `slug`, `breadcrumb`, `keywords`, `description`, `parent_id`, `layout_file`, `behavior_id`, `status_id`, `created_on`, `published_on`, `updated_on`, `created_by_id`, `updated_by_id`, `position`, `needs_login`) VALUES
(1, 'Home', '', 'Home', '', '', 0, 'normal', '', 100, '__DATE__', '__DATE__', '__DATE__', 1, 1, 0, 0),
(2, 'Page not found', 'page-not-found', 'Page not found', '', '', 1, '', 'page_not_found', 101, '__DATE__', '__DATE__', '__DATE__', 1, 1, 0, 2),
(3, 'About us', 'about-us', 'About us', '', '', 1, '', '', 100, '2__DATE__', '__DATE__', '__DATE__', NULL, 1, 3, 2),
(4, 'RSS Feed', 'rss.xml', 'RSS Feed', '', '', 1, 'none', '', 101, '__DATE__', '__DATE__', '__DATE__', 1, 1, 1, 2),
(5, 'My first article', 'my-first-article', 'My first article', '', '', 8, '', '', 100, '__DATE__', '__DATE__', NULL, NULL, NULL, NULL, 2),
(6, '%B %Y archive', 'b-y-archive', '%B %Y archive', '', '', 8, '', 'archive_month_index', 101, '__DATE__', '__DATE__', '__DATE__', 1, 1, 1, 2),
(7, 'My second article', 'my-second-article', 'My second article', '', '', 8, '', '', 100, '__DATE__', '__DATE__', NULL, NULL, NULL, NULL, 2),
(8, 'Articles', 'articles', 'Articles', '', '', 1, '', 'archive', 100, '__DATE__', '__DATE__', '__DATE__', NULL, 1, 2, 2);

INSERT INTO `TABLE_PREFIX_page_parts` (`id`, `name`, `filter_id`, `content`, `content_html`, `page_id`, `is_protected`) VALUES
(1, 'body', 'codemirror', '<?php $page_article = $page->find(''/articles/''); ?>\n<?php $last_article = $page_article->children(array(''limit'' => 1, ''order'' => ''page.created_on DESC'')); ?>\n\n<br />\n\n<div class="first entry">\n	<h2><?php echo $last_article->link(); ?></h2>\n	<?php echo $last_article->content(); ?>\n	<?php if ($last_article->hasContent(''extended'')) echo $last_article->link(''Continue Reading&#8230;''); ?>\n	<p class="info">Posted by <?php echo $last_article->author(); ?> on <?php echo $last_article->date(); ?></p>\n</div>\n\n<br />\n\n<?php foreach ($page_article->children(array(''limit'' => 4, ''offset'' => 1, ''order'' => ''page.created_on DESC'')) as $article): ?>\n<div class="entry">\n	<h3><?php echo $article->link(); ?></h3>\n	<?php echo $article->content(); ?>\n	<?php if ($article->hasContent(''extended'')) echo $article->link(''Continue Reading&#8230;''); ?>\n	<p class="info">Posted by <?php echo $article->author(); ?> on <?php echo $article->date(); ?></p>\n</div>\n<?php endforeach; ?>\n', '<?php $page_article = $page->find(''/articles/''); ?>\n<?php $last_article = $page_article->children(array(''limit'' => 1, ''order'' => ''page.created_on DESC'')); ?>\n\n<br />\n\n<div class="first entry">\n	<h2><?php echo $last_article->link(); ?></h2>\n	<?php echo $last_article->content(); ?>\n	<?php if ($last_article->hasContent(''extended'')) echo $last_article->link(''Continue Reading&#8230;''); ?>\n	<p class="info">Posted by <?php echo $last_article->author(); ?> on <?php echo $last_article->date(); ?></p>\n</div>\n\n<br />\n\n<?php foreach ($page_article->children(array(''limit'' => 4, ''offset'' => 1, ''order'' => ''page.created_on DESC'')) as $article): ?>\n<div class="entry">\n	<h3><?php echo $article->link(); ?></h3>\n	<?php echo $article->content(); ?>\n	<?php if ($article->hasContent(''extended'')) echo $article->link(''Continue Reading&#8230;''); ?>\n	<p class="info">Posted by <?php echo $article->author(); ?> on <?php echo $article->date(); ?></p>\n</div>\n<?php endforeach; ?>\n', 1, 0),
(2, 'body', 'redactor', '<h3>Sorry</h3>\n\n<p>This page not found<br>\n</p>\n', '<h3>Sorry</h3>\n\n<p>This page not found<br>\n</p>\n', 2, 0),
(3, 'body', 'redactor', '<p>This is my site. I live in this city ... I do some nice things, like this and that ... <br></p>', '<p>This is my site. I live in this city ... I do some nice things, like this and that ... <br></p>', 3, 0),
(4, 'sidebar', 'codemirror', '<h3>About Me</h3>\n\n<p>I''m just a demonstration of how easy it is to use KoDi CMS to power a blog. \n	<?php echo HTML::anchor(''about-us'', ''more ...''); ?></p>\n\n<h3>Favorite Sites</h3>\n<ul>\n	<li>\n		<?php echo HTML::anchor(''https://github.com/butschster/flexocms'', ''KoDi CMS''); ?>\n	</li>\n</ul>\n\n<?php if ( URL::match( ''/'' ) ): ?>\n	<h3>Recent Entries</h3>\n	<?php $page_article = $page->find( ''/articles/'' ); ?>\n	<?php if($page_article): ?>\n	<ul>\n		<?php foreach ( $page_article->children( array( ''limit'' => 10, ''order'' => ''page.created_on DESC'' ) ) as $article ): ?>\n			<li><?php echo $article->link(); ?></li> \n		<?php endforeach; ?>\n	</ul>\n	<?php endif; ?>\n<?php endif; ?>\n\n<?php echo HTML::anchor(''articles'', ''Archives''); ?>\n\n<h3>Syndicate</h3>\n\n<?php echo HTML::anchor(''rss.xml'', ''Articles RSS Feed''); ?>', '<h3>About Me</h3>\n\n<p>I''m just a demonstration of how easy it is to use KoDi CMS to power a blog. \n	<?php echo HTML::anchor(''about-us'', ''more ...''); ?></p>\n\n<h3>Favorite Sites</h3>\n<ul>\n	<li>\n		<?php echo HTML::anchor(''https://github.com/butschster/flexocms'', ''KoDi CMS''); ?>\n	</li>\n</ul>\n\n<?php if ( URL::match( ''/'' ) ): ?>\n	<h3>Recent Entries</h3>\n	<?php $page_article = $page->find( ''/articles/'' ); ?>\n	<?php if($page_article): ?>\n	<ul>\n		<?php foreach ( $page_article->children( array( ''limit'' => 10, ''order'' => ''page.created_on DESC'' ) ) as $article ): ?>\n			<li><?php echo $article->link(); ?></li> \n		<?php endforeach; ?>\n	</ul>\n	<?php endif; ?>\n<?php endif; ?>\n\n<?php echo HTML::anchor(''articles'', ''Archives''); ?>\n\n<h3>Syndicate</h3>\n\n<?php echo HTML::anchor(''rss.xml'', ''Articles RSS Feed''); ?>', 1, 0),
(5, 'body', 'codemirror', '<?php $article = $page->find(''/articles/''); ?>\n<?php $archives = $article->archive->archivesByMonth(); ?>\n\n<h3>Archives By Month</h3>\n<ul>\n	<?php foreach ($archives as $date): ?>\n	<li><a href="<?php echo BASE_URL . $page->url .''/''. $date . URL_SUFFIX; ?>"><?php echo strftime(''%B %Y'', strtotime(strtr($date, ''/'', ''-''))); ?></a></li>\n	<?php endforeach; ?>\n</ul>', '<?php $article = $page->find(''/articles/''); ?>\n<?php $archives = $article->archive->archivesByMonth(); ?>\n\n<h3>Archives By Month</h3>\n<ul>\n	<?php foreach ($archives as $date): ?>\n	<li><a href="<?php echo BASE_URL . $page->url .''/''. $date . URL_SUFFIX; ?>"><?php echo strftime(''%B %Y'', strtotime(strtr($date, ''/'', ''-''))); ?></a></li>\n	<?php endforeach; ?>\n</ul>', 8, 0),
(6, 'body', 'redactor', '<p>&nbsp;My first test of my first article.</p>', '<p>&nbsp;My first test of my first article.</p>', 5, 0),
(7, 'body', 'redactor', '<p>&nbsp;This is my second article.</p>', '<p>&nbsp;This is my second article.</p>', 7, 0),
(8, 'body', 'codemirror', '<?php $archives = $page->archive->get(); ?>\n<?php foreach ($archives as $archive): ?>\n<div class="entry">\n	<h3><?php echo $archive->link(); ?></h3>\n	<p class="info">Posted by <?php echo $archive->author(); ?> on <?php echo $archive->date(); ?> \n	</p>\n</div>\n<?php endforeach; ?>', '<?php $archives = $page->archive->get(); ?>\n<?php foreach ($archives as $archive): ?>\n<div class="entry">\n	<h3><?php echo $archive->link(); ?></h3>\n	<p class="info">Posted by <?php echo $archive->author(); ?> on <?php echo $archive->date(); ?> \n	</p>\n</div>\n<?php endforeach; ?>', 6, 0),
(9, 'body', 'codemirror', '<?php echo ''<?''; ?>xml version="1.0" encoding="UTF-8"<?php echo ''?>''; ?> \n<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">\n	<channel>\n		<title>KoDi CMS</title>\n		<link><?php echo BASE_URL ?></link>\n		<atom:link href="<?php echo BASE_URL ?>/rss.xml" rel="self" type="application/rss+xml" />\n		<language>en-us</language>\n		<copyright>Copyright <?php echo date(''Y''); ?>, madebyfrog.com</copyright>\n		<pubDate><?php echo strftime(''%a, %d %b %Y %H:%M:%S %z''); ?></pubDate>\n		<lastBuildDate><?php echo strftime(''%a, %d %b %Y %H:%M:%S %z''); ?></lastBuildDate>\n		<category>any</category>\n		<generator>KoDi CMS</generator>\n		<description>The main news feed from KoDi CMS.</description>\n		<docs>http://www.rssboard.org/rss-specification</docs>\n		<?php $articles = $page->find(''/articles/''); ?>\n		<?php foreach ($articles->children(array(''limit'' => 10, ''order'' => ''page.created_on DESC'')) as $article): ?>\n		<item>\n			<title><?php echo $article->title(); ?></title>\n			<description><![CDATA[<?php if ($article->hasContent(''summary'')) { echo $article->content(''summary''); } else { echo strip_tags($article->content()); } ?>]]></description>\n			<pubDate><?php echo $article->date(''%a, %d %b %Y %H:%M:%S %z''); ?></pubDate>\n			<link><?php echo $article->url(); ?></link>\n			<guid><?php echo $article->url(); ?></guid>\n		</item>\n		<?php endforeach; ?>\n	</channel>\n</rss>', '<?php echo ''<?''; ?>xml version="1.0" encoding="UTF-8"<?php echo ''?>''; ?> \n<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">\n	<channel>\n		<title>KoDi CMS</title>\n		<link><?php echo BASE_URL ?></link>\n		<atom:link href="<?php echo BASE_URL ?>/rss.xml" rel="self" type="application/rss+xml" />\n		<language>en-us</language>\n		<copyright>Copyright <?php echo date(''Y''); ?>, madebyfrog.com</copyright>\n		<pubDate><?php echo strftime(''%a, %d %b %Y %H:%M:%S %z''); ?></pubDate>\n		<lastBuildDate><?php echo strftime(''%a, %d %b %Y %H:%M:%S %z''); ?></lastBuildDate>\n		<category>any</category>\n		<generator>KoDi CMS</generator>\n		<description>The main news feed from KoDi CMS.</description>\n		<docs>http://www.rssboard.org/rss-specification</docs>\n		<?php $articles = $page->find(''/articles/''); ?>\n		<?php foreach ($articles->children(array(''limit'' => 10, ''order'' => ''page.created_on DESC'')) as $article): ?>\n		<item>\n			<title><?php echo $article->title(); ?></title>\n			<description><![CDATA[<?php if ($article->hasContent(''summary'')) { echo $article->content(''summary''); } else { echo strip_tags($article->content()); } ?>]]></description>\n			<pubDate><?php echo $article->date(''%a, %d %b %Y %H:%M:%S %z''); ?></pubDate>\n			<link><?php echo $article->url(); ?></link>\n			<guid><?php echo $article->url(); ?></guid>\n		</item>\n		<?php endforeach; ?>\n	</channel>\n</rss>', 4, 0);

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
('admin_title', '__SITE_NAME__'),
('language', '__LANG__'),
('default_status_id', '100'),
('default_filter_id', ''),
('default_tab', 'page'),
('allow_html_title', 'off'),
('profiling', 'no'),
('debug', 'no'),
('plugins', 'a:4:{s:7:"archive";i:1;s:14:"page_not_found";i:1;s:8:"redactor";i:1;s:10:"codemirror";i:1;}');

INSERT INTO `TABLE_PREFIX_users` (`id`, `email`, `username`, `name`, `password`, `logins`, `last_login`) VALUES
(1, '__EMAIL__', '__USERNAME__', 'Administrator', '__ADMIN_PASSWORD__', 0, 0);

SET FOREIGN_KEY_CHECKS=1;