<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	if ($email != '') {
		$sql = "insert into expansao.mailing_email (cd_mailing, email, dt_inclusao)
				values (". $cd_mailing. ", '". $email."', current_timestamp) ";
		$rs = pg_query($sql);
	}
// --------------------------------------------------------------- Busca os emails desta pessoa
	$sql = $sql = "select cd_mailing, cd_email, email, tipo, 
				to_char(dt_inclusao, 'dd/mm/yyyy') as dt_inclusao
				from expansao.mailing_email where dt_exclusao is null and cd_mailing = ".$cd_mailing." order by dt_inclusao desc ";
	$rs = pg_query($sql);
	echo "<table width='95%' align='center' border='0' cellspacing='1' cellpadding='0' bgcolor='#009933'>
			<tr>
				<td colspan='3' height='33'>
				<table width='100%' border='0' cellspacing='0' cellpadding='3'> 
					<tr>
					<td class='cabecalho'>Emails cadastrados: </td>
					<td align='right'></td>
					</tr> 
				</table>
				</td>
			</tr>
			<tr bgcolor='#D5F4FF'>
				<td class='texto1'>Email:</td>
				<td class='links2'><font size='2' face='Arial, Helvetica, sans-serif'><input name='email' type='text' id='email' size='55'></font>
				</td>
				<td class='links2' align='right'>
				<font size='2' face='Arial, Helvetica, sans-serif'>
				<img src='img/btn_inclui_email.jpg' onClick='fnc_inclui_email(". $cd_mailing. ",'#F5F5F5','#C5C5C5');'>
				</font>
				</td>
			</tr>";
	$ap = '"';
	while ($reg = pg_fetch_object($rs)) 
	{
	 	echo "<tr class='texto1' bgcolor='#D5F4FF'> 
				<td class='texto1'>Email</td>
				<td class='links2' align='center'>".$reg->email."</td>
				<td align='right'><a href='mailto:".$reg->email."'><img src='img/btn_linha_envia_email.jpg' border='0'></a><a href='exclui_email_mailing.php?cd_mailing=".$cd_mailing."&cd_email=".$reg->cd_email."'><img src='img/btn_linha_lixeira.jpg' border='0'></a></td>
			</tr>";
	}
	echo "</table>";
?>