@echo off
echo ==========================================
echo DIAGNOSTICO AUTOMATICO E-SIC
echo ==========================================
echo.

echo [1/5] Verificando se Apache esta rodando...
tasklist /FI "IMAGENAME eq httpd.exe" 2>NUL | find /I /N "httpd.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] Apache esta rodando
) else (
    echo [ERRO] Apache NAO esta rodando!
    echo Execute: C:\xampp\apache_start.bat
    pause
    exit
)

echo.
echo [2/5] Testando conexao com localhost...
ping -n 1 localhost | find "TTL=" >NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] Localhost responde
) else (
    echo [ERRO] Localhost nao responde!
    pause
    exit
)

echo.
echo [3/5] Testando porta 80...
netstat -an | find ":80 " | find "LISTENING" >NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] Porta 80 esta aberta
) else (
    echo [ERRO] Porta 80 nao esta aberta!
    pause
    exit
)

echo.
echo [4/5] Verificando arquivos do E-SIC...
if exist "C:\xampp\htdocs\esic\index.php" (
    echo [OK] index.php existe
) else (
    echo [ERRO] index.php NAO existe!
)

if exist "C:\xampp\htdocs\esic\login.php" (
    echo [OK] login.php existe
) else (
    echo [ERRO] login.php NAO existe!
)

if exist "C:\xampp\htdocs\esic\login-zero.php" (
    echo [OK] login-zero.php existe
) else (
    echo [AVISO] login-zero.php nao existe
)

echo.
echo [5/5] Abrindo navegador para testes...
echo.
echo Abrindo 3 paginas no Chrome...
start chrome "http://localhost/esic/teste-absoluto.php"
timeout /t 2 /nobreak >NUL
start chrome "http://localhost/esic/login-zero.php"
timeout /t 2 /nobreak >NUL
start chrome "http://localhost/esic/"

echo.
echo ==========================================
echo DIAGNOSTICO CONCLUIDO!
echo ==========================================
echo.
echo Verifique as 3 abas abertas no Chrome:
echo.
echo 1. teste-absoluto.php  - Deve mostrar "SE VOCE VE ISSO..."
echo 2. login-zero.php      - Deve mostrar tela de login roxa
echo 3. index.php           - Deve redirecionar para login
echo.
echo Se alguma pagina ficar BRANCA:
echo - Pressione F12
echo - Va em Console e Network
echo - Copie os erros que aparecerem
echo.
pause
