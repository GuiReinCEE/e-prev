<?php
	set_title('A��o Preventiva');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(), 'valida_arquivo(form)') ?>

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

	function ir_lista()
	{
		location.href = "<?= site_url('gestao/acao_preventiva'); ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('gestao/acao_preventiva/cadastro/'.$row['nr_ano'].'/'.$row['nr_ap']); ?>";
	}

	function ir_acompanhamento()
	{
		location.href = "<?= site_url('gestao/acao_preventiva/acompanhamento/'.$row['nr_ano'].'/'.$row['nr_ap']); ?>";
	}

	function ir_prorrogacao()
	{
		location.href = "<?= site_url('gestao/acao_preventiva/prorrogacao/'.$row['nr_ano'].'/'.$row['nr_ap']); ?>";
	}

	function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            "DateTimeBR", 
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
                removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
                addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
            }
        };
        ob_resul.sort(0, true);
    }

    function excluir(cd_acao_preventiva_anexo)
    {
    	var confirmacao = 'Deseja excluir o anexo?\n\n'+
            'Clique [Ok] para Sim\n\n'+
            'Clique [Cancelar] para N�o\n\n';

        if(confirm(confirmacao))
        {
            location.href = "<?= site_url('gestao/acao_preventiva/excluir_anexo/'.$row['nr_ano'].'/'.$row['nr_ap']) ?>/"+ cd_acao_preventiva_anexo;
        }
    }

    $(function(){
    	configure_result_table();
    });
</script>

<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_nc', 'A��o Preventiva', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	$abas[] = array('aba_lista', 'Prorroga��o', FALSE, 'ir_prorrogacao();');
	$abas[] = array('aba_anexo', 'Anexo', TRUE, 'ir_anexo();');

	$head = array(
	  'Dt. Inclus�o',
	  'Arquivo',
	  'Usu�rio',
	  ''
	);

	$body = array();

	foreach ($collection as $item)
	{
	    $body[] = array(
	    	$item['dt_inclusao'],
	    	array(anchor(base_url().'up/acao_preventiva/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;'),
	    	array($item['ds_usuario_inclusao'], 'text-align:left'),
	    	(intval($item['cd_usuario_inclusao']) == $this->session->userdata('codigo') ? '<a href="javascript:void(0);"" onclick="excluir('.$item['cd_acao_preventiva_anexo'].')">[excluir]</a>' : '')
	    );
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->view_count = false;
	$grid->head = $head;
	$grid->body = $body;
	

	echo aba_start($abas);
   		echo form_start_box('default_box', 'Cadastro');
   			echo form_default_row('', 'N�mero:', '<span class="label label-inverse">'.$row['numero_cad_ap'].'</span>');
   			echo form_default_row('', 'Processo:', $row['procedimento']);
   			echo form_default_row('', 'Ger�ncia:', $row['gerencia']);
   			echo form_default_row('', 'Respons�vel:', $row['nome_usuario']);
   			echo form_default_row('', 'Substituto:', $row['nome_substituto']);
   			echo form_default_row('', 'Dt. Cadastro:', $row['dt_inclusao']);
			echo form_default_row('', 'Usu�rio:', $row['usuario_cadastro']);
   		echo form_end_box('default_box');
   		echo form_open('gestao/acao_preventiva/salvar_anexo');
			echo form_start_box('default_box', 'Anexo');
				echo form_default_hidden('cd_acao_preventiva', '', $row);
				echo form_default_hidden('nr_ano', '', $row);
				echo form_default_hidden('nr_ap', '', $row);

				echo form_default_upload_multiplo('arquivo_m', 'Arquivo: (*)', 'acao_preventiva', 'validaArq');
                echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no bot�o [Anexar]</i>');
			echo form_end_box('default_box');
			echo form_command_bar_detail_start();
			echo form_command_bar_detail_end();
		echo form_close();
		echo br();
		echo $grid->render();
		echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>	