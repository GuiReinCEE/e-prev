<script>
	function buscar_<?php echo($id); ?>()
	{
		$("#grid_tipo_documentos_<?php echo($id); ?>").html("<?php echo loader_html(); ?>");
		$.post("<?php echo site_url('ajax/tipo_documentos/form_busca_por_nome_post'); ?>",
			{
				jscallback    : '<?php echo($jscallback); ?>',
				descricao_doc : $("#descricao_doc_<?php echo($id); ?>").val()
			},
			function(data)
			{
				$("#grid_tipo_documentos_<?php echo($id); ?>").html(data);
			});
	}
	
	$(function() {
		$("#descricao_doc_<?php echo($id); ?>").focus();
    });
</script>
<style>
.resultado_busca_tipo_documentos{
	height:250px;	
	text-align:center;
}
</style>
<?php
echo form_start_box("buscar_pelo_nome_box_".$id, "Encontrar documentos pela descrição");
	echo form_default_text("descricao_doc_".$id, "Descrição:",'','style="width:300px;"');
	echo form_default_row("", "", comando("", "Buscar", "buscar_".$id."();").nbsp().comando("","Fechar",$close));
echo form_end_box( "buscar_pelo_nome_box_".$id );
echo br();
echo '<div class="resultado_busca_tipo_documentos" id="grid_tipo_documentos_'.$id.'"></div>';
?>