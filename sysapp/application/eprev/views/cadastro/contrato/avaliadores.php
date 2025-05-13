<?php
set_title('Contrato - Avaliadores');
$this->load->view('header');
?>
<script>
    <?php echo form_default_js_submit(array('cd_usuario'))?>

 	function ir_lista()
	{
	    location.href='<?= site_url('/cadastro/contrato')?>';
	}

	function ir_cadastro()
	{
	    location.href='<?= site_url('/cadastro/contrato/cadastro/'.$row['cd_contrato'])?>';
	}

	function get_usuarios(cd_gerencia, campo)
	{
		$.post("<?= site_url('cadastro/contrato/get_usuarios') ?>",
		{
			cd_gerencia : cd_gerencia
		},
		function(data)
		{
			if(campo == 0)
			{
				var select = $('#cd_usuario'); 
			}
						
			if(select.prop) 
			{
				var options = select.prop('options');
			}
			else
			{
				var options = select.attr('options');
			}
			
			$('option', select).remove();
			
			options[options.length] = new Option('Selecione', '');
			
			$.each(data, function(val, text) {
				options[options.length] = new Option(text.text, text.value);
			});
			
		}, 'json', true);
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById('table-1'),
		[
		    'CaseInsensitiveString',
		    'DateTimeBR',
		    'CaseInsensitiveString',
		    null
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

	function excluir(cd_contrato_avaliador)
	{
		var confirmacao = 'Deseja excluir Avaliador?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('cadastro/contrato/excluir_avaliadores/'.$row['cd_contrato']) ?>/'+cd_contrato_avaliador;
		}
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Contrato', FALSE, 'ir_cadastro();');
$abas[] = array('aba_avaliadores', 'Avaliadores', TRUE, 'location.reload();');

$head = array(
	'Avaliador',
	'Dt. Inclusão ',
	'Usuário',
	''
);

$body = array();

foreach($collection as $item)
{
	$body[] = array(

		array($item['usuario'],'text-align:left;'),
		$item['dt_inclusao'],
		array($item['usuario_inclusao'],'text-align:left;'),
		'<a href="javascript:void(0)" onclick="excluir('.$item['cd_contrato_avaliador'].')">[excluir]</a><br>'
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start($abas);
echo form_open('cadastro/contrato/salvar_avaliadores');
	echo form_start_box('default_cadastro_box', 'Cadastro');
			echo form_default_hidden('cd_contrato','',  $row);	
		echo form_default_gerencia('cd_gerencia', 'Gerência :','cd_usuario', '	onchange="get_usuarios(this.value, 0)"');
		echo form_default_dropdown('cd_usuario','Usuário : (*)' );
	echo form_end_box("default_cadastro_box");
		echo form_command_bar_detail_start();
			echo button_save('Adicionar');
		echo form_command_bar_detail_end();
	echo $grid->render();
	echo br(2);
echo form_close();
echo aba_end();
$this->load->view('footer_interna');
?>