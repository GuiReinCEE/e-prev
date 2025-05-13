<?php

######################################################
# CASO ALTERE AS COLUNAS ALTERAR NO CRONOGRAMA E NO  #
# CRONOGRAMA PARTIAL O NUMERO DA COLUNA OCULTA       #   
######################################################

set_title('Cronograma - Atividades');
$this->load->view('header');
?>
<script>
	var visivel_operacional = 6;
	var visivel_gerente     = 7;
	var oculta_operacional  = 8;
	var oculta_gerente      = 9;

	function filtrar()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");
		
		$.post( '<?php echo site_url("/atividade/atividade_cronograma/listar_cronograma_item")?>',
		{
			cd_atividade_cronograma       : $('#cd_atividade_cronograma').val(),
			cd_divisao                    : $('#cd_divisao').val(),
			cd_atividade_cronograma_grupo : $('#cd_atividade_cronograma_grupo').val(),
			ini_operacional               : $('#ini_operacional').val(),
			fim_operacional               : $('#fim_operacional').val(),
			ini_gerente                   : $('#ini_gerente').val(),
			fim_gerente                   : $('#fim_gerente').val(),
			sistema                       : $('#sistema').val(),
			complexidade                  : $('#complexidade').val(),
			status_atual                  : $('#status_atual').val(),
			fl_prioridade_area            : $('#fl_prioridade_area').val(),
			fl_prioridade_consenso        : $('#fl_prioridade_consenso').val(),
			cd_solicitante                : $('#cd_solicitante').val()
		},
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),[
					"Number",
					"CaseInsensitiveString",
					"DateTimeBR",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
					"CaseInsensitiveString",
					"Number",
					"Number",
					null,
					null,
					null,
					"CaseInsensitiveString",
					"CaseInsensitiveString",
					"CaseInsensitiveString"
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
		ob_resul.sort(4, true);
	}
	
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/atividade_cronograma"); ?>';
	}
	
	function cronograma(cd_atividade_cronograma)
	{
		location.href='<?php echo site_url("atividade/atividade_cronograma/cadastro"); ?>' + "/" + cd_atividade_cronograma;
	}
	
	function cronogramaItem(cd_atividade_cronograma)
	{
		location.href='<?php echo site_url("atividade/atividade_cronograma/item"); ?>' + "/" + cd_atividade_cronograma;
	}	
	
	
	function incluir_todas(cd_atividade_cronograma)
	{
		location.href='<?php echo site_url("atividade/atividade_cronograma/incluir_todas"); ?>' + "/" + cd_atividade_cronograma;
	}	
	
	function ir_acompanhamento(cd_atividade_cronograma)
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma/acompanhamento"); ?>' + "/" + cd_atividade_cronograma;
	}	
	
	function imprimir()
    {
        filter_bar_form.method = "post";
        filter_bar_form.action = '<?php echo site_url("/atividade/atividade_cronograma/imprimir") ?>';
        filter_bar_form.target = "_self";
        filter_bar_form.submit();
    }
	
	function editar_grupo(cd_atividade_cronograma_item, t, cd_atividade_cronograma_grupo, fl)
	{
		var obj = $("#" + $(t).parent().get(0).id);

		var parent_linha  = obj.attr('linha');
		var parent_coluna = obj.attr('coluna');
		
		$.post( '<?php echo site_url("/atividade/atividade_cronograma/carrega_grupo")?>',
		{
			cd_atividade_cronograma_item : cd_atividade_cronograma_item,
			cd_atividade_cronograma_grupo : cd_atividade_cronograma_grupo,
			fl : fl
		},
        function(data)
        {
			$('#'+parent_linha+'_'+parent_coluna+'-table-1').html(data);
		});
	}
	
	function salvar_grupo(cd_atividade_cronograma_item, t, fl)
	{
		var obj = $("#" + $(t).parent().get(0).id);

		var parent_linha = obj.attr('linha');
		var parent_coluna = obj.attr('coluna');
		cd_atividade_cronograma_grupo = 0;
		
		if($("#cd_grupo_" + cd_atividade_cronograma_item).val() > 0)
		{
			cd_atividade_cronograma_grupo = $("#cd_grupo_" + cd_atividade_cronograma_item).val()
		}
		
		var editar = '<a href="javascript: void(0)" id="grupo_editar_'+cd_atividade_cronograma_item+'" onclick="editar_grupo('+cd_atividade_cronograma_item+', this,'+cd_atividade_cronograma_grupo+', '+fl+');" title="Editar grupo">[editar]</a>';
	

		
		$.post( '<?php echo site_url("/atividade/atividade_cronograma/salva_grupo")?>',
        {
            cd_atividade_cronograma_item : cd_atividade_cronograma_item,
            cd_grupo                     : $("#cd_grupo_" + cd_atividade_cronograma_item).val()	
        },
        function(data)
        {
		    $("#nr_grupo_" + cd_atividade_cronograma_item).hide();
			
			if($("#cd_grupo_" + cd_atividade_cronograma_item + " option:selected").text() != 'Selecione') 
			{				
				$("#"+parent_linha+"_"+parent_coluna+"-table-1").html($("#cd_grupo_" + cd_atividade_cronograma_item+ " option:selected").text()+' '+ editar); 
			}
			else
			{		
				$("#"+parent_linha+"_"+parent_coluna+"-table-1").html(editar); 
			}
        });
	}
	
	
	function oculta_coluna()
	{	
		$('#table-1 tbody tr').each(function(){
			$(this).children('td:eq('+visivel_operacional+')').hide();
			$(this).children('td:eq('+oculta_operacional+')').show();
			<?php 
			if(($this->session->userdata('tipo') == 'G' OR $this->session->userdata("indic_01") == "S") OR $fl_responsavel)
			{
			?>
			$(this).children('td:eq('+visivel_gerente+')').hide();
			$(this).children('td:eq('+oculta_gerente+')').show();
			<?php
			}
			?>
		});
	}	
	
	function editar_gerente(cd_atividade_cronograma_item)
	{
		oculta_coluna();
		$("#prioridade_valor_" + cd_atividade_cronograma_item).hide(); 
		$("#gerente_editar_" + cd_atividade_cronograma_item).hide(); 
		$("#gerente_salvar_" + cd_atividade_cronograma_item).show(); 
		
		$("#nr_prioridade_operacional_" + cd_atividade_cronograma_item).show(); 
		$("#nr_prioridade_operacional_" + cd_atividade_cronograma_item).focus();
	
		<?php 
		if(($this->session->userdata('tipo') == 'G' OR $this->session->userdata("indic_01") == "S") OR $fl_responsavel)
		{
		?>
		$("#gerente_valor_" + cd_atividade_cronograma_item).hide(); 		
		$("#nr_gerente_" + cd_atividade_cronograma_item).show(); 
		<?php
		}
		?>
	}
	
	function salvar_operacinal_gerente(cd_atividade_cronograma_item, t)
	{
		var obj = $("#" + $(t).parent().get(0).id);

		var parent_linha = obj.attr('linha');
	
		salvar_operacional(cd_atividade_cronograma_item, parent_linha);
		
		<?php 
		if(($this->session->userdata('tipo') == 'G' OR $this->session->userdata("indic_01") == "S") OR $fl_responsavel)
		{
		?>
		salvar_gerente(cd_atividade_cronograma_item, parent_linha);
		<?php
		}
		?>
		
		$("#gerente_salvar_" + cd_atividade_cronograma_item).hide(); 
		$("#gerente_editar_" + cd_atividade_cronograma_item).show(); 
		
	}
	
	function salvar_gerente(cd_atividade_cronograma_item, linha)
	{
		$("#ajax_gerente_" + cd_atividade_cronograma_item).html("<?php echo loader_html("P"); ?>");
		
		$.post( '<?php echo site_url("/atividade/atividade_cronograma/salva_gerente")?>',
        {
            cd_atividade_cronograma_item : cd_atividade_cronograma_item,
            nr_prioridade_gerente        : $("#nr_gerente_" + cd_atividade_cronograma_item).val()	
        },
        function(data)
        {
		    $("#ajax_gerente_" + cd_atividade_cronograma_item).empty();
		    $("#nr_gerente_" + cd_atividade_cronograma_item).hide();
            $("#gerente_valor_" + cd_atividade_cronograma_item).html($("#nr_gerente_" + cd_atividade_cronograma_item).val()); 
						
			$("#"+linha+"_"+visivel_gerente+"-table-1").html($("#nr_gerente_" + cd_atividade_cronograma_item).val()); 
		    $("#gerente_valor_" + cd_atividade_cronograma_item).show(); 
			
			$("#"+linha+"_"+visivel_gerente+"-table-1").show();
			$("#"+linha+"_"+oculta_gerente+"-table-1").hide();
        });
	}
	
	function salvar_operacional(cd_atividade_cronograma_item, linha)
	{
		$("#ajax_prioridade_" + cd_atividade_cronograma_item).html("<?php echo loader_html("P"); ?>");
		
		$.post( '<?php echo site_url("/atividade/atividade_cronograma/salva_operacional")?>',
        {
            cd_atividade_cronograma_item : cd_atividade_cronograma_item,
            nr_prioridade_operacional    : $("#nr_prioridade_operacional_" + cd_atividade_cronograma_item).val()	
        },
        function(data)
        {
			$("#ajax_prioridade_" + cd_atividade_cronograma_item).empty();
			$("#nr_prioridade_operacional_" + cd_atividade_cronograma_item).hide();
            $("#prioridade_valor_" + cd_atividade_cronograma_item).html($("#nr_prioridade_operacional_" + cd_atividade_cronograma_item).val()); 
			
			$("#"+linha+"_"+visivel_operacional+"-table-1").html($("#nr_prioridade_operacional_" + cd_atividade_cronograma_item).val()); 
			$("#prioridade_valor_" + cd_atividade_cronograma_item).show(); 
			
			$("#"+linha+"_"+visivel_operacional+"-table-1").show();
			$("#"+linha+"_"+oculta_operacional+"-table-1").hide();
        });
	}
	
	function editar_projeto(cd_atividade)
	{
		$("#projeto_valor_" + cd_atividade).hide(); 
		$("#projeto_editar_" + cd_atividade).hide(); 
		$("#projeto_salvar_" + cd_atividade).show(); 
		
		$("#projeto_nome_" + cd_atividade).show(); 
		$("#projeto_nome_" + cd_atividade).focus();	
	}
	
	function salvar_projeto(cd_atividade)
	{
		$("#ajax_projeto_" + cd_atividade).html("<?php echo loader_html("P"); ?>");
		
		$.post( '<?php echo site_url("/atividade/atividade_cronograma/salva_projeto")?>',
        {
            cd_atividade : cd_atividade,
            sistema      : $("#projeto_nome_" + cd_atividade).val()	
        },
        function(data)
        {
		    $("#ajax_projeto_" + cd_atividade).empty();
		    
		    $("#projeto_nome_" + cd_atividade).hide();
		    $("#projeto_salvar_" + cd_atividade).hide(); 
			
		    if($("#projeto_nome_" + cd_atividade + " option:selected").text() != 'Selecione') 
			{
				$("#projeto_valor_" + cd_atividade).html($("#projeto_nome_" + cd_atividade + " option:selected").text()); 
			}
			else
			{
				$("#projeto_valor_" + cd_atividade).html(''); 
			}
			
		    $("#projeto_valor_" + cd_atividade).show(); 
		    $("#projeto_editar_" + cd_atividade).show(); 
			
        });
	}
	
	function editar_complexidade(cd_atividade)
	{
		$("#complexidade_valor_" + cd_atividade).hide(); 
		$("#complexidade_editar_" + cd_atividade).hide(); 
		$("#complexidade_salvar_" + cd_atividade).show(); 
		
		$("#ds_complexidade_" + cd_atividade).show(); 
		$("#ds_complexidade_" + cd_atividade).focus();	
	}
	
	function salvar_complexidade(cd_atividade)
	{
		$("#ajax_complexidade_" + cd_atividade).html("<?php echo loader_html("P"); ?>");
		
		$.post( '<?php echo site_url("/atividade/atividade_cronograma/salva_complexidade")?>',
        {
            cd_atividade    : cd_atividade,
            cd_complexidade : $("#ds_complexidade_" + cd_atividade).val()	
        },
        function(data)
        {
		    $("#ajax_complexidade_" + cd_atividade).empty();
		    
		    $("#ds_complexidade_" + cd_atividade).hide();
		    $("#complexidade_salvar_" + cd_atividade).hide(); 
			
		    if($("#ds_complexidade_" + cd_atividade + " option:selected").text() != 'Selecione') 
			{
				$("#complexidade_valor_" + cd_atividade).html($("#ds_complexidade_" + cd_atividade + " option:selected").text()); 
			}
			else
			{
				$("#complexidade_valor_" + cd_atividade).html(''); 
			}
			
		    $("#complexidade_valor_" + cd_atividade).show(); 
		    $("#complexidade_editar_" + cd_atividade).show(); 
			
        });
	}

	function ir_quadro_resumo(cd_atividade_cronograma)
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma/quadro_resumo"); ?>' + "/" + cd_atividade_cronograma;
	}
	
	function encerrar()
	{
		if(confirm("Deseja encerrar?"))
		{
			location.href='<?php echo site_url("atividade/atividade_cronograma/encerrar_cronograma/".$cd_atividade_cronograma); ?>';
		}
	}	
	
	function excluir_cronograma()
	{
		if(confirm("Deseja excluir?"))
		{
			location.href='<?php echo site_url("atividade/atividade_cronograma/excluir_cronograma/".$cd_atividade_cronograma); ?>';
		}
	}	
	
	function excluir_item(cd_atividade_cronograma_item)
	{
		if(confirm("Deseja excluir?"))
		{
			location.href='<?php echo site_url("atividade/atividade_cronograma/excluir_item/".$cd_atividade_cronograma); ?>'+ "/" + cd_atividade_cronograma_item;
		}
	}

	function ir_concluidas_fora(cd_atividade_cronograma)
	{
		location.href = '<?php echo site_url("atividade/atividade_cronograma/concluidas_fora"); ?>' + "/" + cd_atividade_cronograma;
	}

	$(function(){
		filtrar();
	})
