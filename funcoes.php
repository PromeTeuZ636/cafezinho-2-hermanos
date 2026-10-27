<?php
session_start();
function proteger() {
    if (empty($_SESSION['usuario_id'])) { header('Location: login.php'); exit; }
}
function h($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); }
function token() {
    if (empty($_SESSION['token'])) { $_SESSION['token'] = bin2hex(random_bytes(32)); }
    return $_SESSION['token'];
}
function verificar_token() {
    if (empty($_POST['token']) || !hash_equals(token(), $_POST['token'])) { http_response_code(403); exit('Solicitação inválida.'); }
}
function validar_item($nome, $categoria, $preco) {
    if ($nome === '' || mb_strlen($nome) > 100) return 'Informe um nome com até 100 caracteres.';
    if (!in_array($categoria, ['Café', 'Doce', 'Salgado', 'Bebida Gelada'], true)) return 'Escolha uma categoria válida.';
    if (!preg_match('/^\d{1,8}(\.\d{1,2})?$/', $preco)) return 'Informe um preço válido e não negativo.';
    return '';
}
