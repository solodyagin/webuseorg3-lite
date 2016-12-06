<?php
/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчики:
 *   Грибов Павел,
 *   Сергей Солодягин (solodyagin@gmail.com)
 * Сайт: http://грибовы.рф
 */

/*
 * Справочники / Список организаций
 */

// Запрещаем прямой вызов скрипта.
defined('WUO_ROOT') or die('Доступ запрещён');

// Проверка: если пользователь - не администратор и не назначена одна из ролей, то
if (($user->mode != 1) && (!$user->TestRoles('1'))):
	?>

	<div class="alert alert-danger">
		У вас нет доступа в раздел "Справочники / Список организаций"!<br><br>
		Возможно не назначена <a href="http://грибовы.рф/wiki/doku.php/основы:доступ:роли" target="_blank">роль</a>:
		"Полный доступ".
	</div>

<?php else: ?>

	<link rel="stylesheet" href="controller/client/themes/<?php echo $cfg->theme; ?>/css/upload.css">
	<script src="js/FileAPI/FileAPI.min.js"></script>
	<script src="js/FileAPI/FileAPI.exif.js"></script>
	<script src="js/jquery.fileapi.min.js"></script>
	<script src="js/jcrop/jquery.Jcrop.min.js"></script>
	<script src="js/statics/jquery.modal.js"></script>
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-md-12 col-sm-12">
				<table id="o_list"></table>
				<div id="o_pager"></div>
				<div id="simple-btn" class="btn btn-success js-fileapi-wrapper" style="visibility:hidden">
					<div class="js-browse">
						<span class="btn-txt">Загрузить схему в формате PNG</span>
						<input type="file" name="filedata">
					</div>
					<div class="js-upload" style="display: none">
						<div class="progress progress-success"><div class="js-progress bar"></div></div>
						<span class="btn-txt">Загрузка... (<span class="js-size"></span>)</span>
					</div>
				</div>
				<div id="pg_add_edit"></div>
			</div>
		</div>
	</div>
	<script src="controller/client/js/org_list.js"></script>

<?php endif;
