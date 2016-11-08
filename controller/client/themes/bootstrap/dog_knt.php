<?php
/*
 * Данный код создан и распространяется по лицензии GPL v3
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 *   (добавляйте себя если что-то делали)
 * http://грибовы.рф
 */

/*
 * Инструменты / Контроль договоров
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

// Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (($user->mode != 1) && (!$user->TestRoles('1,3,4,5,6'))):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Инструменты / Контроль договоров"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ", "Просмотр", "Добавление", "Редактирование", "Удаление".
	</div>

<?php else: ?>

	<div class="container-fluid">
		<div class="row-fluid">
			<table id="list2"></table>
			<div id="pager2"></div>            
			<div id="info_contract">
			</div>
		</div>
	</div>
	<script src="controller/client/js/contract_control.js"></script>    

<?php endif;
