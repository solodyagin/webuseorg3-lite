<?php

/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

$newsid = (isset($_GET['id'])) ? $_GET['id'] : '1';

if ($newsid != '') {
	$sql = 'SELECT * FROM news WHERE id= :newsid';
	try {
		$row = DB::prepare($sql)->execute(array(':newsid' => $newsid))->fetch();
		if ($row) {
			$news_dt = $row['dt'];
			$news_title = $row['title'];
			$news_body = $row['body'];
		}
	} catch (PDOException $ex) {
		throw new DBException('Не могу выбрать список новостей!', 0, $ex);
	}
}
