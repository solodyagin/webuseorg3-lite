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

$eqid = GetDef('eqid');
$step = GetDef('step');

if ($step == 'edit') {
	$sql = "SELECT * FROM repair WHERE id = '$eqid'";
	$result = $sqlcn->ExecuteSQL($sql)
			or die('Не получилось выбрать список ремонтов! ' . mysqli_error($sqlcn->idsqlconnection));
	while ($row = mysqli_fetch_array($result)) {
		$kntid = $row['kntid'];
		$cost = $row['cost'];
		$dtpost = MySQLDateTimeToDateTimeNoTime($row['dt']);
		echo "<script>dtpost='$dtpost';</script>";
		$dt = MySQLDateTimeToDateTimeNoTime($row['dtend']);
		echo "<script>dt='$dt';step='edit';</script>";
		$comment = $row['comment'];
		$status = $row['status'];
		$userfrom = $row['userfrom'];
		$userto = $row['userto'];
		$doc = $row['doc'];
	}
} else {
	$kntid = '-1';
	$cost = '0.0';
	$dtpost = '';
	echo "<script>dtpost='$dtpost';</script>";
	$dt = '';
	echo "<script>dt='$dt';step='add';</script>";
	$comment = '';
	$status = '1';
	$userfrom = '-1';
	$userto = '-1';
	$doc = '';
}
?>
<script>
	$(function () {
		var field = new Array('dtpost', 'dt', 'kntid');//поля обязательные
		$('form').submit(function () {// обрабатываем отправку формы
			var error = 0; // индекс ошибки
			$('form').find(':input').each(function () {// проверяем каждое поле в форме
				for (var i = 0; i < field.length; i++) { // если поле присутствует в списке обязательных
					if ($(this).attr('name') == field[i]) { //проверяем поле формы на пустоту
						if (!$(this).val()) {// если в поле пустое
							$(this).css('border', 'red 1px solid');// устанавливаем рамку красного цвета
							error = 1;// определяем индекс ошибки
						} else {
							$(this).css('border', 'gray 1px solid');// устанавливаем рамку обычного цвета
						}
					}
				}
			});
			if (error == 0) { // если ошибок нет то отправляем данные
				return true;
			} else {
				var err_text = 'Не все обязательные поля заполнены!';
				$('#messenger').addClass('alert alert-error');
				$('#messenger').html(err_text);
				$('#messenger').fadeIn('slow');
				return false; //если в форме встретились ошибки , не  позволяем отослать данные на сервер.
			}
		});
	});
	$(document).ready(function () {
		// навесим на форму 'myForm' обработчик отлавливающий сабмит формы и передадим функцию callback.
		$('#myForm').ajaxForm(function (msg) {
			if (msg != 'ok') {
				$('#messenger').html(msg);
			} else {
				$('#pg_add_edit').dialog('destroy');
				$('#pg_add_edit').html('');
				jQuery('#workmen').jqGrid().trigger('reloadGrid');
				jQuery('#tbl_rep').jqGrid().trigger('reloadGrid');
			}
		});
	});
</script>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="col-xs-12 col-md-12 col-sm-12">
			<div id="messenger"></div>
			<form role="form" id="myForm" enctype="multipart/form-data" action="index.php?route=/controller/server/equipment/service.php?step=<?php echo "$step"; ?>&eqid=<?php echo "$eqid" ?>" method="post" name="form1" target="_self">
				<label>Кто ремонтирует:</label>
				<div id="sorg1">
					<select class="chosen-select" name="kntid" id="kntid">
						<?php
						$morgs = GetArrayKnt();
						for ($i = 0; $i < count($morgs); $i++) {
							$nid = $morgs[$i]['id'];
							$sl = ($nid == $kntid) ? 'selected' : '';
							echo "<option value=\"$nid\" $sl>{$morgs[$i]['name']}</option>";
						}
						?>
					</select>
				</div>
				<div class="row-fluid">
					<div class="col-xs-6 col-md-6 col-sm-6">
						<label>Начало ремонта:</label>
						<input class="form-control" name="dtpost" id="dtpost" value="<?php echo $dtpost; ?>">
						<label>Конец ремонта:</label>
						<input class="form-control" name="dt" id="dt" value="<?php echo $dt; ?>">
						<label>Стоимость ремонта:</label>
						<input class="form-control" name="cst" id="cst" value="<?php echo $cost; ?>">
					</div>
					<div class="col-xs-6 col-md-6 col-sm-6">
						<label>Отправитель:</label>
						<div id="susers1">
							<select class="chosen-select"name="suserid1" id="suserid1">
								<option value="-1">Не выбрано</option>
								<?php
								$sql = <<<TXT
