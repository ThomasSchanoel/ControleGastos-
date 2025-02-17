<?php
include("../lib/usuarios.php");
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['usuario']) || !($_SESSION['usuario'])) {
    header("Location: ../index.php");
    die();
}
if ($_SESSION['usuario'] != $bia) {
    header("Location: ../index.php");
    die();
}

if (count($_POST) > 0) {
    include("../lib/conexao.php");
    $erro = false;
    $nome = $_POST['nome'];
    $data = $_POST['data'];
    $pagamento = $_POST['pagamento'];
    $categoria = $_POST['categoria'];
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
    if (empty($categoria)) {
        $erro = "Selecione o categoria!";
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
        $sql_code = "INSERT INTO bia_compras (nome, data, pagamento, valor, categoria) VALUES ('$nome', '$data', '$pagamento', '$valor', '$categoria')";
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
        <h1>Cadastrar Compra</h1>
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
            <?php if (isset($erro) && $erro): ?>
                <div class="error"><?php echo $erro; ?></div>
            <?php endif; ?>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="nome">Nome</label>
                    <input value="<?php if(isset($_POST['nome'])) echo $_POST['nome']; ?>" type="text" id="nome" name="nome" placeholder="Digite o nome">
                </div>
                <div class="form-group">
                    <label for="data">Data</label>
                    <h6>Deixe em branco para data atual</h6>
                    <input value="<?php if(isset($_POST['data'])) echo $_POST['data']; ?>" type="text" id="data" name="data" placeholder="dd/mm/aaaa">
                </div>
                <div class="form-group">
                    <label for="pagamento">Tipo de Pagamento</label>
                    <select id="pagamento" name="pagamento">
                        <option value="">-- Selecione --</option>
                        <option value="credito">Crédito</option>
                        <option value="creditoP">Crédito Parcelado</option>
                        <option value="debito">Débito</option>
                        <option value="dinheiro">Dinheiro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="categoria">Categoria</label>
                    <select id="categoria" name="categoria">
                        <option value="">-- Selecione --</option>
                        <option value="alimentacao">Alimentação</option>
                        <option value="lazer">Lazer</option>
                        <option value="saude">Saúde</option>
                        <option value="fixos">Fixos</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="valor">Valor</label>
                    <input value="<?php if(isset($_POST['valor'])) echo $_POST['valor']; ?>" type="text" id="valor" name="valor" placeholder="Digite o valor (ex: 100,50)">
                </div>
                <button type="submit" class="btn">Cadastrar</button>
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



