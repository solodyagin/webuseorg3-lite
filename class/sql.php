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

class Tsql {

	var $idsqlconnection; // Идентификатор соединения с БД
	var $query_result; // Результат запроса
	var $num_queries = 0; // Количество запросов

	/**
	 * Соединяемся с БД и выбираем таблицу, получаем $idsqlconnection
	 * @global type $mysql_char
	 * @param type $host
	 * @param type $name
	 * @param type $pass
	 * @param type $base
	 * @return type
	 */

	function connect($host, $name, $pass, $base) {
		global $mysql_char;
		$this->idsqlconnection = new mysqli($host, $name, $pass, $base);
		if (mysqli_connect_errno()) {
			$serr = mysqli_connect_error();
			echo "Error connect to Mysql or select base: $serr";
			return $serr;
		} else {
			mysqli_query($this->idsqlconnection, "SET NAMES $mysql_char");
			mysqli_query($this->idsqlconnection, "SET sql_mode=''");
			mysqli_set_charset($this->idsqlconnection, "$mysql_char");
		}
	}

	function ExecuteSQL($sql) {
		$this->query_result = mysqli_query($this->idsqlconnection, $sql);
		if ($this->query_result) {
			++$this->num_queries;
			return $this->query_result;
		} else {
			return false;
		}
	}

	function get_num_queries() {
		return $this->num_queries;
	}

	function escape($str) {
		return mysqli_real_escape_string($this->idsqlconnection, $str);
	}

	function start_transaction() {
		return mysqli_query($this->idsqlconnection, 'START TRANSACTION');
	}

	function commit() {
		return mysqli_query($this->idsqlconnection, 'COMMIT');
	}

	function rollback() {
		return mysqli_query($this->idsqlconnection, 'ROLLBACK');
	}

	function close() {
		if ($this->idsqlconnection) {
			if ($this->query_result) {
				mysqli_free_result($this->query_result);
			}
			return mysqli_close($this->idsqlconnection);
		} else {
			return false;
		}
	}

}
