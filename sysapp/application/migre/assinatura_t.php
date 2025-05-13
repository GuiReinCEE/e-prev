<html><body bgcolor="#FFFFFF">
<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	if ($E == 'S') {
		$emp = substr($r, 0, 2);
		$re = substr($r, 2, 6);
		$seq = substr($r, 8, 2);
	}
	else {
		$emp = substr($r, 0, 1);
		$re = substr($r, 1, 6);
		$seq = substr($r, 7, 2);
	}
// ------------------------------------------------------- Busca informações do Cadastro Eleitoral
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
		if ($reg['dt_recebimento_etiqueta'] != '') { // Inverter este if ao começar a apuração
// ------------------------------------------------------- Voto deste participante ainda não foi computado:
			?>
<script language="JavaScript" type="text/javascript">
        	    opener.document.getElementById("msg").innerHTML = "<table bgcolor='#FF0000' height='200' width='85%'><tr><td colspan='2' align='center'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>Participante já votou!</strong></font></td></tr><tr><td align='right'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>Empresa:</strong></font></td><td align='left'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong><?echo $emp;?></strong></font></td></tr><tr><td align='right'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>RE d:</strong></font></td><td align='left'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong><?echo $re;?></strong></font></td></tr><tr><td align='right'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>Seq:</strong></font></td><td align='left'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong><?echo $seq;?></strong></font></td></tr></strong></font></table>";
				window.close();
			</script>
			<?
		}
		else {
// ------------------------------------------------------- Através do Re buscar o tipo e endereço dos documentos na tabela documentos:
			if ($h == 'S') {
				$sql = "select d.caminho_imagem, x, y, altura, largura, pagina, td.nome_documento, p.nome ";
				$sql = $sql . " from documentos d, participantes p, projetos.documentos_coordenadas c, tipo_documentos td "; 
				$sql = $sql . " where d.cd_empresa = $emp ";			
				$sql = $sql . " and d.cd_registro_empregado = $re ";
				$sql = $sql . " and d.seq_dependencia = $seq ";		
				$sql = $sql . " and d.cd_empresa = p.cd_empresa ";			
				$sql = $sql . " and d.cd_registro_empregado = p.cd_registro_empregado ";
				$sql = $sql . " and d.seq_dependencia = p.seq_dependencia ";
				$sql = $sql . " and d.cd_tipo_doc = c.cd_tipo_doc "; 
				$sql = $sql . " and d.cd_tipo_doc = td.cd_tipo_doc "; 
//				$sql = $sql . " and d.caminho_imagem is not null "; 
// --------------------------------------------------------			
				$rs = pg_exec($db, $sql);
				while ($reg = pg_fetch_array($rs)) {			
//					if ($reg['caminho_imagem'] != '') {
//						$reg=pg_fetch_array($rs);
						$figura = str_replace('/','_',$reg['caminho_imagem']);				
						$figura = str_replace('__srvimagem','10.63.255.50/JPGs/FD',$figura);
						$figura = str_replace('__SRVIMAGEM','10.63.255.50/JPGs/FD',$figura);
						$figura = str_replace('.tif','_' . $reg['pagina'] . '.jpg',$figura);
						$figura = str_replace('.TIF','_' . $reg['pagina'] . '.jpg',$figura);
						$x = $reg['x'];
						$y = $reg['y'];
						$altura = $reg['altura'];
						$largura = $reg['largura'];
						$pagina = $reg['pagina'];
						$tabela = $tabela . "<tr>";
						$tabela = $tabela . "<td align='center'><a href='redimensiona_imagens.php?img=http://".$figura."' target='_blank'><img src='img/btn_ver_imagem.jpg' border='0'></a><br><font face='verdana' size=2>JPG</font></td>";
						$tabela = $tabela . "<td align='center'><a href='file:".$reg['caminho_imagem']."' target='_blank'><img src='img/btn_ver_imagem.jpg' border='0'></a><br><font face='verdana' size=2>TIF</font></td>";
						$tabela = $tabela . "<td align='center'><img src='recorta_assinatura.php?x=".$x."&y=".$y."&w=".$largura."&h=".$altura."&src=http://".$figura."'></td>";
						$tabela = $tabela . "</tr>";
						$tabela = $tabela . "<tr><td colspan='3' align='center'><font face='verdana' size=2>Participante: ".$reg['nome']." => REd: ".$re."/".$seq.", Empresa: ".$emp."</font></td></tr>";
						$tabela = $tabela . "<tr><td colspan='3' align='center'><font face='verdana' size=2>Documento: ".$reg['nome_documento']."</font><hr></td></tr>";
//						$tabela = $tabela . "<tr><td colspan='3' align='center'>".$h."</td></tr>";
	//				}
				}
			}
			else {
				$sql = "select caminho_imagem_doc_novo, caminho_imagem_doc_antigo, td1.nome_documento as doc_novo, td2.nome_documento as doc_antigo, p.nome, ";
				$sql = $sql . " c1.x as x_novo, c1.y as y_novo, c1.altura as altura_novo, c1.largura as largura_novo, c1.pagina as pagina_novo, ";
				$sql = $sql . " c2.x as x_antigo, c2.y as y_antigo, c2.altura as altura_antigo, c2.largura as largura_antigo, c2.pagina as pagina_antigo, ";
				$sql = $sql . " cd_tipo_doc_novo, cd_tipo_doc_antigo ";
				$sql = $sql . " from documentos d, participantes p, projetos.documentos_coordenadas c1, projetos.documentos_coordenadas c2, assinaturas_participantes a, tipo_documentos td1, tipo_documentos td2  ";
				$sql = $sql . " where a.cd_empresa = $emp ";			
				$sql = $sql . " and a.cd_registro_empregado = $re ";
				$sql = $sql . " and a.seq_dependencia = $seq ";		
				$sql = $sql . " and a.cd_empresa = d.cd_empresa ";			
				$sql = $sql . " and a.cd_registro_empregado = d.cd_registro_empregado ";
				$sql = $sql . " and a.seq_dependencia = d.seq_dependencia ";
				$sql = $sql . " and ((cd_tipo_doc_novo = c1.cd_tipo_doc) or (cd_tipo_doc_novo = 0))"; 				
				$sql = $sql . " and ((cd_tipo_doc_antigo = c2.cd_tipo_doc) or (cd_tipo_doc_antigo = 0))"; 
				$sql = $sql . " and d.cd_empresa = p.cd_empresa ";			
				$sql = $sql . " and d.cd_registro_empregado = p.cd_registro_empregado ";
				$sql = $sql . " and d.seq_dependencia = p.seq_dependencia ";
				$sql = $sql . " and ((cd_tipo_doc_novo = td1.cd_tipo_doc) or (cd_tipo_doc_novo = 0)) "; 
				$sql = $sql . " and ((cd_tipo_doc_antigo = td2.cd_tipo_doc ) or (cd_tipo_doc_antigo = 0))"; 

//			$sql = $sql . " and d.cd_tipo_doc in (select cd_tipo_doc from projetos.documentos_coordenadas) "; 
				$rs = pg_exec($db, $sql);
				if ($reg = pg_fetch_array($rs)) {			
					$reg=pg_fetch_array($rs);
					$figura = str_replace('/','_',$reg['caminho_imagem_doc_novo']);				
					$figura = str_replace('__srvimagem','10.63.255.50/JPGs/FD',$figura);
					$figura = str_replace('__SRVIMAGEM','10.63.255.50/JPGs/FD',$figura);
					$figura = str_replace('.tif','_' . $reg['pagina_novo'] . '.jpg',$figura);
					$figura = str_replace('.TIF','_' . $reg['pagina_novo'] . '.jpg',$figura);
					$x = $reg['x_novo'];
					$y = $reg['y_novo'];
					$altura = $reg['altura_novo'];
					$largura = $reg['largura_novo'];
					$pagina = $reg['pagina_novo'];
					if ($figura != '') {
						$tabela = $tabela . "<td align='center'><a href='redimensiona_imagens.php?img=http://".$figura."' target='_blank'><img src='img/btn_ver_imagem.jpg' border='0'></a><br><font face='verdana' size=2>Doc</font></td>";
						$tabela = $tabela . "<td align='center'></td>";
						$tabela = $tabela . "<td align='center'><img src='recorta_assinatura.php?x=".$x."&y=".$y."&w=".$largura."&h=".$altura."&src=http://".$figura."'></td>";
						$tabela = $tabela . "</tr>";
						$tabela = $tabela . "<tr><td colspan='3' align='center'><font face='verdana' size=2>Participante: ".$reg['nome']." => REd: ".$re."/".$seq.", Empresa: ".$emp."</font></td></tr>";
						$tabela = $tabela . "<tr><td colspan='3' align='center'><font face='verdana' size=2>Documento: ".$reg['doc_novo']."</font><br><img src='img/img_divisoria1.gif' width='90%' height='1'></td></tr>";
//					$tabela = $tabela . "<tr><td colspan='3' align='center'>".$h."</td></tr>";
					}					
					$figura = str_replace('/','_',$reg['caminho_imagem_doc_antigo']);				
					$figura = str_replace('__srvimagem','10.63.255.50/JPGs/FD',$figura);
					$figura = str_replace('__SRVIMAGEM','10.63.255.50/JPGs/FD',$figura);
					$figura = str_replace('.tif','_' . $reg['pagina_antigo'] . '.jpg',$figura);
					$figura = str_replace('.TIF','_' . $reg['pagina_antigo'] . '.jpg',$figura);
					$x = $reg['x_antigo'];
					$y = $reg['y_antigo'];
					$altura = $reg['altura_antigo'];
					$largura = $reg['largura_antigo'];
					$pagina = $reg['pagina_antigo'];
					if ($figura != '') {
						$tabela = $tabela . "<td align='center'><a href='redimensiona_imagens.php?img=http://".$figura."' target='_blank'><img src='img/btn_ver_imagem.jpg' border='0'></a><br><font face='verdana' size=2>Doc</font></td>";
						$tabela = $tabela . "<td align='center'></td>";
						$tabela = $tabela . "<td align='center'><img src='recorta_assinatura.php?x=".$x."&y=".$y."&w=".$largura."&h=".$altura."&src=http://".$figura."'></td>";
						$tabela = $tabela . "</tr>";
						$tabela = $tabela . "<tr><td colspan='3' align='center'><font face='verdana' size=2>Participante: ".$reg['nome']." => REd: ".$re."/".$seq.", Empresa: ".$emp."</font></td></tr>";
						$tabela = $tabela . "<tr><td colspan='3' align='center'><font face='verdana' size=2>Documento: ".$reg['doc_antigo']."</font><br><img src='img/img_divisoria1.gif' width='90%' height='1'></td></tr>";
					}
//					$tabela = $tabela . "<tr><td colspan='3' align='center'>".$h."</td></tr>";
				}
			}
// ------------------------------------------------------- Código de recorte das assinaturas:
			?><strong><font color="#0046ad" size="3" face="Verdana, Arial, Helvetica, sans-serif">Buscando imagem no Servidor... ... ... </font></strong>
			<script language="JavaScript" type="text/javascript">
					opener.document.getElementById("msg").innerHTML = "<table border='0' bgcolor='#AACD00' width='100%' height='100%'><?echo $tabela;?></table>";
				window.close();
			</script>
			<?
		}
	}
	else {
// ------------------------------------------------------- Se participante não for localizado:
			?>
			<script language="JavaScript" type="text/javascript">
        	    opener.document.getElementById("msg").innerHTML = "<table bgcolor='#000000' height='200' width='85%'><tr><td colspan='2' align='center'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>Participante não localizado no cadastro eleitoral!</strong></font></td></tr><tr><td align='right'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>Empresa:</strong></font></td><td align='left'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong><?echo $emp;?></strong></font></td></tr><tr><td align='right'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>RE d:</strong></font></td><td align='left'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong><?echo $re;?></strong></font></td></tr><tr><td align='right'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong>Seq:</strong></font></td><td align='left'><font color='#FFFF00' size='5' face='Verdana, Arial, Helvetica, sans-serif'><strong><?echo $seq;?></strong></font></td></tr></strong></font></table>";
				window.close();
			</script>
<?
	}
	pg_close($db);
?>
</html>