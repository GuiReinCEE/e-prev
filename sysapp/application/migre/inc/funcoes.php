<?
   include_once('funcoes_gerais.php');

   function dateBrToIso($dt) {
      // Recebe uma string no formato DD/MM/YYYY e retorna no formato YYYY-MM-DD, ideal para gravar no PostgreSQL
      $dia = substr($dt,0,2);
      $mes = substr($dt,3,2);
      $ano = substr($dt,6,4);
      return "$ano-$mes-$dia";
   }


   function envia_email_nova_os($num_os, $origem, $destino) {
	   $sql =        " select os.numero, ";
		$sql = $sql . "        os.sistema, ";
		$sql = $sql . "        ate.nome as atendente, ";
		$sql = $sql . "        sol.nome as solic, ";
		$sql = $sql . "        li.descricao as nome_sist, ";
		$sql = $sql . "        os.descricao, ";
		$sql = $sql . "        os.problema ";
      $sql = $sql . " from   os_software os, ";
		$sql = $sql . "        usuarios_controledi sol, ";
		$sql = $sql . "        usuarios_controledi ate, ";
		$sql = $sql . "        listas li ";
      $sql = $sql . " where  ate.usuario = os.atendente ";
      $sql = $sql . "   and  sol.usuario = os.solicitante ";
      $sql = $sql . "   and  (li.codigo = os.sistema ";
      $sql = $sql . "         and  li.categoria='SIST') ";
      $sql = $sql . " and numero=$num_os";
	   $assunto = "OS de manutenção de sistema número $num_os";
		// Corpo da mensagem
      $msg = "Prezada(o) " . $reg['nome_atend'] . ":";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "Foi enviada uma solicitação de manutenção de sistemas.";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "---------------------------------------------------------";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "Solicitante: " . $reg['nome_solic'];
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "Número da Ordem de Serviço: " . $reg['numero'];
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "Sistema: " . $reg['nome_sist'];
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "---------------------------------------------------------";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "Descrição da manutenção: ";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . $reg['descricao'];
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "---------------------------------------------------------";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "Justificativa: ";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . $reg['problema'];
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "---------------------------------------------------------";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "Esta mensagem foi enviada pelo Controle Divisão de Informática.";
		$a = envia_email($origem, $destino, $origem, '', $assunto, $mensagem);
		return $a;
	}
	
   function envia_email_os_concluida($num_os, $origem, $destino) {
	   $sql =        " select os.numero, ";
		$sql = $sql . "        os.sistema, ";
		$sql = $sql . "        ate.nome as atendente, ";
		$sql = $sql . "        sol.nome as solic, ";
		$sql = $sql . "        li.descricao as nome_sist, ";
		$sql = $sql . "        os.descricao, ";
		$sql = $sql . "        os.problema, ";
		$sql = $sql . "        os.solucao ";
      $sql = $sql . " from   os_software os, ";
		$sql = $sql . "        usuarios_controledi sol, ";
		$sql = $sql . "        usuarios_controledi ate, ";
		$sql = $sql . "        listas li ";
      $sql = $sql . " where  ate.usuario = os.atendente ";
      $sql = $sql . "   and  sol.usuario = os.solicitante ";
      $sql = $sql . "   and  (li.codigo = os.sistema ";
      $sql = $sql . "         and  li.categoria='SIST') ";
      $sql = $sql . " and numero=$num_os";
	   $assunto = "OS de manutenção de sistema número $num_os";
		// Corpo da mensagem
      $msg = "Prezada(o) " . $reg['nome_solic'] . ":";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "Sua solicitação de manutenção de sistemas foi atendida.";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "---------------------------------------------------------";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "Solicitante: " . $reg['nome_solic'];;
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "Número da Ordem de Serviço: " . $reg['numero'];
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "Sistema: " . $reg['nome_sist'];;
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "---------------------------------------------------------";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "Descrição da manutenção: ";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . $reg['descricao'];
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "Esta Ordem de Serviço foi considerada como CONCLUÍDA pelo analista responsável. ";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "---------------------------------------------------------";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "Para que o processo seja devidamente encerrado você deve preencher o ";
      $msg = $msg . "documento anexo a esta mensagem e, através do recurso 'Encaminhar', enviá-lo ";
      $msg = $msg . "para o Analista Responsável. ";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "Somente após este processo será providenciado o 'De acordo' ";
      $msg = $msg . "concluindo com esta OS sendo declarada 'LIBERADA'.";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "---------------------------------------------------------";
      $msg = $msg . chr(13) . chr(10);
      $msg = $msg . "Esta mensagem foi enviada pelo Controle Divisão de Informática.";
    
      $assunto = "OS de manutenção de sistemas número " . $reg['numero'] . " concluída";
		
		$a = envia_email($origem, $destino, $origem, '', $assunto, $mensagem);
		return $a;
	}
