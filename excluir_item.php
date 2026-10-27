<?php
require 'funcoes.php'; proteger();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Método não permitido.'); }
verificar_token(); require 'conexao.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) { header('Location: painel.php?status=nao_encontrado'); exit; }
$stmt = mysqli_prepare($conexao, 'DELETE FROM cardapio WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id); mysqli_stmt_execute($stmt);
$status = mysqli_stmt_affected_rows($stmt) > 0 ? 'excluido' : 'nao_encontrado';
mysqli_stmt_close($stmt); header('Location: painel.php?status=' . $status); exit;
