<?php
set_title('Controles TI - Renovação');
$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_dominio', 'dt_dominio_renovacao')) ?>

	function ir_lista()
	{
		location.href = '<?= site_url('servico/dominio') ?>';
	}

	function ir_cadastro()
	{
		location.href = '<?= site_url('servico/dominio/cadastro/'.$row['cd_dominio']) ?>';
	}

	function ir_anexo()
	{
		location.href = '<?= site_url('servico/dominio/anexo/'.$row['cd_dominio']) ?>';
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById('table-1'),
		[   
			'DateBR',
			'DateBR'
		]);

		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? 'sort-par' : 'sort-impar' );
				addClassName( rows[i], i % 2 ? 'sort-impar' : 'sort-par' );
			}
		};
		ob_resul.sort(1, true);
	}

	$(function(){
		configure_result_table();
	})
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_renovacao', 'Renovação', TRUE, 'location.reload();');
$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');

$head = array(
	'Dt. Inclusão',
	'Dt. Expiração'
);

$body = array();

foreach($collection as $item)
{
	$body[] = array(
		(trim($item['fl_editar']) == 'S'  ? anchor('servico/dominio/renovacao/'.$item['cd_dominio'].'/'.$item['cd_dominio_renovacao'], $item['dt_inclusao']) : $item['dt_inclusao']),
		(trim($item['fl_editar']) == 'S'  ? anchor('servico/dominio/renovacao/'.$item['cd_dominio'].'/'.$item['cd_dominio_renovacao'], $item['dt_dominio_renovacao']) : $item['dt_dominio_renovacao'])
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start($abas);
	echo form_open('servico/dominio/salvar_renovacao'); 
		echo form_start_box('default_box', 'Renovação');
			echo form_default_hidden('cd_dominio', '', $row['cd_dominio']);	
			echo form_default_hidden('cd_dominio_renovacao', '', $renovacao);
			echo form_default_row('descricao', 'Descrição:',  $row['descricao'], 'style="width:350px;"');	
			echo form_default_date('dt_dominio_renovacao', 'Dt. Expiração: (*)', $renovacao);		
		echo form_end_box('default_box');
		echo form_command_bar_detail_start();
			echo button_save('Salvar');	
		echo form_command_bar_detail_end();
	echo form_close();
	echo $grid->render();
	echo br(2);
echo aba_end();

$this->load->view('footer');
?>