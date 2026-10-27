<?php
require 'funcoes.php'; proteger(); require 'conexao.php';
$erro = ''; $nome = ''; $categoria = ''; $descricao = ''; $preco = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificar_token();
    $nome = trim($_POST['nome_item'] ?? ''); $categoria = $_POST['categoria'] ?? ''; $descricao = trim($_POST['descricao'] ?? ''); $preco = str_replace(',', '.', trim($_POST['preco'] ?? ''));
    $erro = validar_item($nome, $categoria, $preco);
    if (!$erro) {
        $stmt = mysqli_prepare($conexao, 'INSERT INTO cardapio (nome_item, categoria, descricao, preco) VALUES (?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'ssss', $nome, $categoria, $descricao, $preco);
        if (mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); header('Location: painel.php?status=cadastrado'); exit; }
        $erro = 'Não foi possível cadastrar o item.'; mysqli_stmt_close($stmt);
    }
}
$stmt = mysqli_prepare($conexao, 'SELECT id, nome_item, categoria, descricao, preco FROM cardapio ORDER BY id DESC');
mysqli_stmt_execute($stmt); mysqli_stmt_bind_result($stmt, $id, $item_nome, $item_categoria, $item_descricao, $item_preco);
$itens = []; while (mysqli_stmt_fetch($stmt)) { $itens[] = [$id, $item_nome, $item_categoria, $item_descricao, $item_preco]; } mysqli_stmt_close($stmt);
$mensagens = ['cadastrado' => 'Item cadastrado com sucesso.', 'editado' => 'Item atualizado com sucesso.', 'excluido' => 'Item excluído com sucesso.', 'nao_encontrado' => 'Item não encontrado.'];
?>
<!doctype html><html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Painel | Cafeteria</title><link rel="stylesheet" href="estilo.css"></head><body><header><div><span class="tag">CAFÉ DOIS IRMÃOS</span><h1>Gestão do cardápio</h1></div><form action="logout.php" method="post"><input type="hidden" name="token" value="<?= h(token()) ?>"><button class="secundario">Sair</button></form></header><main class="container"><?php if (isset($mensagens[$_GET['status'] ?? ''])): ?><p class="sucesso"><?= h($mensagens[$_GET['status']]) ?></p><?php endif; ?><?php if ($erro): ?><p class="erro"><?= h($erro) ?></p><?php endif; ?><section class="caixa"><h2>Cadastrar novo item</h2><form method="post" class="grid"><input type="hidden" name="token" value="<?= h(token()) ?>"><label>Nome do item<input name="nome_item" maxlength="100" required value="<?= h($nome) ?>"></label><label>Categoria<select name="categoria" required><option value="">Selecione</option><?php foreach (['Café','Doce','Salgado','Bebida Gelada'] as $opcao): ?><option value="<?= h($opcao) ?>" <?= $categoria === $opcao ? 'selected' : '' ?>><?= h($opcao) ?></option><?php endforeach; ?></select></label><label>Preço (R$)<input type="number" name="preco" min="0" max="99999999.99" step="0.01" required value="<?= h($preco) ?>"></label><label class="inteira">Descrição<textarea name="descricao" rows="3"><?= h($descricao) ?></textarea></label><div class="inteira"><button>Cadastrar item</button></div></form></section><section class="caixa"><h2>Itens do cardápio</h2><div class="tabela"><table><thead><tr><th>Nome</th><th>Categoria</th><th>Descrição</th><th>Preço</th><th>Ações</th></tr></thead><tbody><?php foreach ($itens as $item): ?><tr><td><?= h($item[1]) ?></td><td><?= h($item[2]) ?></td><td><?= h($item[3]) ?></td><td>R$ <?= h(number_format((float)$item[4], 2, ',', '.')) ?></td><td class="acoes"><a class="botao secundario" href="editar_item.php?id=<?= (int)$item[0] ?>">Editar</a><form method="post" action="excluir_item.php?id=<?= (int)$item[0] ?>" onsubmit("return confirm('Deseja excluir este item?')")?><input type "hidden" name="token" value="<?= h(token()) ?>"><button class("perigo">Excluir</button></form></td></tr><?php endforeach; ?><?php if (!$itens): ?><tr><td colspan("5">Nenhum item cadastrado ainda.</td></tr><?php endif; ?></tbody></table></div></section></main></body></html>
