<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width initial-scale=1"><!-- Configura a largura da página 
	de modo que se ajuste a do dispositivo -->
	<meta name="description" content="Banco digital, especializado em serviços online."> <!-- Descrição -->
	<meta name="keywords" content="Banco digital, remoto, online, Serviços ao cliente"> <!-- Palavras chave -->
	<meta name="robots" content="index, follow"> <!-- Motores de busca - links -->
	<meta name="author" content="Igor Gomes Oliveira Ramos">
	<title>Banco Dos Is - <?php echo $title; ?></title>
	<link rel="stylesheet" type="text/css" href="_css/style.css">
	<link href="https://fonts.googleapis.com/css?family=Fira+Sans" rel="stylesheet"><!-- Fonte externa -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css"><!-- Adiciona botões, como importações, e os deixa prontos para utilização -->
	<link rel="icon" href="_images/icon-bdis2.png">
	<?php
		if (!empty($links)) {
			foreach ($links as $link) {
				echo '<link rel="stylesheet" type="text/css" href="_css/'. $link. '.css">';
			}
		}
	?>
</head>
<body>
<div id="interface">
	<header class="cabecalho">
		<a href="index.php"><h1 class="logo">Banco Dos Is</h1></a>

		<button class="btn-menu"><i class="fa fa-bars fa-lg"><!-- Adiciona um botão, 
		"fa-lg" aumenta o tamanho do botão. Obs: o botão é importado para o site 
		através do link
		--></i></button>
		<nav class="menu">
			<a class="btn-close"><i class="fa fa-times"></i></a>
			<ul id="list-menu">
				<?php 
					if (!isset($_SESSION))
						@session_start();
					if (isset($_SESSION['cliente_id'])) {
						if (!isset($dbc)) {
							require_once('connect_vars.php');
							$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
						}
						$query = "SELECT cl.nome, cl.sobrenome, co.conta_id from cliente AS cl JOIN conta AS co USING (cliente_id) WHERE cliente_id = '$_SESSION[cliente_id]'";
						$data = mysqli_query($dbc, $query);
						$row = mysqli_fetch_array($data);
				?>
						<a href="conta.php?op=1"><button id="btn-user"><?php echo substr($row['nome'], 0, 1);
						if (!empty($row['sobrenome']))
							echo substr($row['sobrenome'], 0, 1); ?></button></a>
						<li><a href="conta.php">Conta</a></li>
						<li><a href="logout.php">Deslogar</a></li>
				<?php
					} else {
				?>
					<li><a href="access.php">Acessar</a></li>
					<li><a href="create.php">Criar</a></li>
				<?php
					}
				?>
				<li><a href="index.php">Home</a></li>
			</ul>
		</nav>
		<!-- JQuery -->
	<script src="http://code.jquery.com/jquery-1.12.0.min.js"></script><!-- Faz a importação de código
		Javascript para o menu -->
	<script>
		$(".btn-menu").click(function() {
			$(".menu").show(); // Exibe o menu quando o botão for acionado
		});
		$(".btn-close").click(function() {
			$(".menu").hide(); // Oculta o menu
		});
	</script>
	</header>
	<!-- Banner da página -->
	<div class="banner">
		<div class="title">
			<?php if (!empty($title1)) echo "<h2>$title1</h2>"; ?>
			<?php if (!empty($subtitle)) echo "<h3>$subtitle</h3>"; ?>
		</div>
		<?php
		if ($title === 'BDIs') {
		?>
		<div class="buttons">
			<a href="create.php"><button class="btn-criar">Criar<i class="fa fa-arrow-circle-right">
			</i></button></a>
			<a href="access.php"><button class="btn-acessar">Acessar<i class="fa fa-question-circle">
			</i></button></a>
		</div>
		<?php
		}
		?>
	</div>
	<div id="corpo">
	<!-- Barra de opções -->
	<div id="opt_usuario">
	<?php 
		if (isset($_SESSION['cliente_id'])) {
	?>
		<!-- Informações do cliente -->
		<div id="info_cliente">
			<ul id="info_user">
			<li id="num_conta"><?php echo $row['conta_id']; ?></li>
			<li id="nome_cliente"><?php echo $row['nome']. ' '. $row['sobrenome']; ?></li>
			</ul>
		</div>
		<p><a href="index.php">Home</a> > <?php echo $title; ?></p>
	<?php
	}
	?>
	</div>
