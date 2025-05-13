<?php
	set_title('Meu Retrato Edição - Verificar');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/meu_retrato_edicao/verificar_listar') ?>",
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
		});	
	}

	function ir_lista()
    {
    	location.href = "<?= site_url('ecrm/meu_retrato_edicao') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/cadastro/'.$row['cd_edicao']) ?>";
    }
			
    function ir_participante()
    {
        location.href = "<?= site_url('ecrm/meu_retrato_edicao/participante/'.$row['cd_edicao']) ?>";
    }
			
	$(function(){
		if($("#qt_amostra").val() == "")
		{
			$("#qt_amostra").val(20);
		}
		
		if($("#cd_item").val() == "")
		{
			$("#cd_item").val("SALDO_ACUMULADO");
		}

		filtrar();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_participante', 'Participante', FALSE, 'ir_participante();');
    $abas[] = array('aba_verificar', 'Verificar', TRUE, 'location.reload();');

	$AR_ITEM_VERIFICAR[] = array('value' => 'SALDO_ACUMULADO', 'text' => "Saldo acumulado");
	$AR_ITEM_VERIFICAR[] = array('value' => 'SALDO_RENDIMENTO', 'text' => "Rendimento Financeiro");
	$AR_ITEM_VERIFICAR[] = array('value' => 'SALARIO_CONTRIB', 'text' => "Valor referência contribuição");	
	$AR_ITEM_VERIFICAR[] = array('value' => 'CONTRIB_MES_TOTAL', 'text' => "Contribuição do mês");
	$AR_ITEM_VERIFICAR[] = array('value' => 'CONTRIB_ATE_HOJE_PARTIC', 'text' => "Contribuição acumulado Participante");
	$AR_ITEM_VERIFICAR[] = array('value' => 'CONTRIB_ATE_HOJE_PATROC', 'text' => "Contribuição acumulado Patrocinadora/Empregador");
	$AR_ITEM_VERIFICAR[] = array('value' => 'CONTRIB_ATE_HOJE_PORTAB', 'text' => "Portabilidade");
	$AR_ITEM_VERIFICAR[] = array('value' => 'BEN_INICIAL,BEN_INICIAL_1', 'text' => "Benefício inicial simulado");
	$AR_ITEM_VERIFICAR[] = array('value' => 'BEN_SALDO_ACUMULADO_1,SIMULA_SALDO_ACUMULADO_ATUAL_C1', 'text' => "Saldo acumulado simulado");
	
	echo aba_start($abas);
		echo form_list_command_bar(array());
		echo form_start_box_filter(); 
			echo form_default_hidden('cd_edicao', '', $row['cd_edicao']);
			echo filter_dropdown('cd_item', 'Item:', $AR_ITEM_VERIFICAR, array('SALDO_ACUMULADO'));
			echo filter_integer('qt_amostra', 'Qt Amostra:');
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>