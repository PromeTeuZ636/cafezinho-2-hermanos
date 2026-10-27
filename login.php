<?php
require 'funcoes.php';
require 'conexao.php';
if (!empty($_SESSION['usuario_id'])) { header('Location: painel.php'); exit; }
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_token();
    $usuario = trim($_POST['usuario'] ?? ''); $senha = $_POST['senha'] ?? '';
    $stmt = mysqli_prepare($conexao, 'SELECT id, nome, senha FROM usuarios WHERE usuario = ?');
    mysqli_stmt_bind_param($stmt, 's', $usuario); mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $nome, $senha_banco);
    $existe = mysqli_stmt_fetch($stmt); mysqli_stmt_close($stmt);
    
    $legado = $existe && $senha_banco === 'admin123' && $usuario === 'admin';
    if ($existe && (password_verify($senha, $senha_banco) || ($legado && hash_equals($senha_banco, $senha)))) {
        if ($legado) {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conexao, 'UPDATE usuarios SET senha = ? WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'si', $hash, $id); mysqli_stmt_execute($stmt); mysqli_stmt_close($stmt);
        }
        session_regenerate_id(true); $_SESSION['usuario_id'] = $id; $_SESSION['nome'] = $nome;
        header('Location: painel.php'); exit;
    }
    $erro = 'Usuário ou senha incorretos.';
}
?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Login | Cafeteria</title><link rel="stylesheet" href="estilo.css"></head><body><main class="login caixa"><span class="tag">GESTÃO DE CARDÁPIO</span><h1>Bem-vindo:</h1><p>Entre para administrar sua cafeteria.</p><?php if ($erro): ?><p class="erro"><?= h($erro) ?></p><?php endif; ?><form method="post"><input type="hidden" name="token" value="<?= h(token()) ?>"><label>Usuário<input name="usuario" required autofocus></label><label>Senha<input type="password" name="senha" required></label><button type="submit">Entrar (login)</button></form></main></body></html>