</script>
<?php
$config = array();

$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');

if($fl_responsavel)
{
	//$abas[] = array('aba_cadastro', 'Cadastro', FALSE, "cronograma('".$cd_atividade_cronograma."');");
}	

$abas[] = array('aba_cronograma', 'Cronograma', TRUE, "location.reload();");
$abas[] = array('aba_cronograma', 'Acompanhamento', FALSE, "ir_acompanhamento('".$cd_atividade_cronograma."');");
$abas[] = array('aba_cronograma', 'Quadro Resumo', FALSE, "ir_quadro_resumo('".$cd_atividade_cronograma."');");
$abas[] = array('aba_cronograma', 'Concluídas Fora', FALSE, "ir_concluidas_fora('".$cd_atividade_cronograma."');");

if($fl_responsavel AND $dt_encerra == '')
{		
	$config['button'][] = array('Incluir Atividade', 'cronogramaItem('.intval($cd_atividade_cronograma).')');
	$config['button'][] = array('Incluir Todas Atividade', 'incluir_todas('.intval($cd_atividade_cronograma).')');
}

$config['button'][] = array("Excel", 'imprimir();','', 'botao_disabled');

if($fl_responsavel AND $row['dt_exclusao'] == "" AND trim($row['dt_encerra']) == "")
{
	$config['button'][] = array("Encerrar Cronograma", "encerrar()",'', 'botao_vermelho');
	$config['button'][] = array("Excluir Cronograma", "excluir_cronograma()",'', 'botao_vermelho');
}

