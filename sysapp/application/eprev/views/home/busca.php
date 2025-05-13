<?php
set_title("Resultados da busca");
$this->load->view('header');

echo form_start_box("resultado", "Resultado da busca", FALSE);
?>

<div style="padding: 10px 10px 10px 10px;">

<h2>Busca por "<?= $keyword ?>"</h2><br />

<? foreach($resultados as $resultado): ?>

	<a href="<?= $resultado['link'] ?>" target="<?php echo $resultado['target']; ?>">
	<b><?= $resultado['nome']; ?></b><br />
	<span style='color:GRAY'><?= $resultado['path']; ?></span><br />
	<?php if($resultado['resumo']!="") echo "<i>" . $resultado['resumo'] . "</i><br />"; ?>
	<span style='color:GREEN'><?= $resultado['label_link']; ?></span></a>

	<br><br>

<? endforeach; ?>

</div>

<?php
echo form_end_box("resultado", FALSE);

$this->load->view('footer_interna');
?>