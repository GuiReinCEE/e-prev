<script>
	function ir_lista()
    {
        location.href = "<?= site_url('ecrm/intranet/pagina/PE/10475/0') ?>";
    }   
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_ações_lista', 'Ações', TRUE, 'location.reload();');

	echo aba_start($abas);
	?>

	<iframe src="<?= base_url().'up/planejamento_estrategico/'.$row['arquivo_plano_execucao'] ?>" width="100%" frameborder="0" name="rel" id="iFrameRelatorioId" style="margin-top:2px;height:800px;"></iframe>

<?php
		echo br(2);
	echo aba_end();
?>