<?php
set_title('Atualiza Intranet - '. $cd_gerencia);
$this->load->view('header');
?>

<script>
<?php
    echo form_default_js_submit(array('cd_intranet_pai','titulo'));
?>
    
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/intranet/index/".$cd_gerencia); ?>';
    }   
	
	function excluir()
	{
		var confirmacao = 'Deseja excluir?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{
			location.href='<?php  echo site_url("ecrm/intranet/excluir/".$cd_gerencia.'/'.intval($row['cd_intranet'])); ?>';
		}	
	}

	function salvar_arquivo()
	{
		if(true) //verifica_navegador())
		{
			if($('#texto_link').val() == '')
			{
				alert('Texto está em branco');
			}
			else
			{
				if(confirm('Salvar?'))
				{
					$('#local_link').val($('#link').val());
					$('#form_salvar_arquivo').submit();
				}
			}
		}
	}
	
	function listar()
	{
		$('#result_div').html("<?php echo loader_html(); ?>");

		$.post( '<?php echo site_url('ecrm/intranet/listar_links'); ?>',
		{
			cd_intranet : $('#cd_intranet').val(),
			cd_gerencia : '<?php echo $cd_gerencia; ?>'
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
			'Number',
			null,
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'DateTimeBR'
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
	
	function excluir_link(cd_intranet_link)
	{
		var confirmacao = 'Deseja excluir o link?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{
			location.href='<?php  echo site_url("ecrm/intranet/excluir_link/".$cd_gerencia.'/'.intval($row['cd_intranet'])); ?>/'+cd_intranet_link;
		}
	}
	
	function editar_ordem(cd_intranet_link)
	{
		/*
		var obj = $("#" + $(t).parent().get(0).id);

		var parent_linha  = obj.attr('linha');
		var parent_coluna = obj.attr('coluna');
		
		$('#'+parent_linha+'_'+parent_coluna+'-table-1').hide();
		$('#'+parent_linha+'_1-table-1').show();
		*/

		$("#valor_ordem_" + cd_intranet_link).hide(); 
		$("#editar_ordem_" + cd_intranet_link).hide(); 

		$("#salvar_ordem_" + cd_intranet_link).show(); 
		$("#nr_ordem_" + cd_intranet_link).show(); 
		$("#nr_ordem_" + cd_intranet_link).focus();	
	}
	
	function salvar_ordem(cd_intranet_link)
	{	
		$("#ajax_ordem_valor_" + cd_intranet_link).html("<?php echo loader_html("P"); ?>");
		
		$.post( '<?php echo site_url("ecrm/intranet/editar_ordem")?>',
        {
            cd_intranet_link : cd_intranet_link,
			cd_gerencia      : '<?php echo $cd_gerencia; ?>',
			nr_ordem         :  $("#nr_ordem_" + cd_intranet_link).val()	
        },
        function(data)
        {

			$("#ajax_ordem_valor_" + cd_intranet_link).empty();
			
			$("#nr_ordem_" + cd_intranet_link).hide();
			$("#salvar_ordem_" + cd_intranet_link).hide(); 
			
            $("#valor_ordem_" + cd_intranet_link).html($("#nr_ordem_" + cd_intranet_link).val()); 
			$("#valor_ordem_" + cd_intranet_link).show(); 
			$("#editar_ordem_" + cd_intranet_link).show(); 
        });
		
	}
	
	function verifica_navegador()
	{
		if($('#cd_intranet').val() > 0)
		{
			if ( !$.browser.msie )
			{
				alert('Para Criar link para arquivos da rede, acesse pelo Internet Explorer.');
				return false;
			}
			else
			{
				return true;
			}
		}
	}
	 
	$(function(){
		//verifica_navegador();
		//alert('Para Criar link para arquivos da rede, acesse pelo Internet Explorer.');
		
		if($('#cd_intranet').val() > 0)
		{
			listar();
		}
	});
	
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_open('ecrm/intranet/salvar', 'name="filter_bar_form"');
		echo form_start_box( "default_box", "Cadastro" );
			if(intval($row['cd_intranet']) > 0)
			{
				echo form_default_text("cd_intranet_readonly", "Código :", $row['cd_intranet'], 'style="width:500px; border:0" readonly=""');
				echo form_default_text("dt_inclusao", "Dt Inclusão :", $row['dt_inclusao'], 'style="width:500px; border:0" readonly=""');
				echo form_default_text("dt_exclusao", "Dt Exclusão :", $row['dt_exclusao'], 'style="width:500px; border:0" readonly=""');
				echo form_default_text("ds_url", "Url :", site_url('ecrm/intranet/pagina/'.$cd_gerencia.'/'.intval($row['cd_intranet'])), 'style="width:500px; border:0" readonly=""');
			}
			echo form_default_hidden('cd_intranet', '', $row);
			echo form_default_hidden('cd_gerencia', '', $cd_gerencia);
			echo form_default_dropdown('cd_intranet_pai', 'Item Superior :*', $arr_itens_sup, array($row['cd_intranet_pai']));
			echo form_default_text('titulo', 'Título :*', $row, 'style="width:500px;"');
			echo form_default_upload_iframe('arquivo', 'intranet', 'Imagem :', array($row['arquivo'],$row['arquivo_nome']), 'intranet', true);
			echo form_default_row('','',nbs());
			echo form_default_editor_html('conteudo_pagina', "Conteúdo :", $row['conteudo'], 'style="height: 200px;"');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();     
            echo button_save("Salvar");
			 if(intval($row['cd_intranet']) > 0)
			 {
				 echo button_save("Excluir", "excluir()", "botao_vermelho");
			 }
        echo form_command_bar_detail_end();
	echo form_close();
	
	
		if(intval($row['cd_intranet']) > 0)
		{	
			echo form_open('ecrm/intranet/salvar_link', 'name="form_salvar_arquivo" id="form_salvar_arquivo"');
				echo form_start_box( "doc_box", "Criar link para arquivos da rede" );
					echo form_default_hidden('cd_intranet_link', '', 0);
					echo form_default_hidden('cd_intranet', '', $row);
					echo form_default_hidden('cd_gerencia', '', $cd_gerencia);
					echo form_default_hidden('local_link');
					echo form_default_text('link', 'Arquivo :', "", 'style="width:500px;"');
					echo form_default_text('texto_link', 'Descrição :*', '', 'style="width:500px;"');
				echo form_end_box("doc_box");
				echo form_command_bar_detail_start();
					echo button_save("Adicionar", "salvar_arquivo()");
				echo form_command_bar_detail_end();
			echo form_close();
			
			echo '<div id="result_div"></div>';
		}
	
    echo br(10);	

echo aba_end();

$this->load->view('footer_interna');
?>