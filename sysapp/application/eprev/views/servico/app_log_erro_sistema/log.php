<?php
	set_title('Log Erro de Sistema');
	$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href = "<?= site_url('servico/app_log_erro_sistema') ?>";
	}
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_log', 'Log', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo '<pre>'.$conteudo.'</pre>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>