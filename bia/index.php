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
if($_SESSION['usuario']!=$bia){
    header("Location: ../index.php");
    die();
}

$alimentacao = 0;
$sql_alimentacao = "SELECT * FROM bia_compras WHERE categoria = 'alimentacao'";
$query_alimentacao = $mysqli->query($sql_alimentacao) or die($mysqli->error);
while($compra = $query_alimentacao->fetch_assoc()){
    $alimentacao += $compra['valor'];
}


$lazer = 0;
$sql_lazer = "SELECT * FROM bia_compras WHERE categoria = 'lazer'";
$query_lazer = $mysqli->query($sql_lazer) or die($mysqli->error);
while($compra = $query_lazer->fetch_assoc()){
    $lazer += $compra['valor'];
}

$saude = 0;
$sql_saude = "SELECT * FROM bia_compras WHERE categoria = 'saude'";
$query_saude = $mysqli->query($sql_saude) or die($mysqli->error);
while($compra = $query_saude->fetch_assoc()){
    $saude += $compra['valor'];
}

$fixos = 0;
$sql_fixos = "SELECT * FROM bia_compras WHERE categoria = 'fixos'";
$query_fixos = $mysqli->query($sql_fixos) or die($mysqli->error);
while($compra = $query_fixos->fetch_assoc()){
    $fixos += $compra['valor'];
}

$formatter = new IntlDateFormatter(
    'pt_BR', // Localização
    IntlDateFormatter::NONE, // Sem data completa
    IntlDateFormatter::NONE, // Sem hora
    null, // Use o timezone padrão
    null,
    'MMMM' // Formato personalizado para exibir somente o mês
);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Gastos</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color:#ffffff; /* Cor de fundo suave */
            color: #4a4a4a;
            line-height: 1.6;
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
            height: 70px; /* Altura fixa do header */
        }

        header h1 {
            font-size: 1.6em;
            color: #fff;
            font-weight: bold;
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

        main {
            padding: 80px 20px 20px; /* Ajuste para compensar o header fixo */
        }

        .sb-background {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background-position: center;
            background-size: cover;
            background-attachment: fixed; /* Imagem fixa durante o rolar */
            z-index: -1; /* Coloca a imagem atrás do conteúdo */
        }

        .option-section-background-image-fixed .sb-background {
            background-attachment: fixed;
        }

        .title-container {
            position: relative;
            width: 100%;
            height: 150px;
            background-image: url('https://tschanoel.com.br/controleGastos/bia/imagens/gastos.png');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            margin-top: 70px; /* Espaço suficiente para o header */
            margin-bottom: -60px; /* Reduzido para aproximar o conteúdo */
        }


        .title-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
        }

        .title-container h2 {
            font-size: 3.5em;
            color: rgb(255, 255, 255);
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(143, 143, 143, 1.2); /* Sombra para melhorar a legibilidade */
            z-index: 2;
        }

        .card {
            background-color: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-10px); /* Efeito sutil de hover */
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-content {
            padding: 20px;
        }

        .card-content h2 {
            font-size: 1.3em;
            color: #f15c8f; /* Rosa mais forte para os títulos das categorias */
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-content h2 a {
            text-decoration: none;
            color: #f15c8f;
            font-weight: bold;
        }

        .card-content p {
            color: #7d7d7d;
            font-size: 1em;
            font-weight: 600;
        }

        .card-content p span {
            color: #f15c8f; /* Rosa para valores destacados */
        }

        .main-content {
            margin-top: 30px; /* Espaço entre a imagem e os cards */
        }

    </style>
</head>
<body>
    <header>
        <h1>Controle de Gastos</h1>
        <div class="menu" id="menu-toggle">☰</div>
    </header>

    <!-- Título e imagem de fundo -->
    <div class="title-container">
        <h2><?php echo ucfirst($formatter->format(new DateTime())); ?></h2>
    </div>

    <main>
        <!-- Menu de navegação -->
        <nav id="menu" class="menu-nav">
            <ul>
                <li><a href="cadastrar_compra.php">Cadastrar Compra</a></li>
                <li><a href="total.php">Total</a></li>
                <li><a href="deletar.php">Deletar Dados</a></li>
            </ul>
        </nav>

        <!-- Área dos Cards -->
        <div class="main-content">
            <div class="card">
                <img src="https://tschanoel.com.br/controleGastos/bia/imagens/alimentacao.png" alt="Alimentação">
                <div class="card-content">
                    <h2>Alimentação <a href="compras/alimentacao.php">&gt;</a></h2>
                    <p><span>R$<?php echo number_format($alimentacao, 2, ',', '.'); ?></span> | R$420,00</p>
                </div>
            </div>

            <div class="card">
                <img src="https://tschanoel.com.br/controleGastos/bia/imagens/lazer.png" alt="Lazer">
                <div class="card-content">
                    <h2>Lazer <a href="compras/lazer.php">&gt;</a></h2>
                    <p><span>R$<?php echo number_format($lazer, 2, ',', '.'); ?></span> | R$100,00</p>
                </div>
            </div>

            <div class="card">
                <img src="https://tschanoel.com.br/controleGastos/bia/imagens/saude.png" alt="Saúde">
                <div class="card-content">
                    <h2>Saúde <a href="compras/saude.php">&gt;</a></h2>
                    <p><span>R$<?php echo number_format($saude, 2, ',', '.'); ?></span> | R$50,00</p>
                </div>
            </div>

            <div class="card">
                <img src="https://tschanoel.com.br/controleGastos/bia/imagens/fixos.jpg" alt="Fixos">
                <div class="card-content">
                    <h2>Fixos <a href="compras/fixos.php">&gt;</a></h2>
                    <p><span>R$<?php echo number_format($fixos, 2, ',', '.'); ?></span> | R$480,00</p>
                </div>
            </div>
        </div>
    </main>
    <!-- JavaScript para o menu -->
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