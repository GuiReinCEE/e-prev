<?php
set_title('Encaminhamento Suporte -> Cadastro');
$this->load->view('header');
?>
<script type="text/javascript">
	<?= form_default_js_submit(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia', 'cd_atendimento', 'cd_atendimento_encaminhamento_tipo', 'descricao')); ?>

    function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/encaminhamento"); ?>';
	}	

	function carregar_dados_participante(data)
    {
		$('#nome').val(data.nome);
	}	
	
	$(function() 
	{		
		consultar_participante__cd_empresa();
	});	
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_jogo', 'Cadastro', TRUE, 'location.reload();');
echo aba_start( $abas );
	echo form_open('ecrm/encaminhamento/cadastroSalvar');
		echo form_start_box( "default_box", "Detalhe", true, false);
		
			$c['emp']['id']    = 'cd_empresa';
			$c['re']['id']     = 'cd_registro_empregado';
			$c['seq']['id']    = 'seq_dependencia';
			$c['emp']['value'] = $cd_empresa;
			$c['re']['value']  = $cd_registro_empregado;
			$c['seq']['value'] = $seq_dependencia;
			$c['caption']      = 'Participante: (*)';
			$c['callback']     = 'carregar_dados_participante';	
			
			echo form_default_text('cd_atendimento', "Protocolo do Atendimento: (*)", $cd_atendimento, "style='width:100%;border: 0px;' readonly" );
			echo form_default_participante_trigger($c);
			echo form_default_text('nome', 'Nome:', '', 'style="width:500px;" readonly');
			echo form_default_dropdown('cd_atendimento_encaminhamento_tipo', 'Tipo: (*)', $ar_atendimento_encaminhamento_tipo, '', 'style="width:500px;"');
			echo form_default_textarea('descricao', 'Descrição: (*)', '', 'style="width:500px; height: 70px;"');
			
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save('Salvar');
		echo form_command_bar_detail_end();	
		
	echo form_close();
	echo br(5);
echo aba_end();
$this->load->view('footer_interna');
?>
