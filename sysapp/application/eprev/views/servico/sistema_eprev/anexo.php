<?php
	set_title('Sistema - Anexo');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_sistema_arquivo'), 'valida_arquivo(form)');?>

	function excluir(cd_sistema_anexo)
	{
		var confirmacao = 'Deseja excluir o arquivo?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url('servico/sistema_eprev/anexo_excluir/'.$sistema['cd_sistema']) ?>/"+cd_sistema_anexo;
		}
	}

	function ir_lista()
	{
		location.href = '<?= site_url('servico/sistema_eprev') ?>';
	}

	function ir_cadastro()
	{
		location.href = '<?= site_url('servico/sistema_eprev/cadastro/'.$sistema['cd_sistema']) ?>';
	}

	function ir_acompanhamento()
	{
		location.href = "<?= site_url('servico/sistema_eprev/acompanhamento/'.intval($sistema['cd_sistema']))?>";
	}

	function ir_atividade()
	{
		location.href = "<?= site_url('servico/sistema_eprev/atividade/'.intval($sistema['cd_sistema'])) ?>";
	}

	function ir_rotina()
	{
		location.href = "<?= site_url('servico/sistema_eprev/rotina/'.intval($sistema['cd_sistema'])) ?>";
	}

	function ir_metodo()
	{
		location.href = "<?= site_url("servico/sistema_eprev/metodo/".intval($sistema['cd_sistema'])) ?>";
	}

	function ir_pendencia()
	{
		location.href = "<?= site_url("servico/sistema_eprev/pendencia/".intval($sistema['cd_sistema'])) ?>";
	}

	function valida_arquivo(form)
    {
		if(($("#arquivo").val() == "") && ($("#arquivo_nome").val() == ""))
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
                removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
                addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
            }
        };
        ob_resul.sort(1, true);
    }

    
    $(function(){
		configure_result_table();
	});
</script>
<?php 
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_metodo', 'Método',FALSE, 'ir_metodo();');
	$abas[] = array('aba_rotina', 'Rotina', FALSE, 'ir_rotina();');
	$abas[] = array('aba_pendencia', 'Pendências', FALSE, 'ir_pendencia();');
	$abas[] = array('aba_atividade', 'Atividade', FALSE, 'ir_atividade();');	
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	$abas[] = array('aba_arquivo', 'Anexo', TRUE, 'location.reload();');
	 


	$head = array( 
		'Descrição',
		'Dt Inclusão',
		'Arquivo',
		''
	);

	$body = array();

	foreach($collection as $item )
	{	
	    $body[] = array(
			$item['ds_sistema_arquivo'],
			$item['dt_inclusao'],
			array(anchor(base_url().'up/sistema_eprev/' . $item['arquivo'], $item['arquivo_nome'] , array('target' => '_blank')), 'text-align:left;'),
			'<a href="javascript:void(0);" onclick="excluir('.$item['cd_sistema_arquivo'].')">[excluir]</a>' 
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->view_count = false;

	echo aba_start($abas);
		echo form_open('servico/sistema_eprev/anexo_salvar');
			echo form_start_box('default_sistema_box', 'Sistema');
					echo form_default_hidden('cd_sistema', '', $sistema['cd_sistema']);
					echo form_default_row('ds_sistema', 'Sistema:', $sistema['ds_sistema']);
					echo form_default_row('cd_gerencia_responsavel', 'Gerência Responsável:', $sistema['cd_gerencia_responsavel']);		
					echo form_default_row('cd_usuario_responsavel', 'Responsável:', $sistema['ds_responsavel']);			
				echo form_end_box('default_sistema_box');

			echo form_start_box('default_arquivo_box', ' Cadastro - Arquivo');
				echo form_default_text('ds_sistema_arquivo', 'Descrição: (*)', $arquivo, 'style="width:350px;"');
				echo form_default_upload_iframe('arquivo', 'sistema_eprev', 'Arquivo:', array(), 'sistema_eprev', true);
			echo form_end_box('default_arquivo_box');
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