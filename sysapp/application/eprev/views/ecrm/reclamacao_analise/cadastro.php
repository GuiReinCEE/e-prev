<?php
set_title('Reclamação - Análise');
$this->load->view('header');
?>
<script>
	<?php echo form_default_js_submit(Array('cd_reclamacao_analise_classifica', 'cd_usuario_responsavel', 'cd_usuario_substituto', 'dt_limite')); ?>    
	
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/reclamacao_analise"); ?>';
    }
		
	function listar_reclamacao()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
		
		$.post('<?php echo site_url('ecrm/reclamacao_analise/listar_reclamacao');?>',
		{
			cd_reclamacao_analise : $('#cd_reclamacao_analise').val()
		},
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			null,
			"CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "CaseInsensitiveString",
			"RE",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateTimeBR",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateTimeBR",
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateBR",
			"DateBR",
			"DateTimeBR",
			"DateTimeBR"
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
	
	function salvar_reclamacao(obj)
	{
		var str = $(obj).val();
		var arr = str.split("_");
		
		$.post('<?php echo site_url('ecrm/reclamacao_analise/salvar_reclamacao');?>',
		{
			ano                        : arr[0],
			numero                     : arr[1],
			tipo                       : arr[2],
			cd_reclamacao_analise_item : arr[3],
			fl_marcado                 : ($(obj).is(':checked') ? "S" : "N"),
			cd_reclamacao_analise      : $('#cd_reclamacao_analise').val()
		},
		function(data)
		{
			$("#btn_enviar").hide();
			
			if(data)
			{
				if(data.fl_enviar == "S")
				{
					$("#btn_enviar").show();
				}
			}
		},
		'json');
	}
	
	function enviar()
	{
		if(confirm('Deseja enviar?'))
		{
			location.href='<?php echo site_url("ecrm/reclamacao_analise/enviar/".intval($row['cd_reclamacao_analise'])); ?>';
		}
	}
	
	$(function(){
		if($('#cd_reclamacao_analise').val() > 0)
		{
			listar_reclamacao();
		}
	});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');
	
echo aba_start( $abas );
    echo form_open('ecrm/reclamacao_analise/salvar');
        echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_reclamacao_analise', '', $row);
            echo form_default_hidden('dt_envio', '', $row);
			if(intval($row['cd_reclamacao_analise']) > 0)
			{
				echo form_default_row('ano_numero', 'Ano/Número :', $row['ano_numero']);
			}
			
			if(trim($row['dt_envio']) == '')
			{
				echo form_default_dropdown_db("cd_reclamacao_analise_classifica", "Classificação :*", array('projetos.reclamacao_analise_classifica', 'cd_reclamacao_analise_classifica', 'ds_reclamacao_analise_classifica'),
																									  array($row['cd_reclamacao_analise_classifica']), "", "", TRUE);
																									  
				echo form_default_usuario_ajax('cd_usuario_responsavel', $row['cd_usuario_responsavel_gerencia'], $row['cd_usuario_responsavel'], "Responsável: *", "Gerência: *");
				echo form_default_usuario_ajax('cd_usuario_substituto', $row['cd_usuario_substituto_gerencia'], $row['cd_usuario_substituto'], "Substituto: *", "Gerência: *");
				echo form_default_date('dt_limite', 'Dt Limite :*', $row);
				echo form_default_textarea('observacao', 'Observação :', $row['observacao'], "style='width:500px; height: 60px;'");
			}
			else
			{
				echo form_default_hidden('cd_reclamacao_analise_classifica', '', $row);
				echo form_default_hidden('cd_usuario_responsavel', '', $row);
				echo form_default_hidden('cd_usuario_substituto', '', $row);
				echo form_default_hidden('dt_limite', '', $row);
			 
				echo form_default_row('row_cd_reclamacao_analise_classifica', 'Classificação :', $row['ds_reclamacao_analise_classifica']);
				echo form_default_row('row_cd_usuario_responsavel', 'Responsável :', $row['responsavel']);
				echo form_default_row('row_cd_usuario_substituto', 'Substituto :', $row['substituto']);
				echo form_default_row('row_dt_limite', 'Dt Limite :', '<span class="label '.(trim($row['dt_prorrogacao']) == "" ? "label-important" : "").'">'.$row['dt_limite'].'</span>');
				
				if(trim($row['dt_retorno']) != '')
				{
					echo form_default_hidden('dt_prorrogacao', '', $row);
					echo form_default_row('row_dt_prorrogacao', 'Dt Prorrogação :', '<span class="label label-important">'.$row['dt_prorrogacao'].'</span>');
					echo form_default_row('row_observacao', 'Observação :', nl2br($row['observacao']));
					echo form_default_row('row_dt_retorno', 'Dt Parecer da Gerência :', '<span class="label label-success">'.$row['dt_retorno'].'</span>');
					echo form_default_row('row_usuario_retorno', 'Usuário Parecer da Gerência :', $row['usuario_retorno']);
				}
				else 
				{
					echo form_default_date('dt_prorrogacao', 'Dt Prorrogação :', $row);
					echo form_default_row('row_observacao', 'Observação :', nl2br($row['observacao']));
				}
			}
		echo form_end_box("default_box");
        echo form_command_bar_detail_start();   
			if(trim($row['dt_retorno']) == '')
			{
				echo button_save("Salvar");
			}
			if((intval($row['cd_reclamacao_analise']) > 0) AND (trim($row['dt_envio']) == ''))
			{
				echo button_save("Enviar", 'enviar()', 'botao_verde', 'id="btn_enviar"');
			}
        echo form_command_bar_detail_end();
    echo form_close();
	echo '<div id="result_div"></div>';
    echo br(5);	
echo aba_end();

$this->load->view('footer_interna');
?>