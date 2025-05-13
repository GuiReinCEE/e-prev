<?php 
set_title('Clipping Diário - Cadastro');
$this->load->view('header'); 
?>
<script>
	<?php echo form_default_js_submit(array("fl_formato","editorial", "titulo"));?>

	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/informativo"); ?>';
	}
	
	function excluirClipping()
	{
		if(confirm('Deseja excluir?'))
		{
			$.post("<?php echo site_url('ecrm/informativo/excluir'); ?>/",
			{
				codigo : $("#codigo").val()
			},
			function(data){
				location.href='<?php echo site_url("ecrm/informativo"); ?>';
			});
		}
	}
	
	function novo()
	{
		location.href = '<?php echo site_url("ecrm/informativo/cadastro");?>';
	}	

	$(document).ready(function()
	{
		$("#fl_formato").change(function(){ 
		    var formato = $('#fl_formato').val();
			$('#default_box_content').html("<?php echo loader_html(); ?>");
			$("#command_bar").hide();
			location.href = '<?php echo site_url("ecrm/informativo/cadastro/".intval($row['codigo'])); ?>/' + formato;
		});
	});		
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_detalhe', 'Cadastro', true, 'location.reload();');

echo aba_start($abas);
	echo form_open('ecrm/informativo/salvar');
		echo form_start_box("default_box", "Cadastro");
			echo form_hidden('codigo', intval($row['codigo']));
			
			if(intval($row['codigo']) > 0)
			{
				echo form_hidden('fl_formato', trim($row['fl_formato']));
				echo form_default_row("","", "<a target='_blank' href='http://www.e-prev.com.br/clipping/noticias.php?d=".intval($row['dia'])."&m=".intval($row['mes'])."&a=".intval($row['ano'])."#A".intval($row['codigo'])."'>[Ver notícia]<a/>");
			}
			else
			{
				$ar_formato[] = array('value'=>'HTML', 'text'=>'HTML');
				$ar_formato[] = array('value'=>'TEXT', 'text'=>'Texto');			
				echo form_default_dropdown("fl_formato", "Formato:(*)", $ar_formato, array($row['fl_formato']));
			}
			
			echo form_default_dropdown("id_noticia_editorial", "Editorial:(*)", $arr_editorial, array($row['editorial'])); 
			echo form_default_text("titulo", "Título:(*)", $row['titulo'], "style='width: 100%;'"); 
			
			if($row['fl_formato'] == "HTML")
			{
				echo form_default_editor_html('descricao', "Descrição:(*)", $row['descricao'], 'style="height: 300px;"',true);
			}
			else
			{
				echo form_default_textarea("descricao", "Descrição:(*)", $row['descricao'], "", "0");  
			}
			
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save();
			
			if(intval($row['codigo']) > 0)
			{
				echo button_save("Excluir","excluirClipping()","botao_vermelho");
				echo button_save("Novo Registro","novo()","botao_disabled");
			}
			
		echo form_command_bar_detail_end();
	echo form_close();
	echo br(5);
echo aba_end();

$this->load->view('footer_interna');
?>