<?php
	set_title('Treinamentos - Documentos');
	$this->load->view('header');
?>
<script>
    function ir_lista()
    {
        location.href = "<?= site_url('servico/treinamentos_documento/index') ?>";
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            "DateTimeBR", 
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
        ob_resul.sort(0, true);
    }

    $(function(){
        configure_result_table();
    });
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_documento', 'Documento', TRUE, 'location.reload();');

    $head = array(
      'Dt. Inclusão',
      'Documento'
    );

    $body = array();

    foreach ($collection as $item)
    {
        $body[] = array(
            $item['dt_inclusao'],
            array(anchor(base_url().'up/meus_treinamentos/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;')
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->view_count = false;
    $grid->head = $head;
    $grid->body = $body;

    echo aba_start($abas);
        echo form_start_box('default_box', 'Documento'); 
            echo form_default_hidden('cd_treinamento_colaborador', '', $row['cd_treinamento_colaborador']);
            echo form_default_row('', 'Ano/Numero:', $row['numero']);
            echo form_default_row('', 'Nome:', $row['nome']);
            echo form_default_row('', 'Promotor:', $row['promotor']);
            echo form_default_row('', 'Cidade', $row['cidade']);
            echo form_default_row('', 'Uf', $row['uf']);
            echo form_default_row('', 'Dt. Inicio:', $row['dt_inicio']);
            echo form_default_row('', 'Dt. Final:', $row['dt_final']);
            echo form_default_row('', 'Tipo:', $row['ds_treinamento_colaborador_tipo']);
        echo form_end_box('default_box');    
        echo br();
        echo $grid->render();
        echo br(2);
    echo aba_end();

	$this->load->view('footer');
?>