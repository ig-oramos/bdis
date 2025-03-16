<?php
	function depositar ($conta, $valor, $dbc) {

		// Executa o método depositar do objeto conta e salva o retorno em uma variável
		$info = $conta->depositar($valor);

		// Consulta SQL de atualização de dados
		$query = "UPDATE conta SET saldo = {$conta->getSaldo()} WHERE conta_id = {$conta->getNumConta()}";
		mysqli_query($dbc, $query)
			or die ('Erro ao tentar consultar o banco de dados.<br/>'.
				'Detalhes: '. mysqli_error($dbc));

		// Retorna o resultado do depósito
		return $info;
	}
?>