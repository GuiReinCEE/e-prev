<?php
$this->load->view('header_interna');

?>
<script>
function aba_1(o)
{
	alert('aba 1');
}
function aba_2(o)
{
	window.open('http://www.google.com.br');
}
</script>
<?

echo form_open();

// monta abas

$abas[0] = array('aba_usuario', 'Teste de usu�rio', true, 'aba_1(this)');
$abas[1] = array('aba_google', 'Ir para o google!', false, 'aba_2(this)');
   
echo aba_start( $abas );
echo aba_end( 'abas');

// monta formul�rio

echo form_start_box( 'solicitacao' , 'Usu�rio solicitante:' );
echo form_default_usuario_ajax('cod_solicitante', '', '191', 'Solicitante:', 'Ger�ncia do solicitante:');
echo form_end_box( 'solicitacao');

echo form_start_box( 'atendimento' , 'Usu�rio atendente:' );
echo form_default_usuario_ajax('cod_atendente', '', '191', 'Atendente:', 'Ger�ncia do atendente:');
echo form_end_box( 'atendimento');

echo form_close();

$this->load->view('footer_interna');
?>