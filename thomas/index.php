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
if($_SESSION['usuario']!=$thomas){
    header("Location: ../index.php");
    die();
}


$credito = 0;
$sql_credito = "SELECT * FROM thomas_compras WHERE pagamento = 'credito' || pagamento = 'creditoB'";
$query_credito = $mysqli->query($sql_credito) or die($mysqli->error);
while($compra = $query_credito->fetch_assoc()){
    $credito += $compra['valor'];
}


$debito = 0;
$sql_debito = "SELECT * FROM thomas_compras WHERE pagamento = 'debito'";
$query_debito = $mysqli->query($sql_debito) or die($mysqli->error);
while($compra = $query_debito->fetch_assoc()){
    $debito += $compra['valor'];
}

$thomasC = 0;
$sql_thomasC = "SELECT * FROM thomas_compras WHERE pagamento = 'credito'";
$query_thomasC = $mysqli->query($sql_thomasC) or die($mysqli->error);
while($compra = $query_thomasC->fetch_assoc()){
    $thomasC += $compra['valor'];
}

$mae = 0;
$sql_mae = "SELECT * FROM thomas_compras WHERE pagamento = 'mae'";
$query_mae = $mysqli->query($sql_mae) or die($mysqli->error);
while($compra = $query_mae->fetch_assoc()){
    $mae += $compra['valor'];
}

$torta = 0;
$sql_torta = "SELECT * FROM thomas_compras WHERE pagamento = 'torta'";
$query_torta = $mysqli->query($sql_torta) or die($mysqli->error);
while($compra = $query_torta->fetch_assoc()){
    $torta += $compra['valor'];
}

$casa = 0;
$sql_casa = "SELECT * FROM thomas_compras WHERE pagamento = 'casa'";
$query_casa = $mysqli->query($sql_casa) or die($mysqli->error);
while($compra = $query_casa->fetch_assoc()){
    $casa += $compra['valor'];
}

$lucas = 0;
$sql_lucas = "SELECT * FROM thomas_compras WHERE pagamento = 'lucas'";
$query_lucas = $mysqli->query($sql_lucas) or die($mysqli->error);
while($compra = $query_lucas->fetch_assoc()){
    $lucas += $compra['valor'];
}

