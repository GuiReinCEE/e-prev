<?php
set_title('Informativo do Cenário Legal');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post('<?php echo site_url('/ecrm/informativo_cenario_legal/conteudo_listar'); ?>',
	{
		cd_edicao: "<?php echo $row['cd_edicao']; ?>"
	},
	function(data)
	{
		$('#result_div').html(data);
		configure_result_table();
	});
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),
	[
		'Number',
		'CaseInsensitiveString',
		'DateBR',
		'DateBR'
	]);
	ob_resul.onsort = function()
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

function novo()
{
	location.href='<?php echo site_url("ecrm/informativo_cenario_legal/conteudo_cadastro/".$row['cd_edicao']); ?>';
}

function ir_lista()
{
	location.href='<?php echo site_url("ecrm/informativo_cenario_legal"); ?>';
}

function enviar_email()
{
	var confirmacao = 'Deseja enviar email cenário legal?\n\n'+
		'Clique [Ok] para Sim\n\n'+
		'Clique [Cancelar] para Não\n\n';

	if(confirm(confirmacao))
	{
		location.href='<?php  echo site_url("ecrm/informativo_cenario_legal/enviar_email/".$row['cd_edicao']); ?>';
	}
}

$(function(){
	filtrar();
});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Cadastro', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_start_box("default_box", $row['tit_capa']);
		if(trim($row['dt_envio_email']) == '')
		{
			echo form_default_row("","",button_save('Novo Item Cenário Legal', 'novo()') . button_save('Enviar Email Cenário Legal', 'enviar_email()','botao_vermelho'));
		}
		else
		{
			echo form_default_row("","","Enviado: ".$row['dt_envio_email']);
			echo form_default_row("","","Por: ".$row['nome']);
		}
	echo form_end_box("default_box");	
	echo br();
	echo '<div id="result_div"></div>';
	echo br(2);
echo aba_end();
$this->load->view('footer');
?>
