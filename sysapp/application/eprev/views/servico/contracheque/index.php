<?php
set_title('Contracheque - Colaborador');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		buscar_contracheque("","");
	}

	function buscar_contracheque(cc_dt_pagamento,cc_registro_empregado)
	{
		$("#result_div").html("<BR><BR><?php echo loader_html(); ?>");
			
		$.post('<?php echo site_url('servico/contracheque/listar');?>',
		{
			cc_dt_pagamento       : cc_dt_pagamento,
			cc_registro_empregado : cc_registro_empregado
		},
		function(data)
		{
			$("#result_div").html(data);
		});		
	}
	
	$(function(){
		filtrar();
	})
</script>
<?php
	echo '<div id="result_div" style="text-align: center; width: 100%;"></div>';
	echo br(8);
$this->load->view('footer'); 
?>