<?php
set_title('Simulação - Site - Acompanhamento');
$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_simulacao_site_acompanhamento')) ?>

	function ir_lista()
	{
		location.href = '<?= site_url('planos/simulacao_site_senge')?>';
	}

	function ir_simulacao()
	{
		location.href = '<?= site_url('planos/simulacao_site_senge/simulacao/'.$row['cd_simulacao_site'])?>';
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"CaseInsensitiveString",
			"DateTimeBR",
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
		ob_resul.sort(1, true);
	}

	function cancelar()
	{
		location.href = "<?= site_url('planos/simulacao_site_senge/cadastro/'.$row['cd_simulacao_site']) ?>";
	}

	function excluir(cd_simulacao_site_acompanhamento)
	{
		var confirmacao = 'Deseja excluir Acompanhamento?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('planos/simulacao_site_senge/excluir/'.$row['cd_simulacao_site']) ?>/' + cd_simulacao_site_acompanhamento;
		}
	}

	$(function(){
		configure_result_table();
	})
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_simulacao', 'Simulação', FALSE, 'ir_simulacao();');
$abas[] = array('aba_cadastro', 'Acompanhamento', TRUE, 'location.reload();');

$head = array(
	'Descrição',
	'Dt. Inclusão',
	'Usuário ',
	''
);

$body = array();


foreach($collection as $item)
{
	$link = 
		anchor('planos/simulacao_site_senge/cadastro/'.$item['cd_simulacao_site'].'/'.$item['cd_simulacao_site_acompanhamento'], '[editar]').''.
		'<a href="javascript:void(0)" onclick="excluir('.$item['cd_simulacao_site_acompanhamento'].')">[excluir]</a><br>';

	$body[] = array(
		array(nl2br($item['ds_simulacao_site_acompanhamento']),"text-align:left;"),
		$item['dt_acompanhamento'],
		array($item['cd_usuario_inclusao'],"text-align:left;"),
		$link 
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
	echo form_open('planos/simulacao_site_senge/salvar');
		echo form_start_box('default_box', 'Simulação');
			echo form_default_hidden('cd_simulacao_site', '', $row);
			echo form_default_row('nome', 'Nome :', $row['nome']);
			echo form_default_row('dt_inclusao', 'Dt. Simulação :', $row['dt_inclusao']);
		echo form_end_box('default_box');
		echo form_start_box('default_box', 'Cadastro');
			echo form_default_hidden('cd_simulacao_site', '', $row);
			echo form_default_hidden('cd_simulacao_site_acompanhamento', '', $acompanhamento);	
			echo form_default_textarea('ds_simulacao_site_acompanhamento', 'Descrição : (*)', $acompanhamento['ds_simulacao_site_acompanhamento'],'style="height:80px;"');
		echo form_end_box('default_box');
		echo form_command_bar_detail_start();
			echo button_save('Salvar');	
			if(intval($acompanhamento['cd_simulacao_site_acompanhamento']) > 0)
			{
				echo button_save('Cancelar', 'cancelar()', 'botao_disabled');	
			}
		echo form_command_bar_detail_end();
	echo form_close();
echo $grid->render();
echo aba_end();
$this->load->view('footer');

?>