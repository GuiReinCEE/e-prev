<?php
	set_title('Relato de Ocorrências - Cadastro');
	$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href = "<?= site_url('gestao/relato_ocorrencias') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('gestao/relato_ocorrencias/cadastro/'.intval($row['cd_relato_ocorrencias'])) ?>";
	}

	function excluir(cd_relato_ocorrencias_anexo)
	{
		var confirmacao = 'Deseja excluir o anexo?\n\n'+
				'Clique [Ok] para Sim\n\n'+
				'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href = "<?= site_url('gestao/relato_ocorrencias/excluir_anexo/'.intval($row['cd_relato_ocorrencias'])) ?>/" + cd_relato_ocorrencias_anexo;
		}
	}

	function valida_arquivo(form)
    {

		if($('#arquivo').val() == '' && $('#arquivo_nome').val() == '')
        {
            alert('Nenhum arquivo foi anexado.');
            return false;
        }
        else
        {
            if( confirm('Salvar?') )
            {
                form.submit();
            }
        }
    }
	
	function validaArq(enviado, nao_enviado, arquivo)
	{
		$("form").submit();
	}

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_anexo', 'Anexo', TRUE, 'location.reload();');

	$head = array( 
		'Código',
		'Dt Inclusão',
		'Arquivo',
		'Usuário',
		''
	);

	$body = array();

	foreach($collection as $item)
	{
	    $link = '';

	    if($this->session->userdata('codigo') == $item['cd_usuario_inclusao'])
	    {
	        $link = '<a href="javascript:void(0);" onclick="excluir('.$item['cd_relato_ocorrencias_anexo'].')">[excluir]</a>';
	    }

	    $body[] = array(
			$item['cd_relato_ocorrencias_anexo'],
			$item['dt_inclusao'],
			array(anchor(base_url().'up/relato_ocorrencias/'.$item['arquivo'], $item['arquivo_nome'] , array('target' => '_blank')), 'text-align:left;'),
			$item['ds_nome_usuario'],
			$link
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
        echo form_open('gestao/relato_ocorrencias/salvar_anexo');
            echo form_start_box('default_box', 'Cadastro');
                echo form_default_hidden('cd_relato_ocorrencias', '', $row);	
                echo form_default_row('', 'Ano/N°:', '<span class="label label-inverse">'.$row['nr_ano_numero_relato_ocorrencia'].'</span>');
            	echo form_default_row('', 'Dt. Inclusão:', $row['dt_inclusao']);
            	echo form_default_row('', 'Usuário Inclusão:', $row['ds_usuario_inclusao']);
            	echo form_default_upload_multiplo('arquivo_m', 'Arquivo: (*)', 'relato_ocorrencias', 'validaArq');
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
	            
			echo form_command_bar_detail_end();
        echo form_close();
        echo $grid->render();
		echo br(3);

    echo aba_end();

    $this->load->view('footer');
?>