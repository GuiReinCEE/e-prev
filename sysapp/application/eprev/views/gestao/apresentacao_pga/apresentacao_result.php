<div class="text-center">
	<h2 class="font_yannoka" style="margin-top:0px"><?= utf8_decode($indicador['ds_indicador']) ?></h2>
	
	<div id="analise" style="margin-left: 670px">
		<? 
			if (trim($indicador['tp_analise']) != "") 
			{
		?>
			<img src="<?= base_url('img/indicador_melhor_'.(trim($indicador['tp_analise']) == '+' ? 'mais' : 'menos').'.png') ?>" />
		<?
			}
		?>
	</div>

	<div id="grafico">
		<? if($grafico != ''): ?>
		<img src="<?= base_url($grafico) ?>" />
		<? endif; ?>
	</div>
	<br/>
	<div id="tabela" class="text-center">
		<? if($tabela != ''): ?>
		<?= $tabela ?>
		<? endif; ?>
	</div>
</div>
<script>
	$(function(){
		$(".indicador_table tr td").each(function(i, td){
			var font_weight = $(this).css("font-weight");
			var text_align  = $(this).css("text-align");
			var display     = $(this).css("display");

			console.log(display);

			$(this).removeAttr("style");

			$(this).css("text-align", text_align);
			$(this).css("font-weight", font_weight);
			$(this).css("display", display);
		});
	});
</script>
<br/><br/>