$credito_debito = $credito + $debito;
if ($credito_debito == 0) {
    $percentCredito = 50;
    $percentDebito = 50;
} else {
  $percentCredito = ($credito / $credito_debito) * 100;
  $percentDebito = ($debito / $credito_debito) * 100;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home</title>
  <link rel="stylesheet" href="css/index.css">
</head>

<body>
  <div class="container">
    <div class="header">
      <h1>Bem vindo, Thomas</h1>
      <p>Aqui estão seus gastos mensais</p>

      <div class="summary">
        <div class="headerGastos">
          <span class="titleGastos">Gastos</span>
        </div>
        <div class="accountGastos">
          <div class="account-info">
            <span class="account-name"><a href="compras/compras_credito.php" class="link-titulo">Crédito</a></span>
            <span class="balanceGastos">R$ <?php echo number_format($credito, 2, ',', '.'); ?></span>
          </div>
        </div>
        <div class="accountGastos">
          <div class="account-info">
            <span class="account-name"><a href="compras/compras_debito.php" class="link-titulo">Débito</a></span>
            <span class="balanceGastos">R$ <?php echo number_format($debito, 2, ',', '.'); ?></span>
          </div>
        </div>
      </div>


    </div>

    <div class="main2">
      <div class="expenses-card">
        <div class="expenses-header">
          <h3>Todos</h3>
          <div class="dropdown">July ▾</div>
        </div>
        <div class="expenses-content">
          <div class="chart">
            <div id="dynamic-circle" class="circle"></div>
          </div>
          <div class="legend" id="legend">
            <!-- Legend items will be dynamically added here -->
          </div>
        </div>
        <div class="expenses-footer">
          <span><a href="compras/debitocredito.php" class="link-titulo">Total:</a></span>
          <span id="total-expenses" class="total-expenses">
            R$ <?php echo number_format($credito_debito, 2, ',', '.'); ?>
          </span>
        </div>
      </div>

    </div>

    <div class="main">
      <div class="accounts-container">
        <div class="headerOutros">
          <span class="titleOutros">Outros</span>
        </div>
        <div class="account">
          <div class="account-info">
            <span class="account-name"><a href="compras/torta.php" class="link-titulo">Torta</a></span>
            <span class="balanceOutros">R$ <?php echo number_format($torta, 2, ',', '.'); ?></span>
          </div>
        </div>
        <div class="account">
          <div class="account-info">
            <span class="account-name"><a href="compras/casa.php" class="link-titulo">Casa</a></span>
            <span class="balanceOutros">R$ <?php echo number_format($casa, 2, ',', '.'); ?></span>
          </div>
        </div>
        <div class="account">
          <div class="account-info">
            <span class="account-name"><a href="compras/mae.php" class="link-titulo">Mãe</a></span>
            <span class="balanceOutros">R$ <?php echo number_format($mae, 2, ',', '.'); ?></span>
          </div>
        </div>
        <div class="account">
          <div class="account-info">
            <span class="account-name"><a href="compras/lucas.php" class="link-titulo">Lucas</a></span>
            <span class="balanceOutros">R$ <?php echo number_format($lucas, 2, ',', '.'); ?></span>
          </div>
        </div>
      </div>
    </div>


    <div class="footer">
      <div class="footer-icon">
        <div class="icon"><a href="index.php" class="link-icones"><img
              src="https://tschanoel.com.br/controleGastos/icones/home.png" alt="Settings" width="24"></a></div>
      </div>
      <div class="footer-icon">
        <div class="icon"><a href="cadastrar_compra.php" class="link-icones"><img
              src="https://tschanoel.com.br/controleGastos/icones/shopping-bag.png" alt="Settings" width="24"></a></div>
      </div>
      <div class="footer-icon">
        <div class="icon"><a href="total.php" class="link-icones"><img
              src="https://tschanoel.com.br/controleGastos/icones/total.png" alt="Settings" width="24"></a></div>
      </div>
      <div class="footer-icon">
        <div class="icon"><a href="deletar.php" class="link-icones"><img
              src="https://tschanoel.com.br/controleGastos/icones/trash.png" alt="Settings" width="24"></a></div>
      </div>
    </div>
  </div>

  <script>
    const expensesData = [
      { category: "Crédito", percentage: <?php echo number_format($percentCredito, 2, '.', ''); ?>, color: "#30d5c8" },
      { category: "Débito", percentage: <?php echo number_format($percentDebito, 2, '.', ''); ?>, color: "#22b573" },
    ];

    // Function to generate the chart dynamically
    function generateChart(data) {
      const circle = document.getElementById("dynamic-circle");
      const legend = document.getElementById("legend");
      const totalExpenses = document.getElementById("total-expenses");
      let gradient = "";
      let currentPercentage = 0;
      let total = 0;

      // Create the conic-gradient for the chart
      data.forEach((item, index) => {
        total += item.percentage;
        const start = currentPercentage;
        const end = currentPercentage + item.percentage;

        gradient += `${item.color} ${start}% ${end}%, `;
        currentPercentage = end;

        // Add legend items
        const legendItem = document.createElement("div");
        legendItem.className = "legend-item";
        legendItem.innerHTML = `
                      <div class="color-box" style="background-color: ${item.color}"></div>
                      <span>${item.percentage}% ${item.category}</span>
                  `;
        legend.appendChild(legendItem);
      });

      // Remove trailing comma and space
      gradient = gradient.slice(0, -2);

      // Set the background of the circle
      circle.style.background = `conic-gradient(${gradient})`;

      // Update the total expenses
    }

    // Initialize the chart
    generateChart(expensesData);
  </script>
</body>

</html>
