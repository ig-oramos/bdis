<?php
	// Destroi - se configurada - a variável de sessão
	// fazendo com que o usuário atual seja deslogado
	if (!isset($_SESSION))
		session_start();
	if (isset($_SESSION['cliente_id'])) {
		unset($_SESSION['cliente_id']);
		header('location: acess.php');
	}
?>