<?php
set_title('S�mulas Interventor - Anexo');
$this->load->view('header');
?>

<script>
<?php
echo form_default_js_submit(array(), 'valida_arquivo(form)');
?>

function ir_lista()
{
	location.href='<?php echo site_url("gestao/sumula_interventor/minhas"); ?>';
}

function ir_resposta()
{
	location.href='<?php echo site_url("gestao/sumula_interventor/resposta/".$row['cd_sumula_interventor_item']); ?>';
}

function listar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post( '<?php echo site_url('gestao/sumula_interventor/listar_anexo'); ?>/',
	{
		cd_sumula_interventor_item : $('#cd_sumula_interventor_item').val()
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
	ob_resul.sort(1, true);
}

function excluir_anexo(cd_sumula_interventor_item_anexo)
{
	if(confirm("ATEN��O\n\nDeseja excluir?\n\n"))
	{
		location.href='<?php echo site_url("gestao/sumula_interventor/excluir_anexo/".$row['cd_sumula_interventor_item']); ?>' + "/" + cd_sumula_interventor_item_anexo;
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

function validaArq(enviado, nao_enviado, arquivo)
{
	$("form").submit();
}

$(function(){
	 listar();
})
	
</script>

<?php

$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Resposta', FALSE, 'ir_resposta();');
$abas[] = array('aba_nc', 'Anexo', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_open('gestao/sumula_interventor/salvar_anexo', 'name="filter_bar_form"');
		echo form_start_box("default_item_box", "Item S�mula");
			echo form_default_hidden('cd_sumula_interventor_item','', $row);
			echo form_default_hidden('dt_resposta','', $row);
            echo form_default_text('nr_sumula_interventor', 'N�mero:', $row, "style='width:100%;border: 0px;' readonly");
            echo form_default_text('dt_inclusao', 'Dt S�mula:', $row, "style='width:100%;border: 0px;' readonly");
            echo form_default_text('dt_divulgacao', 'Dt Divulga��o:', $row, "style='width:100%;border: 0px;' readonly");
            echo form_default_row("sumula_interventor", "Arquivo:", '<a href="' . site_url('gestao/sumula_interventor/sumula_interventor_pdf')."/".$row['cd_sumula_interventor'] . '" target="_blank">' . $row['arquivo_nome'] . ' [abrir]</a>');
            echo form_default_text('nr_sumula_interventor_item', 'N�mero do Item da S�mula:', $row, "style='width:100%;border: 0px;' readonly");
            echo form_default_textarea('descricao', 'Descri��o da S�mula', $row, " style='border: 1px solid gray;' readonly");
		echo form_end_box("default_item_box");
		if(trim($row['dt_resposta']) == '')
		{
			echo form_start_box( "default_box", "Anexo" );
				echo form_default_upload_multiplo('arquivo_m', 'Arquivo :*', 'sumula_interventor', 'validaArq');
			echo form_end_box("default_box");
			
			echo form_command_bar_detail_start();         
			echo form_command_bar_detail_end();
		}
	echo form_close();
	echo '<div id="result_div"></div>';
    echo br(3);	

echo aba_end();

$this->load->view('footer_interna');
?>