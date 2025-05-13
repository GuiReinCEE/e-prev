<?php
	set_title('Equipe - '.$cd_divisao);
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");

		$.post("<?= site_url('cadastro/equipe/listar'); ?>",
		{
			cd_divisao : "<?= $cd_divisao ?>"
		},
		function(data)
		{
			$("#result_div").html(data);
		});
	}

	function ir_area(cd_gerencia)
	{
		location.href = "<?= site_url('cadastro/equipe/index') ?>/" + cd_gerencia;
	}	
	
	$(function(){
		filtrar();
	});	
</script>
<?php
	foreach(array('AI', 'GAP.', 'GC',  'GFC', 'GIN', 'GJ', 'GNR', 'GS', 'DE') as $item)
	{
		$abas[] = array('aba_area_'.$item, $item, ($cd_divisao == $item), 'ir_area(\''.$item.'\');');
	}
	
	echo aba_start($abas);
		echo '<div id="result_div"></div>';
	echo aba_end(); 

	$this->load->view('footer');
?>