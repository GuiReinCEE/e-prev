<?php 
	set_title('Eventos Institucionais - Cadastro');
	$this->load->view('header'); 
?>
<script>
	
	<?php 
		if(intval($row['cd_site_parceiro'])>0)
		{
			echo form_default_js_submit(array("nome","nr_ordem"));
		}
		else
		{
			echo form_default_js_submit(array("nome","nr_ordem","img_parceiro","fl_libera"));
		}
	
	?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/site_parceiro"); ?>';
	}
	
	function imagem(cd_site_parceiro)
	{
		location.href='<?php echo site_url("ecrm/site_parceiro/imagem"); ?>' + "/" + cd_site_parceiro;
	}	

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

	echo aba_start( $abas );

	echo form_open('ecrm/site_parceiro/salvar');
	echo form_start_box( "default_box", "Parceiro" );
		echo form_default_text('cd_site_parceiro', "Código: ", intval($row['cd_site_parceiro']), "style='width:500px;border: 0px;' readonly" );
		echo form_default_text("nome", "Nome:* ", $row, "style='width:100%;'");
		echo form_default_text("url", "Link: ", $row, "style='width:100%;'");
		echo form_default_integer("nr_ordem", "Ordem:* ", $row, "style='width:100%;'");
		
		if(intval($row['cd_site_parceiro'])>0 )
		{
			echo form_default_upload_iframe('img_parceiro', 'site_parceiro', 'Imagem:* (A = 50px, .jpg)');
			if(trim($row['img_parceiro']) != "")
			{
				echo form_default_row('', 'Imagem atual:', '<img src="../../../../../eletroceee/img/site_parceiro/'.$row['img_parceiro'].'" border="0">');
			}
			
			$ar_libera = Array(Array('text' => 'Sim', 'value' => 'S'),Array('text' => 'Não', 'value' => 'N')) ;
			echo form_default_dropdown('fl_libera', 'Liberar exibição:*', $ar_libera, Array($row['fl_libera']));
		}
		
	echo form_end_box("default_box");

	echo form_command_bar_detail_start();
		echo button_save();

		if(intval($row['cd_site_parceiro']) > 0)
		{
			echo button_delete("ecrm/site_parceiro/excluir",$row["cd_site_parceiro"]);
		}
	echo form_command_bar_detail_end();

	echo aba_end();

	echo form_close();

	$this->load->view('footer_interna');
?>