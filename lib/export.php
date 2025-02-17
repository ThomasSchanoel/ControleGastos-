<?php

function exportarBancoDados($mysqli, $tabela, $arquivo_sql) {
    $handle = fopen($arquivo_sql, 'w');

    // Adiciona comando DROP TABLE
    fwrite($handle, "DROP TABLE IF EXISTS `$tabela`;\n\n");

    // Obter o comando CREATE TABLE
    $res_create = $mysqli->query("SHOW CREATE TABLE `$tabela`");
    $row_create = $res_create->fetch_assoc();
    fwrite($handle, $row_create['Create Table'] . ";\n\n");

    // Inserir dados da tabela
    $res_dados = $mysqli->query("SELECT * FROM `$tabela`");
    while ($row = $res_dados->fetch_assoc()) {
        $valores = array_map([$mysqli, 'real_escape_string'], array_values($row));
        $valores = "'" . implode("', '", $valores) . "'";
        $colunas = '`' . implode('`, `', array_keys($row)) . '`';
        fwrite($handle, "INSERT INTO `$tabela` ($colunas) VALUES ($valores);\n");
    }

    fclose($handle);
    return file_exists($arquivo_sql);
}