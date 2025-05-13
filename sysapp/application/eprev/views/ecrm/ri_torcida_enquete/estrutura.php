<?php 
set_title('Torcida - Estrutura da Enquete');
$this->load->view('header'); 
?>
<script>
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/ri_torcida_enquete"); ?>';
	}
	
	function ir_cadastro()
	{
		location.href="<?php echo site_url('ecrm/ri_torcida_enquete/detalhe/'.$cd_enquete); ?>";
	}
	
	function ir_estrutura()
	{
		location.href="<?php echo site_url('ecrm/ri_torcida_enquete/estrutura/'.$cd_enquete); ?>";
	}
	
	function ir_resultado()
	{
		location.href="<?php echo site_url('ecrm/ri_torcida_enquete/resultado/'.$cd_enquete); ?>";
	}

	function salvar_pergunta(f)
	{
		if(confirm('Salvar pergunta?'))
		{
			url = "<?php echo site_url('ecrm/ri_torcida_enquete/salvar_pergunta'); ?>";
			$.post( url, {cd_enquete:<?php echo $cd_enquete; ?>, ds_pergunta:$('#ds_pergunta').val()},
			function(data)
			{
				if(data!='true'){ alert(data) } 
			}
			);
		}
	}

	function adicionar_item()
	{
		if($('#ds_item').val()=='')
		{
			alert( 'Nome do Item deve ser informado.' );
			$('#ds_item').focus();
		}
		else
		{
			if(confirm('Adicionar?'))
			{
				url="<?php echo site_url('ecrm/ri_torcida_enquete/adicionar_pergunta_item'); ?>";
				$.post(url,{cd_enquete:<?php echo $cd_enquete; ?>,ds_item:$('#ds_item').val(),nr_ordem:$('#nr_ordem').val()},
				function(data)
				{
					if(data=='true')
					{
						$('#ds_item').val('');
						$('#nr_ordem').val('');
						location.href="<?php echo site_url('ecrm/ri_torcida_enquete/estrutura/'.$cd_enquete); ?>";
					}
				});
			}
		}
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', false, 'ir_cadastro();');
$abas[] = array('aba_estrutura', 'Estrutura', true, 'ir_estrutura();');
$abas[] = array('aba_resultado', 'Resultado', false, 'ir_resultado();');
echo aba_start( $abas );

echo form_open( 'ecrm/ri_torcida_enquete/salvar' );
echo form_hidden( 'cd_enquete', intval($cd_enquete) );

// Registros da tabela principal ...
echo 
	form_start_box( "default_box", "Pergunta" )
		.form_default_text('ds_pergunta', 'Pergunta', $pergunta, "style='width:300px;'")
		.form_default_row('', '', br().comando('salvar_pergunta_cmd', 'Salvar', 'salvar_pergunta(this.form);') )
	.form_end_box("default_box");

echo form_start_box( "item_box", "Itens" );
if(sizeof($pergunta_item))
{
	echo form_default_text( 'ds_item', 'Item', '', "style='width:300px;'" );
	echo form_default_integer( 'nr_ordem', 'Ordem', '', "style='width:100px;'" );
	echo form_default_row( "", "", comando('adicionar_btn', 'Adicionar', 'adicionar_item();').br() );
	echo form_default_lista_simples( '<b>Possíveis respostas</b>', $pergunta_item, 'ecrm/ri_torcida_enquete/excluir_pergunta_item', 'cd_enquete_pergunta_item' );
}
echo form_end_box("item_box");
?>
<script>
	$('#ds_item').focus();
</script>
<?php
echo aba_end();
echo form_close();
$this->load->view('footer_interna');
?>
