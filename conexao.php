<?php
// Altere estas configurações se o seu MySQL usar outro usuário ou senha.
$conexao = mysqli_connect('localhost', 'root', '', 'gestao_cafeteria');
if (!$conexao) { exit('Não foi possível conectar ao banco. Confira conexao.php e inicie o MySQL.'); }
mysqli_set_charset($conexao, 'utf8mb4');
