<?php
set_title('Programas e Projetos');
$this->load->view('header');
?>
<script>
	<?php
		if($this->session->userdata('divisao') == 'GC')
		{
			echo form_default_js_submit(array('nr_valor_aprovado'));
		}
		else
		{
			echo form_default_js_submit(array('ds_projeto_custo', 'nr_valor'));
		}
	?>

	function ir_lista()
	{
		location.href = "<?= site_url('gestao/projeto') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('gestao/projeto/cadastro/'.$row['cd_projeto']) ?>";
	}

	function ir_indicador()
	{
		location.href = "<?= site_url('gestao/projeto/indicador/'.$row['cd_projeto']) ?>";
	}

	function ir_cronograma()
	{
		location.href = "<?= site_url('gestao/projeto/cronograma/'.$row['cd_projeto']) ?>";
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    
		    "CaseInsensitiveString",
		    "Number",
		    "Number"
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

	$(function(){
		configure_result_table();
	});

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_indicador', 'Indicador', FALSE, 'ir_indicador();');
	$abas[] = array('aba_custo', 'Custos Projetados', TRUE, 'location.reload();');
	$abas[] = array('aba_cronograma', 'Cronograma', FALSE, 'ir_cronograma();');
	
	$head = array( 
		'Item',
		'Valor (R$)',
		'Valor Aprovado (R$)'
	);

	$body = array();

	foreach( $collection as $item )
	{
		$body[] = array(
			array(anchor('gestao/projeto/custo/'.$row["cd_projeto"].'/'.$item['cd_projeto_custo'], $item['ds_projeto_custo']), 'text-align:left;'),
			number_format($item['nr_valor'], 2, ',', '.'),
			number_format($item['nr_valor_aprovado'], 2, ',', '.')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_open('gestao/projeto/salvar_custo');
			echo form_start_box('default_box', 'Projeto');
				echo form_default_hidden('cd_projeto', '', $row);	
				echo form_default_row('ds_projeto', 'Projeto :', $row['ds_projeto'], 'style="width:350px;"');
			echo form_end_box('default_box');
			if(((intval($custo['cd_projeto_custo']) > 0)) OR ((intval($custo['cd_projeto_custo']) == 0) AND ($this->session->userdata('divisao') == $row['cd_gerencia_resposanvel'])))
			{

				echo form_start_box('default_indicador_box', 'Custo');
					echo form_default_hidden('cd_projeto_custo', '', $custo);				
					if(($this->session->userdata('divisao') == $row['cd_gerencia_resposanvel']))
					{
						echo form_default_text('ds_projeto_custo', 'Item :*', $custo, 'style="width:350px;"');
						echo form_default_numeric('nr_valor', 'Valor (R$) :*', number_format($custo['nr_valor'], 2, '.', ','));
					}
					else
					{
						echo form_default_text('ds_projeto_custo', 'Item :*', $custo, 'style="width:350px;" readonly');
						echo form_default_numeric('nr_valor', 'Valor (R$) :*', number_format($custo['nr_valor'], 2, '.', ','), 'readonly');
					}

					echo form_default_numeric('nr_valor_aprovado', 'Valor Aprovado (R$) :*', number_format($custo['nr_valor_aprovado'], 2, '.', ','), (($this->session->userdata('divisao') != 'GC') ? 'readonly' : ''));
				echo form_end_box('default_indicador_box');
				
				echo form_command_bar_detail_start();
					if(($this->session->userdata('divisao') == $row['cd_gerencia_resposanvel']) OR ($this->session->userdata('divisao') == 'GC'))
					{
						echo button_save('Salvar');	
					}
				echo form_command_bar_detail_end();
			}
		echo form_close();
		echo $grid->render();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>