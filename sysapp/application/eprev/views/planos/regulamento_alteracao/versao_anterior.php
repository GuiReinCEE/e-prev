<?php
	set_title('Regulamento de Plano');
	$this->load->view('header');
?>
<script>
    function ir_lista()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/index') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/cadastro/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_estrutura()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/estrutura/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_artigo()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/estrutura_artigo/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_remissao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/remissao/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_revisao()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/revisao/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_quadro_comparativo()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/quadro_comparativo/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_glossario()
    {
        location.href = "<?= site_url('planos/regulamento_alteracao/glossario/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
    }

    function ir_atividades()
	{
		location.href = "<?= site_url('planos/regulamento_alteracao/atividades/'.$regulamento_alteracao['cd_regulamento_alteracao']) ?>";
	}

    function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
            "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "DateTimeBR",
		    "DateTimeBR"
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
		ob_resul.sort(0, true);
	}

    $(function(){
        configure_result_table();
    });

</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_glossario', 'Glossário', FALSE, 'ir_glossario();');
    $abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
    $abas[] = array('aba_artigo', 'Artigos', FALSE, 'ir_artigo();');
    $abas[] = array('aba_remissao', 'Remissão', FALSE, 'ir_remissao();');
    $abas[] = array('aba_quadro_comparativo', 'Quadro Comparativo', FALSE, 'ir_quadro_comparativo();');
    $abas[] = array('aba_atividades', 'Atividades', FALSE, 'ir_atividades();');
    $abas[] = array('aba_revisao', 'Revisão', FALSE, 'ir_revisao();');
    $abas[] = array('aba_versao', 'Versão Anterior', TRUE, 'location.reload();');

    $head = array( 
        'Dt. Inclusão',
        'Fim Alt. Regulamento',
        'Ini. Quadro Comparativo',
        'Fim Quadro Comparativo',
        'Dt. Envio PREVIC',
        'Aprovação PREVIC',
        'Doc. PREVIC',
        ''
    );

    $body = array();

    foreach($collection as $item)
	{
        $link_regulamento = anchor('planos/regulamento_alteracao/pdf/'.$item['cd_regulamento_alteracao'].'/'.(trim($item['dt_alteracao_finalizada']) == '' ? 'S' : 'N'), '[regulamento]', 'target="_blank"');

        $link_quadro_comparativo = anchor('planos/regulamento_alteracao/pdf_quadro_comparativo/'.$item['cd_regulamento_alteracao'], '[quadro comparativo]', 'target="_blank"');

        $link_doc_aprovacao = '';

        if(trim($item['arquivo']) != '')
        {
            $link_doc_aprovacao = anchor(base_url().'up/regulamento/'.$item['arquivo'], '[doc aprovação]', 'target = "_blank"');
        }

        $body[] = array(
            $item['dt_inclusao'],
            $item['dt_alteracao_finalizada'],
            $item['dt_inicio_quadro_comparativo'],
            $item['dt_fim_quadro_comparativo'],
            $item['dt_envio_previc'],
            $item['dt_aprovacao_previc'],
            array($item['ds_aprovacao_previc'], 'text-align:left'),
            $link_regulamento.br().$link_quadro_comparativo.br().$link_doc_aprovacao
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo aba_start($abas);
        echo form_start_box('default_box', 'Regulamento');
            echo form_default_row('', 'Regulamento:', $regulamento_alteracao['ds_regulamento_tipo']);
            echo form_default_row('', 'CNPB:', $regulamento_alteracao['ds_cnpb']);
        echo form_end_box('default_box');
        echo br();
        echo $grid->render();
        echo br();
    echo aba_end();

    $this->load->view('footer');

?>