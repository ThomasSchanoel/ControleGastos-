<?php
include("../lib/usuarios.php");
include("../lib/conexao.php");
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


$id = intval($_GET['id']);
if (count($_POST) > 0) {
    
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
        $sql_code = "UPDATE thomas_compras SET 
            nome = '$nome',
            data = '$data',
            pagamento = '$pagamento',
            valor = $valor
            WHERE id = $id";
        $deu_certo = $mysqli->query($sql_code) or die($mysqli->error);

        if ($deu_certo) {
            echo "<script>alert('Gasto fixo atualizado com sucesso!'); window.location.href='index.php';</script>";
        } else {
            $erro = "Erro ao editar gasto.";
        }
    }
}

$sql_compra = "SELECT * FROM thomas_compras WHERE id = '$id'";
$query_compra = $mysqli->query($sql_compra) or die($mysqli->error);
$compra = $query_compra->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Compra</title>
    <link rel="stylesheet" href="css/atualizar.css">
</head>
<body>
    <div class="container">
        <h1>Editar Compra</h1>
        <?php if (isset($erro) && $erro): ?>
            <div class="error"> <?php echo $erro; ?> </div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="nome">Nome</label>
                <input value="<?php echo $compra['nome']; ?>"type="text" id="nome" name="nome" placeholder="Digite o nome">
            </div>

            <div class="form-group">
                <label for="data">Data</label>
                <input value="<?php echo $compra['data']; ?>"type="text" id="data" name="data" placeholder="dd/mm/aaaa | Deixe em branco para usar a data atual">
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
                <input value="<?php echo $compra['valor']; ?>" type="text" id="valor" name="valor" placeholder="Digite o valor (ex: 100,50)">
            </div>

            <button type="submit" class="btn">Cadastrar</button>
        </form>
        <a href="index.php">Voltar para Home</a>
    </div>
</body>
</html>

