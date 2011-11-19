<?php

$data = Botconnect::getAll();
forEach ($data as $row) {
	Buddylist::add($row->name, 'botconnect');
}

?>