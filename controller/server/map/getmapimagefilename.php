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

$eqid = GetDef('id');

$sql = "SELECT * FROM org WHERE id = '$eqid'";
$result = $sqlcn->ExecuteSQL($sql)
		or die('Не могу выбрать список фото! ' . mysqli_error($sqlcn->idsqlconnection));
$photo = '';
while ($row = mysqli_fetch_array($result)) {
	$photo = $row['picmap'];
}

echo ($photo != '') ? $photo : 'null';
