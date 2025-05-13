<?php
set_title('Acompanhamento de Projetos - Reuniões');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('dt_reuniao'));
	?>
	
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/acompanhamento"); ?>';
	}
	
	function ir_cadastro()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/cadastro/".intval($row['cd_acomp'])); ?>';
	}	
	
	function ir_etapa()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/etapa/".intval($row['cd_acomp'])); ?>';
	}

	function ir_previsao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/previsao/".intval($row['cd_acomp'])); ?>';
	}	

	function ir_lista_reunicao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/reuniao/".intval($row['cd_acomp'])); ?>';
	}
	
	function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
			'DateTimeBR', 
			'CaseInsensitiveString', 
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
        ob_resul.sort(0, true);
    }
	
	function load()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");

        $.post( '<?php echo site_url('atividade/acompanhamento/listar_reuniao_anexo'); ?>',
		{
			cd_reuniao : $('#cd_reuniao').val()
		},
        function(data)
        {
			$('#result_div').html(data);
            configure_result_table();
        });
	}
	
	function excluir_anexo(cd_reuniao_anexo)
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir o anexo?\n\n"))
		{
			$.post( '<?php echo site_url('atividade/acompanhamento/excluir_reuniao_anexo'); ?>',
			{
				cd_reuniao_anexo : cd_reuniao_anexo
			},
			function(data)
			{
				load();
			});
		}
	}
	
	function imprimir()
    {
        window.open('<?php echo site_url("atividade/acompanhamento/imprimir_reuniao/".intval($row['cd_acomp']).'/'.intval($row_reunicao['cd_reuniao'])); ?>');
    }
	
	$(function(){
		if($('#cd_reuniao').val() > 0)
		{
			load();
		}
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Acompanhamento', FALSE, 'ir_cadastro();');
$abas[] = array('aba_reuniao', 'Reuniões', FALSE, 'ir_lista_reunicao();');
$abas[] = array('aba_reuniao', 'Cadastro Reunião', TRUE, 'location.reload();');
$abas[] = array('aba_etapas', 'Etapas', FALSE, 'ir_etapa();');
$abas[] = array('aba_previsao', 'Previsão', FALSE, 'ir_previsao();');	
	
$status = "Projeto em andamento";
$cor_status = "blue";

if (trim($row['dt_encerramento']) != '') 
{
	$status = 'Projeto encerrado em: '. $row['dt_encerramento'];
	$cor_status = "red";
}	

if (trim($row['dt_cancelamento']) != '') 
{
	$status = 'Projeto cancelado em: '. $row['dt_cancelamento'];
	$cor_status = "red";
}
	
echo aba_start( $abas );
	echo form_open('atividade/acompanhamento/salvar_reuniao');
		echo form_start_box( "default_box", "Acompanhamento" );
			echo form_default_hidden('cd_acomp', '', $row);
			echo form_default_hidden('cd_reuniao', '', $row_reunicao);
			echo form_default_text('cd_acomp_h', "Código :", intval($row['cd_acomp']), "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('ds_projeto', "Projeto :", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('status', "Status :", $status, "style='color: ".$cor_status."; font-weight:bold; width:400px;border: 0px;' readonly" );
		echo form_end_box("default_box");
		echo form_start_box( "default_reuniao_box", "Reunião" );
			echo form_default_date('dt_reuniao', 'Dt. Reunião :*', $row_reunicao);
			echo form_default_textarea('descricao', 'Resumo :', $row_reunicao, 'style="height:100px;"');
			echo form_default_textarea('motivo', 'Motivo não ocorrência :', $row_reunicao, 'style="height:100px;"');
			echo form_default_checkbox_group('arr_presentes', 'Presentes :', $arr_presente, $arr_presente_checked, 300);
			echo form_default_textarea('assunto', 'Assuntos Tratados :', $row_reunicao, 'style="height:100px;"');
			if(intval($row_reunicao['cd_reuniao']) > 0)
			{
				if(trim($row_reunicao['ds_arquivo_fisico']) != '')
				{
					echo form_default_row('ds_arquivo', 'Anexo :', anchor_file(str_replace('\\', '/', $row_reunicao['ds_arquivo_fisico']), $row_reunicao['ds_arquivo'], array('target' => '_black')));
				}
			}
		echo form_end_box("default_reuniao_box");
		echo form_command_bar_detail_start();
			if ((trim($row['dt_encerramento']) == '') and (trim($row['dt_cancelamento']) == ''))
			{
				echo button_save("Salvar");
			}
			
			if(intval($row_reunicao['cd_reuniao']) > 0)
			{
				echo button_save("Imprimir", 'imprimir();', 'botao_disabled');
			}
		echo form_command_bar_detail_end();	
	echo form_close();		
	if(intval($row_reunicao['cd_reuniao']) > 0)
	{
		if ((trim($row['dt_encerramento']) == '') and (trim($row['dt_cancelamento']) == ''))
		{
			echo form_open('atividade/acompanhamento/salvar_reuniao_anexo');
				echo form_start_box( "default_anexos_box", "Anexos" );
					echo form_default_hidden('cd_acomp', '', $row);
					echo form_default_hidden('cd_reuniao', '', $row_reunicao);
					echo form_default_upload_iframe('arquivo', 'reuniao_projeto', 'Arquivo :*', '', 'reuniao_projeto', false, '$("form").submit();');
				echo form_end_box("default_anexos_box");
			echo form_close();	
		}
		echo br();
		echo'<div id="result_div"></div>';
	}
	echo br();
echo aba_end();

$this->load->view('footer_interna');
?>