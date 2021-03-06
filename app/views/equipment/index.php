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

use core\config;
use core\utils;

$cfg = config::getInstance();
?>
<div class="container-fluid">
	<h4><?= $section; ?></h4>
	<div class="row">
		<div class="col-md-3 col-sm-3">
			<select id="orgs" class="select2 form-control">
				<?php
				$morgs = utils::getArrayOrgs(); // список активных организаций
				for ($i = 0; $i < count($morgs); $i++) {
					$idorg = $morgs[$i]['id'];
					$nameorg = $morgs[$i]['name'];
					$sl = ($idorg == $cfg->defaultorgid) ? 'selected' : '';
					echo "<option value=\"$idorg\" $sl>$nameorg</option>";
				}
				?>
			</select>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-md-12 col-sm-12">
			<table id="tbl_equpment"></table>
			<div id="pg_nav"></div>
			<div id="pg_add_edit"></div>
			<div class="row-fluid">
				<div class="col-xs-2 col-md-2 col-sm-2">
					<div id="photoid"></div>
				</div>
				<div class="col-xs-10 col-md-10 col-sm-10">
					<table id="tbl_move"></table>
					<div id="mv_nav"></div>
					<table id="tbl_rep"></table>
					<div id="rp_nav"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var $bmd = $('#bmd_iframe');

	function loadTable() {
		var $tblEquipment = $('#tbl_equpment'),
				$dlgAddEdit = $('#pg_add_edit');
		$tblEquipment.jqGrid({
			url: 'equipment/list?sorgider=' + defaultorgid,
			datatype: 'json',
			colNames: [' ', 'Id', 'IP', 'Помещение', 'Номенклатура', 'Группа', 'В пути', 'Производитель', 'Имя по бухгалтерии', 'Сер.№', 'Инв.№',
				'Штрихкод', 'Организация', 'Мат.отв.', 'Оприходовано', 'Стоимость', 'Тек. стоимость', 'ОС', 'Списано', 'Карта', 'Комментарий',
				'Ремонт', 'Гар.срок', 'Поставщик', 'Действия'],
			colModel: [
				{name: 'active', index: 'active', width: 22, fixed: true, sortable: false, search: false},
				{name: 'equipment.id', index: 'equipment.id', width: 55, search: false, frozen: true, hidden: true, fixed: true},
				{name: 'ip', index: 'ip', width: 100, hidden: true, fixed: true},
				{name: 'placesid', index: 'placesid', width: 155, stype: 'select', frozen: true, fixed: true,
					searchoptions: {dataUrl: 'equipment/getlistplaces?addnone=true'}},
				{name: 'nomename', index: 'getvendorandgroup.nomename', width: 135, frozen: true},
				{name: 'getvendorandgroup.groupname', index: 'getvendorandgroup.grnomeid', width: 100, stype: 'select', fixed: true,
					searchoptions: {dataUrl: 'equipment/getlistgroupname?addnone=true'}},
				{name: 'tmcgo', index: 'tmcgo', width: 80, search: true, stype: 'select', fixed: true,
					searchoptions: {dataUrl: 'route/deprecated/server/equipment/getlisttmcgo.php?addnone=true'},
					formatter: 'checkbox', edittype: 'checkbox', editoptions: {value: 'Yes:No'}, editable: true, hiddem: true
				},
				{name: 'getvendorandgroup.vendorname', index: 'getvendorandgroup.vendorname', width: 100},
				{name: 'buhname', index: 'buhname', width: 155, editable: true, hidden: true},
				{name: 'sernum', index: 'sernum', width: 100, editable: true, fixed: true},
				{name: 'invnum', index: 'invnum', width: 100, editable: true, fixed: true},
				{name: 'shtrihkod', index: 'shtrihkod', width: 100, editable: true, hidden: true, fixed: true},
				{name: 'org.name', index: 'org.name', width: 155, hidden: true},
				{name: 'fio', index: 'fio', width: 100},
				{name: 'datepost', index: 'datepost', width: 80, fixed: true},
				{name: 'cost', index: 'cost', width: 55, editable: true, hidden: true, fixed: true},
				{name: 'currentcost', index: 'currentcost', width: 55, editable: true, hidden: true, fixed: true},
				{name: 'os', index: 'os', width: 35, editable: true, formatter: 'checkbox', edittype: 'checkbox', fixed: true,
					editoptions: {value: 'Yes:No'}, search: false, hidden: true},
				{name: 'mode', index: 'equipment.mode', width: 55, editable: true, formatter: 'checkbox', edittype: 'checkbox', fixed: true,
					editoptions: {value: 'Yes:No'}, search: false, hidden: true},
				{name: 'eqmapyet', index: 'eqmapyet', width: 55, editable: true, formatter: 'checkbox', edittype: 'checkbox', fixed: true,
					editoptions: {value: 'Yes:No'}, search: false, hidden: true},
				{name: 'comment', index: 'equipment.comment', width: 200, editable: true, edittype: 'textarea',
					editoptions: {rows: '3', cols: '10'}, search: false, hidden: true},
				{name: 'eqrepair', hidden: true, index: 'eqrepair', width: 35, editable: true, formatter: 'checkbox', edittype: 'checkbox',
					editoptions: {value: 'Yes:No'}, search: false},
				{name: 'dtendgar', index: 'dtendgar', width: 55, editable: false, hidden: true, search: false, fixed: true},
				{name: 'kntname', index: 'kntname', width: 55, editable: false, hidden: true, search: false},
				{name: 'myac', width: 80, fixed: true, sortable: false, resize: false, formatter: 'actions',
					formatoptions: {keys: true}, search: false}
			],
			gridComplete: function () {
				$tblEquipment.loadCommonParam('tbleq');
			},
			resizeStop: function () {
				$tblEquipment.saveCommonParam('tbleq');
			},
			onSelectRow: function (ids) {
				if (ids) {
					$('#photoid').load('route/deprecated/server/equipment/getphoto.php?eqid=' + ids);
					$('#tbl_move').jqGrid('setGridParam', {url: 'moveinfo/list?eqid=' + ids});
					$('#tbl_move').jqGrid({
						url: 'moveinfo/list?eqid=' + ids,
						editurl: 'moveinfo/change?eqid=' + ids,
						datatype: 'json',
						colNames: ['Id', 'Дата', 'Организация', 'Помещение', 'Сотрудник', 'Организация', 'Помещение', 'Сотрудник', '', 'Комментарий', ''],
						colModel: [
							{name: 'id', index: 'id', width: 25, hidden: true},
							{name: 'dt', index: 'dt', width: 60, sorttype: 'date', formatter: 'date', formatoptions: {srcformat: 'Y-m-d H:i:s', newformat: 'd.m.Y H:i:s'}},
							{name: 'orgname1', index: 'orgname1', width: 120, hidden: true},
							{name: 'place1', index: 'place1', width: 80},
							{name: 'user1', index: 'user1', width: 90},
							{name: 'orgname2', index: 'orgname2', width: 120, hidden: true},
							{name: 'place2', index: 'place2', width: 80},
							{name: 'user2', index: 'user2', width: 90},
							{name: 'name', index: 'name', width: 90, hidden: true},
							{name: 'comment', index: 'comment', width: 200, editable: true},
							{name: 'myac', width: 60, fixed: true, sortable: false, resize: false,
								formatter: 'actions', formatoptions: {keys: true}}
						],
						autowidth: true,
						pager: '#mv_nav',
						sortname: 'dt',
						scroll: 1,
						shrinkToFit: true,
						viewrecords: true,
						height: 200,
						sortorder: 'asc',
						caption: 'История перемещений'
					}).trigger('reloadGrid');
					$('#tbl_move').jqGrid('destroyGroupHeader');
					$('#tbl_move').jqGrid('setGroupHeaders', {
						useColSpanStyle: true,
						groupHeaders: [
							{startColumnName: 'orgname1', numberOfColumns: 3, titleText: 'Откуда'},
							{startColumnName: 'orgname2', numberOfColumns: 3, titleText: 'Куда'}
						]
					});
					$.jgrid.gridUnload('#tbl_rep');
					$('#tbl_rep').jqGrid('setGridParam', {url: 'route/deprecated/server/equipment/getrepinfo.php?eqid=' + ids});
					$('#tbl_rep').jqGrid({
						url: 'route/deprecated/server/equipment/getrepinfo.php?eqid=' + ids,
						datatype: 'json',
						colNames: ['Id', 'Дата начала', 'Дата окончания', 'Организация', 'Стоимость', 'Комментарий', 'Статус', ''],
						colModel: [
							{name: 'id', index: 'id', width: 25, editable: false},
							{name: 'dt', index: 'dt', width: 95, editable: true, sorttype: 'date', editoptions: {size: 20,
									dataInit: function (el) {
										vl = $(el).val();
										$(el).datepicker();
										$(el).datepicker('option', 'dateFormat', 'dd.mm.yy');
										$(el).datepicker('setDate', vl);
									}}
							},
							{name: 'dtend', index: 'dtend', width: 95, editable: true, editoptions: {size: 20,
									dataInit: function (el) {
										vl = $(el).val();
										$(el).datepicker();
										$(el).datepicker('option', 'dateFormat', 'dd.mm.yy');
										$(el).datepicker('setDate', vl);
									}}
							},
							{name: 'kntname', index: 'kntname', width: 120},
							{name: 'cost', index: 'cost', width: 80, editable: true, editoptions: {size: 20,
									dataInit: function (el) {
										$(el).focus();
									}}
							},
							{name: 'comment', index: 'comment', width: 200, editable: true},
							{name: 'status', index: 'status', width: 80, editable: true, edittype: 'select',
								editoptions: {value: '1:Ремонт;0:Сделано'}},
							{name: 'myac', width: 60, fixed: true, sortable: false, resize: false, formatter: 'actions',
								formatoptions: {keys: true,
									afterSave: function () {
										$tblEquipment.jqGrid().trigger('reloadGrid');
									}
								}}
						],
						autowidth: true,
						pager: '#rp_nav',
						sortname: 'dt',
						scroll: 1,
						viewrecords: true,
						height: 200,
						sortorder: 'asc',
						editurl: 'route/deprecated/server/equipment/getrepinfo.php?eqid=' + ids,
						caption: 'История ремонтов'
					}).trigger('reloadGrid');
					$('#tbl_rep').jqGrid('navGrid', '#rp_nav', {edit: false, add: false, del: false, search: false});
					$('#tbl_rep').jqGrid('navButtonAdd', '#rp_nav', {
						caption: '<i class="fas fa-exclamation-triangle"></i>',
						title: 'Отдать в ремонт',
						buttonicon: 'none',
						onClickButton: function () {
							var id = $tblEquipment.jqGrid('getGridParam', 'selrow');
							if (id) { // если выбрана строка ТМЦ который уже в ремонте, открываем список с фильтром по этому ТМЦ
								$tblEquipment.jqGrid('getRowData', id);
								$dlgAddEdit.dialog({autoOpen: false, height: 380, width: 620, modal: true, title: 'Ремонт имущества'});
								$dlgAddEdit.load('route/deprecated/client/view/equipment/repair.php?step=add&eqid=' + id, function () {
									$dlgAddEdit.dialog('open');
								});
							} else {
								$.notify('Выберите оргтехнику для ремонта!');
							}
						}
					});
				}
			},
			subGridRowExpanded: function (subgrid_id, row_id) {
				var subgrid_table_id = subgrid_id + '_t',
						pager_id = 'p_' + subgrid_table_id;
				$('#' + subgrid_id).html('<table id="' + subgrid_table_id + '" class="scroll"></table><div id="' + pager_id + '" class="scroll"></div>');
				$('#' + subgrid_table_id).jqGrid({
					url: 'route/deprecated/server/equipment/paramlist.php?eqid=' + row_id,
					datatype: 'json',
					colNames: ['Id', 'Наименование', 'Параметр', ''],
					colModel: [
						{name: 'id', index: 'id', width: 60, hidden: true},
						{name: 'pname', index: 'pname', width: 150},
						{name: 'pparam', index: 'pparam', width: 310, editable: true},
						{name: 'myac', width: 80, fixed: true, sortable: false, resize: false,
							formatter: 'actions', formatoptions: {keys: true}}
					],
					editurl: 'route/deprecated/server/equipment/paramlist.php?eqid=' + row_id,
					pager: pager_id,
					sortname: 'pname',
					sortorder: 'asc',
					scroll: 1,
					height: 'auto'
				});
			},
			subGridRowColapsed: function (subgrid_id, row_id) {
				var subgrid_table_id = subgrid_id + '_t';
				$('#' + subgrid_table_id).remove();
			},
			subGrid: true,
			//multiselect: true,
			autowidth: true,
			shrinkToFit: true,
			pager: '#pg_nav',
			sortname: 'equipment.id',
			rowNum: 40,
			viewrecords: true,
			sortorder: 'asc',
			editurl: 'equipment/change?sorgider=' + defaultorgid,
			caption: 'Оргтехника'
		});
		$tblEquipment.jqGrid('setGridHeight', $(window).innerHeight() / 2);
		$tblEquipment.jqGrid('filterToolbar', {stringResult: true, searchOnEnter: false});
		$tblEquipment.jqGrid('bindKeys', '');
		$tblEquipment.jqGrid('navGrid', '#pg_nav', {edit: false, add: false, del: false, search: false});
		$tblEquipment.jqGrid('setFrozenColumns');
		$tblEquipment.jqGrid('navButtonAdd', '#pg_nav', {
			caption: '<i class="fas fa-tag"></i>',
			title: 'Выбор колонок',
			buttonicon: 'none',
			onClickButton: function () {
				$tblEquipment.jqGrid('columnChooser', {
					done: function () {
						$tblEquipment.saveCommonParam('tbleq');
					},
					width: 550,
					dialog_opts: {
						modal: true,
						minWidth: 470,
						height: 470
					},
					msel_opts: {
						dividerLocation: 0.5
					}
				});
			}
		});
		$tblEquipment.jqGrid('navButtonAdd', '#pg_nav', {
			caption: '<i class="fas fa-plus-circle"></i>',
			title: 'Добавить',
			buttonicon: 'none',
			onClickButton: function () {
				$bmd.bmdIframe({
					title: 'Добавление имущества',
					src: 'equipment/add'
				}).modal();
			}
		});
		$tblEquipment.jqGrid('navButtonAdd', '#pg_nav', {
			caption: '<i class="fas fa-edit"></i>',
			title: 'Редактировать',
			buttonicon: 'none',
			onClickButton: function () {
				var gsr = $tblEquipment.jqGrid('getGridParam', 'selrow');
				if (gsr) {
					$bmd.bmdIframe({
						title: 'Редактирование имущества',
						src: 'equipment/edit?id=' + gsr
					}).modal();
				} else {
					$.notify('Сначала выберите строку!');
				}
			}
		});
		$tblEquipment.jqGrid('navButtonAdd', '#pg_nav', {
			caption: '<i class="fas fa-arrows-alt"></i>',
			title: 'Переместить',
			buttonicon: 'none',
			onClickButton: function () {
				var gsr = $tblEquipment.jqGrid('getGridParam', 'selrow');
				if (gsr) {
					$bmd.bmdIframe({
						title: 'Перемещение имущества',
						src: 'equipment/move?id=' + gsr
					}).modal();
				} else {
					$.notify('Сначала выберите строку!');
				}
			}
		});
		$tblEquipment.jqGrid('navButtonAdd', '#pg_nav', {
			caption: '<i class="fas fa-exclamation-triangle"></i>',
			title: 'Отдать в ремонт',
			buttonicon: 'none',
			onClickButton: function () {
				var id = $tblEquipment.jqGrid('getGridParam', 'selrow');
				if (id) { // если выбрана строка ТМЦ который уже в ремонте, открываем список с фильтром по этому ТМЦ
					$tblEquipment.jqGrid('getRowData', id);
					$dlgAddEdit.dialog({autoOpen: false, height: 380, width: 620, modal: true, title: 'Ремонт имущества'});
					$dlgAddEdit.load('route/deprecated/client/view/equipment/repair.php?step=add&eqid=' + id, function () {
						$dlgAddEdit.dialog('open');
					});
				} else {
					$.notify('Сначала выберите строку!');
				}
			}
		});
		$tblEquipment.jqGrid('navButtonAdd', '#pg_nav', {
			caption: '<i class="fas fa-table"></i>',
			title: 'Вывести штрихкоды',
			buttonicon: 'none',
			onClickButton: function () {
				var gsr = $tblEquipment.jqGrid('getGridParam', 'selrow');
				if (gsr) {
					var s;
					s = $tblEquipment.jqGrid('getGridParam', 'selarrrow');
					newWin = window.open('route/inc/ean13print.php?mass=' + s, 'printWindow');
				} else {
					$.notify('Сначала выберите строку!');
				}
			}
		});
		$tblEquipment.jqGrid('navButtonAdd', '#pg_nav', {
			caption: '<i class="fas fa-book"></i>',
			title: 'Отчеты',
			buttonicon: 'none',
			onClickButton: function () {
				newWin2 = window.open('report', 'printWindow2');
			}
		});
		$tblEquipment.jqGrid('navButtonAdd', '#pg_nav', {
			caption: '<i class="fas fa-save"></i>',
			title: 'Экспорт XML',
			buttonicon: 'none',
			onClickButton: function () {
				newWin2 = window.open('route/deprecated/server/equipment/export_xml.php', 'printWindow4');
			}
		});
		$tblEquipment.jqGrid('setFrozenColumns');
	}

	$(function () {
		$('.select2').select2({theme: 'bootstrap'});

		$('#orgs').change(function () {
			var exdate = new Date();
			exdate.setDate(exdate.getDate() + 365);
			orgid = $('#orgs :selected').val();
			defaultorgid = orgid;
			document.cookie = 'defaultorgid=' + orgid + '; path=/; expires=' + exdate.toUTCString();
			$.jgrid.gridUnload('#tbl_equpment');
			loadTable();
		});

		loadTable();
	});
</script>
