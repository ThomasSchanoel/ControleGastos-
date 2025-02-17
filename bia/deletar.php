<?php
include("../lib/usuarios.php");
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['usuario']) || !($_SESSION['usuario'])) {
    header("Location: ../index.php");
    die();
}
if($_SESSION['usuario']!=$bia){
    header("Location: ../index.php");
    die();
}

$destinatario = "anabeatrizgoncalves288@gmail.com";

if (count($_POST) > 0) {
    include("../lib/conexao.php");
    include("index.php");
    include("total.php");  
    require('../lib/fpdf/fpdf.php'); // Biblioteca FPDF para gerar o PDF
    require('../lib/mail.php');  // Inclua a função de envio de e-mail
    require('../lib/export.php');

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
        $sql_code = "SELECT * FROM bia_compras ORDER BY data ASC";
        $compras_query = $mysqli->query($sql_code) or die($mysqli->error);

        if ($compras_query->num_rows > 0) {
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Relatorio de Compras - ' . date("d/m/Y H:i"), 0, 1, 'C');
            $pdf->Ln(10);
            $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 
            'Alimentação: R$' . number_format($alimentacao, 2, ',', '.') . ' | ' 
            . 'Lazer: R$' . number_format($lazer, 2, ',', '.') . ' | ' 
            . 'Saúde: R$' . number_format($saude, 2, ',', '.') . ' | ' 
            . 'Fixos: R$' . number_format($fixos, 2, ',', '.')));
            $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Total: R$' . number_format($total, 2, ',', '.')), 0, 1, 'C');
            $pdf->Ln(5);

            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(40, 10, 'Data', 1);
            $pdf->Cell(50, 10, 'Nome', 1);
            $pdf->Cell(50, 10, 'Pagamento', 1);
            $pdf->Cell(30, 10, 'Valor', 1);
            $pdf->Cell(50, 10, 'Categoria', 1);

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
                    case 'creditoP':
                        $pagamento = "Crédito Parcelado";
                        break;
                    case 'dinheiro':
                        $pagamento = "Dinheiro";
                        break;
                }
                switch ($compra["categoria"]) {
                    case 'alimentacao':
                        $categoria = "Alimentação";
                        break;
                    case 'lazer':
                        $categoria = "Lazer";
                        break;
                    case 'saude':
                        $categoria = "Saúde";
                        break;
                    case 'fixos':
                        $categoria = "Fixos";
                        break;
                }
                $pdf->Cell(40, 10, date('d/m/Y', strtotime($compra['data'])), 1);
                $pdf->Cell(50, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $compra['nome']), 1);
                $pdf->Cell(50, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $pagamento), 1);
                $pdf->Cell(30, 10, 'R$ ' . number_format($compra['valor'], 2, ',', '.'), 1);
                $pdf->Cell(50, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $categoria), 1);
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
            if (exportarBancoDados($mysqli, 'bia_compras', $arquivo_sql)) {
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
                    $sql_code = "DELETE FROM bia_compras";
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
    <title>Deletar Compra</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #fdf1f6;
            color: #333;
        }

        header {
            background-color: #f8c7d4; /* Rosa suave */
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f1a3b3; /* Linha sutil em rosa mais forte */
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        header h1 {
            font-size: 1.6em;
            color: #fff;
            font-weight: bold;
        }

        h6 {
            font-size: 0.7em;
            color:rgb(248, 199, 212);
            margin-top: 0.5rem;
        }

        .menu {
            font-size: 1.8em;
            cursor: pointer;
            color: #fff;
            transition: color 0.3s ease;
        }

        .menu.active {
            color: #f15c8f; /* Cor para indicar estado ativo */
        }

        .menu-nav {
            display: none;
            position: absolute;
            top: 60px;
            right: 20px;
            background-color: #f8c7d4;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            position: fixed;
        }

        .menu-nav.open {
            display: block;
        }

        .menu-nav ul {
            list-style-type: none;
        }

        .menu-nav ul li {
            margin: 10px 0;
        }

        .menu-nav ul li a {
            text-decoration: none;
            color: #fff;
            font-size: 1.2em;
        }

        .menu-nav ul li a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 600px;
            margin: 3rem auto;
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .container h1 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #f15c8f;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            outline: none;
        }

        input:focus, select:focus {
            border-color: rgb(224, 118, 224);
        }

        .btn {
            background-color: #f15c8f;
            color: white;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }

        .btn:hover {
            background-color: #f8c7d4;
        }

        .error {
            color: red;
            margin-bottom: 1rem;
            text-align: center;
        }

        main {
            padding: 60px 20px; /* Para compensar o header fixo */
        }
    </style>
</head>
<body>
    <header>
        <h1>Deletar</h1>
        <div class="menu" id="menu-toggle" tabindex="0" role="button" aria-expanded="false">☰</div>
    </header>
    <main>
        <nav id="menu" class="menu-nav" aria-label="Menu de navegação">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="total.php">Total</a></li>
                <li><a href="deletar.php">Deletar Dados</a></li>
            </ul>
        </nav>
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
        </div>
    </main>
    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const menu = document.getElementById('menu');

        // Alternar o estado do menu
        menuToggle.addEventListener('click', () => {
            const isOpen = menu.classList.toggle('open');
            menuToggle.classList.toggle('active', isOpen);
            menuToggle.setAttribute('aria-expanded', isOpen);
        });

        // Fechar o menu ao clicar em um link
        menu.addEventListener('click', (event) => {
            if (event.target.tagName === 'A') {
                menu.classList.remove('open');
                menuToggle.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Fechar o menu ao clicar fora dele
        document.addEventListener('click', (event) => {
            if (!menu.contains(event.target) && !menuToggle.contains(event.target)) {
                menu.classList.remove('open');
                menuToggle.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });
    </script>
</body>
</html>

