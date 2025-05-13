<?php
    set_title('Contrato Digital - Cadastro');
    $this->load->view('header');
?>
<script>
    function ir_lista()
    {
        location.href = "<?= site_url('ecrm/contrato_digital') ?>";
    }

	function listarAssinadores()
	{
		$("#result_div").html("<?= loader_html() ?>");
				
		$.post("<?= site_url('ecrm/contrato_digital/listarAssinadores') ?>",
		{
			cd_contrato_digital : $("#cd_contrato_digital").val()
		},
		function(data)
		{
			$("#result_div").html(data);
			//configure_result_table();
		});	
	}
	
	$(function(){
		listarAssinadores();
	});	
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_start_box('default_box', 'Cadastro');
			echo form_default_hidden('cd_contrato_digital', 'Cód:', $row['cd_contrato_digital']);
			echo form_default_row('', 'Cód:', $row['cd_contrato_digital']);
			echo form_default_row('', 'RE:', $row['cd_empresa']."/".$row['cd_registro_empregado']."/".$row['seq_dependencia']);
			echo form_default_row('', 'Nome:', $row['nome_participante']);
			echo form_default_row('', 'Situação:','<span class="'.$row["situacao_label"].'">'.$row["situacao"].'</span>');
			echo form_default_row('', 'Dt. Inclusão:', $row['dt_inclusao']);
			echo form_default_row('', 'Dt. Limite:', '<span class="label label-info">'.$row["dt_limite"].'</span>');
			echo form_default_row('', 'Dt. Concluído (Assinado):', '<span class="label label-success">'.$row["dt_concluido"].'</span>');
			echo form_default_row('', 'Dt. Cancelado:', '<span class="label">'.$row["dt_cancelado"].'</span>');
			echo form_default_row('', 'Dt. Finalizado:', '<span class="label">'.$row["dt_finalizado"].'</span>');
			echo form_default_row('', 'Cód Liquid:', $row['cd_liquid']);
		echo form_end_box('default_box');
		
		echo form_start_box('default_ass_box', 'Assinadores');
			echo form_default_row('', '', '<div id="result_div" style="text-align: center;width: 800px;"></div>');
		echo form_end_box('default_ass_box');

        echo br(5);
    echo aba_end();

    $this->load->view('footer_interna');
?>