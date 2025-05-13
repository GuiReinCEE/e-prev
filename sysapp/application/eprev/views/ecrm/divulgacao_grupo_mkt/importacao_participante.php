<?php
    set_title('Email Divulgação - Grupo');
    $this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('ds_divulgacao_grupo','tp_grupo')) ?>
    
    function ir_lista()
    {
        location.href = "<?= site_url('ecrm/divulgacao_grupo_mkt') ?>";
    }

    function ir_cadastro()
    {
        location.href = "<?= site_url('ecrm/divulgacao_grupo_mkt/cadastro/'.$row['cd_divulgacao_grupo']) ?>";
    }

    function atualiza_registro()
    {
        location.href = "<?= site_url('ecrm/divulgacao_grupo_mkt/atualiza_registro/'.intval($row['cd_divulgacao_grupo']).'/P') ?>";
    }	

    function excluir(cd_divulgacao_grupo_participante)
    {
    	var confirmacao = "Deseja EXCLUIR o RE?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('ecrm/divulgacao_grupo_mkt/excluir_participante/'.$row['cd_divulgacao_grupo']) ?>/"+cd_divulgacao_grupo_participante;
        }
    }

    function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "CaseInsensitiveString",
		    "DateTimeBR"
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

	$(function(){
		configure_result_table()
	});
</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
    $abas[] = array('aba_importacao', 'Importação', TRUE, 'location.reload();');

    $head = array(
        'RE',
        'Nome',
		'Dt Inclusão',
		''
	);

    $body = array();

    foreach ($collection as $key => $item)
    {
		$body[] = array(
            $item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
            array($item['nome'], 'text-algin:left;'),
			$item['dt_inclusao'],
			'<a href="javascript:void(0);" onclick="excluir('.$item['cd_divulgacao_grupo_participante'].');">[excluir]</a>'
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

    echo aba_start($abas);
    	echo form_open('ecrm/divulgacao_grupo_mkt/salvar_participante');
            echo form_start_box('default_box', 'Cadastro');
            	echo form_default_hidden('cd_divulgacao_grupo', '', $row['cd_divulgacao_grupo']);
            	echo form_default_row('', 'Descrição:', $row['ds_divulgacao_grupo']);
            	echo form_default_row('', 'Qt Total Registro(s):', '<span class="label label-success">'.$row['qt_registro'].'</span>');
            	echo form_default_row('', '', '');
            	echo form_default_upload_iframe('arquivo', 'grupo_email_mkt', 'Arquivo:', array($anexo['arquivo'], $anexo['arquivo_nome']), 'grupo_email_mkt', true);
                echo form_default_row('', '', '<i>Arquivo .csv ou .txt com os RE;EMPRESA;SEQUÊNCIA separados por linha</i>');
            echo form_end_box('default_box');	   
            echo form_command_bar_detail_start();   
                echo button_save('Salvar');
          
                if(intval($row['cd_divulgacao_grupo']) > 0)
                {
                    echo button_save('Atualizar Total Registro(s)', 'atualiza_registro();', 'botao_amarelo');
                }
            echo form_command_bar_detail_end();
        echo form_close();
        echo br();
        echo $grid->render();
        echo br(2);
    echo aba_end();

	$this->load->view('footer_interna');
?>
