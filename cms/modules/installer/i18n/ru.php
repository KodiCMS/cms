<?php defined( 'SYSPATH' ) or die( 'No direct access allowed.' );

return array(
	/********************* Database information *******************/
	':cms_name &rsaquo; installation' => 'Установка системы :cms_name',
	'Database information' => 'Информация о базе данных',
	'Database driver' => 'Драйвер',
	'Database server' => 'Сервер',
	'Database port' => 'Порт',
	'Database user' => 'Имя пользователя',
	'Database password' => 'Пароль',
	
	'Database name' => 'Имя базы данных',
	'Prefix' => 'Префикс',
	'You have to create a database manually and enter its name here.'
			=> 'При создании базы данных, вы дали ей имя, укажите его здесь.',
	
	'If there is no database password, leave it blank.'
			=> 'Если у базы данных нет пароля &mdash; оставьте пустым.',
	
	'Usefull to prevent conflicts if you have, or plan to have, multiple :cms installations with a single database.'
			=> 'Укажите префикс, если хотите установить несколько версий :cms в одну базу данных.',
	
	/********************* Other information *******************/
	'Site information' => 'Настройки',
	'Administrator username' => 'Имя пользователя администратора',
	
	'Administrator email' => 'E-mail адрес',
	'Generate password' => 'Сгенерировать пароль автоматически',
	'Admin dir name' => 'Точка входа в административный интерфейс',
	'URL suffix' => 'Окончание URL адреса',
	'Add a suffix to simulate static html files.'
			=> 'Можно указать суффикс для эмуляции статических html файлов.',
	'Timezone' => 'Временная зона',
	'Cache system' => 'Кеширование',
	'Cache type' => 'Тип кеширования',
	'Install demo data' => 'Установить демо контент',
	'Demo site' => 'Демо сайт',
	'Language' => 'Язык',
	'Current language' => 'Текущий язык',

	/********************* Environment *******************/
	'Environment Tests' => 'Проверка окружения',
	'Pass' => 'Успешно',
	'PHP Version' => 'Версия PHP',
	'Kohana requires PHP 5.3.3 or newer, this version is :version.'
		=> 'Kohana требуется версия PHP 5.3.3 или выше, текущая версия :version.',
	'System Directory' => 'Путь до директории system',
	'Application Directory' => 'Путь до директории application',
	'Cache Directory' => 'Путь до директории cache',
	'Logs Directory' => 'Путь до директории logs',
	'Config file placement' => 'Расположение конфиг файла',
	'To change config file placement edit index.php file' => 'Дли изменения пути расположения конфиг файла, необходимо редактировать index.php',
	'The config :dir directory does not exist or config file is exists.' => 'Указанный путь до директории :dir не существует или файл (:file) с таким именем уже существует.',
	'The configured <code>system</code> directory does not exist or does not contain required files.'
		=> 'Указанный путь до директории :dir не существует или не содержит необходимых файлов.',
	'The :dir directory is not writable.' => 'Директория защищена от записи.',
	
	'Kohana may not work correctly with your environment.' 
		=> 'Kohana не может корректно работать в вашем окружении.',
	'Your environment passed all requirements.' 
		=> 'Ваше окружение прошло все требования.',
	'Optional Tests' => 'Опционально',
	'The following extensions are not required to run the Kohana core, but if enabled can provide access to additional classes.'
		=> 'Следующие расширения не требуются для работы основных компонентов Kohana, но если включены, могут обеспечить доступ к дополнительным классам.',
	'Kohana can use the :extension extension for the :class class.' 
		=> 'Kohana может использовать расширение: :extension для класса :class',
	'Kohana requires :extension for the :class class'
		=> 'Kohana требуется расширение :extension для класса :class',
	'Kohana can use the :extension to support additional databases.'
		=> 'Kohana может использовать расширение: :extension для поддержки доболнительных БД',
	
	
	/******************** Install  *************************/
	'Install now!' => 'Установить!',
	'No install data!' => 'Отсутсвуют данные!',
	'KodiCMS installed succefully' => 'KodiCMS успешно установлена',
	'Login: :login' => 'Ваш логин: :login',
	'Password: :password' => 'Ваш пароль: :password',
	'Database schema file :file not found!' 
		=> 'Файл :file схемы Базы данных не найден!',
	'Database dump file :file not found!' 
		=> 'Файл :file с дампом Базы данных не найден!',
	'Config template file :file not found!'
		=> 'Шаблон конфиг файла :file не найден!',
	'The config :dir directory must be writable.' 
		=> 'Директория :dir расположения конфиг файла должна быть доступна для записи',
	'Can\'t write config.php file!'
		=> 'Невозможно записать данные в файл config.php!',
	'Below you should enter your database connection details. If you’re not sure about these, contact your host.'
		=> 'Ниже вы должны ввести данные подключения к вашей БД. Если вы их не знаете, обратитесь к вашему хостеру.'
);