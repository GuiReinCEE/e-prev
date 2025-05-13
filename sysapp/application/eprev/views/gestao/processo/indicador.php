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

    function ir_instrumento()
    {
        location.href = "<?= site_url('gestao/processo/instrumento/'.$processo['cd_processo']) ?>";
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

    function ir_revisao()
    {
        location.href = "<?= site_url('gestao/processo/revisao_historico/'.$processo['cd_processo']) ?>";
    }

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
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
	
	$(function(){
		configure_result_table();
	});	
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro()');
    $abas[] = array('aba_indicador', 'Indicadores', TRUE, 'location.reload();');
    $abas[] = array('aba_instrumento', 'IT\'s', FALSE, 'ir_instrumento();');
    $abas[] = array('aba_fluxo', 'Fluxograma', FALSE, 'ir_fluxo();');
    $abas[] = array('aba_pop', 'POP', FALSE, 'ir_pop();');
    $abas[] = array('aba_registros', 'Registros', FALSE, 'ir_registro();');
    $abas[] = array('aba_revisao', 'Histórico de Revisões', FALSE, 'ir_revisao();');

    $head = array( 
        'Grupo',
        'Indicador'
    );

    $body = array();

    foreach($collection as $item)
    {
        $body[] = array(
            array($item['ds_indicador_grupo'], 'text-align:left;'),
            array($item['ds_indicador'], 'text-align:left;')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo aba_start($abas);
        echo form_start_box('default_box', 'Processo');
            echo form_default_row('', 'Descrição:', $processo['procedimento'], 'style="width:400px;"');
    	echo form_end_box('default_box');
        echo $grid->render();
        echo br(2);	
    echo aba_end();

    $this->load->view('footer_interna');
?>