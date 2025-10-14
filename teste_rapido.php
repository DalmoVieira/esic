<?php
echo "Teste simples - " . date('Y-m-d H:i:s');
echo "<br>Servidor: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A');
echo "<br>PHP: " . phpversion();
?>