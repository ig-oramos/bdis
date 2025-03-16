<?php
	// Função que retorna informações da conta do usuário
	function info ($cliente_id, $dbc) {
		$query = "SELECT co.tipo, cl.nome, cl.sobrenome, co.saldo, co.conta_id FROM conta AS co JOIN cliente AS cl USING (cliente_id) WHERE co.cliente_id = '$cliente_id'";
  		$data = mysqli_query($dbc, $query)
  			or die ('Erro ao tentar consultar o banco de dados.<br/>'.
  				'Detalhes: '. mysqli_error($dbc));
  		return $row = mysqli_fetch_array($data);
	}
?>