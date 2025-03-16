<?php
	// Verifica se a sessão já foi configurada
	if (!isset($_SESSION)) {
		session_start();
	}

	// Verifica se o índice do cliente já foi iniciado
	// o que configuraria um login
	if (!isset($_SESSION['cliente_id'])) {
		header("location: javascrip:history.back()");
	}

	// Faz a adição dos scripts necessários
	require_once('info_conta.php');
  require_once('mensalidade.php');
  require_once('ContaBanco.php');
	require_once('connect_vars.php');

	// Faz a conexão com o banco de dados
	$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
		or die ('Erro ao tentar fazer a conexão com o servidor MySQL.<br/>'.
			'Detalhes: '. mysqli_error($dbc));

	// Variável que recebe a expressão regular
	$regex = '/^[0-6]$/'; // Um dígito de 0 a 6

	// Verifica se o valor $_GET['op'] está entre 0 e 6
	if (preg_match($regex, isset($_GET['op'])?$_GET['op']:0)) {
	
		// Variável que recebe a opção do usuário
		$op = isset($_GET['op'])?$_GET['op']:0;
	} else {	

		// Se não a variável $op recebe o valor padrão
		$op = 0;
	}

	$title = 'Conta'; // Título da página
	$title1 = 'Acesso a conta'; // Título do cabeçalho
  // Subtítulo do cabeçalho
	$subtitle = 'Veja suas informações, Pague sua mensalidade, deposite, saque ou feche sua aconta!';
	$links = array('conta'); // arquivos css a serem importados
	require_once('header.php'); // adiciona o cabeçalho

	// Chama a funcão de informações da conta
	$row = info($_SESSION['cliente_id'], $dbc);
