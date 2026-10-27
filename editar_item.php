<?php
require 'funcoes.php'; proteger(); require 'conexao.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if (!$id) { header('Location: painel.php?status=nao_encontrado'); exit; }
$stmt = mysqli_prepare($conexao, 'SELECT nome_item, categoria, descricao, preco FROM cardapio WHERE id = ?');
mysqli_stmt_bind_param($stmt, 'i', $id); mysqli_stmt_execute($stmt); mysqli_stmt_bind_result($stmt, $nome, $categoria, $descricao, $preco); $existe = mysqli_stmt_fetch($stmt); mysqli_stmt_close($stmt);
if (!$existe) { header('Location: painel.php?status=nao_encontrado'); exit; }
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_token(); $nome = trim($_POST['nome_item'] ?? ''); $categoria = $_POST['categoria'] ?? ''; $descricao = trim($_POST['descricao'] ?? ''); $preco = str_replace(',', '.', trim($_POST['preco'] ?? ''));
    $erro = validar_item($nome, $categoria, $preco);
    if (!$erro) {
        $stmt = mysqli_prepare($conexao, 'UPDATE cardapio SET nome_item = ?, categoria = ?, descricao = ?, preco = ? WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'ssssi', $nome, $categoria, $descricao, $preco, $id);
        if (mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); header('Location: painel.php?status=editado'); exit; }
        $erro = 'Não foi possível atualizar o item.'; mysqli_stmt_close($stmt);
    }
}
?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Editar item | Cafeteria</title><link rel="stylesheet" href="estilo.css"></head><body><main class="container"><section class="caixa"><span class="tag">CARDÁPIO</span><h1>Editar item</h1><?php if ($erro): ?><p class="erro"><?= h($erro) ?></p><?php endif; ?><form method="post" class="grid"><input type="hidden" name="token" value="<?= h(token()) ?>"><label>Nome do item<input name="nome_item" maxlength="100" required value="<?= h($nome) ?>"></label><label>Categoria<select name="categoria" required><?php foreach (['Café','Doce','Salgado','Bebida Gelada'] as $opcao): ?><option value="<?= h($opcao) ?>" <?= $categoria === $opcao ? 'selected' : '' ?>><?= h($opcao) ?></option><?php endforeach; ?></select></label><label>Preço (R$)<input type="number" name="preco" min="0" max="99999999.99" step="0.01" required value="<?= h($preco) ?>"></label><label class="inteira">Descrição<textarea name="descricao" rows="3"><?= h($descricao) ?></textarea></label><div class="inteira"><button>Salvar alterações</button> <a class="botao secundario" href="painel.php">Cancelar</a></div></form></section></main></body></html>
