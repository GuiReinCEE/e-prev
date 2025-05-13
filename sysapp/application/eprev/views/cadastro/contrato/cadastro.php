<?php
set_title('Contrato');
$this->load->view('header');
?>
<script>
<?php echo form_default_js_submit( array( 'ds_empresa', 'ds_servico', 'cd_contrato_pagamento', 'dt_inicio', 'cd_divisao') ); ?>

function ir_lista()
{
    location.href='<?php echo site_url('/cadastro/contrato');?>';
}

function ir_avaliadores()
{
    location.href='<?php echo site_url('/cadastro/contrato/avaliadores/'.$row['cd_contrato']);?>';
}

function adicionar(form)
{
	if( $('#cd_usuario').val()=='' )
	{
		alert('Usuário não informado!');
		$('#cd_usuario').focus();

		return false;
	}

	if( confirm('Adicionar?') )
	{
		$.post( '<?php echo site_url('cadastro/contrato/adicionar_responsavel');?>',
		{
			cd_contrato : $('#cd_contrato').val(),
			cd_usuario  : $('#cd_usuario').val()           
		},
		function(data)
		{
			listar();
		});
	}
}
	
function listar()
{
	$("#result_div").html("<?php echo loader_html(); ?>");

	$.post( '<?php echo site_url('cadastro/contrato/listar_responsaveis');?>',
	{
		cd_contrato : $('#cd_contrato').val()
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
	ob_resul.sort(0, false);
}
	
$(function(){
	if($('#cd_contrato').val() > 0)
	{
		listar();
	}
})

function excluir_responsavel(cd_contrato_responsavel)
{
	if( confirm('Deseja excluir o responsavél?') )
	{
		$.post( '<?php echo site_url('cadastro/contrato/excluir_responsavel');?>',
		{
			cd_contrato_responsavel : cd_contrato_responsavel
		},
		function(data)
		{
			listar();
		});
	}
}

function excluir()
{
	if( confirm('Deseja excluir o contrato?') )
	{
		location.href='<?php echo site_url('/cadastro/contrato/excluir/'.$row['cd_contrato']);?>';
	}
}

</script>
<?php
$abas[] = array('aba_lista', 'Lista', false, "ir_lista()");
$abas[] = array('aba_detalhe', 'Contrato', true, 'location.reload();');

if(intval($row['cd_contrato']) > 0)
{
	$abas[] = array('aba_avaliadores', 'Avaliadores', false, 'ir_avaliadores()');
}

echo aba_start($abas);
	echo form_open('cadastro/contrato/salvar' , 'name="filter_bar_form"');
		echo form_start_box("default_box", "Cadastro");
			echo form_hidden('cd_contrato', $row['cd_contrato']);
			echo form_default_text('ds_empresa', 'Empresa: *', $row, "style='width:300px'"); 
			echo form_default_text('ds_servico', 'Serviço: *', $row, "style='width:300px'"); 
			echo form_default_text('ds_valor', 'Valor:', $row); 
			echo form_default_dropdown('cd_contrato_pagamento', 'Pagamento: *', $arr_contrato_pagamentos, array($row['cd_contrato_pagamento']));
			echo form_default_date('dt_inicio', 'Dt Início: *', $row); 
			echo form_default_date('dt_encerramento', 'Dt Encerramento:', $row); 
			echo form_default_date('dt_reajuste', 'Dt Reajuste:', $row); 
			echo form_default_dropdown('cd_divisao', 'Gerência: *', $arr_gerencias, array($row['cd_divisao']));
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();
			if(intval($row['cd_contrato'])>0)
			{
				echo button_save('Excluir', 'excluir()', 'botao_vermelho');
			}
		echo form_command_bar_detail_end();

		if( $row['cd_contrato']!='' )
		{
			echo form_start_box("responsaveis_box", "Responsáveis");
				echo form_default_usuario_ajax('cd_usuario');
			echo form_end_box( "responsaveis_box" );
			echo form_command_bar_detail_start();
				echo button_save('Adicionar', 'adicionar(form)');
			echo form_command_bar_detail_end();
			echo '<div id="result_div"></div>';
		}
	echo form_close();
echo aba_end();

$this->load->view('footer_interna');
?>