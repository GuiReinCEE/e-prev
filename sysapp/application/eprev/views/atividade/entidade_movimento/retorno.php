<?php
set_title('Entidade - Movimento');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('cd_movimento_retorno_tipo'), 'valida_arquivo(form)');
	?>
	
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/entidade_movimento"); ?>';
	}
	
	function ir_receber()
	{
		location.href='<?php echo site_url("atividade/entidade_movimento/receber/".$row['cd_movimento']); ?>';
	}
		
	function retorno()
	{
		var confirmacao = 'Deseja enviar o retorno?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url('atividade/entidade_movimento/salvar_retorno/'.$row['cd_movimento']); ?>';
		}
	}
	
	function valida_arquivo(form)
    {
        if($('#arquivo').val() == '' && $('#arquivo_nome').val() == '')
        {
            alert('Nenhum arquivo foi anexado.');
            return false;
        }
        else
        {
            if( confirm('Salvar?') )
            {
                form.submit();
            }
        }
    }
	
	function listar_anexo()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");

		$.post( '<?php echo site_url('atividade/entidade_movimento/listar_anexo'); ?>',
		{
			cd_movimento : $('#cd_movimento').val()
		},
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'CaseInsensitiveString',
			'DateTimeBR',
			'CaseInsensitiveString',
			null
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(1, true);
	}
	
	function excluir_anexo(cd_calculo_irrf_anexo)
	{
		var confirmacao = 'Deseja excluir o anexo?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
	
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url('atividade/entidade_movimento/excluir_anexo/'.$row['cd_movimento']); ?>/'+ cd_calculo_irrf_anexo;
		}
	}
	
	$(function(){
		listar_anexo();
	});
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Movimento', FALSE, 'ir_receber();');
$abas[] = array('aba_lista', 'Retorno', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_open('atividade/entidade_movimento/salvar_anexo');
		echo form_start_box( "default_box", "Movimento" );
			echo form_default_hidden('cd_movimento', "", $row);	
			echo form_default_text('nr_ano_numero', "Ano/Número :", $row, 'style="font-weight: bold; width:350px;border: 0px;" readonly' );
			echo form_default_text('ds_entidade', "Entidade :", $row, 'style="font-weight: bold; width:350px;border: 0px;" readonly' );
			echo form_default_text('nr_mes_nr_ano', "Mês/Ano Ref :", $row['mes_referencia'].'/'.$row['ano_referencia'], 'style="font-weight: bold; width:350px;border: 0px;" readonly' );
			echo form_default_text('dt_envio', "Dt. Envio :", $row, 'style="font-weight: bold; width:350px;border: 0px;" readonly' );
			echo form_default_text('dt_recebido', "Dt. Recebido :", $row, 'style="font-weight: bold; width:350px;border: 0px;" readonly' );
			echo form_default_text('dt_retorno', "Dt. Retorno :", $row, 'style="font-weight: bold; width:350px;border: 0px;" readonly' );
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			if((trim($row['dt_retorno']) == '') AND (trim($row['dt_recebido']) != '') AND (intval($row['tl_anexo']) > 0))
			{
				echo button_save("Enviar Retorno", "retorno();", "botao_verde");	
			}
		echo form_command_bar_detail_end();
		if((trim($row['dt_retorno']) == '') AND (trim($row['dt_recebido']) != ''))
		{
			echo form_start_box( "default_box", "Retorno" );
				echo form_default_upload_iframe('arquivo', 'entidade_movimento', 'Anexo :*', '', 'entidade_movimento', false);
				echo form_default_dropdown('cd_movimento_retorno_tipo', 'Tipo :*', $arr_retorno);
			echo form_end_box("default_box");
		}
		echo form_command_bar_detail_start();
			if((trim($row['dt_retorno']) == '') AND (trim($row['dt_recebido']) != ''))
			{
				echo button_save("Adicionar");	
			}
		echo form_command_bar_detail_end();
	echo form_close();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>