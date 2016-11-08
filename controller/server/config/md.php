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

$page = GetDef('page', '1');
$limit = GetDef('rows');
$sidx = GetDef('sidx', '1');
$sord = GetDef('sord');
$oper = PostDef('oper');
$id = PostDef('id');
$active = PostDef('active');

if ($oper == '') {
	// Проверяем может ли пользователь просматривать?
	(($user->mode == 1) || $user->TestRoles('1,3,4,5,6')) or die('Недостаточно прав');
	$result = $sqlcn->ExecuteSQL("SELECT COUNT(*) AS cnt FROM config_common WHERE nameparam LIKE 'modulename_%'");
	$row = mysqli_fetch_array($result);
	$count = $row['cnt'];
	$total_pages = ($count > 0) ? ceil($count / $limit) : 0;
	if ($page > $total_pages) {
		$page = $total_pages;
	}
	$start = $limit * $page - $limit;
	$responce = new stdClass();
	if ($start < 0) {
		$responce->page = 0;
		$responce->total = 0;
		$responce->records = 0;
		jsonExit($responce);
	}
	$sql = <<<TXT
SELECT    t1.id `id`,
          SUBSTR(t1.nameparam, 12) AS `name`,
          t2.valueparam `comment`,
          t3.valueparam `copy`,
          t1.valueparam `active`
FROM      config_common t1
LEFT JOIN config_common t2
ON        (
                    SUBSTR(t2.nameparam, 15) = SUBSTR(t1.nameparam, 12)
          AND       t2.nameparam LIKE "modulecomment_%" )
LEFT JOIN config_common t3
ON        (
                    SUBSTR(t3.nameparam, 12) = SUBSTR(t1.nameparam, 12)
          AND       t3.nameparam LIKE "modulecopy_%" )
WHERE     t1.nameparam LIKE "modulename_%"
ORDER BY  $sidx $sord
LIMIT     $start, $limit
TXT;
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не могу выбрать список модулей!' . mysqli_error($sqlcn->idsqlconnection));
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	$i = 0;
	while ($row = mysqli_fetch_array($result)) {
		$responce->rows[$i]['id'] = $row['id'];
		$responce->rows[$i]['cell'] = array($row['id'], $row['name'], $row['comment'], $row['copy'], $row['active']);
		$i++;
	}
	jsonExit($responce);
}

if ($oper == 'edit') {
	// Проверяем может ли пользователь редактировать?
	(($user->mode == 1) || $user->TestRoles('1,5')) or die('Недостаточно прав');
	$sql = "UPDATE config_common SET valueparam = '$active' WHERE id = '$id'";
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не могу обновить данные по модулю!' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}

if ($oper == 'del') {
	// Проверяем может ли пользователь удалять?
	(($user->mode == 1) || $user->TestRoles('1,6')) or die('Недостаточно прав');
	$result = $sqlcn->ExecuteSQL("SELECT * FROM config_common WHERE id = '$id'")
			or die('Не могу выбрать список модулей!' . mysqli_error($sqlcn->idsqlconnection));
	while ($row = mysqli_fetch_array($result)) {
		$modname = $row['nameparam'];
		$str1 = explode('_', $modname);
		$mc = 'modulecomment_' . $str1[1];
		$sqlcn->ExecuteSQL("DELETE FROM config_common WHERE nameparam = '$mc'")
				or die('Не могу выбрать список комментариев!' . mysqli_error($sqlcn->idsqlconnection));
		$mcopy = 'modulecopy_' . $str1[1];
		$sqlcn->ExecuteSQL("DELETE FROM config_common WHERE nameparam = '$mcopy'")
				or die('Не могу выбрать список авторов!' . mysqli_error($sqlcn->idsqlconnection));
	}
	$sqlcn->ExecuteSQL("DELETE FROM config_common WHERE id = '$id'")
			or die('Не могу выбрать список модулей!' . mysqli_error($sqlcn->idsqlconnection));
	exit;
}
