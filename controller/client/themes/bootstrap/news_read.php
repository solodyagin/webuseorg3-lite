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
?>
<div class="row-fluid">
    <div class="span12 well" id="news_read">
		<span class="label label-info"><?php echo "$news_title / $news_dt"; ?></span>
		<p><?php echo $news_body; ?></p>
    </div>
</div>
