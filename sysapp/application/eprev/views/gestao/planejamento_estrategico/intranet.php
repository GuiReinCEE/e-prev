<style>
	.table_planejamento {
		width:100%;
	    display: table;
	    font-family: Arial;
	}

	.table_planejamento thead {;
	    color: #FFFFFF;
	    font-size: 10pt;
	    font-weight: bold;
	    text-align: center;
	    background-color: #244062;
	}

	.table_planejamento thead th {
	    font-weight: bold;
	    text-align: center;
	    padding: 2px 5px;
	    cursor: pointer;
	    border: 1px solid #99BBE8;
	    height: 25px;
	    white-space: nowrap;
	}

	.table_planejamento tbody tr {
		padding: 2px 5px;
		cursor: pointer;
	    border: 1px solid #99BBE8;
	    height: 25px;
	    white-space: nowrap;
	}

	.table_planejamento tbody tr.planejamento_desdobramento {
		font-weight: bold;
	    text-align: center;
	    background-color: #366092;
	    font-size: 10pt;
	    color: #FFFFFF;
	}

	.table_planejamento tbody tr.planejamento_objetivo {
		font-weight: bold;
	    text-align: center;
	    background-color: #4F81BD;
	    font-size: 10pt;
	    color: #FFFFFF;
	}

	.table_planejamento tbody tr.planejamento_programa_projeto {
		font-weight: bold;
	    background-color: #DCE6F1;
	    font-size: 11pt;
	    color: #000000;
	}

	.table_planejamento tbody tr a {
		color: blue;
	}

</style>
<script>
	function ir_acoes()
    {
    	<? if(intval($row['cd_planejamento_estrategico']) == 2): ?>
    		location.href = "<?= site_url('ecrm/intranet/pagina/PE/10475/1') ?>";
    	<? elseif(intval($row['cd_planejamento_estrategico']) == 3): ?>
    		location.href = "<?= site_url('ecrm/intranet/pagina/PE/10525/1') ?>";
    	<? elseif(intval($row['cd_planejamento_estrategico']) == 4): ?>
    		location.href = "<?= site_url('ecrm/intranet/pagina/PE/10537/1') ?>";
    	<? elseif(intval($row['cd_planejamento_estrategico']) == 5): ?>
    		location.href = "<?= site_url('ecrm/intranet/pagina/PE/10553/1') ?>";
    	<? elseif(intval($row['cd_planejamento_estrategico']) == 6): ?>
    		location.href = "<?= site_url('ecrm/intranet/pagina/PE/10575/1') ?>";
    	<? endif; ?>
    }   
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	$abas[] = array('aba_ações_lista', 'Ações', FALSE, 'ir_acoes();');

	$this->load->helper('grid');

	echo aba_start($abas);
		echo br();
		echo '<center><img src="'.base_url().'up/planejamento_estrategico/'.$row['arquivo'].'" style="height:562px;" /></center>';
		//echo $grid->render();

		echo br();
		if(count($collection) > 0)
		{
			echo '
				<center>
					<h1 style="font-size: 18pt; font-family: calibri, arial; padding-left: 50px; padding-bottom: 5px; color:#000000;">
						Programas e Projetos aprovados para '.$row['nr_ano_inicial'].'/'.$row['nr_ano_final'].'
					</h1>
				</center>';
			echo br();
		}
		else
		{
			/*
			echo '
				<center>
					<h1 style="font-size: 18pt; font-family: calibri, arial; padding-left: 50px; padding-bottom: 5px; color:#000000;">
						Os programas e projetos estão no Sistema S.A. (Interact).
					</h1>
				</center>';
				*/
		}
		

		?>
		<? if(count($collection) > 0): ?>
		<table class="table_planejamento">
			<thead>
				<tr>
					<th>PROGRAMA/PROJETO</th>
					<?php
					$colspan = 2;
					foreach ($ano as $key => $item) 
					{
						echo '<th>'.$item.'</th>';

						$colspan++;
					}
					?>
					<th>RESPONSÁVEL</th>
				</tr>
			<thead>
			<tbody>
				<?php
					foreach ($collection as $key => $item) 
					{
						echo '
							<tr class="planejamento_desdobramento">
								<td colspan="'.intval($colspan).'">'.$item['ds_planejamento_estrategico_desdobramento'].'</td>
							</tr>';

						foreach ($item['objetivo'] as $key2 => $item2) 
						{
							echo '
							<tr class="planejamento_objetivo">
								<td colspan="'.intval($colspan).'">'.$item2.'</td>
							</tr>';
						}

						foreach ($item['programa_projeto'] as $key2 => $item2) 
						{
							echo '
							<tr class="planejamento_programa_projeto">
								<td style="text-align:left;">'.$item2['ds_programa_projeto'].'</td>';

								foreach ($ano as $key3 => $item3) 
								{
									echo '<td style="text-align:center;">'.$item2[$item3].'</td>';
								}
							echo '
								<td style="text-align:center;">'.$item2['cd_gerencia_responsavel'].'</td>
							</tr>';
						}
					}
				?>
			</tbody>
		</table>
		<? endif; ?>

		<?php
		echo br(2);
	echo aba_end();
?>