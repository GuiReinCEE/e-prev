<?php
$this->load->view('header');
?>
<script>
function salvar(f)
{
	if(confirm('Salvar?'))
	{
		f.submit();
	}
	else
	{
		return false;
	}
}
</script>
<?

echo form_open('avaliacao/controle/salvar');
echo form_hidden("dt_periodo", $dt_periodo);

echo form_start_box('configuracao', "Configuração de abertura e encereramento de atividade para o ano de $dt_periodo!");

echo form_default_date('dt_abertura', 'Data de abertura:', $record);
echo form_default_date('dt_fechamento', 'Data de encerramento:', $record);

echo form_end_box('configuracao');

echo form_command_bar_detail_start();
echo form_command_bar_detail_button("Enviar", "salvar(this.form);");
echo form_command_bar_detail_end();

echo form_close();

$this->load->view('footer_interna');
?>