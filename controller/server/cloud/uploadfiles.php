<?php

/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

// Проверяем: может ли пользователь добавлять файлы?
(($user->mode == 1) || $user->TestRoles('1,4')) or die('Недостаточно прав');

$selectedkey = PostDef('selectedkey');
$orig_file = $_FILES['filedata']['name'];
$dis = array('.htaccess'); // Запрещённые для загрузки файлы

$rs = array('msg' => 'error'); // Ответ по умолчанию, если пойдёт что-то не так

if (!in_array($orig_file, $dis)) {
	$userfile_name = GetRandomId(8) . '.' . pathinfo($orig_file, PATHINFO_EXTENSION);
	$src = $_FILES['filedata']['tmp_name'];
	$dst = WUO_ROOT . '/files/' . $userfile_name;
	$res = move_uploaded_file($src, $dst);
	if ($res) {
		$rs['msg'] = $userfile_name;
		$sz = filesize($dst);
		if ($selectedkey != '') {
			$sql = <<<TXT
INSERT INTO cloud_files
            (id,cloud_dirs_id,title,filename,dt,sz)
VALUES      (NULL, :selectedkey, :orig_file, :userfile_name, NOW(), :sz)
TXT;
			try {
				DB::prepare($sql)->execute(array(
					':selectedkey' => $selectedkey,
					':orig_file' => $orig_file,
					':userfile_name' => $userfile_name,
					':sz' => $sz
				));
			} catch (PDOException $ex) {
				throw new DBException('Не могу добавить файл', 0, $ex);
			}
		}
	}
}

jsonExit($rs);