?>
<section id="corpoC">
		<?php
		if ($op == 0) { // Opção padrão
			if (isset($row['conta_id'])) { // Verifica se a conta foi configurada
		?>
		<ul id="menu_conta">
			<a href="conta.php?op=1" id="info"><li id="info">Informações da conta</li></a>
			<?php
        // Instancia um novo objeto da classe ContaBanco
        $conta = new ContaBanco($row['conta_id'], $row['tipo'], $row['nome'], 
                  $row['saldo'], 1);
        $men = mensalidade($dbc, $conta);
        if ($men >= 12) {
      ?>
      <a href="conta.php?op=2"><li id="pagMen">Pagar Mensalidade</li></a>
      <?php
        }
      ?>
			<a href="conta.php?op=3"><li id="deposit">Depositar</li></a>
			<a href="conta.php?op=4"><li id="fechar">Fechar</li></a>
			<a href="conta.php?op=5"><li id="sacar">Sacar</li></a>
		</ul>
  		<?php
  			} elseif ($op != 6) { // Se não e se a opção for diferente de 6
  		?>
  		<ul id="menu_conta">
  			<a href="conta.php?op=6"><li id="n_conta">+ Abrir nova conta</li></a>
  		</ul>
  		<?php
  			}
  		} else { // Outra opção

        // Instancia um novo objeto da classe ContaBanco
        $conta = new ContaBanco($row['conta_id'], $row['tipo'], $row['nome'], 
                  $row['saldo'], 1);

  			// Testa a variável de opção do usuário
  			switch ($op) {
  				case 1: // Mostra as informações da conta
  					echo '<table id="t_info"><tr><td colspan="2" id="title">Informações da conta</td></tr>';
  					echo '<tr><td class="cl">Número da conta:</td><td class="cr">'. $conta->getNumConta().
              '</td></tr>';
  					echo '<tr><td class="cl">Dono:</td><td class="cr">'. $conta->getDono(). ' '. 
            $row['sobrenome']. '</td></tr>';
  					echo '<tr><td class="cl">Saldo:</td><td class="cr">R$'. 
            number_format($conta->getSaldo(), 2, ',', '.'). '</td></tr>';
  					echo '<tr><td class="cl">Tipo de conta:</td><td class="cr">'.
             ($conta->getTipo()==0?'corrente':'poupança'). '</td></tr></table>';
  					break;
  				case 2: // Pagar Mensalidade
            
            // Chama o método mensalidade e guarda seu valor na variável
            $men = mensalidade($dbc, $conta);

            if ($men >= 12) {

              // Verifica se o formulário já foi submetido através do array $_POST
              if (!isset($_POST['sN'])) {
                // Exibe a mensalidade atual do usuário
                echo '<div id="men"><p>Mensalidade Atual de R$'. 
                number_format($men, 2, ',', '.').'.<br/>';
                echo 'Saldo atual de R$'. $conta->getSaldo(). '</p></div>';
                echo '<form method="post" action="conta.php?op=2" id="val"><label>Deseja '.
                  'efetuar o pagamento?</label><br/><input type="submit" name="sN" class'.
                  '="botao" value="Sim" id="s"><input type="submit" name="sN" class="botao'.
                  '" value="Não" id="n"></form>';
              } else {
                if ($_POST['sN'] == 'Sim') {

                  // Armazena o resultado do método em uma variável
                  $result = $conta->pagarMensal($men);
                  if ($result == 0) {
                    echo '<p class="error">Impos. pagar mensalidade.<br/>'.
                      'Detalhes: saldo insuficiente.</p>';
                  } else {
                    echo '<p class="success">Mensalidade paga com sucesso. Saldo de R$'.
                      number_format($conta->getSaldo(), 2, ',', '.'). '.</p>';

                    // Consulta SQL de alteração/atualização de dados
                    $query = "UPDATE conta SET mensalidade = '". date('Y-m-d'). "', saldo = {$conta->getSaldo()} WHERE conta_id = {$conta->getNumConta()}";

                    // Executa a consulta e altera os dados da conta do usuário (saldo e mensalidade)
                    mysqli_query($dbc, $query) or die ('Erro ao tentar consultar o banco de dados.'.
                      '<br/>Detalhes: '. mysqli_error($dbc));
                  }

                } else { // Operação cancelada
                  echo '<p class="opc">Operação cancelada.<br/></p>';
                }
              }
            } else {
              echo '<p class="success" style="color: #000000">Mensalidade já paga.</p>';
            }
  					break;
  				case 3:// Faz um depósito na conta
  					if (!isset($_POST['submit'])) {
              echo '<div id="men"><p>Saldo atual de R$'. $conta->getSaldo(). '</p></div>';
	  					echo '<form method="post" action="" id="val">';
	  					echo '<label for="dep">Valor a ser depositado: R$</label>';
	  					echo '<input type="number" name="dep" id="dep" min="10" max="1000000000"'.
	  						'placeholder="Apenas números" step="0.01">';
	  					echo '<input type="hidden" name="op" value="2"><br/>';
	  					echo '<input type="submit" name="submit" id="submit" class="botao" '.
	  						'value="Depositar"></form>';
  					} else {

  						// Usa as funções PHP trim e mysql para evitar possíveis
  						// injeções de SQL, além disso verifica se o valor e maior
  						// do que 10 e menor do que 1 mi
  						$dep = trim(mysqli_real_escape_string($dbc,
  							($_POST['dep']>=10&&$_POST['dep']<=1000000000?$_POST['dep']:0)));

  						// Adiciona o scrip depositar 
  						require_once('depositar.php');

  						// chama a função depositar
  						$result = depositar($conta, $dep, $dbc);

  						// Exibe a informação sobre o depóstio
  						echo $result. '<br/>';
  					}
  					break;
  				case 4: // Fechar a conta
  					if (!isset($_POST['submit'])) {
  						echo '<div id="f_confirm">';
  						echo '<form method="post" action="" id="val"><label id="ctz">Tem certeza'.
  							' que deseja fechar sua conta?</label><br/><input type="submit"'. 
  							' name="submit" value="Sim" id="s" class="botao"><input type="'. 
  							'submit" name="submit" value="Não" id="n" class="botao"><input'.
  							' type="hidden" name="op" value="3"></form><br/><p style="'.
                'text-align: center; margin: -1% 0% 2% 0%; color: #FF0000">'.
                '* Obs: para abrir uma nova conta é necessário pagar uma taxa'.
                ' de R$'. ($conta->getTipo()==0?12:20). '.</p>';
  						echo '</div>';
  					} else {
  						if ($_POST['submit'] == 'Sim') { // Se sim fecha a conta
  							require_once('close.php');
                require_once('mensalidade.php');

                // Armazena a mensalidade do usuário na variável
                $men = mensalidade($dbc, $conta);

  							// Se a conta atender aos requisitos de fechamento então
  							// uma mensagem de sucesso é exibida
  							echo fechar($conta, $dbc, $men). '<br/>';
  						} else { // Se não cancela a operação
  							echo '<p class="opc">Operação cancelada.<br/></p>';
  						}
  					}
  					
  					break;
  				case 5: // Sacar quantia da conta
  					if (!isset($_POST['submit1'])) {
              echo '<div id="men"><p>Saldo atual de R$'. $conta->getSaldo(). '</p></div>';
	  					echo '<form method="post" action="" id="val"><label for="saq">Valor'.
	  						' a ser sacado: R$</label><input type="number" step="0.01"'.
	  						' name="saq" id="saq" placeholder="Apenas números">'.
	  						'<input type="hidden" name="op" value="3">'.
	  						'<br/><input type="submit" name="submit1" value="Sacar"'.
	  						' class="botao" id="submit" style="margin-left: 38%"></form>';
	  					} else {

	  						// Adiciona o script sacar
	  						require_once('sacar.php');

	  						// Valor a ser sacado
	  						$valor = trim(mysqli_real_escape_string($dbc, $_POST['saq']));

		  					// Exibe o resultado do saque
		  					echo sacar($dbc, $valor, $conta). 
		  						'<br/>';
		  				}
  					break;
  				case 6 : // Abrir nova conta
  					if (isset($row['conta_id'])) { // Verifica se a conta existe
  						// Redireciona o usuário para as opções padrões
  						header('location: conta.php');
  					}
  					if (!isset($_POST['submit'])) { // Verifica se o tipo da nova conta foi submetido
	  					echo '<div id="t_conta">';
	  					echo '<form method="post" action="conta.php?op=6" id="val">'.
	  						'<label for="tipo" id="sn">Tipo da conta:</label><select'.
	  						' name="tipo" id="tipo">'.
	  						'<option value="0" checked>Conta corrente</opt>'. 
	  						'<option value="1">Conta poupança</opt></select><br/>'. 
	  						'<input type="submit" name="submit" id="submit" class="botao"'. 
	  						' value="Enviar"></form>';
  					} else { // Caso o tipo tenha sido submetido

  						// Instancia uma nova conta da classe ContaBanco
  						$conta1 = new ContaBanco();

  						// tipo da conta
  						$t_conta = $_POST['tipo'];

  						// utiliza o método abrirConta do objeto $conta para criar
  						// uma nova conta
  						$conta1->abrirConta($t_conta, $row['nome']);

              if ($conta->getTipo() === 0)
                $saldo = -12; // mensalidade conta corrente
              else
                $saldo = -20; // mensalidade conta poupança

  						// Por ser uma nova conta o usuário inicia com o saldo negativo
              // ou seja é necessário pagar uma taxa para abrir uma nova conta
  						$conta1->setSaldo($saldo);

  						// Consulta de inserção SQL
  						$query = "INSERT INTO conta (cliente_id, tipo, mensalidade, saldo, status) VALUES ($_SESSION[cliente_id], {$conta1->getTipo()}, '". date('Y-m-d'). "', {$conta1->getSaldo()}, {$conta1->getStatus()})";

  						// Insere um novo registro na tabela conta
  						mysqli_query($dbc, $query)
  							or die ('Erro ao tentar consultar o banco de dados.<br/>'.
  								'Detalhes: '. mysqli_error($dbc)); // ou retorna um erro

  						// Mensagem de sucesso
  						echo '<p class="success">Conta cadastrada com êxito.</p>';
  					}
  					break;
  				default: // Opção inválida
  					echo '<p class="error">Opção inválida.<br/></p>';
  					break;
  			}
  			// Botão voltar
  			echo '<div id="back"><a href="conta.php?op=0">Voltar ao menu</a></div>';
  		}
      mysqli_close($dbc); // Fecha a conexão com o banco de dados
  ?>
</section>
<?php
	// Adiciona o rodapé a página
	require_once('footer.php');
?>