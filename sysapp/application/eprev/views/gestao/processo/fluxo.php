<?php
    set_title('Processos');
    $this->load->view('header');
?>
<script>
    <?php
        if(false)
        {
            echo form_default_js_submit(array(), 'valida_arquivo(form)');
        }
        else
        {
            echo form_default_js_submit(array('codigo', 'ds_processos_fluxo_anexo'));
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

    function editar(cd_processos_fluxo_anexo)
    {
        location.href = "<?= site_url('gestao/processo/fluxo/'.$processo['cd_processo']) ?>/"+cd_processos_fluxo_anexo;
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

    function excluir(cd_processos_fluxo_anexo)
    {
        var confirmacao = "Deseja excluir?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para N�o\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('gestao/processo/excluir_fluxo/'.$processo['cd_processo']) ?>/"+cd_processos_fluxo_anexo;
        }
    }

    function enviar_email(cd_processos_fluxo_anexo)
    {
        var confirmacao = "Deseja enviar este e-mail?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para N�o\n\n"; 

        if(confirm(confirmacao))
        {
            location.href = '<?= site_url('gestao/processo/fluxo_enviar_email/'.$processo['cd_processo']) ?>/'+cd_processos_fluxo_anexo;
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
    $abas[] = array('aba_fluxo', 'Fluxograma', TRUE, 'location.reload();');
    $abas[] = array('aba_pop', 'POP', FALSE, 'ir_pop();');
    $abas[] = array('aba_registros', 'Registros', FALSE, 'ir_registro();');
    $abas[] = array('aba_revisao', 'Hist�rico de Revis�es', FALSE, 'ir_revisao();');

    $head = array( 
        'Dt Inclus�o',
        'C�digo',
        'Descri��o',
        'Arquivo/Link do SA Interact',
        'Usu�rio',
        ''
    );

    $body = array();

    foreach($collection as $item)
    {

        $link = ''; 

        if(trim($item['ds_link_interact']) != '')
        {
            $link = '<a href="'.$item['ds_link_interact'].'" target="_blank">Link</a>';
        }
        else
        {
           $link = anchor(base_url().'up/processos/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank'));
        }   


        $body[] = array(
            $item['dt_inclusao'],
            array($item['codigo'], 'text-align:left;'),
            array($item['ds_processos_fluxo_anexo'], 'text-align:left;'),
            array($link, 'text-align:left;'),
            array($item['ds_usuario_inclusao'], 'text-align:left;'),
            '<a href="javascript:void(0);" onclick="editar('.$item['cd_processos_fluxo_anexo'].')">[editar]</a> '.
            '<a href="javascript:void(0);" onclick="enviar_email('.$item['cd_processos_fluxo_anexo'].')">[enviar e-mail]</a> '.
            '<a href="javascript:void(0);" onclick="excluir('.$item['cd_processos_fluxo_anexo'].')">[excluir]</a>'
        );
    }

    $this->load->helper('grid');
    $grid = new grid();
    $grid->head = $head;
    $grid->body = $body;

    echo aba_start($abas);
    	echo form_open('gestao/processo/salvar_fluxo');
            echo form_start_box('default_processo_box', 'Processo');
                echo form_default_hidden('cd_processo', '', $processo['cd_processo']);   
                echo form_default_row('', 'Descri��o:', $processo['procedimento'], 'style="width:400px;"');
            echo form_end_box('default_processo_box');

    		echo form_start_box('default_anexo_box', 'Anexo');
              //if(count($fluxo) == 0)
                if(false)
                {
        			echo form_default_row('', '', '');
                    echo form_default_upload_multiplo('arquivo_m', 'Arquivo: (*)', 'processos', 'validaArq');
                    echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no bot�o [Anexar]</i>');
                }
                else
                {
                  
                    echo form_default_hidden('cd_processos_fluxo_anexo', '', $fluxo['cd_processos_fluxo_anexo']);
                    echo form_default_text('codigo', 'C�digo: (*)', $fluxo['codigo'], 'style="width:400px;"');
                    echo form_default_text('ds_processos_fluxo_anexo', 'Descri��o: (*)', $fluxo['ds_processos_fluxo_anexo'], 'style="width:400px;"');
                    echo form_default_text('ds_link_interact', 'Link do SA Interact: (*)', $fluxo['ds_link_interact'], 'style="width:400px;"');
                    echo form_default_upload_iframe('arquivo', 'processos', 'Arquivo: (*)', array($fluxo['arquivo'], $fluxo['arquivo_nome']), 'processos');
                }
    		echo form_end_box('default_anexo_box');
            if(count($fluxo) > 0)
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