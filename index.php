<script>
	// Configuração da API baseada no ambiente
	const API_BASE_URL = '<?php echo env('BASE_URL', 'http://localhost'); ?>/api';
	window.API_BASE_URL = API_BASE_URL;
</script>

<?php
header('Location: dashboard.php');
exit();
?>