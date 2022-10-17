<?php

	// Verifica se a sessão já foi configurada
	if (!isset($_SESSION)) {
		session_start();
	}

	// Verifica se o usuário está logado
	if (isset($_SESSION['cliente_id'])) {

		// Redireciona o usuário para a página da conta
		header('location: conta.php');
	}

	// Adiciona as variáveis de conexão
	require_once('connect_vars.php');

	// Variável que controla a visibilidade do formulário
	$output_form = true;

	// Recebe os valores do usuário, se o botão submit for configurado
	if (isset($_POST['submit'])) {

		// Faz a conexão com o banco de dados
		$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
		or die ('Erro ao tentar fazer a conexão com o servidor MySQL.<br/>'.
			'Detalhes: '. mysqli_error($dbc));

		// Evita possíveis ataques utilizando a função trim e mysql
		// responsáveis respectivamente por remover espaços no começo
		// e no fim da página e, evitar injeções de SQL
		$nome = trim(mysqli_real_escape_string($dbc, $_POST['name']));
		$l_name = trim(mysqli_real_escape_string($dbc, $_POST['lName']));
		$data_nasc = isset($_POST['data'])?$_POST['data']:'NULL';
		$data_nasc = substr($data_nasc, 6). '-'. substr($data_nasc, 3, 2). '-'.
			substr($data_nasc, 0, 2);
		$estado = $_POST['state'];
		$tipo = $_POST['tConta'];
		$usuario = trim(mysqli_real_escape_string($dbc, $_POST['login']));

		// Criptografa a senha e remove espaços no fim e começo, e evita injeções
		$senha = sha1(trim(mysqli_real_escape_string($dbc, $_POST['pswd'])));
		$c_senha = sha1(trim(mysqli_real_escape_string($dbc, $_POST['cpswd'])));
		$erros = array(); // Inicializa o array (vetor) de erro

		// Verifica se as variáveis de nome usuario e senha nõa estão vazias
		if (empty($nome) || empty($usuario) || empty($senha)) {

			// Adiciona um erro ao array erro
			$erros[] = 'Preencha todos os campos necessários.';
		}

		// Verifica se as senhas são iguais 
		if ($senha !== $c_senha) {
			$erros[] = 'As senhas não são iguais.';
		}

		// Se não houverem erros
		if (empty($erros)) {

			// Consulta SQL
			$query = "SELECT usuario FROM cliente WHERE usuario = '$usuario'";

			// Procura por um usuário no banco
			$data = mysqli_query($dbc, $query)
				or die ('Erro ao tentar consultar o banco de dados.<br/>'.
					'Detalhes: '. mysqli_error($dbc));

			// Caso haja um retorno, ou seja, alguma pessoa cadastrada com esse usuário
			if (mysqli_num_rows($data) > 0) {
				$erros[] = 'O usuário digitado já está em uso.';
			} else { // Senão

				// Executa o cadastro

				// Adiciona o scrip ContaBanco
				require_once('ContaBanco.php');

				// Instancia um novo objeto da classe ContaBanco
				$conta = new ContaBanco();

				// Chama o método para abrir uma nova conta
				$conta->abrirConta($tipo, $nome);

				// Consulta SQL de inserção
				$query = "INSERT INTO cliente (nome, sobrenome, data_nasc, estado, usuario, senha) VALUES ('{$conta->getDono()}', '$l_name', '$data_nasc', '$estado', '$usuario', '$senha')";
				
				// Executa a conulta e insere os dados do usuário no BD
				mysqli_query($dbc, $query) 
					or die ('Erro ao tentar consultar o banco de dados.<br/>.'. 
					'Detalhes: '. mysqli_error($dbc));

				// Consulta SQL
				$query = "SELECT cliente_id FROM cliente WHERE usuario = '$usuario'";

				// Faz uma busca no banco à procura do identificador do cliente
				$data = mysqli_query($dbc, $query) 
					or die ('Erro ao tentar consultar o banco de dados.<br/>'. 
						'Detalhes: '. mysqli_error($dbc));

				// Coloca o id do cliente em um array
				$cliente_id = mysqli_fetch_array($data);
				
				// Variável que recebe a data do servidor (AAAA-MM-DD)
				$data_atual = date('Y-m-d');

				// Consutla SQL de inserção
				$query = "INSERT INTO conta (cliente_id, tipo, mensalidade, saldo, status) VALUES (". $cliente_id['cliente_id']. ", '{$conta->getTipo()}', '$data_atual', '{$conta->getSaldo()}', {$conta->getStatus()})";

				// Executa a inserção na nova conta do cliente
				mysqli_query($dbc, $query)
					or die ('Erro ao tentar consultar o banco de dados.<br/>'. 
					'Detalhes: '. mysqli_error($dbc));
				
				// Usuário cadastrado, o formulário não precisa mais ser exibido
				$output_form = false;

				// Inicia a sessão
				session_start();

				// Adiciona a sessão no índice cliente_id o valor do id do cliente
				$_SESSION['cliente_id'] = $cliente_id['cliente_id'];
			}
		}
		mysqli_close($dbc);
	}
	$title = 'Criar';
	$title1 = 'Crie sua conta';
	$subtitle = 'E utilize todos os recursos disponíveis aos membros!';
	$links = array('create');
	require_once('header.php');
