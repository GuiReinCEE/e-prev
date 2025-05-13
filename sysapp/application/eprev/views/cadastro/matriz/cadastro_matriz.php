<?php
set_title('Avaliação - Matriz Salarial ');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post( '<?php echo site_url("cadastro/matriz/listar_cadastro_matriz"); ?>/',
	{
		cd_familia : $('#cd_familia').val()
	},
	function(data)
	{
		$('#result_div').html(data);
	});
}

function ir_lista()
{
    location.href='<?php echo site_url('/cadastro/matriz/matriz_salarial');?>';
}

function ir_colaboradores()
{
    location.href='<?php echo site_url('cadastro/matriz');?>';
}

function salvar(faixa)
{
	if(confirm('Deseja salvar?'))
	{
		var vl_ini = $('#vl_ini_'+faixa).val();
		var vl_fim = $('#vl_fim_'+faixa).val();
	
		$.post( '<?php echo site_url("cadastro/matriz/salvar_matriz_salarial"); ?>/',
		{
			faixa      : faixa,
			vl_ini     : vl_ini,
			vl_fim     : vl_fim,
			cd_familia : $('#cd_familia').val()
		},
		function()
		{
			filtrar();	
		});
	}
}

$(function(){
	filtrar();
})

</script>
<?php
$abas[] = array( 'aba_lista', 'Colaboradores', false, 'ir_colaboradores();' );
$abas[] = array('aba_lista', 'Matriz Salarial', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_hidden('cd_familia', $cd_familia);
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();

$this->load->view('footer_interna');
?>