function nvl($variavel, $valor)
{
   if (is_null($variavel) or (!isset($variavel)) or ($variavel==''))
   {
      return $valor;
   }
   else
   {
      return $variavel;
   }
}

function pagina_disponivel($nome_pag)
{
   if (strpos($PAGDISP, $nome_pag) === false)
   {
      return false;
   }
   else
   {
      $sql = "INSERT INTO LOG_ACESSOS_USUARIO (SID, HORA, PAGINA) VALUES ($SID, NOW(), '$nome_pag')";
	  pg_exec($db, $sql);
	  return true;
   }
}

	function verificaCPF($cpf) 
	{
	    $cpf[11] = $cpf[10];
	    $cpf[10] = $cpf[9];
	    $cpf[9]  = '-';				
//		echo $cpf;
		if (strlen($cpf) <> 12) return 0;
		$soma1 = ($cpf[0] * 10) +
				 ($cpf[1] * 9)  +
				 ($cpf[2] * 8)  +
				 ($cpf[3] * 7)  +
				 ($cpf[4] * 6)  +
				 ($cpf[5] * 5)  +
				 ($cpf[6] * 4)  +
				 ($cpf[7] * 3)  +
				 ($cpf[8] * 2);
        
		$resto = $soma1 % 11;
		
		$digito1 = $resto < 2 ? 0 : 11 - $resto;

		$soma2 = ($cpf[0]  * 11) +
		  		 ($cpf[1]  * 10) +
				 ($cpf[2]  * 9)  +
				 ($cpf[3]  * 8)  +
				 ($cpf[4]  * 7)  +
				 ($cpf[5]  * 6)  +
				 ($cpf[6]  * 5)  +
				 ($cpf[7]  * 4)  +
				 ($cpf[8]  * 3)  +
				 ($cpf[10] * 2);
		$resto = $soma2 % 11;
		$digito2 = $resto < 2 ? 0 : 11 - $resto;

		return (($cpf[10] == $digito1) && ($cpf[11] == $digito2));
	}
	
   function vlrBR($vUS) {
      $ret = str_replace(',', '', $vUS);
	  $ret = str_replace('.', ',', $ret);
      return $ret;
   }
	
   function vlrUS($vBR) {
      $ret = str_replace('.', '', $vBR);
	  $ret = str_replace(',', '.', $ret);
      return $ret;
   }
   
   function fmtVlrBrUs($v, $s) {
      $ret = '';
	  $primeiro = true;
      for ($i=strlen($v); $i>=0; $i--) {
	     if (((substr($v,$i,1) == ',') or (substr($v,$i,1) == '.')) and ($primeiro == true)) {
		    $primeiro = false;
			if ($s == 'BR') {
               $ret = "," . $ret;
			}
			else {
               $ret = "." . $ret;
			}
		 }
		 else {
		    if (preg_match("/([0-9])/", substr($v,$i,1))) {
			   $ret = substr($v,$i,1) . $ret;
			}
		 }
	  }
	  return $ret;
   }
   
   if ($vlr <> '') {
      echo '<br>'.$vlr;
      echo '<br>BR: '.vlrBR($vlr);
      echo '<br>US: '.vlrUS($vlr);
      echo '<br>Fmt BR: '.fmtVlrBrUs($vlr, 'BR');
      echo '<br>Fmt US: '.fmtVlrBrUs($vlr, 'US');
   }
	
   function prepara_conteudo_html($conteudo) {
      // Altera o contrúdo do texto, retirando os <Enter's> e os apóstrofes, 
	  // evitando problemas com o editor 
      return str_replace("'", "\'", str_replace(chr(10),'',str_replace(chr(13),'',$conteudo)));
   }
   
	function carrega_bloqueto($cnx, $arq) {
      $h = fopen($arq, "r");
      $tam_linha = 1000;
      $linha = fgets($h, 4096); // ignora primeira linha
      $linha = fgets($h, 4096);
      $cont = 0;
      $erros = 0;
      while (! feof($h)) {
 	     if (substr($linha, 0, 3) == '1  ') {
            $sql =        " INSERT INTO bloqueto_txt (";
            $sql = $sql . "        cod_cedente, ";
            $sql = $sql . "        valor, ";
            $sql = $sql . "        nosso_numero, ";
            $sql = $sql . "        dia_vcto, ";
            $sql = $sql . "        mes_vcto, ";
            $sql = $sql . "        ano_vcto, ";
            $sql = $sql . "        nome, ";
            $sql = $sql . "        endereco, ";
            $sql = $sql . "        cidade, ";
            $sql = $sql . "        uf, ";
            $sql = $sql . "        cep, ";
//            $sql = $sql . "        obs, ";
            $sql = $sql . "        seu_numero, ";
            $sql = $sql . "        cd_empresa, ";
            $sql = $sql . "        cd_registro_empregado, ";
            $sql = $sql . "        seq_dependencia, ";
            $sql = $sql . "        dt_emissao, ";
            $sql = $sql . "        dt_vencimento) ";
            $sql = $sql . " VALUES (";
            $sql = $sql . "        '". substr($linha,  17, 12) . "',"; //cedente
            $sql = $sql . "        '". substr($linha, 126,11) . "." . substr($linha, 137, 2) . "',"; // valor
            $sql = $sql . "        '". substr($linha,  62, 10) . "',"; // nosso numero
            $sql = $sql . "        '". substr($linha, 120,  2) . "',"; // dia vcto
            $sql = $sql . "        '". substr($linha, 122,  2) . "',"; // mes vcto
            $sql = $sql . "        '". substr($linha, 124,  2) . "',"; // ano vcto
            $sql = $sql . "        '". str_replace("'","''", substr($linha, 234, 35)) . "',"; // nome
            $sql = $sql . "        '". str_replace("'","''", substr($linha, 274, 35)) . "',"; // endereco
            $sql = $sql . "        '". str_replace("'","''", substr($linha, 334, 15)) . "',"; // cidade 
            $sql = $sql . "        '". substr($linha, 349,  2) . "',"; // uf
            $sql = $sql . "        '". substr($linha, 326,  8) . "',"; // cep
//            $sql = $sql . "        '". substr($linha, 17, 12) . "',"; // obs
            $sql = $sql . "        '". substr($linha, 110, 10) . "',"; // seu numero
			
            $sql = $sql . "        '". substr($linha,  62,  1) . "',"; // cd_empresa
            $sql = $sql . "        '". substr($linha,  63,  6) . "',"; // cd_registro_empregado
            $sql = $sql . "        '". substr($linha,  69,  1) . "',"; // seq_dependencia
/*
            $sql = $sql . "        '". substr($linha,  62,  1) . "',"; // cd_empresa
            $sql = $sql . "        '". substr($linha,  63,  6) . "',"; // cd_registro_empregado
            $sql = $sql . "        '". substr($linha,  70,  1) . "',"; // seq_dependencia
*/
            $sql = $sql . "        '". '20'.substr($linha, 154, 2)."-".substr($linha, 152, 2)."-".substr($linha, 150, 2) . "',"; // dt_emissao
            $sql = $sql . "        '". '20'.substr($linha, 124, 2)."-".substr($linha, 122, 2)."-".substr($linha, 120, 2) . "'"; // dt_vencimento
            $sql = $sql . " )";
			//echo $sql."<br><br>"; exit;
            if (pg_exec($cnx, $sql)) {
               $cont = $cont + 1;
            }
            else {
               $erros = $erros + 1;
               if ($erros == 1) {
                  echo "<table>";
               }
               echo "<tr><td>".$linha[0]."</td><td>".$linha[1]."</td></tr>";
            }
 		 }
         $linha = fgets($h, 4096);
      }
      if ($erros > 0) {
         echo "</table>";
      }
      echo "<table>";
      echo "  <tr><td>Registros importados</td><td>$cont</td></tr>";
      echo "  <tr><td>Erros encontrados</td><td>$erros</td></tr>";
      echo "</table>";
      fclose($h);
      return $cont;
   }
?>