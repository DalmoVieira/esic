<?php
echo "✅ FUNCIONANDO! Sistema E-SIC está acessível via Apache XAMPP";
echo "<br><br>";
echo "<strong>Teste realizado em:</strong> " . date('d/m/Y H:i:s');
echo "<br><strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido';
echo "<br><strong>PHP:</strong> " . phpversion();
echo "<br><br>";
echo "<a href='index.php'>Ir para Sistema Principal</a>";
?>