<?php 
include("../lib/conexao.php"); 
include("../lib/usuarios.php");

if(!isset($_SESSION)){
    session_start();
}
if(!isset($_SESSION['usuario'])){
    header("Location: ../index.php");
    die();
}
if($_SESSION['usuario'] != $bia){
    header("Location: ../index.php");
    die();
}

$sql_total = "SELECT * FROM bia_compras ORDER BY data ASC";
$query_total = $mysqli->query($sql_total) or die($mysqli->error);
$num_compras = $query_total->num_rows; 

$total = 0;
while ($compra = $query_total->fetch_assoc()) {
    $total += $compra['valor'];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total</title>
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
            padding: 15px 20px; /* Ajustado conforme o arquivo enviado */
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
            font-size: 1.6em; /* Ajustado conforme o arquivo enviado */
            color: #fff;
            font-weight: bold;
        }

        .menu {
            font-size: 1.8em; /* Ajustado conforme o arquivo enviado */
            cursor: pointer;
            color: #fff;
            transition: color 0.3s ease;
        }

        .menu.active {
            color: #f15c8f; /* Cor para indicar estado ativo */
        }

        .menu-nav {
            display: none;
            position: fixed;
            top: 60px; /* Ajustado conforme o arquivo enviado */
            right: 20px; /* Ajustado conforme o arquivo enviado */
            background-color: #f8c7d4;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        .menu-nav.open {
            display: block;
        }

        .menu-nav ul {
            list-style-type: none;
        }

        .menu-nav ul li {
            margin: 8px 0;
        }

        .menu-nav ul li a {
            text-decoration: none;
            color: #fff;
            font-size: 1.2em; /* Ajustado conforme o arquivo enviado */
        }

        .menu-nav ul li a:hover {
            text-decoration: underline;
        }

        main {
            padding: 80px 10px 20px; /* Ajustado conforme o arquivo enviado */
        }

        .container {
            background: #fff;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .empty {
            text-align: center;
            font-weight: bold;
            color: #f15c8f;
        }

        .purchase-card {
            display: flex;
            flex-direction: column;
            background-color: #fcfaf8;
            padding: 1rem;
            border-radius: 12px; /* Bordas arredondadas */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.25rem; /* Espaço entre os cards */
        }

        .purchase-header {
            display: flex;
            justify-content: space-between; /* Nome e data em lados opostos */
            align-items: center;
        }

        .purchase-name {
            font-size: 1em;
            font-weight: 500;
            color: #f15c8f;
        }

        .purchase-date {
            font-size: 0.9em;
            font-weight: 400;
            color: #f1a3b3;
        }

        .purchase-details {
            margin-top: 0.5rem; /* Espaço entre o cabeçalho e os detalhes */
            font-size: 0.9em;
            color: #f1a3b3;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            header h1 {
                font-size: 1.4em;
            }

            .menu {
                font-size: 1.6em;
            }

            .container {
                padding: 1rem 0.5rem;
            }
        }

        @media (max-width: 480px) {
            header h1 {
                font-size: 1.5em;
            }

            .menu {
                font-size: 1.4em;
            }

            .container {
                padding: 0.8rem 0.4rem;
            }

            .purchase-name {
                font-size: 0.9em;
                color: #f15c8f;
            }

            .purchase-date {
                font-size: 0.8em;
                color: #f1a3b3;
            }

            .purchase-details {
                font-size: 0.8em;
                color: #f1a3b3;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Total - R$<?php echo number_format($total, 2, ',', '.'); ?></h1>
        <div class="menu" id="menu-toggle">☰</div>
    </header>
    <main>
        <nav id="menu" class="menu-nav">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="cadastrar_compra.php">Cadastrar Compra</a></li>
                <li><a href="deletar.php">Deletar Dados</a></li>
            </ul>
        </nav>
        <div class="container">
            <?php if ($num_compras == 0): ?>
                <p class="empty">Nenhuma compra cadastrada.</p>
            <?php else: ?>
                <?php $query_total->data_seek(0); ?>
                <?php while ($compra = $query_total->fetch_assoc()): 
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
                    ?>
                <div class="purchase-card">
                    <!-- Cabeçalho com nome e data -->
                    <div class="purchase-header">
                        <p class="purchase-name">
                            <?php echo ucfirst(htmlspecialchars($compra['nome'])); ?>
                        </p>
                        <p class="purchase-date">
                            <?php echo htmlspecialchars(date('d/m/Y', strtotime($compra['data']))); ?>
                        </p>
                    </div>
                    <!-- Detalhes do valor e forma de pagamento -->
                    <div class="purchase-details">
                        R$ <?php echo number_format($compra['valor'], 2, ',', '.'); ?> | <?php echo htmlspecialchars($pagamento); ?><br>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </main>
    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const menu = document.getElementById('menu');

        menuToggle.addEventListener('click', () => {
            const isOpen = menu.classList.toggle('open');
            menuToggle.classList.toggle('active', isOpen);
        });

        document.addEventListener('click', (event) => {
            if (!menu.contains(event.target) && !menuToggle.contains(event.target)) {
                menu.classList.remove('open');
                menuToggle.classList.remove('active');
            }
        });
    </script>
</body>
</html>