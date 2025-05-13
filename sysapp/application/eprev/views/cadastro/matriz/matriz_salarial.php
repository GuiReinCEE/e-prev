<?php
set_title('Avaliação - Matriz Salarial ');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");
	document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

	$.post( '<?php echo site_url("cadastro/matriz/lista_matriz_salarial"); ?>/',
	function(data)
	{
		$('#result_div').html(data);
	});
}

function ir_colaboradores()
{
    location.href='<?php echo site_url('/cadastro/matriz');?>';
}

$(function(){
	filtrar();
})

</script>
<?php
$abas[] = array( 'aba_lista', 'Colaboradores', false, 'ir_colaboradores();' );
$abas[] = array( 'aba_lista', 'Matriz Salarial', true, 'location.reload();' );

echo aba_start( $abas );
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();

$this->load->view('footer');
?>