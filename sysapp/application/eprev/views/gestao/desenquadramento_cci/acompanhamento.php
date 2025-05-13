<?php
set_title('Desenquadramento - Acompanhamento');
$this->load->view('header');
?>

<script>
<?php
echo form_default_js_submit(Array('descricao'));
?>

function ir_lista()
{
	location.href='<?php echo site_url("gestao/desenquadramento_cci"); ?>';
}

function ir_cadastro()
{
	location.href='<?php echo site_url("gestao/desenquadramento_cci/cadastro/".$cd_desenquadramento_cci); ?>';
}

	function ir_anexo()
    {
        location.href='<?php echo site_url("gestao/desenquadramento_cci/anexo/".$cd_desenquadramento_cci); ?>';
    }	

function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post( '<?php echo site_url('gestao/desenquadramento_cci/listar_acompanhamento'); ?>/',
	{
		cd_desenquadramento_cci : $('#cd_desenquadramento_cci').val()
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
		"DateTimeBR",
		"CaseInsensitiveString",
		"CaseInsensitiveString",
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

function salvar_acompanhamento()
{
	var descricao = $('#descricao').val();
	
	if(descricao != '')
	{
		$.post( '<?php echo site_url('gestao/desenquadramento_cci/salvar_acompanhamento'); ?>/',
		{
			cd_desenquadramento_cci : $('#cd_desenquadramento_cci').val(),
			descricao               : descricao
		},
		function(data)
		{
			$('#descricao').val("");
			filtrar();
		});
	}
	else
	{
		alert("Informe a descrição do acompanhamento.");
	}
}

function excluir(cd_desenquadramento_cci_acompanhamento)
{
	var confirmacao = 'Deseja excluir o acompanhamento?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
	
	if(confirm(confirmacao))
	{
		$.post( '<?php echo site_url('gestao/desenquadramento_cci/excluir_acompanhamento'); ?>/',
		{
			cd_desenquadramento_cci_acompanhamento : cd_desenquadramento_cci_acompanhamento
		},
		function(data)
		{
			filtrar();
		});
	}
}

$(function(){
	filtrar();
})
	
</script>

<?php

$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_nc', 'Acompanhamento', TRUE, 'location.reload();');
$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

echo aba_start( $abas );
	echo form_open('gestao/desenquadramento_cci/salvar_acompanhamento', 'name="filter_bar_form"');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_desenquadramento_cci', '', $cd_desenquadramento_cci);
			echo form_default_hidden('cd_desenquadramento_cci_acompanhamento', '', 0);
			echo form_default_textarea('descricao', 'Descrição : *', '', "style='height:100px;'");
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();         
            echo button_save("Salvar", "salvar_acompanhamento()");
        echo form_command_bar_detail_end();
	echo form_close();
	echo '<div id="result_div"></div>';
    echo br(3);	
echo aba_end();

$this->load->view('footer_interna');
?>