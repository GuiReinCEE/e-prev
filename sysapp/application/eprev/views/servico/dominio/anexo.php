<?php
set_title('Controles TI - Anexo');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(array('ds_dominio_arquivo'), 'valida_arquivo(form)');?>

	function excluir(cd_dominio_anexo)
	{
		var confirmacao = 'Deseja excluir o arquivo?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url("servico/dominio/anexo_excluir/".$row["cd_dominio"]) ?>/'+cd_dominio_anexo;
		}
	}

	function ir_lista()
	{
		location.href = '<?= site_url('/servico/dominio') ?>';
	}

	function ir_renovacao()
	{
		location.href = '<?= site_url('servico/dominio/renovacao/'.$row['cd_dominio']) ?>' ;
	}

	function ir_cadastro()
	{
		location.href = '<?= site_url('servico/dominio/cadastro/'.$row['cd_dominio']) ?>';
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

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
        	'CaseInsensitiveString',
			'DateTimeBR', 
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
        ob_resul.sort(1, true);
    }

    
    $(function(){
		configure_result_table();
	});
</script>
<?php 
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_renovacao', 'Renovação', FALSE, 'ir_renovacao();');
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
		$item['ds_dominio_arquivo'],
		$item['dt_inclusao'],
		array(anchor(base_url().'up/dominio/' . $item['arquivo'], $item['arquivo_nome'] , array('target' => '_blank')), "text-align:left;"),
		'<a href="javascript:void(0);" onclick="excluir('.$item['cd_dominio_arquivo'].')">[excluir]</a>' 
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start($abas);
	echo form_open('servico/dominio/anexo_salvar');
		echo form_start_box('default_box', 'Controle TI');
			echo form_default_hidden('cd_dominio', '', $row);
			echo form_default_row('descricao', 'Descrição: ',  $row['descricao']);
			echo form_default_row('dt_dominio_renovacao', 'Dt. Expiração: ', $row['dt_dominio_renovacao']);
		echo form_end_box('default_box');

		echo form_start_box('default_arquivo_box', ' Cadastro - Arquivo');
			echo form_default_text('ds_dominio_arquivo', 'Descrição: (*)', $row, 'style="width:350px;"');
			echo form_default_upload_iframe('arquivo', 'dominio', 'Arquivo:', array(), 'dominio', true);
		echo form_end_box('default_arquivo_box');
		echo form_command_bar_detail_start();
		    echo button_save('Salvar');	
		echo form_command_bar_detail_end();
	echo form_close();
    echo $grid->render();
	echo br(2);
echo aba_end();
$this->load->view('footer_interna');
?>