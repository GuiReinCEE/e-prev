<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	$emp = substr($r, 0, 2);
	$re = substr($r, 2, 6);
	$seq = substr($r, 8, 2);
	$sql = "select ce.*, p.nome from eleicoes.cadastros_eleicoes ce, participantes p where ce.cd_empresa = $emp";
	$sql = $sql . " and ce.cd_registro_empregado = $re ";
	$sql = $sql . " and ce.seq_dependencia = $seq ";		
	$sql = $sql . " and ce.cd_empresa = p.cd_empresa ";
	$sql = $sql . " and ce.cd_registro_empregado = p.cd_registro_empregado ";
	$sql = $sql . " and ce.seq_dependencia = p.seq_dependencia ";
	$rs = pg_exec($db, $sql);
	if (pg_numrows($rs) > 0) {
		$reg=pg_fetch_array($rs);
		$nome = $reg['nome'];
		if ($reg['dt_recebimento_etiqueta'] != '') {
			?>
			<script language="JavaScript" type="text/javascript">
        	    opener.document.getElementById("msg").innerHTML = "<table bgcolor='#FF0000' height='200' width='85%'><tr><td colspan='2' align='center'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>Participante já votou!</strong></font></td></tr><tr><td align='right'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>Empresa:</strong></font></td><td align='left'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong><?echo $emp;?></strong></font></td></tr><tr><td align='right'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>RE d:</strong></font></td><td align='left'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong><?echo $re;?></strong></font></td></tr><tr><td align='right'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>Seq:</strong></font></td><td align='left'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong><?echo $seq;?></strong></font></td></tr></strong></font></table>";
				window.close();
			</script>
			<?
		}
		else {
		$sql = "update eleicoes.cadastros_eleicoes set dt_recebimento_etiqueta = current_timestamp where cd_empresa = $emp ";			
		$sql = $sql . " and cd_registro_empregado = $re ";
		$sql = $sql . " and seq_dependencia = $seq ";		
		$rs = pg_exec($db, $sql);		
		?>
		<script language="JavaScript" type="text/javascript">
//            opener.document.getElementById("msg").innerHTML = "<font color='#0046ad' size='3' face='Verdana, Arial, Helvetica, sans-serif'><strong>Voto confirmado! <br> Empresa: <?echo $emp;?> Re: <?echo $re;?>  Seq: <?echo $seq;?></strong></font>";
        	    opener.document.getElementById("msg").innerHTML = "<table bgcolor='#F4F4F4' height='255' width='85%'><tr><td colspan='2' align='center'><font color='#0046ad' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>VOTO CONFIRMADO!</strong></font></td></tr><tr><td align='right'><font color='#0046ad' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>Empresa:</strong></font></td><td align='left'><font color='#0046ad' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong><?echo $emp;?></strong></font></td></tr><tr><td align='right'><font color='#0046ad' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>RE d:</strong></font></td><td align='left'><font color='#0046ad' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong><?echo $re;?></strong></font></td></tr><tr><td align='right'><font color='#0046ad' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>Seq:</strong></font></td><td align='left'><font color='#0046ad' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong><?echo $seq;?></strong></font></td></tr><tr><td colspan='2' align='center'><font color='#0046ad' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong><?echo $nome;?></strong></font></td></tr></strong></font></table>";
			window.close();
		</script>
		<?
		}
	}
	else {
			?>
			<script language="JavaScript" type="text/javascript">
        	    opener.document.getElementById("msg").innerHTML = "<table bgcolor='#000000' height='200' width='85%'><tr><td colspan='2' align='center'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>Participante não cadastrado ou número de controle inválido!</strong></font></td></tr><tr><td align='right'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>Empresa:</strong></font></td><td align='left'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong><?echo $emp;?></strong></font></td></tr><tr><td align='right'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>RE d:</strong></font></td><td align='left'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong><?echo $re;?></strong></font></td></tr><tr><td align='right'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>Seq:</strong></font></td><td align='left'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong><?echo $seq;?></strong></font></td></tr></strong></font></table>";
				window.close();
			</script>
			<?
	}
	pg_close($db);
?>