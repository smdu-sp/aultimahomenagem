<?php
/*
Template Name: Lista de Homenagens
*/
header('Content-Type: application/json; charset=utf-8');
require_once('homenagem.php');
$lista = [];
$lista['homenagens'] = getHomenagens(is_user_logged_in());
$lista['private'] = is_user_logged_in();
echo json_encode($lista);
return;
