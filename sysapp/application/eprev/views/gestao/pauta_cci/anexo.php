<?php
set_title('Pauta CCI - Arquivos');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(array(), 'valida_arquivo(form)');
	?>

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

	function ir_lista()
	{
		location.href = '<?= site_url('gestao/pauta_cci') ?>';
	}

	function ir_cadastro()
	{
		location.href = '<?= site_url('gestao/pauta_cci/cadastro/'.$assunto['cd_pauta_cci']) ?>';
	}

	function ir_assunto()
	{
		location.href = '<?= site_url('gestao/pauta_cci/assunto/'.$assunto['cd_pauta_cci']) ?>';
	}

	function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
			'DateTimeBR', 
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
        ob_resul.sort(0, true);
    }

    function excluir(cd_pauta_cci_assunto_anexo)
	{
		var confirmacao = 'Deseja excluir o arquivo?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = "<?= site_url("gestao/pauta_cci/anexo_excluir/".$pauta["cd_pauta_cci"]."/".$assunto["cd_pauta_cci_assunto"]) ?>/"+cd_pauta_cci_assunto_anexo;
		}
	}

    $(function(){
		configure_result_table();
	});

</script>
<?php 
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_assunto', 'Assunto', FALSE, 'ir_assunto();');
$abas[] = array('aba_anexo', 'Arquivos', TRUE, 'location.reload();');

$head = array( 
	'Dt Inclusão',
	'Arquivo',
	'Usuário',
	''
);

$body = array();

foreach($collection as $item )
{	
    $body[] = array(
		$item['dt_inclusao'],
		array(anchor(base_url().'up/pauta_cci/' . $item['arquivo'], $item['arquivo_nome'] , array('target' => '_blank')), "text-align:left;"),
		array($item['usuario_inclusao'], "text-align:left;"),
		(trim($pauta['dt_aprovacao']) == '' ?
		'<a href="javascript:void(0);" onclick="excluir('.$item['cd_pauta_cci_assunto_anexo'].')">[excluir]</a>' : '')
		
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start($abas);
	echo form_open('gestao/pauta_cci/anexo_salvar');
		echo form_start_box('default_box', 'Pauta');
			echo form_default_hidden('cd_pauta_cci', '', $pauta);	
			echo form_default_hidden('cd_pauta_cci_assunto', '', $assunto);	
			echo form_default_row('nr_pauta_cci', 'Número da Ata :', '<label class="label label-inverse">'.$pauta['nr_pauta_cci'].'</label>');
			echo form_default_row('ds_local', 'Local :', $pauta['ds_local']);
			echo form_default_row('dt_reuniao', 'Dt. Reunião :', $pauta['dt_pauta_cci']." ".$pauta['hr_pauta_cci']);
			
			if(trim($pauta['dt_aprovacao']) == '')
			{
				echo form_default_row('cd_usuario_aprovacao', 'Usuário Encerramento :', $pauta['cd_usuario_aprovacao']);
			    echo form_default_row('dt_aprovacao', 'Dt. Encerramento :', $pauta['dt_aprovacao']);
			}
		echo form_end_box('default_box');
		echo form_command_bar_detail_start();
		echo form_start_box('default_assunto_box', 'Assunto');
		    echo form_default_row('nr_item', 'Número :', '<label class="label label-inverse">'.$assunto['nr_item'].'</label>');
		    echo form_default_row('cd_gerencia_responsavel', 'Gerência Responsável :', $assunto['cd_gerencia_responsavel']);
		    echo form_default_row('cd_usuario_responsavel', 'Usuário Responsável :', $assunto['usuario_responsavel']);
			echo form_default_textarea('ds_pauta_cci_assunto', 'Assunto :', $assunto, 'style="height:80px;"');
		echo form_end_box('default_assunto_box');
		if(trim($pauta['dt_aprovacao']) == '')
		{
		echo form_start_box('default_arquivo_box', ' Cadastro - Arquivo');
			echo form_default_upload_multiplo('arquivo_m', 'Arquivo :*', 'pauta_cci', 'validaArq');
			echo form_default_row('', '', '<i>Adicione o(s) arquivo(s) e depois clique no botão [Anexar]</i>');
		echo form_end_box('default_arquivo_box');
		}
		echo form_command_bar_detail_start();
		echo form_command_bar_detail_end();
	echo form_close();
         
	echo $grid->render();
	echo br(2);
echo aba_end();

$this->load->view('footer_interna');
?>