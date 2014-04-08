INSERT INTO `__TABLE_PREFIX__roles` (`id`, `name`, `description`) VALUES
(1, 'login', 'Login privileges, granted after account confirmation'),
(2, 'administrator', 'Administrative user, has access to everything.'),
(3, 'developer', 'Developers role'),
(4, 'editor', '');

INSERT INTO `__TABLE_PREFIX__roles_users` (`user_id`, `role_id`) VALUES
(1, 1),
(1, 2);

INSERT INTO `__TABLE_PREFIX__users` (`id`, `email`, `username`, `password`, `logins`, `last_login`) VALUES
(1, '__EMAIL__', '__USERNAME__', '__ADMIN_PASSWORD__', 0, 0);

INSERT INTO `__TABLE_PREFIX__user_profiles` (`id`, `name`, `user_id`, `locale`, `created_on`) VALUES
(1, 'Administrator', 1, '__LANG__', '__DATE__');

INSERT INTO `__TABLE_PREFIX__page_roles` (`page_id`, `role_id`) VALUES
(1, 2),
(2, 2);