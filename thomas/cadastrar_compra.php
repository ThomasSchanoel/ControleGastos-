<?php
include("../lib/usuarios.php");
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['usuario']) || !($_SESSION['usuario'])) {
    header("Location: ../index.php");
    die();
}
if($_SESSION['usuario']!=$thomas){
    header("Location: ../index.php");
    die();
}

if (count($_POST) > 0) {
    include("../lib/conexao.php");
    $erro = false;
    $nome = $_POST['nome'];
    $data = $_POST['data'];
    $pagamento = $_POST['pagamento'];
    $valor = $_POST['valor'];

    // Validação dos campos
    if (empty($valor)) {
        $erro = "Preencha o valor!";
    } else {
        $valor = implode('.', explode(',', $valor)); // Substitui vírgula por ponto
    }
    if (empty($pagamento)) {
        $erro = "Selecione o pagamento!";
    }
    if (empty($nome)) {
        $erro = "Preencha o nome!";
    }
    

    // Processamento da data
    if (!empty($data)) {
        $pedacos = array_reverse(explode('/', $data));
        $data = implode('-', $pedacos); // Converte a data para o formato correto
    } else {
        date_default_timezone_set('America/Sao_Paulo');
        $data = date("Y-m-d"); // Se não informar data, usa a data atual
    }

    // Inserção no banco de dados
    if (!$erro) {
        $sql_code = "INSERT INTO thomas_compras (nome, data, pagamento, valor) VALUES ('$nome', '$data', '$pagamento', '$valor')";
        $deu_certo = $mysqli->query($sql_code) or die($mysqli->error);

        if ($deu_certo) {
            echo "<script>alert('Compra cadastrada com sucesso!'); window.location.href='index.php';</script>";
        } else {
            $erro = "Erro ao cadastrar a compra.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Compra</title>
    <link rel="stylesheet" href="css/cadastrar.css">
</head>
<body>
    <div class="container">
        <h1>Cadastrar Nova Compra</h1>
        <?php if (isset($erro) && $erro): ?>
            <div class="error"> <?php echo $erro; ?> </div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input value="<?php if(isset($_POST['nome'])) echo $_POST['nome']; ?>"type="text" id="nome" name="nome" placeholder="Digite o nome">
            </div>

            <div class="form-group">
                <label for="data">Data</label>
                <input value="<?php if(isset($_POST['data'])) echo $_POST['data']; ?>"type="text" id="data" name="data" placeholder="dd/mm/aaaa | Deixe em branco para usar a data atual">
            </div>

            <div class="form-group">
                <label for="pagamento">Tipo de Pagamento</label>
                <select id="pagamento" name="pagamento">
                    <option value="">-- Selecione --</option>
                    <option value="credito">Crédito</option>
                    <option value="creditoB">Crédito Bradesco</option>
                    <option value="debito">Débito</option>
                    <option value="mae">Mãe</option>
                    <option value="torta">Torta</option>
                    <option value="casa">Casa</option>
                    <option value="lucas">Lucas</option>
                </select>
            </div>

            <div class="form-group">
                <label for="valor">Valor</label>
                <input value="<?php if(isset($_POST['valor'])) echo $_POST['valor']; ?>" type="text" id="valor" name="valor" placeholder="Digite o valor (ex: 100,50)">
            </div>

            <button type="submit" class="btn">Cadastrar</button>
        </form>
        <a href="index.php">Voltar para Home</a>
    </div>
</body>
</html>


