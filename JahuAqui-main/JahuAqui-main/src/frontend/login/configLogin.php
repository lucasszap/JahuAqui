$host = "localhost";
$user = "root";
$pass = "tccjau";
$banco = "dblogins";

$conexao = @mysqli_connect($host, $user, $pass, $banco)
 or die ("Problemas com a conexão do Banco de Dados");