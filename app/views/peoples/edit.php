<?php
/*
 * WebUseOrg3 - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчик: Грибов Павел
 * Сайт: http://грибовы.рф
 */
/*
 * Inventory - учёт оргтехники в организации
 * Лицензия: GPL-3.0
 * Разработчик: Сергей Солодягин (solodyagin@gmail.com)
 */

namespace app\views;

use core\baseuser;
use core\config;
use core\request;
use core\utils;

$cfg = config::getInstance();

$req = request::getInstance();
$id = $req->get('id');

$tmpuser = new baseuser();
$tmpuser->getById($id);
$orgid = $tmpuser->orgid;
$login = $tmpuser->login;
$email = $tmpuser->email;
$mode = $tmpuser->mode;
unset($tmpuser);
?>
<!DOCTYPE html>
<html lang="ru-RU">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<base href="<?= $cfg->rewrite_base; ?>">
		<!--FontAwesome-->
		<link rel="stylesheet" href="public/css/all.min.css">
		<!--jQuery-->
		<script src="public/js/jquery-1.11.0.min.js"></script>
		<!--Bootstrap-->
		<link rel="stylesheet" href="public/themes/<?= $cfg->theme; ?>/bootstrap.min.css">
		<script src="public/js/bootstrap.min.js"></script>
		<!--Localisation assistance for jQuery-->
		<script src="public/js/plugins/localisation/jquery.localisation-min.js"></script>
		<!--jQuery Form Plugin-->
		<script src="public/js/jquery.form.js"></script>
		<!--jqGrid-->
		<link rel="stylesheet" href="public/css/ui.jqgrid-bootstrap.css">
		<script src="public/js/i18n/grid.locale-ru.js"></script>
		<script src="public/js/jquery.jqGrid.min.js"></script>
		<!--Select2-->
		<link rel="stylesheet" href="public/css/select2.min.css">
		<link rel="stylesheet" href="public/css/select2-bootstrap.min.css">
		<script src="public/js/select2.full.min.js"></script>
		<!--PassGen-->
		<script src="public/js/jquery.passgen.min.js"></script>
		<style>
			#pass_gen, #pass_show {
				font: initial;
			}
		</style>
		<script>
			$(function () {
				var fields = ['login', 'email'];

				$('form').submit(function () {
					var $form = $(this),
							error = false;
					$form.find(':input').each(function () {
						var $input = $(this);
						for (var i = 0; i < fields.length; i++) {
							if ($input.attr('name') === fields[i]) {
								if (!$input.val()) {
									error = true;
									$input.parent().addClass('has-error');
								} else {
									$input.parent().removeClass('has-error');
								}
							}
						}
					});
					if (error) {
						$('#messenger').addClass('alert alert-danger').html('Не все обязательные поля заполнены!').fadeIn('slow');
						return false;
					}
					return true;
				});

				$('#myForm').ajaxForm(function (msg) {
					if (msg !== 'ok') {
						$('#messenger').html(msg);
					} else {
						if (window.top) {
							window.top.$('#bmd_iframe').modal('hide');
							window.top.$('#grid1').jqGrid().trigger('reloadGrid');
						}
					}
				});

				$('#pass_gen').click(function () {
					$('#pass').val($.passGen());
				});

				$('#pass_show').click(function () {
					var $btn = $(this);
					$btn.toggleClass('active');
					if ($btn.hasClass('active')) {
						$btn.find('i').removeClass('fa-eye-slash').addClass('fa-eye');
						$btn.closest('.input-group').find('input').prop('type', 'text');
					} else {
						$btn.find('i').removeClass('fa-eye').addClass('fa-eye-slash');
						$btn.closest('.input-group').find('input').prop('type', 'password');
					}
				});

				$('.select2').select2({
					theme: 'bootstrap',
					width: '100%'
				});
			});
		</script>
	</head>
	<body style="font-size:<?= $cfg->fontsize; ?>;">
		<form id="myForm" enctype="multipart/form-data" action="route/deprecated/server/users/libre_users_form.php?step=edit&id=<?= $id; ?>" method="post">
			<div class="form-group">
				<label class="control-label">Организация:</label>
				<select class="form-control select2" name="orgid" id="orgid">
					<?php
					$orgs = utils::getArrayOrgs();
					for ($i = 0; $i < count($orgs); $i++) {
						$oid = $orgs[$i]['id'];
						$sl = ($oid == $cfg->defaultorgid) ? 'selected' : '';
						echo "<option value=\"$oid\" $sl>{$orgs[$i]['name']}</option>";
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<label class="control-label">Роль:</label>
				<select name="mode" id="mode" class="form-control select2">
					<option value="0" <?= ($mode == 0) ? 'selected' : ''; ?>>Пользователь</option>
					<option value="1" <?= ($mode == 1) ? 'selected' : ''; ?>>Администратор</option>
				</select>
			</div>
			<div class="form-group">
				<label class="control-label">Логин:</label>
				<input class="form-control" placeholder="Логин" name="login" id="login" value="<?= $login; ?>">
			</div>
			<div class="form-group">
				<label class="control-label">Пароль:</label>
				<div class="input-group">
					<input type="password" class="form-control" placeholder="Пароль" name="pass" id="pass" value="">
					<span class="input-group-btn">
						<button type="button" class="btn btn-default" id="pass_gen"><i class="fas fa-dice"></i></button>
						<button type="button" class="btn btn-default" id="pass_show"><i class="fas fa-eye-slash"></i></button>
					</span>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label">Почта:</label>
				<input class="form-control" placeholder="Email" name="email" id="email" size="16" value="<?= $email; ?>">
			</div>
			<div class="form-group">
				<button class="btn btn-primary" type="submit">Сохранить</button>
			</div>
		</form>
		<div id="messenger"></div>
	</body>
</html>