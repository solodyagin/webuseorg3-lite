<?php

// Данный код создан и распространяется по лицензии GPL v3
// Разработчики:
//   Грибов Павел,
//   Сергей Солодягин (solodyagin@gmail.com)
//   (добавляйте себя если что-то делали)
// http://грибовы.рф

define('INCLUDED', true);
define('ROOT', dirname(__FILE__));

$time_start = microtime(true); // Засекаем время начала выполнения скрипта

// Загружаем первоначальные настройки. Если не получилось - запускаем инсталлятор
$rez = @include_once(ROOT.'/config.php');
if ($rez == false) {
	include_once(ROOT.'/install.php');
	die();
}

// Загружаем классы
include_once(ROOT.'/class/sql.php'); // Класс работы с БД
include_once(ROOT.'/class/config.php'); // Класс настроек
include_once(ROOT.'/class/users.php'); // Класс работы с пользователями
// Получаем маршрут
if (isset($_GET['route'])) {
	$route = $_GET['route'];
	if (is_file(ROOT.$route)) {
		// Загружаем классы
		include_once(ROOT.'/class/employees.php'); // Класс работы с профилем пользователя
		// Загружаем все что нужно для работы движка
		include_once(ROOT.'/inc/connect.php'); // Соединяемся с БД, получаем $mysql_base_id
		include_once(ROOT.'/inc/config.php'); // Подгружаем настройки из БД, получаем заполненый класс $cfg
		include_once(ROOT.'/inc/functions.php'); // Загружаем функции
		include_once(ROOT.'/inc/login.php'); // Создаём пользователя $user
		// Разрешаем доступ только выполнившим вход пользователям
		if ($user->id == '') {
			die('Доступ ограничен');
		}
		include_once(ROOT.$route);
	} else {
		die('На сервере отсутствует указанный путь');
	}
} else {    
	// Загружаем классы
	include_once(ROOT.'/class/mod.php'); // Класс работы с модулями
	include_once(ROOT.'/class/cconfig.php'); // Класс работы с пользовательскими настройками
	include_once(ROOT.'/class/bp.php'); // Класс работы с БП
	include_once(ROOT.'/class/class.phpmailer.php'); // Класс управления почтой
	include_once(ROOT.'/class/menu.php'); // Класс работы с меню

	// Загружаем все что нужно для работы движка
	include_once(ROOT.'/inc/connect.php'); // Соединяемся с БД, получаем $mysql_base_id
	include_once(ROOT.'/inc/config.php'); // Подгружаем настройки из БД, получаем заполненый класс $cfg
	include_once(ROOT.'/inc/functions.php'); // Загружаем функции
	include_once(ROOT.'/inc/login.php'); // Проверяем вход пользователя

	include_once(ROOT.'/inc/autorun.php'); // Запускаем сторонние скрипты

	// Инициализируем заполнение меню
	$gmenu = new Tmenu();
	$gmenu->GetFromFiles(ROOT.'/inc/menu');

	$content_page = (isset($_GET['content_page'])) ? $_GET['content_page'] : 'home';

	// Загружаем и выполняем сначала /modules/$content_page.php, затем /controller/client/themes/$cfg->theme/$content_page.php
	// Если таких файлов нет, то принудительно выполняем только /controller/client/themes/$cfg->theme/home.php
	if (!is_file(ROOT."/controller/client/themes/$cfg->theme/$content_page.php")) {
		$content_page = 'home';
		$err[] = 'Вы попытались открыть несуществующий раздел!';
	}
	if (!is_file(ROOT."/modules/$content_page.php")) {
		include_once(ROOT.'/modules/home.php');
	} else {
		include_once(ROOT."/modules/$content_page.php");
	}
        $zz="/controller/client/themes/$cfg->theme/$content_page.php";        
	// Загружаем главный файл темы, который разруливает что отображать на экране
	include_once(ROOT."/controller/client/themes/$cfg->theme/index.php");

	// Запускаем сторонние скрипты
	include_once(ROOT.'/inc/footerrun.php');

	unset($gmenu);
}
?>