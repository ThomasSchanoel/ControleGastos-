<?php 
include("../../lib/conexao.php");
include("../../lib/usuarios.php"); 

if(!isset($_SESSION)){
    session_start();
}
if(!isset($_SESSION['usuario'])){
    header("Location: ../index.php");
    die();
}
if($_SESSION['usuario']!=$thomas){
    header("Location: ../index.php");
    die();
}

$sql_lucas = "SELECT * FROM thomas_compras WHERE pagamento = 'lucas' ORDER BY data ASC";
$query_lucas = $mysqli->query($sql_lucas) or die($mysqli->error);
$num_compras = $query_lucas->num_rows; 
$total = 0;
while($compra = $query_lucas->fetch_assoc()){
    $total+= $compra['valor'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Lucas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="header-title">Lucas</h1>
            <div class="balance">R$ <?php echo number_format($total, 2, ',', '.'); ?></div>
            <div class="balance-subtext">2% a mais do que mês passado</div>
        </div>

        <div class="sort-bar">
            <button>Ordenar por:</button>
            <button>Data</button>
        </div>

        <div class="main">
            <ul class="transaction-list">
                <li class="transaction">
                    <?php if($num_compras == 0){ ?>
                        <div class="transaction-details">
                            <p class="transaction-title">Nenhuma compra foi registrada</p>
                        </div>
                    <?php } else {
                        $query_lucas->data_seek(0);
                        while($compra = $query_lucas->fetch_assoc()){
                    ?>
                </li>
                <li class="transaction">
                    <div class="transaction-details">
                        <p class="transaction-title"><a href="atualizar_compra.php?id=<?php echo $compra['id']; ?>" class="link-titulo"><?php echo htmlspecialchars($compra['nome']); ?></a></p>
                        <p class="transaction-subtitle">Lucas | <?php echo htmlspecialchars(date('d/m/Y', strtotime($compra['data']))); ?></p>
                    </div>
                    <div class="transaction-amount">R$ <?php echo number_format($compra['valor'], 2, ',', '.'); ?></div>
                <?php 
                    }
                } ?>

                </li>

            </ul>
        </div>

        <div class="footer">
            <div class="footer-icon">
                <div class="icon">
                    <a href="index.php" class="link-icones">
                        <img src="https://tschanoel.com.br/controleGastos/icones/home.png" alt="Home" width="24">
                    </a>
                </div>
            </div>
            <div class="footer-icon">
                <div class="icon">
                    <a href="cadastrar_compra.php" class="link-icones">
                        <img src="https://tschanoel.com.br/controleGastos/icones/shopping-bag.png" alt="Shopping Bag" width="24">
                    </a>
                </div>
            </div>
            <div class="footer-icon active">
                <div class="icon">
                    <a href="total.php" class="link-icones">
                        <img src="https://tschanoel.com.br/controleGastos/icones/total.png" alt="Total" width="24">
                    </a>
                </div>
            </div>
            <div class="footer-icon">
                <div class="icon">
                    <a href="deletar.php" class="link-icones">
                        <img src="https://tschanoel.com.br/controleGastos/icones/trash.png" alt="Trash" width="24">
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
