<?php
set_title('Campanha Venda - Família');
$this->load->view('header');
?>
<script>
	function filtrar()
	{
		if($('#cd_campanha_venda').val() != '')
		{	
			$("#result_div").html("<?=loader_html()?>");
			
			$.post('<?=site_url('planos/campanha_venda/familia_listar')?>',
			$('#filter_bar_form').serialize(),
			function(data)
			{
				$("#result_div").html(data);
				
				configure_result_table();
				
				verifica_checked();
			});
		}
		else
		{
			$("#result_div").html('<br/><br/><span class="label label-success">INFORME A CAMPANHA.</span>');
			alert('Informe a campanha.');
		}
	}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		null,
		"CaseInsensitiveString",
		"CaseInsensitiveString",
		"Number",
		"Number",
		"NumberFloat",
		"CaseInsensitiveString",
		"CaseInsensitiveString",
		"CaseInsensitiveString",
		"CaseInsensitiveString",
		"CaseInsensitiveString",
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
	ob_resul.sort(1, false);
}

function salvar_item($t, cpf, ds_origem, cd_origem, cd_campanha_venda)
{	
	if ($t.is(':checked'))
	{
		$.post('<?=site_url('planos/campanha_venda/salvar_item')?>',
		{
			cd_campanha_venda : cd_campanha_venda,
			cpf               : cpf,
			ds_origem         : ds_origem,
			cd_origem         : cd_origem
		},
		function(data){
			if(data)
			{
				$t.val(data.value);
			}
		}, 'json');
	}
	else
	{
		$.post('<?=site_url('planos/campanha_venda/excluir_item')?>',
		{
			cd_campanha_venda      : cd_campanha_venda,
			cd_campanha_venda_item : $t.val(),
		},function(data){});
	}
}

function checkAll()
{
	var ipts = $("#table-1>tbody").find("input:checkbox");
	var check = document.getElementById("checkboxCheckAll");
	var arr = [];
	var arr_checked = [];
	var contador;
	var cd_campanha_venda = $("#cd_campanha_venda_new_0").val();
	
	i = 0;
	j = 0;
	
	item = [];

	check.checked ?
		jQuery.each(ipts, function(){
			this.checked = true;
			contador = $(this).attr('contador');
			
			item = [
				$("#cpf_" + contador).val(),
				$("#ds_origem_" + contador).val(),
				$("#cd_origem_" + contador).val()
			];

			arr_checked[i] = item;

			i++;
		}) :
		jQuery.each(ipts, function(){
			this.checked = false;
			
			contador = $(this).attr('contador');

			item = [
				$("#cpf_" + contador).val(),
				$("#ds_origem_" + contador).val(),
				$("#cd_origem_" + contador).val()
			];
			
			arr[j] = item;
			
			j++;
			
		});
		
	$.post('<?=site_url('planos/campanha_venda/salvar_all_item')?>',
	{
		cd_campanha_venda : cd_campanha_venda,
		"arr_checked[]"   : arr_checked,
		"arr[]"           : arr
	},
	function(data){
		filtrar();
	});
	
}

function verifica_checked()
{
	var ipts = $("#table-1>tbody").find("input:checkbox");
	var check = document.getElementById("checkboxCheckAll");

	i = 0;
	i_checked = 0;
	
	jQuery.each(ipts, function(){
		i ++;
		if(this.checked == true)
		{
			i_checked ++ ;
		}
	});
	
	if(i == i_checked)
	{
		check.checked = true;

	}
}

function fechar(cd_campanha_venda)
{
	var confirmacao = 'Deseja fechar?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';

	if(confirm(confirmacao))
	{
		$.post('<?=site_url('planos/campanha_venda/fechar_campanha')?>',
		{
			cd_campanha_venda : cd_campanha_venda
		},
		function(data){
			$("#fl_incluido").val("S");
			filtrar();
		});
	}
}

function novo()
{
	location.href='<?=site_url('planos/campanha_venda/cadastro')?>';
}

$(function(){
	/*
	if(($('#nome').val() != '') || ($('#cpf').val() != '') || ($('#delegacia').val() != '') || ($('#cidade').val() != ''))
	{
		if($('#cd_campanha_venda').val() != '')
		{
			filtrar();
		}
		else
		{
			$("#result_div").html('<br/><br/><span class="label label-success">INFORME A CAMPANHA.</span>');
		}
	}
	*/
});

</script>

<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
$config['button'][] = array('Nova Campanha', 'novo()');
	
$arr[] = array('text' => 'Não', 'value' => 'N');
$arr[] = array('text' => 'Sim', 'value' => 'S');

$ar_origem[] = array('text' => 'CADASTRO', 'value' => 'CADASTRO');
$ar_origem[] = array('text' => 'AFCEEE', 'value' => 'AFCEEE');
$ar_origem[] = array('text' => 'SINTEC', 'value' => 'SINTEC');


echo aba_start( $abas );
	echo form_list_command_bar($config);
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_dropdown('cd_campanha_venda', 'Campanha:', $ar_campanha);
		echo form_default_row("","","");
	
		echo filter_dropdown('fl_incluido', 'Incluído(s) na campanha:', $arr);
		
		echo filter_text('nome', 'Nome:', '', 'style="width:300px;"');
		echo filter_cpf('cpf', 'CPF:');
		echo filter_text('bairro', 'Bairro:', '', 'style="width:300px;"');
		
		echo filter_empresa("cd_empresa", "", "Empresa:");
		
		echo filter_checkbox_group('ar_origem', 'Origem:', $ar_origem);
		echo filter_checkbox_group('ar_cidade', 'Cidade:', $ar_cidade);
		echo filter_checkbox_group('ar_idade', 'Idade Titular:', $ar_idade);
		echo filter_checkbox_group('ar_idade_dependente', 'Idade Dependente:', $ar_idade);
		echo filter_checkbox_group('ar_renda', 'Renda Titular:', $ar_renda);
		echo filter_checkbox_group('ar_tipo_participante', 'Tipo Participante:', $ar_tipo_participante);
		echo filter_checkbox_group('ar_delegacia', 'Delegacia:', $ar_delegacia);
		
	echo form_end_box_filter();
	echo '<div id="result_div"><br/><br/><span class="label label-success">INFORME UM FILTRO.</span></div>';
	echo br(10);
echo aba_end('');

$this->load->view('footer');
