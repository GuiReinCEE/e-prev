<?php 
set_title('Avaliação - Comitê');
$this->load->view('header'); 
?>
<script>
<?php echo form_default_js_submit(array('cd_usuario', 'fl_responsavel'));	?>

function ir_lista()
{
	location.href='<?php echo site_url("cadastro/avaliacao_comite"); ?>';
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		null,
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
	ob_resul.sort(1, false);
}

function excluir(cd_avaliacao_comite)
{
	if(confirm('Deseja excluir?'))
	{
		location.href='<?php echo site_url("cadastro/avaliacao_comite/excluir/".intval($row['cd_avaliacao_capa'])); ?>/'+cd_avaliacao_comite;
	}
}

function alterar_responsavel(cd_avaliacao_comite)
{
	$.post('<?php echo site_url('cadastro/avaliacao_comite/alterar_responsavel');?>',
	{
		cd_avaliacao_comite : cd_avaliacao_comite,
		cd_avaliacao_capa   : $('#cd_avaliacao_capa').val()
	},
	function(data)
	{
		$('#btn_encaminhar').show();
	});
}


function alterar_responsavel_avaliador()
{
	$.post('<?php echo site_url('cadastro/avaliacao_comite/alterar_responsavel_avaliador');?>',
	{
		cd_avaliacao_capa   : $('#cd_avaliacao_capa').val()
	},
	function(data)
	{
		$('#btn_encaminhar').show();
	});
}

function encaminhar()
{
	if(confirm('Encaminhar para o comitê?'))
	{
		location.href='<?php echo site_url("cadastro/avaliacao_comite/encaminhar/".intval($row['cd_avaliacao_capa'])); ?>';
	}
}
	
	
$(function(){
	configure_result_table();
});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

$arr_responsavel[] = array('value' => 'N', 'text' => 'Não');
$arr_responsavel[] = array('value' => 'S', 'text' => 'Sim');

$body = array();
$head = array(
	'Responsável',
	'Avaliador',
	'Já Avaliou',
	''
);

foreach( $collection as $item )
{
	$radio   = '';
	$excluir = '';
	
	if(trim($row['fl_status']) == 'E')
	{
		$radio   = '<input type="radio" name="responsavel" id="responsavel" onchange="alterar_responsavel($(this).val())" value="'.intval($item['cd_avaliacao_comite']).'" '.(trim($item['fl_responsavel']) == 'S' ? 'checked=""' : '').' />';
		$excluir = '<a href="javascript:void(0);" onclick="excluir('.intval($item['cd_avaliacao_comite']).')">[excluir]</a>';
	}
	else
	{
		if(trim($item['fl_responsavel']) == 'S')
		{
			$radio = '<b>X</b>';
		}
	}

	$body[] = array(
		$radio,
		array($item["nome"], 'text-align:left'),
	    '<span class="label label-'.(trim($item["fl_avaliou"]) == 'S' ? 'success' : 'important').'">'.(trim($item["fl_avaliou"]) == 'S' ? 'Sim' : 'Não').'</span>',
		$excluir
	);
}

$body[] = array(
	'<input type="radio" name="responsavel" id="responsavel" onchange="alterar_responsavel_avaliador()" '.(trim($row['avaliador_responsavel_comite']) == 'S' ? 'checked=""' : '').' />',
	array($avaliador, 'text-align:left'),
	'<span class="label label-success">Sim</span>',
	''
);

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
	echo form_open('cadastro/avaliacao_comite/salvar');
		echo form_start_box( "default_box", "Avaliado" );
			echo form_default_hidden('cd_avaliacao_capa', '', $row['cd_avaliacao_capa']);
			echo form_default_row('nome', 'Nome :', $row['nome']);
			echo form_default_row('tipo', 'Tipo :', '<span class="'.trim($row['cor_tipo_promocao']).'">'.$row['tipo_promocao'].'</span>');
			echo form_default_row('status', 'Status :', '<span class="'.trim($row['cor_status']).'">'.$row['status'].'</span>');
		echo form_end_box("default_box");
		if(trim($row['fl_status']) == 'E')
		{
			echo form_start_box( "default_comite_box", "Comitê" );
				echo form_default_dropdown('cd_usuario', 'Avaliador :*', $arr_usuario);
				echo form_default_dropdown('fl_responsavel', 'Responsavél :*', $arr_responsavel, array('N'));
			echo form_end_box("default_comite_box");
		}
		echo form_command_bar_detail_start();
		if(trim($row['fl_status']) == 'E')
		{
			echo button_save('Adicionar');
			echo button_save('Encaminhar', 'encaminhar()', 'botao_verde', 'id="btn_encaminhar" style="'.(intval($row['tl_responsavel']) == 0 ? 'display:none;' : '').'"');
		}
		echo form_command_bar_detail_end();
	echo form_close();
	echo $grid->render();
	echo br(2);
echo aba_end();

$this->load->view('footer_interna');
?>