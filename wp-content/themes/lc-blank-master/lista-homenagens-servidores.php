<?php
/*
Template Name: Lista de Homenagens Servidores
*/
header('Content-Type: application/json; charset=utf-8');
require_once('homenagem.php');
$lista = [];
$lista['homenagens'] = getHomenagens(is_user_logged_in(), true);
// var_dump(getHomenagens(is_user_logged_in(), true));
// $lista['private'] = is_user_logged_in();
echo json_encode($lista);
return;
