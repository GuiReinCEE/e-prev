<?php
	set_title('Abaixo Assinado - Anexo');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array()); ?>

    function ir_lista()
    {
        location.href = "<?= site_url('ecrm/abaixo_assinado') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('ecrm/abaixo_assinado/cadastro/'.$cd_abaixo_assinado) ?>";
    }

    function ir_acompanhamento()
    {
        location.href = "<?= site_url('ecrm/abaixo_assinado/acompanhamento/'.$cd_abaixo_assinado) ?>";
    }

    function ir_retorno()
    {
        location.href = "<?= site_url('ecrm/abaixo_assinado/retorno/'.$cd_abaixo_assinado) ?>";
    }

    function excluir(cd_abaixo_assinado, cd_abaixo_assinado_anexo)
    {
    	var confirmacao = "Deseja excluir este anexo?\n\n"+
    					  "[OK] para Sim\n\n"+	
    					  "[Cancelar] para Não\n\n";

    	if(confirm(confirmacao))
    	{
        	location.href = "<?= site_url('ecrm/abaixo_assinado/excluir_anexo') ?>/" + cd_abaixo_assinado + "/" + cd_abaixo_assinado_anexo;
    	}
    }

    function validaArq(enviado, nao_enviado, arquivo)
	{
        $("form").submit();
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
                removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
                addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
            }
        };
        ob_resul.sort(1, true);
    }

    $(function (){
        configure_result_table();
    });
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
    $abas[] = array('aba_anexo', 'Anexo', TRUE, 'location.reload();');
    $abas[] = array('aba_retorno', 'Retorno', FALSE, 'ir_retorno();');

    $head = array(
        'Arquivo',
        'Dt. Inclusão',
        'Usuário',
        ''
	);

    $body = array();

	foreach($collection as $item)
	{
		$body[] = array(
            array(anchor(base_url().'up/abaixo_assinado/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
            $item['dt_inclusao'],
            $item['ds_usuario_inclusao'],
            '<a href="javascript:void(0);" onclick="excluir('.$cd_abaixo_assinado.', '.$item['cd_abaixo_assinado_anexo'].')">[excluir]</a>'
		);
	}

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
    $grid->body = $body; 

    if($fl_permissao != TRUE OR trim($row['dt_retorno']) != '')
    {
        $grid->col_oculta = array(3);
    }

    echo aba_start($abas);
        echo form_start_box('default_cadastro_box', 'Cadastro');
            echo form_default_row('', 'Ano/N°:', '<span class="label label-inverse">'.$row['nr_numero_ano'].'</span>');
            echo form_default_row('', 'Dt. Protocolo:', '<span class="label label-info">'.$row['dt_protocolo'].'</span>');
            echo form_default_row('', 'Dt. Limite Retorno:', '<span class="label label-important">'.$row['dt_limite_retorno'].'</span>');
            echo form_default_row('', 'RE:', $row['nr_re']);
            echo form_default_row('', 'Nome:', $row['ds_nome']);
        echo form_end_box('default_cadastro_box');
        if($fl_permissao)
        {
            echo form_open('ecrm/abaixo_assinado/salvar_anexo');
                echo form_start_box('default_anexo_box', 'Anexo');
                    echo form_default_hidden('cd_abaixo_assinado', '', $cd_abaixo_assinado);
                    echo form_default_upload_multiplo('arquivo_m', 'Arquivo: (*)', 'abaixo_assinado', 'validaArq');
                    echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
                echo form_end_box('default_anexo_box');
            echo form_close();
        }
        echo br();
        echo $grid->render(); 
        echo br(2);
    echo aba_end();

    $this->load->view('footer');
?>
