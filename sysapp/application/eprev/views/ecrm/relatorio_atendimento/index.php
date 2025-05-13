<?php
set_title('Relat�rio de atendimentos');
$this->load->view('header');
?>
<script>
    function gerarPDF()
	{
		filter_bar_form.method = "post";
		filter_bar_form.action = '<?php echo base_url() . index_page(); ?>/ecrm/relatorio_atendimento/gerarPDF';
		filter_bar_form.target = "_blank";
		filter_bar_form.submit();
	}
</script>
<?php

$abas[] = array('aba_lista', 'Relat�rio', TRUE, 'location.reload();');

echo aba_start( $abas );
echo form_start_box( "default_box", 'Relat�rio de atendimentos ' );
    echo '<form id="filter_bar_form" name="filter_bar_form" onsubmit="return false;">';
    echo 'Selecione o per�odo abaixo e clique em "Gerar Relat�rio":';
    echo filter_date_interval('dt_inicio', 'dt_fim', 'Per�odo:', '01/'.date('m/Y'), date('d/m/Y'), FALSE);
    echo '</form>';
echo form_end_box("default_box");

echo form_command_bar_detail_start();
    echo button_save('Gerar Relat�rio', 'gerarPDF();');
echo form_command_bar_detail_end();

?>