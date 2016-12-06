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

$id = GetDef('groupid', '1');
$vid = GetDef('vendorid', '1');
$nomeid = GetDef('nomeid');

echo '<select class="chosen-select" name="snomeid" id="snomeid">';

$sql = 'SELECT id, name FROM nome WHERE groupid = :id AND vendorid = :vid';
try {
	$arr = DB::prepare($sql)->execute(array(':id' => $id, ':vid' => $vid))->fetchAll();
	foreach ($arr as $row) {
		$rid = $row['id'];
		$rname = $row['name'];
		$sl = ($rid == $nomeid) ? 'selected' : '';
		echo "<option value=\"$rid\" $sl>$rname</option>";
	}
} catch (PDOException $ex) {
	throw new DBException('Не могу выбрать список номенклатуры', 0, $ex);
}

echo '</select>';