$arr_opcao[] = array('text' => 'Sim', 'value' => 'S');
$arr_opcao[] = array('text' => 'Não', 'value' => 'N');
	
echo aba_start( $abas );
	echo form_list_command_bar($config);	
	echo form_start_box_filter('filter_bar', 'Filtros',FALSE);
		echo form_default_hidden('cd_atividade_cronograma', "Código: ", $cd_atividade_cronograma );
		echo filter_integer_interval('ini_operacional', 'fim_operacional', 'Prioridade Área: ');
		echo filter_dropdown('fl_prioridade_area', 'Prioridade Área: ', $arr_opcao);
		echo filter_integer_interval('ini_gerente', 'fim_gerente', 'Prioridade Consenso: ');
		echo filter_dropdown('fl_prioridade_consenso', 'Prioridade Consenso: ', $arr_opcao);
		echo filter_dropdown('status_atual', 'Status: ', $arr_status);
		echo filter_dropdown('complexidade', 'Complexidade: ', $arr_complexidades);
		echo filter_dropdown('sistema', 'Projeto: ', $arr_projetos);
		echo filter_dropdown('cd_divisao', 'Gerência: ', $gerencia_dd);
		echo filter_dropdown('cd_solicitante', 'Solicitante: ', $arr_solicitante);
		echo filter_dropdown('cd_atividade_cronograma_grupo', 'Grupo: ', $arr_atividade_cronograma_grupo);
	echo form_end_box_filter();	

	echo form_start_box( "default_box", "Cronograma", true, false, 'style="text-align:left"' );
		echo form_default_text('descricao', "Descrição: ", $row, 'style="width:500px; border: 0px;" readonly' );
		echo form_default_text('periodo', "Período: ", $row['dt_inicio'] .' á '. $row['dt_final'], 'style="width:500px; border: 0px;" readonly' );
		echo form_default_text('nome', "Responsável: ", $row, 'style="width:500px; border: 0px;" readonly' );
	echo form_end_box("default_box");
	echo '<div id="result_div" style="text-align:center;"></div>';
	echo br();

echo aba_end();
$this->load->view('footer_interna');
?>