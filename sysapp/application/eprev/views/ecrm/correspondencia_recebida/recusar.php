<?php 
set_title('Protocolo Correspond�ncia Recebida');
$this->load->view('header'); 
?>
<script>
function recusar(form)
{
	if($("#motivo_recusa").val() == "")
	{
		alert( "Informe os campos obrigat�rios! \n\n(os campos obrigat�rios tem um * logo ap�s a identifica��o).\n\n[motivo_recusa]" );
		$("#motivo_recusa").focus();
		return false;
	}
	
	if(confirm("ATEN��O\n\nDeseja recusar a correspond�ncia?\n\nClique [Ok] para Sim\nClique [Cancelar] para N�o\n\n"))
	{
		$('form').submit();
	}
}

function ir_lista()
{
	location.href='<?php echo site_url('/ecrm/correspondencia_recebida');?>';
}

function ir_relatorio()
{
	location.href='<?php echo site_url("ecrm/correspondencia_recebida/relatorio/"); ?>';
}

function ir_receber()
{
	location.href='<?php echo site_url("ecrm/correspondencia_recebida/receber/".intval($cd_correspondencia_recebida)); ?>';
}

</script>
<?
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_lista', 'Relat�rio', false, 'ir_relatorio();');
$abas[] = array('aba_lista', 'Receber', false, 'ir_receber();');
$abas[] = array('aba_detalhe', 'Recusar', true, 'location.reload();');

echo aba_start( $abas );
	echo form_open('ecrm/correspondencia_recebida/recusar_correspondecia', 'name="filter_bar_form"');
		echo form_start_box("default_box", "Cadastro");
			echo form_hidden('cd_correspondencia_recebida', $cd_correspondencia_recebida);
			echo form_hidden('cd_correspondencia_recebida_item', $cd_correspondencia_recebida_item);
			echo form_default_textarea('motivo_recusa', 'Motivo:*');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();     
			echo button_save("Recusar", 'recusar(form)', 'botao_vermelho');
        echo form_command_bar_detail_end();
	echo form_close();
echo aba_end();

$this->load->view('footer_interna');
?>