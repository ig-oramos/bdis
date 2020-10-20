<?php
	// Função que calcula a mensalidade a partir dos parâmetros $dbc -
	// variável de referência ao banco de dados -, e $conta - variável
	// de referência ao objeto conta da classe ContaBanco
	function mensalidade ($dbc, $conta) {

		// Consulta SQL
		$query = "SELECT mensalidade FROM conta WHERE conta_id = {$conta->getNumConta()}";

		// Executa a consulta SQL à procura da mensalidade, que nada
		// mais é do que uma data
		$data = mysqli_query($dbc, $query)
			or die ('Erro ao tentar consultar o banco de dados.<br/>'.
				'Detalhes: '. mysqli_error($dbc));

		// Coloca a data em um array (row)
		$row = mysqli_fetch_array($data);

		// Valor da Mensalidade
		$val = ($conta->getTipo()==0?12:20);

		$ano_a = date('Y'); // Ano atual
		$mes_a = date('m'); // Mês atual

		$ano = substr((int)$row['mensalidade'], 0, 4); // Ano da ultima mensalidade paga
		$mes = substr($row['mensalidade'], 5, 2); // Mês da ultima mensalidade paga

		$a = $ano_a - $ano; // Diferença entre os anos
		$m = $mes_a - $mes; // Diferença entre os meses

		if ($a > 0 && $m >= 0) { // Um ano completo, mais os meses
			$men = $val * (12 + $m);
		} elseif ($a > 0 && $m < 0) { // Apenas meses
			$men = (((12 - $mes) + $mes_a) * $val);
		} elseif ($a == 0 && $m > 0) { // Apenas meses
			$men = $m * $val;
		}

		return isset($men)?$men:0;
	}
?>