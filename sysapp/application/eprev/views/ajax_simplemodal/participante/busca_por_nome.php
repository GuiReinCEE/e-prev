<script>
	function buscar_<?php echo($id); ?>()
	{
		$("#grid_participantes_<?php echo($id); ?>").html("<?php echo loader_html(); ?>");
		$.post("<?php echo site_url('ajax/participante/form_busca_por_nome_post'); ?>",
			{
				jscallback        : '<?php echo($jscallback); ?>',
				nome_participante : $("#nome_participante_<?php echo($id); ?>").val()
			},
			function(data)
			{
				$("#grid_participantes_<?php echo($id); ?>").html(data);
			});
	}
	
	$(function() {
		$("#nome_participante_<?php echo($id); ?>").focus();
    });
</script>
<style>
.resultado_busca_participante{
	height:250px;
	text-align:center;
}
</style>
<?php
echo form_start_box("buscar_pelo_nome_box_".$id, "Encontrar documentos pelo nome");
	echo form_default_text("nome_participante_".$id, "Nome:",'','style="width:300px;"');
	echo form_default_row("", "", comando("", "Buscar", "buscar_".$id."();").nbsp().comando("","Fechar",$close));
echo form_end_box( "buscar_pelo_nome_box_".$id );
echo br();
echo '<div class="resultado_busca_participante" id="grid_participantes_'.$id.'"></div>';
?>