SELECT users.id,users.login,users_profile.fio
FROM   users
       INNER JOIN users_profile
               ON users.id = users_profile.usersid
WHERE  users.active = 1
ORDER  BY users.login
TXT;
								$result = $sqlcn->ExecuteSQL($sql)
										or die('Не могу выбрать список пользователей! ' . mysqli_error($sqlcn->idsqlconnection));
								while ($row = mysqli_fetch_array($result)) {
									$sl = ($row['id'] == $userfrom) ? 'selected' : '';
									echo "<option value=\"{$row['id']}\" $sl>{$row['fio']}</option>";
								}
								?>
							</select>
						</div>
						<label>Получатель:</label>
						<div id="susers2">
							<select class="chosen-select" name="suserid2" id="suserid2">
								<option value="-1">Не выбрано</option>
								<?php
								$sql = <<<TXT
SELECT users.id,users.login,users_profile.fio
FROM   users
       INNER JOIN users_profile
               ON users.id = users_profile.usersid
WHERE  users.active = 1
ORDER  BY users.login
TXT;
								$result = $sqlcn->ExecuteSQL($sql)
										or die('Не могу выбрать список пользователей! ' . mysqli_error($sqlcn->idsqlconnection));
								while ($row = mysqli_fetch_array($result)) {
									$sl = ($row['id'] == $userto) ? 'selected' : '';
									echo "<option value=\"{$row['id']}\" $sl>{$row['fio']}</option>";
								}
								?>
							</select>
						</div>
						<label>Статус:</label>
						<select class="form-control" name="status" id="status">
							<option value='1' <?php echo ($status == '1') ? 'selected' : ''; ?>>В сервисе</option>
							<option value='0' <?php echo ($status == '0') ? 'selected' : ''; ?>>Работает</option>
							<option value='2' <?php echo ($status == '2') ? 'selected' : ''; ?>>Есть заявка</option>
							<option value='3' <?php echo ($status == '3') ? 'selected' : ''; ?>>Списать</option>
						</select>
					</div>
				</div>
				<label>Документы:</label>
				<input class="form-control" name="doc" id="doc" size="14" class="span6" value="<?php echo $doc; ?>">
				<label>Комментарии:</label>
				<textarea class="form-control" name="comment"><?php echo $comment; ?></textarea>
				<div align="center">
					<input class="form-control" type="submit" name="Submit" value="Сохранить">
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	function UpdateChosen() {
		for (var selector in config) {
			$(selector).chosen({width: '100%'});
			$(selector).chosen(config[selector]);
		}
	}

	$('#dt').datepicker();
	$('#dt').datepicker('option', 'dateFormat', 'dd.mm.yy');
	if (step != 'edit') {
		$('#dt').datepicker('setDate', '0');
	} else {
		$('#dt').datepicker('setDate', dt);
	}

	$('#dtpost').datepicker();
	$('#dtpost').datepicker('option', 'dateFormat', 'dd.mm.yy');
	if (step != 'edit') {
		$('#dtpost').datepicker('setDate', '0');
	} else {
		$('#dtpost').datepicker('setDate', dtpost);
	}

	$('#status').change(function () {
		$('#dt').datepicker('show');
	});
	UpdateChosen();
</script>
