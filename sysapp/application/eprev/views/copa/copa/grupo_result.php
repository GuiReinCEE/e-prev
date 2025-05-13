<?php
	#print_r($ar_grupo);
	$ar_g = array("A","B","C","D","E","F","G","H");

echo '<table border="0">';
	foreach($ar_g as $i)
	{
echo '
		<tr>
			<td>
		<table id="table-1" width="400" class="sort-table" cellspacing="2" cellpadding="2" align="left">
			<caption style="font-weight:bold; font-size: 12pt; #535C65; font-family: calibri, tahoma,verdana,helvetica; ">Grupo '.$i.'</caption>
			<thead>
			<tr>
				<th>C</th>
				<th>Seleção</th>
				<th>P</th>
				<th>J</th>
				<th>V</th>
				<th>E</th>
				<th>D</th>
				<th>GP</th>
				<th>GC</th>
				<th>S</th>
			</tr>
			</thead>
			<tbody>
     ';
	foreach ($ar_grupo[$i] as $grupo)
	{
echo '
			<tr>
				<td '.(in_array(intval($grupo["nr_classifica"]), array(1,2)) ? 'style="font-weight: bold; font-size: 120%;"' : '').'>'.$grupo["nr_classifica"].'</td>
				<td '.(in_array(intval($grupo["nr_classifica"]), array(1,2)) ? 'style="font-weight: bold; font-size: 120%;"' : '').'>'.$grupo["sigla"].' - '.$grupo["ds_pais"].'</td>
				<td>'.$grupo["nr_pontos"].'</td>
				<td>'.($grupo["nr_vitoria"] + $grupo["nr_empate"] + $grupo["nr_derrota"]).'</td>
				<td>'.$grupo["nr_vitoria"].'</td>
				<td>'.$grupo["nr_empate"].'</td>
				<td>'.$grupo["nr_derrota"].'</td>
				<td>'.$grupo["nr_gol_pro"].'</td>
				<td>'.$grupo["nr_gol_contra"].'</td>
				<td>'.$grupo["nr_saldo"].'</td>
			</tr>
     ';
	}
echo '			
			</tbody>
		</table>
			</td>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>
     ';	
	}
echo '</table>';
?>