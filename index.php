<?php
	$title = 'BDIs';
	$title1 = 'ADQUIRA O QUE VOCÊ PRECISA!';
	$subtitle = '<a href="create.php">Crie</a> sua conta ou '.
		'<a href="access.php">acesse-a</a>!';
	require_once('header.php');
?>
	<section id="corpo">
		<p>O Banco Dos Is (BDIs) é altamente recomendado a qualquer pessoa que esteja a
		procura de um local seguro para <a href="access.php">armazenar</a> seu dinheiro,
		<a href="access.php">sacar</a> e ter fácil acesso a <a href="access.php">informações da
		conta</a>. Além disso, você pode fechar a conta quando quiser, para mais informações
		<a href="access.php">clique aqui</a> e entre em sua conta.</p>
		<figure id="bp">
		<img src="_images/enc_icon.png" alt="Banco protegido">
		<figcaption><a href="http://www.freeiconspng.com/img/15200">Imagem ilustrativa,
		clique para a fonte.</a></figcaption>
		</figure>
	</section>
	<aside id="lateral">
		<p><a href="create.php">Crie</a> sua conta hoje mesmo, ou <a href="access.php">acesse</a>
		ela agora mesmo!</p>
	</aside>
<?php
	require_once('footer.php');
	if (isset($dbc)) {
		mysqli_close($dbc);
	}
?>
