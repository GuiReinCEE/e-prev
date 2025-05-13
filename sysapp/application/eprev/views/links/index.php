<?php
$this->load->view('header_interna');
?>

<? foreach( $grupos as $grupo ): ?>

	<div style='
		background:#EEEEEE;
		border-width:1px;
		border-style:dashed;
		margin-bottom: 10px;
		margin-left: 10px;
		margin-right: 10px;
	'>
	<center><h1><?= $grupo['nome'] ?></h1></center>

	<ul>
	<? foreach( $grupo['links'] as $link ): ?>
		<li><a href="<?= $link['link'] ?>" target="_blank" style="font-size:15px;"><?= $link['nome'] ?></a></li>
	<? endforeach; ?>
	</ul><br>
	</div>

<? endforeach; ?>

<?php
$this->load->view('footer_interna');
?>