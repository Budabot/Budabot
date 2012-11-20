<?php

class DBRow {
	function __get($value) {
		LegacyLogger::log('WARN', 'DB', "Tried to get value '$value' from row that doesn't exist");
	}
}