?>
<section id="corpoC">
	<?php if ($output_form) { ?>
	<form method="post" action="">
		<?php
			if (isset($erros)){ // Verifica se existem erros
				foreach ($erros as $erro) // Faz um loop pelo array de erros
				echo '<p class="error">'. $erro. '</p>'; // Exibe erro por erro
			}
		?>
		<label for="name">Nome:</label>
		<input type="text" name="name" id="name" placeholder="Primeiro nome" maxlength="20"	
			value="<?php if (!empty($nome)) echo $nome; else $ast = '<strong>'. 
			'*</strong>'; ?>"><?php if (!empty($ast)) echo ' '. $ast;?>
		<label for="lName">Sobrenome:</label>
		<input type="text" name="lName" id="lName" placeholder="Sobrenome" maxlength="40"><br/>
		<label for="data" style="display: block">Data de nascimento:</label>
		<input type="text" name="data" id="data" placeholder="__/__/____">
		<label for="state" style="display: block">Estado: </label>
		<select name="state" id="state" placeholder="Estado">
			<option value="AC">Acre</option>
			<option value="AL">Alagoas</option>
			<option value="AP">Amapá</option>
			<option value="AM">Amazonas</option>
			<option value="BA">Bahia</option>
			<option value="CE">Ceará</option>
			<option value="DF">Distrito Federal</option>
			<option value="ES">Espírito Santo</option>
			<option value="GO">Goiás</option>
			<option value="MA">Maranhão</option>
			<option value="MT">Mato Grosso</option>
			<option value="MS">Mato Grosso do Sul</option>
			<option value="MG">Minas Gerais</option>
			<option value="PA">Pará</option>
			<option value="PB">Paraíba</option>
			<option value="PR">Paraná</option>
			<option value="PE">Pernambuco</option>
			<option value="PI">Piauí</option>
			<option value="RJ">Rio de Janeiro</option>
			<option value="RN">Rio Grande do Norte</option>
			<option value="RS">Rio Grande do Sul</option>
			<option value="RO">Rondônia</option>
			<option value="RR">Roraima</option>
			<option value="SC">Santa Catarina</option>
			<option value="SP">São Paulo</option>
			<option value="SE">Sergipe</option>
			<option value="TO">Tocantins</option>
		</select>
		<label for="tConta" style="display: block">Tipo de Conta:</label>
		<select name="tConta" id="tConta">
			<option value="cc" checked>Conta Corrente</option>
			<option value="cp">Conta Poupança</option>
		</select>
		<label for="login">Usuário:</label>
		<input type="text" name="login" id="login" maxlength="40" placeholder="Seu nome de usuário"
			value="<?php if (!empty($usuario)) echo $usuario; else $ast = '<strong> *</strong>'; ?>">
			<?php if (!empty($ast)) echo $ast; ?><br/>
		<label for="pswd">Senha:</label>
		<input type="password" name="pswd" id="pswd" placeholder="Sua senha" maxlength="15">
		<strong> *</strong>
		<input type="password" name="cpswd" id="cpswd" 
			placeholder="Confirme sua senha" maxlength="15"><strong> *</strong><br/>
		<input type="submit" name="submit" class="botao" id="submit" value="Cadastrar">
	</form>
	<?php } else {
		// Redireciona o usuário para a sua própria conta
		header('location: conta.php');
	} ?>
</section>

<!-- Faz a importação dos scripts para mascaras de inputs -->
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="js/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="js/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/jquery.zebra-datapicker.js"></script>
<?php
	require_once('footer.php');
?>
