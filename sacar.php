<?php
	// Função que executa um saque a partir dos parâmetros
	function sacar ($dbc, $valor, $conta) {

		// Armazena o resultado do método sacar em uma variável
		$result = $conta->sacar($valor);

		// Consulta SQL de atualização de dados
		$query = "UPDATE conta SET saldo = {$conta->getSaldo()} WHERE conta_id = {$conta->getNumConta()}";

		// Executa a consulta alterando o saldo do usuário
		mysqli_query($dbc, $query)
			or die ('Erro ao tentar consultar o banco de dados.<br/>'.
				'Detalhes: '. mysqli_error($dbc));

		// Retorna o resultado do saque
		return $result;
	}
?>