<?php
    set_title('Processos');
    $this->load->view('header');
?>
<script>
    <?php
        if(count($pop) == 0)
        {
            echo form_default_js_submit(array(), 'valida_arquivo(form)');
        }
        else
        {
            echo form_default_js_submit(array('codigo', 'ds_processos_pop_anexo'));
        }
    ?>

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

    function ir_instrumento()
    {
        location.href = "<?= site_url('gestao/processo/instrumento/'.$processo['cd_processo']) ?>";
    }

    function ir_fluxo()
    {
        location.href = "<?= site_url('gestao/processo/fluxo/'.$processo['cd_processo']) ?>";
    }

    function ir_registro()
    {
        location.href = "<?= site_url('gestao/processo/registro/'.$processo['cd_processo']) ?>";
    }

    function ir_revisao()
    {
        location.href = "<?= site_url('gestao/processo/revisao_historico/'.$processo['cd_processo']) ?>";
    }

    function editar(cd_processos_pop_anexo)
    {
        location.href = "<?= site_url('gestao/processo/pop/'.$processo['cd_processo']) ?>/"+cd_processos_pop_anexo;
    }
	
	function valida_arquivo(form)
    {
        if($("#arquivo").val() == "" && $("#arquivo_nome").val() == "")
        {
            alert("Nenhum arquivo foi anexado.");
            return false;
        }
        else
        {
            if(confirm("Salvar?"))
            {
                form.submit();
            }
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
			'DateTimeBR', 
            'CaseInsensitiveString', 
            'CaseInsensitiveString', 
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
        ob_resul.sort(2, false);
    }

    function excluir(cd_processos_pop_anexo)
    {
        var confirmacao = "Deseja excluir?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('gestao/processo/excluir_pop/'.$processo['cd_processo']) ?>/"+cd_processos_pop_anexo;
        }
    }

    function enviar_email(cd_processos_pop_anexo)
    {
        var confirmacao = "Deseja enviar este e-mail?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('gestao/processo/pop_enviar_email/'.$processo['cd_processo']) ?>/"+cd_processos_pop_anexo;
        }
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
    $abas[] = array('aba_pop', 'POP', TRUE, 'location.reload();');
    $abas[] = array('aba_registros', 'Registros', FALSE, 'ir_registro();');
    $abas[] = array('aba_revisao', 'Histórico de Revisões', FALSE, 'ir_revisao();');

    $head = array( 
        'Dt Inclusão',
        'Código',
        'Descrição',
        'Arquivo',
        'Usuário',
        ''
    );

    $body = array();

    foreach($collection as $item)
    {
        $body[] = array(
            $item['dt_inclusao'],
            array($item['codigo'], 'text-align:left;'),
            array($item['ds_processos_pop_anexo'], 'text-align:left;'),
            array(anchor(base_url().'up/processos/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
            array($item['ds_usuario_inclusao'], 'text-align:left;'),
            '<a href="javascript:void(0);" onclick="editar('.$item['cd_processos_pop_anexo'].')">[editar]</a> '.
            '<a href="javascript:void(0);" onclick="enviar_email('.$item['cd_processos_pop_anexo'].')">[enviar e-mail]</a> '.
            '<a href="javascript:void(0);" onclick="excluir('.$item['cd_processos_pop_anexo'].')">[excluir]</a>'
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo aba_start($abas);
    	echo form_open('gestao/processo/salvar_pop');
            echo form_start_box('default_processo_box', 'Processo');
                echo form_default_hidden('cd_processo', '', $processo['cd_processo']);   
                echo form_default_row('', 'Descrição:', $processo['procedimento'], 'style="width:400px;"');
            echo form_end_box('default_processo_box');

    		echo form_start_box('default_anexo_box', 'Anexo');
                if(count($pop) == 0)
                {
        			echo form_default_row('', '', '');
                    echo form_default_upload_multiplo('arquivo_m', 'Arquivo: (*)', 'processos', 'validaArq');
                    echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
                }
                else
                {
                    echo form_default_row('arquivo', 'Arquivo:', $pop['arquivo_nome']);
                    echo form_default_hidden('cd_processos_pop_anexo', '', $pop['cd_processos_pop_anexo']);
                    echo form_default_text('codigo', 'Código: (*)', $pop['codigo'], 'style="width:400px;"');
                    echo form_default_text('ds_processos_pop_anexo', 'Descrição: (*)', $pop['ds_processos_pop_anexo'], 'style="width:400px;"');
                }
    		echo form_end_box('default_anexo_box');
            if(count($pop) > 0)
            {
                echo form_command_bar_detail_start();     
                    echo button_save('Salvar');
                    echo button_save('Cancelar', 'editar(0)', 'botao_disabled');
                echo form_command_bar_detail_end();
            }
    	echo form_close();
        echo $grid->render();
        echo br(2);
    echo aba_end();
    $this->load->view('footer_interna');
?>