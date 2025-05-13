<div class="text-center">
	<h2 class="font_yannoka" style="margin-top:0px"><?= $row['descricao'] ?></h2>
	<table style="margin:0 auto;">
		<tr>
			<td style="padding-left: 50px;">Crit�rio: <?= $row['criterio'] ?></td>
			<td style="padding-left: 50px;">Meta: <?= $row['meta'] ?></td>
			<td style="padding-left: 50px;">Status: <?= $row['status'] ?></td>
		</tr>
	</table>
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
	<div id="avaliacao" class="text-center">
		<? if(trim($row['ds_avaliacao']) != ''): ?>
			<h1 class="font_yannoka" style="text-align: left; font-size: 25px;">Avalia��o da Diretoria Executiva</h1>
			<h2 class="font_yannoka" style="text-align: justify; font-size: 20px;"><?= nl2br($row['ds_avaliacao']) ?></h2>
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