<?php
	set_title('Sistema de Avaliação - Abertura');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_avaliacao', 'cd_avaliacao_usuario', 'cd_avaliacao_usuario_capacitacao_tipo', 'nr_pontuacao')); ?>

    function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "CaseInsensitiveString",
		    "Number",
		    null
		]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};
		ob_resul.sort(0, false);
	}

	function ir_lista()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura/cadastro/'.$avaliado['cd_avaliacao']) ?>";
	}	

	function ir_avaliacao()
	{
		location.href = "<?= site_url('cadastro/rh_avaliacao_abertura/avaliacao/'.$avaliado['cd_avaliacao']) ?>";
	}	

    $(function(){
    	configure_result_table();
	});
</script>
<style>
	#avaliacao_usuario_capacitacao_tipo input[type='button']
	{
		margin-left : 5px !important;
		height      : 20px;
	}
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_avaliacao', 'Avaliações', FALSE, 'ir_avaliacao();');
	$abas[] = array('aba_capacitacao', 'Capacitação', TRUE, 'location.reload();');

	$tipo = array(
		'cd_avaliacao_usuario_capacitacao_tipo',
		'Capacitação: (*)',
		array(
			'rh_avaliacao.avaliacao_usuario_capacitacao_tipo', 
			'cd_avaliacao_usuario_capacitacao_tipo',
			'ds_avaliacao_usuario_capacitacao_tipo'
		),
		$row['cd_avaliacao_usuario_capacitacao_tipo'],
		'',
		'avaliacao_usuario_capacitacao_tipo',
		TRUE
	);

	list($id, $label, $db, $value, $par1, $par2, $par3) = $tipo;

	$head = array( 
		'Capacitação',
		'Pontos',
		''
    );

    $body = array();

    foreach ($collection as $key => $item) 
    {
    	$body[] = array(
    		array($item['ds_avaliacao_usuario_capacitacao_tipo'], 'text-align : left'),
    		array($item['nr_pontuacao'], 'text-align : center', 'int'),
    		anchor('cadastro/rh_avaliacao_abertura/capacitacao/'.$avaliado['cd_avaliacao_usuario'].'/'.$item['cd_avaliacao_usuario_capacitacao'], '[editar]')
    	);
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

	echo aba_start($abas);
		echo form_open('cadastro/rh_avaliacao_abertura/salvar_capacitacao');
			echo form_start_box('default_usuario_box', 'Usuário Avaliação');
			    echo form_default_hidden('cd_avaliacao', '', $avaliado);	
    			echo form_default_hidden('cd_avaliacao_usuario', '', $avaliado);	
				echo form_default_row('', 'Período:', $avaliado['nr_ano_avaliacao']);
				echo form_default_row('', 'Avaliado:', $avaliado['ds_avaliado']);
				echo form_default_row('', 'Admissão:', $avaliado['dt_admissao']);
				echo form_default_row('', 'Cargo/Área de Atuação:', $avaliado['ds_cargo_area_atuacao']);
				echo form_default_row('', 'Avaliador:', $avaliado['ds_avaliador']);
			echo form_end_box('default_usuario_box');
            echo form_start_box('default_box', 'Cadastro');
    			echo form_default_hidden('cd_avaliacao_usuario_capacitacao', '', $row);
            	echo form_default_dropdown_db($id, $label, $db, $value, $par1, $par2, $par3);
            	echo form_default_integer('nr_pontuacao', 'Pontos: (*)', $row);
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
                echo button_save('Salvar'); 
			echo form_command_bar_detail_end();
        echo form_close();
		echo br();
		echo $grid->render();
		echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>