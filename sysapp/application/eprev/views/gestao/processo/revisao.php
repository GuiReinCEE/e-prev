<?php
	set_title('Processos');
	$this->load->view('header');
?>
<script>
	function ir_lista()
    {
        location.href = "<?= site_url('gestao/processo') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('gestao/processo/cadastro/'.$processo['cd_processo']) ?>";
    }

    function ir_indicador()
    {
        location.href = "<?= site_url('gestao/processo/indicador/'.$processo['cd_processo']) ?>";
    }

    function ir_fluxo()
    {
        location.href = "<?= site_url('gestao/processo/fluxo/'.$processo['cd_processo']) ?>";
    }

    function ir_pop()
    {
        location.href = "<?= site_url('gestao/processo/pop/'.$processo['cd_processo']) ?>";
    }

    function ir_registro()
    {
        location.href = "<?= site_url('gestao/processo/registro/'.$processo['cd_processo']) ?>";
    }

    function ir_instrumento()
    {
        location.href = "<?= site_url('gestao/processo/instrumento/'.$processo['cd_processo']) ?>";
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
			'DateBR', 
			'DateBR', 
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
        ob_resul.sort(0, true);
    }

    $(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro()');
	$abas[] = array('aba_indicador', 'Indicadores', FALSE, 'ir_indicador();');
	$abas[] = array('aba_instrumento', 'IT\'s', FALSE, 'ir_instrumento();');
	$abas[] = array('aba_fluxo', 'Fluxograma', FALSE, 'ir_fluxo();');
	$abas[] = array('aba_pop', 'POP', FALSE, 'ir_pop();');
    $abas[] = array('aba_registros', 'Registros', FALSE, 'ir_registro();');
	$abas[] = array('aba_revisao', 'Histórico de Revisões', TRUE, 'location.reload();');

	$head = array(
	    'Dt. Referência',
	    'Dt. Limite',
	    'Dt. Revisão',
	    'Usuário Revisão',
	    'Alteração no Processo',
	    'Observação'
	);

	$body = array();

	foreach ($collection as $key => $item)
	{
		$body[] = array(
			$item['dt_referencia'],
			$item['dt_limite'],
			$item['dt_revisao'],
			array($item['ds_usuario_revisao'], 'text-align:left;'),
			'<label class="'.$item['class_alterado'].'">'.$item['alterado'].'</label>',
			array(nl2br($item['observacao']), 'text-align:justify;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo form_start_box('default_processo_box', 'Processo');
			echo form_default_hidden("cd_processo", '', $processo['cd_processo']);
            echo form_default_row('procedimento', 'Descrição:', $processo['procedimento'], 'style="width:400px;"');
		echo form_end_box('default_processo_box');
		echo $grid->render();
		echo br(2);	
	echo aba_end();

	$this->load->view('footer_interna');

?>