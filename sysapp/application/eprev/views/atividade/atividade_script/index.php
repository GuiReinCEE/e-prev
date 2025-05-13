<?php
set_title('Atividade - Script');
$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array(
        'ds_atividade_script'
    )); ?>

	function ir_lista()
    {
        location.href='<?php echo site_url("atividade/minhas"); ?>';
    }
	
	function ir_solicitacao()
    {
        location.href='<?php echo site_url('atividade/atividade_solicitacao/index/'.$cd_gerencia.'/'.$cd_atividade);?>';
    }
    
    function ir_atendimento()
    {
        location.href='<?php echo site_url('atividade/atividade_atendimento/index/'.$cd_atividade.'/'.$cd_gerencia);?>';
    }	
	
	function ir_anexo()
    {
        location.href='<?php echo site_url('atividade/atividade_anexo/index/'.$cd_atividade.'/'.$cd_gerencia);?>';
    }
	
	function ir_acompanhamento()
    {
        location.href='<?php echo site_url('atividade/atividade_acompanhamento/index/'.$cd_atividade.'/'.$cd_gerencia);?>';
    }

    function ir_historico()
    {
        location.href='<?php echo site_url('atividade/atividade_historico/index/'.$cd_atividade.'/'.$cd_gerencia);?>';
    }

    function excluir(cd_atividade_script)
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir?\n\n"))
		{
			location.href='<?php echo site_url("atividade/atividade_script/excluir/".$cd_atividade."/".$cd_gerencia); ?>' + "/" + cd_atividade_script;
		}
	}

    function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "Number",
            "DateTimeBR",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
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

    $(function(){
		configure_result_table();
	});
</script>
<?php
    $body = array();

    foreach($collection as $item)
	{
        $fl_excluir = 'N';
	
        if($cd_usuario == intval($item['cd_usuario_inclusao']))
        {
            $fl_excluir = 'S';
        }
        
        $excluir = '<a href="javascript:void(0);" onclick="excluir('.$item['cd_atividade_script'].')">[excluir]</a>';

        $body[] = array(
            $item['cd_atividade_script'],
            $item['dt_inclusao'],
            array(nl2br($item['ds_atividade_script']), 'text-align:justify;'),
            array(anchor(base_url().'up/atividade_anexo/'.$item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")), 'text-align:left;'),
            $item['ds_usuario'],
            ($fl_excluir == 'S' ? $excluir : '')
        );
    }

    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_cadastro', 'Solicitação', FALSE, 'ir_solicitacao()');
    $abas[] = array('aba_lista', 'Atendimento', FALSE, 'ir_atendimento();');
    $abas[] = array('aba_lista', 'Script', TRUE, 'location.reload();');
    $abas[] = array('aba_lista', 'Anexo', FALSE, 'ir_anexo();');
    $abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
    $abas[] = array('aba_lista', 'Histórico', FALSE, 'ir_historico();');

    $head = array( 
        'Código',
        'Dt. Inclusão',
        'Descrição',
        'Arquivo',
        'Usuário',
        ''
    );

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo aba_start( $abas );
        echo form_open('atividade/atividade_script/salvar');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_gerencia', '', $cd_gerencia);
                echo form_default_hidden('cd_atividade', '', $cd_atividade);
                echo form_default_row('', 'Atividade:', '<span class="label label-inverse">'.$cd_atividade.'</span>');
                echo form_default_upload_iframe('arquivo', 'atividade_anexo', 'Arquivo:', '', 'atividade_anexo', false);
                echo form_default_textarea('ds_atividade_script', 'Descrição: (*)', '','style="width:500px; height:100px;"');
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