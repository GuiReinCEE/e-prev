<?php 
set_title('Meu Computador');
$this->load->library('charts');
$this->load->view('header'); 
?>
<script>
	function ir_lista()
	{
		location.href='<?php echo site_url("cadastro/equipamento/"); ?>';
	}
	
</script>
<?php
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');
echo aba_start( $abas );


echo form_open();


echo form_start_box( "default_box", "Equipamento" );

echo form_default_text("dt_cadastro", "Data Cadastro:", $row, "style='width:100%;border: 0px;' readonly");

echo form_default_text('codigo_patrimonio', "N� Patrim�nio: ", $row, "style='width:100%;border: 0px;' readonly" );
echo form_default_text("bi_descricao", "Descri��o:", $row, "style='width:100%;border: 0px;' readonly");

echo form_default_text('cod_divisao', 'Ger�ncia:', $row, "style='width:100%;border: 0px;' readonly");
echo form_default_text('usuario', 'Usu�rio:', $row, "style='width:100%;border: 0px;' readonly");

echo form_default_text("nome_computador", "Identifica��o na rede:", $row, "style='width:100%;border: 0px;' readonly");
echo form_default_text("ip", "IP:", $row,  "style='width:100%;border: 0px;' readonly");
echo form_default_text("mac_address", "MAC Address:", $row, "style='width:100%;border: 0px;' readonly");

echo form_default_text('tipo_equipamento', 'Tipo:', $row,  "style='width:100%;border: 0px;' readonly");
echo form_default_text('situacao', 'Situa��o:', $row,  "style='width:100%;border: 0px;' readonly");

echo form_default_text('sistema_operacional_categoria', 'Categ. Sistema Operacional:', $row,  "style='width:100%;border: 0px;' readonly");
echo form_default_text('sistema_operacional', 'Sistema Operacional:', $row, "style='width:100%;border: 0px;' readonly");

echo form_default_text('processador_categoria', 'Categoria Processador:', $row,  "style='width:100%;border: 0px;' readonly");
echo form_default_text("processador_nome", "Processador:", $row, "style='width:100%;border: 0px;' readonly");

echo form_default_text('memoria_ram_categoria', 'Categoria Mem�ria RAM:', $row,  "style='width:100%;border: 0px;' readonly");
echo form_default_text("memoria_ram", "Mem�ria RAM (".$row['qt_memoria']."):", $row,  "style='width:100%;border: 0px;' readonly");

echo form_default_text("espaco_disco_total", "HD Total (".$row['qt_espaco_total']."):", $row,  "style='width:100%;border: 0px;' readonly");
echo form_default_text("espaco_disco_livre", "HD Livre (".$row['qt_espaco_livre']."):", $row,  "style='width:100%;border: 0px;' readonly");
echo form_default_text("espaco_disco_usado", "HD Usado (".$row['qt_espaco_usado']."):", $row,  "style='width:100%;border: 0px;' readonly");

echo form_default_text("drv_odbc", "Drv ODBC PostgreSQL:", $row, "style='width:100%;border: 0px;' readonly");
echo form_default_text("monitor_resolucao", "Resolu��o Monitor:", $row, "style='width:100%;border: 0px;' readonly");
echo form_default_text("versao_explorer", "Vers�o Internet Explorer:", $row, "style='width:100%;border: 0px;' readonly");
echo form_default_text("versao_firefox", "Vers�o Firefox:", $row, "style='width:100%;border: 0px;' readonly");
echo form_default_text("dt_instalacao_os", "Dt Instala��o SO:", $row, "style='width:100%;border: 0px;' readonly");

echo form_default_text("ultima_atualizacao", "Dt CPUScanner Autom�tico:", $row, "style='width:100%;border: 0px;' readonly");
echo form_default_text("dt_cpuscanner_verificado", "Dt CPUScanner Manual:", $row, "style='width:100%;border: 0px;' readonly");
echo form_default_text("ds_cpuscanner_verificado_usuario", "Usu�rio CPUScanner Manual:", $row, "style='width:100%;border: 0px;' readonly");

echo form_default_textarea("programas_instalados","Programas instalados:",$row, " style='border: 1px solid gray;' readonly");
echo form_default_textarea("atalhos","Atalhos desktop:",$row, "style='border: 1px solid gray;' readonly");

$ar_titulo = Array();
$ar_dado = Array();
$ar_titulo[] = "Livre";
$ar_titulo[] = "Usado";
$ar_dado[] = $row["espaco_disco_livre"];
$ar_dado[] = $row["espaco_disco_usado"];	
$ar_image = $this->charts->pieChart(80,$ar_dado,$ar_titulo,'','Espa�o em disco');	
echo form_default_row('','','<center><img src="'.$ar_image['name'].'" border="0"></center>');

echo form_end_box("default_box");




// Barra de comandos ...
echo form_command_bar_detail_start();

echo form_command_bar_detail_end();
?>
<BR>
<BR>
<BR>
<?php
echo aba_end();
// FECHAR FORM
echo form_close();

$this->load->view('footer_interna');
?>