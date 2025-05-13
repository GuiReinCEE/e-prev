<?php
set_title('Copa - Resultado');
$this->load->view('header');
?>
<script>
function filtrar()
{
	$("#resultado_div").html("<?php echo loader_html();?>");
    $.post('<?php echo site_url('copa/copa/resultadoListar');?>',
	{},
    function(data)
    {
		$("#resultado_div").html(data);
    });
}

$(function(){
	filtrar();
})

function ir_tabela()
{
	location.href='<?php echo site_url("copa/copa/");?>';
}

function ir_palpite()
{
	location.href='<?php echo site_url("copa/copa/minha/");?>';
}

function ir_regulamento()
{
	location.href='<?php echo site_url("copa/copa/regulamento/");?>';
}	
</script>
<?php
$abas[] = array('aba_tab', 'Tabela', FALSE, 'ir_tabela();');
$abas[] = array('aba_pal', 'Palpite', FALSE, 'ir_palpite();');
$abas[] = array('aba_res', 'Resultado', TRUE, 'location.reload();');
$abas[] = array('aba_reg', 'Regulamento', FALSE, 'ir_regulamento();');

echo aba_start( $abas );
		echo '<div id="resultado_div" style="text-align:center;"></div>';
		echo br(10);
echo aba_end();
$this->load->view('footer'); 
?>