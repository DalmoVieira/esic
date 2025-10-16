<?php
// Habilitar exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>Teste PHP Funcionando!</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Servidor: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Hora atual: " . date('Y-m-d H:i:s') . "</p>";

echo "<h2>Testando redirecionamento do index.php:</h2>";
echo "<p>Conteúdo do index.php:</p>";
echo "<pre>" . htmlspecialchars(file_get_contents(__DIR__ . '/index.php')) . "</pre>";

echo "<h2>Verificando login.php:</h2>";
if (file_exists(__DIR__ . '/login.php')) {
    echo "<p>✅ login.php existe (" . filesize(__DIR__ . '/login.php') . " bytes)</p>";
    echo '<p><a href="login.php">Clique para acessar login.php</a></p>';
} else {
    echo "<p>❌ login.php NÃO ENCONTRADO!</p>";
}

echo "<h2>Headers enviados?</h2>";
if (headers_sent($file, $line)) {
    echo "<p>❌ Headers já enviados em $file linha $line</p>";
} else {
    echo "<p>✅ Headers ainda podem ser enviados</p>";
}
?>
