<?php
set_title('Súmulas do Conselho Fiscal - Resposta');
$this->load->view('header');
?>
<script>
<?php
    echo form_default_js_submit(array('descricao'));
?>	
	function ir_lista()
	{
		location.href='<?php echo site_url("gestao/sumula_conselho_fiscal/minhas"); ?>';
	}
	
	function ir_anexo()
	{
		location.href='<?php echo site_url("gestao/sumula_conselho_fiscal/anexo"); ?>/' + $('#cd_sumula_conselho_fiscal_item').val();
	}
	
	function mudar_responsavel()
	{
		if($('#cd_responsavel').val() != $('#cd_responsavel_p').val())
		{
			location.href='<?php echo site_url("gestao/sumula_conselho_fiscal/mudar_responsavel/".intval($row['cd_sumula_conselho_fiscal_item'])); ?>/'+$('#cd_responsavel').val();
		}
		else
		{
			alert('Para salvar você deve alterar o responsável.');
		}
	}
    
</script>
<?php
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_lista', 'Resposta', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');

echo aba_start( $abas );
    echo form_open('gestao/sumula_conselho_fiscal/salvar_resposta', 'name="filter_bar_form"');
		echo form_start_box("default_item_box", "Item Súmula");
			echo form_default_hidden('cd_sumula_conselho_fiscal_item','', $row);
            echo form_default_text('nr_sumula_conselho_fiscal', 'Número:', $row, "style='width:100%;border: 0px;' readonly");
            
            echo form_default_text('dt_inclusao', 'Dt Súmula:', $row, "style='width:100%;border: 0px;' readonly");
            echo form_default_text('dt_divulgacao', 'Dt Divulgação:', $row, "style='width:100%;border: 0px;' readonly");
            echo form_default_row("sumula", "Arquivo:", '<a href="' . base_url() . 'up/sumula_conselho_fiscal/' . $row['arquivo'] . '" target="_blank">' . $row['arquivo_nome'] . ' [abrir]</a>');
            echo form_default_text('nr_sumula_conselho_fiscal_item', 'Número do Item da Súmula:', $row, "style='width:100%;border: 0px;' readonly");
            echo form_default_textarea('descricao_sumula', 'Descrição da Súmula', $row, " style='border: 1px solid gray;' readonly");
		echo form_end_box("default_item_box");
		echo form_start_box("default_encaminhar_box", "Encaminhar");
			echo form_default_usuario_ajax('cd_responsavel', $row['cd_gerencia'], $row['cd_responsavel'], "Responsável : ", "Gerência  do Responsável: ");
			echo form_default_hidden('cd_responsavel_p','', $row['cd_responsavel']);
		echo form_end_box("default_encaminhar_box");
		echo form_command_bar_detail_start();    
			if($row['dt_resposta'] == '')
			{
				echo button_save("Alterar Responsável", 'mudar_responsavel()');
			}
		echo form_command_bar_detail_end();
		
        echo form_start_box( "default_box", "Resposta" );
        	echo form_default_text('nome_do_responsavel', 'Responsável:', $row, "style='width:100%;border: 0px;' readonly");
            echo form_default_text('substituto', 'Substituto:', $row, "style='width:100%;border: 0px;' readonly");
            echo form_default_text('dt_envio', 'Dt Envio:', $row, "style='width:100%;border: 0px;' readonly");
			echo form_default_text('dt_limite', 'Dt Limite:', $row, "style='width:100%;border: 0px; font-weight:bold; color:red;' readonly");
            echo form_default_textarea('descricao', 'Observação :', $row);
        echo form_end_box("default_box");
        echo form_command_bar_detail_start();    
			if($row['dt_resposta'] == '')
			{
				echo button_save("Responder");
			}
        echo form_command_bar_detail_end();
   echo form_close();
    
   echo br();	

echo aba_end();

$this->load->view('footer_interna');
?>