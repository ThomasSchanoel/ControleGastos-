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

$destinatario = "schanoel500@gmail.com";

if (count($_POST) > 0) {
    include("../lib/conexao.php");  
    require('../lib/fpdf/fpdf.php'); // Biblioteca FPDF para gerar o PDF
    require('../lib/mail.php');  // Inclua a função de envio de e-mail
    require('../lib/export.php');
    ob_start();
    include("index.php");
    include("total.php");
    ob_end_clean();

    $error_message = "";
    $senha = $_POST['senha'];

    // Validação dos campos
    if (empty($senha)) {
        $error_message = "Preencha a senha!";
    } else {
        $id = $_SESSION['usuario'];
        $sql_code = "SELECT * FROM usuarios WHERE id = '$id'";
        $sql_query = $mysqli->query($sql_code) or die($mysqli->error);
        
        $usuario = $sql_query->fetch_assoc();
        if (!password_verify($senha, $usuario['senha'])) {
            $error_message = "A senha está incorreta.";
        }
    }

    if (empty($error_message)) {
        date_default_timezone_set('America/Sao_Paulo');
        // ** 1. GERAR O PDF **
        $sql_code = "SELECT * FROM thomas_compras ORDER BY data ASC";
        $compras_query = $mysqli->query($sql_code) or die($mysqli->error);

        if ($compras_query->num_rows > 0) {
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Relatorio de Compras - ' . date("d/m/Y H:i"), 0, 1, 'C');
            $pdf->Ln(10);
            $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Crédito: R$' . number_format($credito, 2, ',', '.') . ' | ' . 'Débito: R$' . number_format($debito, 2, ',', '.') . ' | ' . 'Total: R$' . number_format($credito_debito, 2, ',', '.')), 0, 1, 'C');
            $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Thomas: R$' . number_format($thomasC, 2, ',', '.') . ' | ' . 'Casa: R$' . number_format($casa, 2, ',', '.') . ' | ' . 'Mãe: R$' . number_format($mae, 2, ',', '.') .' | ' . 'Lucas: R$' . number_format($lucas, 2, ',', '.') . ' | ' . 'Torta: R$' . number_format($torta, 2, ',', '.')), 0, 1, 'C');
            $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Total: R$' . number_format($total, 2, ',', '.')), 0, 1, 'C');
            $pdf->Ln(5);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(40, 10, 'Data', 1);
            $pdf->Cell(50, 10, 'Nome', 1);
            $pdf->Cell(50, 10, 'Pagamento', 1);
            $pdf->Cell(30, 10, 'Valor', 1);
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 10);
            while ($compra = $compras_query->fetch_assoc()) {
                switch ($compra["pagamento"]) {
                    case 'credito':
                        $pagamento = "Crédito";
                        break;
                    case 'debito':
                        $pagamento = "Débito";
                        break;
                    case 'creditoB':
                        $pagamento = "Crédito Bradesco";
                        break;
                    case 'mae':
                        $pagamento = "Mãe";
                        break;
                    case 'torta':
                        $pagamento = "Torta";
                        break;
                    case 'casa':
                        $pagamento = "Casa";
                        break;
                    case 'lucas':
                        $pagamento = "Lucas";
                        break;
                }
                $pdf->Cell(40, 10, date('d/m/Y', strtotime($compra['data'])), 1);
                $pdf->Cell(50, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $compra['nome']), 1);
                $pdf->Cell(50, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $pagamento), 1);
                $pdf->Cell(30, 10, 'R$ ' . number_format($compra['valor'], 2, ',', '.'), 1);
                $pdf->Ln();
            }

            $formatter = new IntlDateFormatter(
                'pt_BR', // Localização
                IntlDateFormatter::NONE, // Sem data completa
                IntlDateFormatter::NONE, // Sem hora
                null, // Use o timezone padrão
                null,
                'MMMM' // Formato personalizado para exibir somente o mês
            );
            

            $arquivo_pdf = 'compras' . $formatter->format(new DateTime()) . '.pdf';
            $pdf->Output('F', $arquivo_pdf);


            // ** 2. EXPORTAR O SQL **
            $arquivo_sql = 'backup_compras_' . $formatter->format(new DateTime()) . '.sql';
            if (exportarBancoDados($mysqli, 'thomas_compras', $arquivo_sql)) {
                // ** 3. ENVIAR OS ARQUIVOS POR E-MAIL **
                $assunto = 'Fatura e Backup de Compras - ' . $formatter->format(new DateTime('now'));
                $mensagem = 'Segue em anexo a fatura em PDF e o backup SQL das compras.';

                // Enviar com os dois anexos
                $resultado_envio = enviarEmail($destinatario, $assunto, $mensagem, '', [$arquivo_pdf, $arquivo_sql]);

                // Excluir os arquivos após o envio
                unlink($arquivo_pdf);
                unlink($arquivo_sql);

                if (strpos($resultado_envio, 'sucesso') !== false) {
                    // ** 4. DELETAR OS DADOS **
                    $sql_code = "DELETE FROM thomas_compras";
                    $deu_certo = $mysqli->query($sql_code) or die($mysqli->error);

                    if ($deu_certo) {
                        echo "<script>alert('Arquivos enviados e dados deletados com sucesso!'); window.location.href='index.php';</script>";
                    } else {
                        $error_message = "Erro ao deletar dados.";
                    }
                } else {
                    $error_message = "Erro ao enviar os arquivos.";
                }
            } else {
                $error_message = "Erro ao criar o backup do banco de dados.";
            }
        } else {
            $error_message = "Nenhuma compra encontrada.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deletar</title>
    <link rel="stylesheet" href="css/deletar.css">
</head>
<body>
    <div class="container">
        <h1>Tem certeza que deseja deletar todos os dados ?</h1>

        <?php if(!empty($error_message)): ?>
            <div class="error"> <?php echo $error_message; ?> </div>
        <?php endif; ?>
        <form action="" method="POST">

            <div class="form-group">
                <label for="senha">Digite a senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Digite a senha">
            </div>

            <button type="submit" class="btn">Deletar</button>
        </form>
        <a href="index.php">Voltar para Home</a>
    </div>
</body>
</html>
