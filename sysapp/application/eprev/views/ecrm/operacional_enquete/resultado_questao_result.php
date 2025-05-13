<?php
$q_body = Array();
$q_head = array('Item');

foreach($ar_reg as $item)
{
	$q_body[] = array(array($item["ds_pergunta"], 'text-align:left; font-weight:bold; font-size: 120%;'));
	
	

	$tb_resp = '
				<table border="0" width="30%" class="sort-table">
					<tbody>
			   ';
	
	foreach($item["ar_resp"] as $itemResp)
	{
		$tb_resp.= '
						<tr>
							<td align="left">'.$itemResp["ds_resposta"].'</td>
							<td align="center">'.(intval($itemResp["qt_complemento"]) > 0 ? '<a href="javascript: resultadoVerComplemento('.$item["cd_pergunta"].','.$itemResp["cd_resposta"].')">[complemento]</a>' : "").'</td>
							<td align="right">'.$itemResp["qt_resposta"].'</td>
						</tr>
				   ';
	}

	$tb_resp.= '
						<tr>
							<td align="left"><b>Total</b></td>
							<td></td>
							<td align="right"><b>'.$item["qt_resposta"].'</b></td>
						</tr>
					</tbody>
				</table>
	           ';
	$q_body[] = array($tb_resp);
	
}

$this->load->helper('grid');
$q_grid = new grid();
$q_grid->id_tabela  = "tbQuestao";
$q_grid->view_count = FALSE;
$q_grid->head = $q_head;
$q_grid->body = $q_body;
echo $q_grid->render();
?>
