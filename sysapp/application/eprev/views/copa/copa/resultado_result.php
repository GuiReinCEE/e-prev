<table border="0" cellspacing="2" cellpadding="2" align="center">
	<tr>
		<td valign="top">
<?php
echo '
		<table id="table-1" width="400" class="sort-table" cellspacing="2" cellpadding="2" align="left">
			<caption>
				<span style="font-weight:bold; font-size: 12pt; #535C65; font-family: calibri, tahoma,verdana,helvetica; ">Resultado Geral</span>
				<BR>
				Total de participantes: '.count($ar_resultado).'
			</caption>
			<thead>
			<tr>
				<th>Pos</th>
				<th>Nome</th>
				<th>Campeão</th>
				<th>Pontos</th>
			</tr>
			</thead>
			<tbody>
     ';
	$i = 0;
	$nr_conta = 1;
	$nr_ponto_ant = 0;
	foreach ($ar_resultado as $item)
	{
		if($i == 0)
		{
			$nr_ponto_ant = intval($item["nr_ponto"]);
		}
		
		if(intval($item["nr_ponto"]) != $nr_ponto_ant)
		{
			$nr_ponto_ant = intval($item["nr_ponto"]);
			$nr_conta++;
		}		
echo '
			<tr>
				<td style="font-weight:normal; font-size: 110%;" valign="middle">'.$nr_conta.'º</td>
				<td style="font-weight:normal; font-size: 110%;" align="left" valign="middle">
					'.anchor("copa/copa/minha/".$item["cd_usuario"], '<span style="font-family: tahoma,verdana,helvetica; font-size:  14px; font-weight:normal;">'.$item["nome"].'</span>').'
				</td>
				<td style="font-weight:normal; font-size: 110%;" align="center" valign="middle">
					<div style="font-size: 80%; line-height: 10px;">
						<img src="'.base_url()."img/copa/".$item["bandeira"].'" border="0" title="'.$item["ds_pais"].'">
						<BR>
						'.$item["ds_pais"].'
					</div>
				</td>
				<td style="font-weight:bold; font-size: 220%; color:#028639;" valign="middle">'.$item["nr_ponto"].'</td>
			</tr>
     ';
		$i++;
	}
echo '			
			</tbody>
		</table>
		<BR>
     ';	
?>			
		</td>
		<td width="50"></td>
		<td valign="top">
<?php
$vl_total = count($ar_resultado_pagou) * 50;
$vl_50    = ceil($vl_total / 2);
$vl_30    = ceil((30 * $vl_total) / 100);
$vl_20    = ($vl_total - $vl_50 - $vl_30);

#echo "<PRE>".print_r($ar_resultado_pagou,true)."</PRE>";

/*

    [3] => Array
        (
            [cd_usuario] => 170
            [nome] => Cristiano Jacobsen de Oliveira
            [fl_pagou] => S
            [nr_ponto] => 72
        )
*/

$ar_divide = array();
foreach ($ar_resultado_pagou as $key => $value)
{
	foreach ($value as $key2 => $value2)
	{
		if($key2 == "nr_ponto")
		{
			$index = $value2;
			if (array_key_exists($index, $ar_divide))
			{
				$ar_divide[$index]++;
			} 
			else 
			{
				$ar_divide[$index] = 1;
			}
		}
    }
}

#echo "<PRE>".print_r($ar_divide,true)."</PRE>";

#print_r(array_count_values($ar_resultado_pagou));

echo '
		<table id="table-1" width="400" class="sort-table" cellspacing="2" cellpadding="2" align="left">
			<caption> 
				<span style="font-weight:bold; font-size: 12pt; #535C65; font-family: calibri, tahoma,verdana,helvetica; ">Resultado Concorrente ao prêmio (R$ '.number_format($vl_total,2,",",".").')</span>
				<BR>Distribuição do prêmio : 
				<BR>1º Colocado: 50% (R$ '.number_format($vl_50,2,",",".").')
				<BR>2º Colocado: 30% (R$ '.number_format($vl_30,2,",",".").')
				<BR>3º Colocado: 20% (R$ '.number_format($vl_20,2,",",".").')
				<BR>&nbsp
				<BR>
				Total de participantes: '.count($ar_resultado_pagou).'	
			</caption>
			<thead>
			<tr>
				<th>Pos</th>
				<th>Nome</th>
				<th>Pontos</th>
				<th>Prêmio R$</th>
			</tr>
			</thead>
			<tbody>
     ';
	$i = 0;
	$nr_conta = 1;
	$nr_ponto_ant = 0;
	foreach ($ar_resultado_pagou as $item)
	{
		if($i == 0)
		{
			$nr_ponto_ant = intval($item["nr_ponto"]);
		}
		
		if(intval($item["nr_ponto"]) != $nr_ponto_ant)
		{
			$nr_ponto_ant = intval($item["nr_ponto"]);
			$nr_conta++;
		}
		
		$vl_premio = "";
		if($nr_conta == 1)
		{
			$vl_premio = $vl_50 / $ar_divide[$item["nr_ponto"]];
			$vl_premio = number_format($vl_premio,2,",",".");
		}
		elseif($nr_conta == 2)
		{
			$vl_premio = $vl_30 / $ar_divide[$item["nr_ponto"]];
			$vl_premio = number_format($vl_premio,2,",",".");
		}
		elseif($nr_conta == 3)
		{
			$vl_premio = $vl_20 / $ar_divide[$item["nr_ponto"]];
			$vl_premio = number_format($vl_premio,2,",",".");
		}		
		
echo '
			<tr>
				<td style="font-weight:normal; font-size: 110%;"  valign="middle">'.$nr_conta.'º</td>
				<td style="font-weight:normal; font-size: 110%;" align="left" valign="middle">
					'.anchor("copa/copa/minha/".$item["cd_usuario"], '<span style="font-family: tahoma,verdana,helvetica; font-size:  13px; font-weight:normal;">'.$item["nome"].'</span>').'
				</td>
				<td style="font-weight:bold; font-size: 130%;"  valign="middle">'.$item["nr_ponto"].'</td>
				<td style="font-weight:bold; font-size: 130%;"  valign="middle">'.$vl_premio.'</td>
			</tr>
     ';
		$i++;
	}
echo '			
			</tbody>
		</table>
		<BR>
     ';	
?>		
		</td>
	</tr>
</table>
