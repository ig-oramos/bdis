<?php
	if (!isset($_SESSION))
		session_start();

	if (isset($_SESSION['cliente_id'])) {
		header('location: conta.php');
	}

	if (isset($_POST['submit'])) {
		$erros = array();
		require_once('connect_vars.php');
		
		// Faz a conexão com o banco de dados
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
			or die ('Erro ao tentar fazer a conexão com o servidor MySQL.<br/>'.
				'Detalhes: '. $mysqli_error($dbc));

		// Recebe os valores do formulário
		$login = trim(mysqli_real_escape_string($dbc, $_POST['login']));
		// Criptografa a senha com a função sha1
		$senha = sha1(mysqli_real_escape_string($dbc, $_POST['pswd']));

		// Define a consulta SQL
		$query = "SELECT cliente_id, usuario, senha FROM cliente WHERE usuario = '$login'";

		// Executa a consuita em busca do usuário e senha
		$data = mysqli_query($dbc, $query)
			or die ('Erro ao tentar consultar o banco de dados.<br/>'.
				'Detalhes: '. $mysqli_error($dbc));

		// Transforma o valor retornado pela consulta em um array
		$row = mysqli_fetch_array($data);

		// Verifica se os dados do usuários são iguais ao do banco
		if ($row['usuario'] === $login && $row['senha'] === $senha) {
			$_SESSION['cliente_id'] = $row['cliente_id'];
			require_once('ContaBanco.php');
			header('location: conta.php');
		} else {
			$erros[] = 'Usuário ou senha inválidos.';
		}
		mysqli_close($dbc); // Fecha a conexão com o banco de dados
	}

	$title = 'Acessar';
	$title1 = 'Acesse sua conta';
	$subtitle = 'E manipule suas informações';
	$links = array('access');
	require_once('header.php');
?>
<section id="corpoC">
	<form method="post" action="">
		<label for="login">Usuário:</label><br/>
		<input type="text" name="login" id="login" size="30" 
			value="<?php if (!empty($login)) echo $login; ?>"
			placeholder="Login ou email"><br/>
		<label for="pswd">Senha:</label><br/>
		<input type="password" name="pswd" id="pswd" placeholder="Senha">
		<input type="submit" name="submit" id="submit" class="botao" value="Acessar"><br/>
		<p>Não possui conta? <a href="create.php" id="cad">Cadastre-se!</a></p>
		<?php if (!empty($erros)) {
			echo '<p class="error">'. $erros[0]. '</p>';
			}?>
	</form>
</section>
<?php require_once('footer.php'); ?>
