<?php
	function fechar ($conta, $dbc, $men) {

		$conta_id = $conta->getNumConta();
		// Armazena o resultado do método fechar do objeto conta em uma variável
		$result = $conta->fecharConta();

		// Verifica o resultado
		if ($result === 0) { // Conta com débito
			return '<p class="error">Impos. fechar conta.<br/>'.
				'Detalhes: conta com débito.</p>';
		} elseif ($result === 1) { // Conta com capital
			return '<p class="error">Impos. fechar conta.<br/>'.
				'Detalhes: conta não está vazia.</p>';
		} elseif ($men >= 12) {
			return '<p class="error">Impos. fechar conta.<br/>'.
				'Detalhes: mensalidade não está em dia.</p>';
		} 
			// Conta pode ser fechada

			// Consulta SQL de exclusão
			$query = "DELETE FROM conta WHERE conta_id = $conta_id";

			// Executa a exclusão da conta
			mysqli_query($dbc, $query)
				or die ('Erro ao tentar consultar o banco de dados.<br/>'.
					'Detalhes: '. mysqli_error($dbc));

			// Retorna o resultado da ação fechar
			return $result;
	}
?>