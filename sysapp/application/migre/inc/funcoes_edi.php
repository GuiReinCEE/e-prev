<?
   /* ------------------------------------------------------------------------------------
      Autor: Júlio Corrêa Pereira
	  Data.: 09/12/2003
   -------------------------------------------------------------------------------------*/
   function caracteres_validos($s, $c_validos) {
      $ret = true;
      for ($i=0; $i < strlen($s); $i++) {
	     if (strpos($c_validos, substr($s, $i, 1) == false)) {
		    $ret = false;
		 }
	  }
	  return $ret;
   }
   
   function eh_data($s, $fmt) {
      $saida = true;
      // Sequencias de caracteres válidas para a máscara de formatação:
	  // DD   - Dia
	  // MM   - Mes
	  // YYYY - Ano
	  // HH   - Hora
	  // MI   - Minutos
	  // SS   - Segundos
	  if (strlen($s) <> strlen($fmt)) {
	     $saida = false;
	  }
	  $dia = strpos($fmt, 'DD');
	  $mes = strpos($fmt, 'MM');
	  $ano = strpos($fmt, 'YYYY');
	  $hor = strpos($fmt, 'HH');
	  $min = strpos($fmt, 'MI');
	  $seg = strpos($fmt, 'SS');
      if ($dia <> false) { $dia = substr($s, $dia, 2); }
      if ($mes <> false) { $mes = substr($s, $mes, 2); }
      if ($ano <> false) { $ano = substr($s, $ano, 4); }
      if ($hor <> false) { $hor = substr($s, $hor, 2); }
      if ($min <> false) { $min = substr($s, $min, 2); }
      if ($seg <> false) { $seg = substr($s, $seg, 2); }
	  // Verifica mes
	  if ($mes <> false) {
	     if (($mes < 1) or ($mes > 12)) {
            $saida = false;
         }
      }
	  // Verifica ano (entre 1850 e 2199 - Valores escolhidos arbitrariamente)
	  if ($ano <> false) {
         if (($ano < 1850) or ($ano > 2199)) { 
            $saida = false; 
         }
      }
	  // Verifica dia (verificando para anos bissextos)
	  if ($ano <> false) {
	     if ($mes <> false) {
		    if ($dia <> false) {
			   if (($mes == 1) or ($mes == 3) or ($mes == 5) or ($mes == 7) or ($mes == 8) or ($mes == 10) or ($mes == 12)) {
                  if (($dia < 1) or ($dia > 31)) {
			         $saida = false;
                  }
               }
               else {
			      if (($mes == 4) or ($mes == 6) or ($mes == 9) or ($mes == 11)) {
                     if (($dia < 1) or ($dia > 30)) {
                        $saida = false;
                     }
				  }
			      else {
                     if (($ano / 4) <> ($ano % 4)) {
                        if (($dia  < 1) or ($dia > 28)) {
                           $saida = false;
                        }
                        else {
                           if (($dia < 1) or ($dia > 29)) {
                              return false;
                           }
                        }
                     }
                  }
               }						 
			}
         }
      }
	  // Verifica Horas
	  if ($hor <> false) {
	     if (($hor < 0) or ($hor > 23)) { 
            $saida = false; 
         }
	  }
	  // Verifica Minutos
	  if ($min <> false) {
	     if (($min < 0) or ($min > 23)) { 
            $saida = false; 
         }
	  }
	  // Verifica Segundos
	  if ($seg <> false) {
	     if (($seg < 0) or ($seg > 23)) { 
            $saida = false; 
         }
	  }
	  return $saida;
   }
   
   function cfe_mascara($s, $mask) {
      /* Possíveis caracteres na máscara: 
	     ? - Qualquer caracter
	     A - Caracteres alfabéticos
	     9 - Caracteres numéricos
	     X - Caracteres numéricos ou alfabéticos
		 Qualquer outro caracter deve ser igual na máscara e na string que está sendo validada.
      */
	  echo "<br>-----------<br>";
      $saida = true;
      if (strlen($s) <> strlen($mask)) {
	     $saida = false;
      }
	  for ($i=0; $i < strlen($mask); $i++) {
	     $c = substr($mask, $i , 1);
         echo "<br>Mascara: ".$c."&nbsp;&nbsp;&nbsp;Caracter: ".substr($s, $i, 1);
         switch($c) {
		    case '9': if (!preg_match('/[^0-9]/', substr($s, $i, 1))) { $saida = false; }; break;
		    case 'A': if (!preg_match('/[^A-Za-z]/', substr($s, $i, 1))) { $saida = false; }; break;
		    case 'X': if (!preg_match('/[^A-Za-z0-9]/', substr($s, $i, 1))) { $saida = false; }; break;
		    default : if (substr($mask, $i, 1) <> '?') {
			             if (substr($mask, $i, 1) <> substr($s, $i, 1)) { $saida = false; }
					  }
		 }
		 if ($saida == false) { break; }
      }
      return $saida;		
   }

/*   
   function carrega_arq($arq, $tipo) {
      $h = fopen($arq, "r");
	  while (! feof($h)) {
	     $linha = fgets($h, 4096);
		 echo "$linha<br>";
	  }
	  fclose($h);
	  return true;
   }
*/

   function carrega_participantes($cnx, $arq, $patroc) { // Baseado no layout da CRM
      $h = fopen($arq, "r");
	  $txtErr = "";
      $tam_linha = 1000;
//      $linha = fgets($h, 4096); // ignora primeira linha
      pg_exec($cnx, 'BEGIN');
      $linha = fgets($h, 4096);
      $cont = 0;
      $erros = 0;
	  $v = array();
      while (! feof($h)) {
	     $errLinha = '';
	     $cont = $cont + 1;
	     if (strlen($linha) <> 357) {
		    $erros = $erros + 1;
		    $errLinha = $cont . ';Tamanho incorreto de linha';
		 }
		 else {
            $v[01] = substr($linha,6,2);    // empresa
            $v[02] = substr($linha,8,10);   // re_patroc
            $v[03] = substr($linha,18,2);   // seq
            $v[04] = substr($linha,20,60);  // nome
            $v[05] = substr($linha,80,8);   // dt nasc
            $v[06] = substr($linha,88,11);  // cpf
            $v[07] = substr($linha,99,1);   // sexo
            $v[08] = substr($linha,100,2);  // est civil
            $v[09] = substr($linha,102,2);  // grau instr
            $v[10] = substr($linha,104,4);  // banco
            $v[11] = substr($linha,108,10); // agencia
            $v[12] = substr($linha,118,20); // c/c
            $v[13] = substr($linha,138,40); // logradouro
            $v[14] = substr($linha,178,25); // bairro
            $v[15] = substr($linha,203,30); // cidade
            $v[16] = substr($linha,233,2);  // uf
            $v[17] = substr($linha,235,5);  // cep
            $v[18] = substr($linha,240,3);  // complemento cep
            $v[19] = substr($linha,243,4);  // ddd
            $v[20] = substr($linha,247,8);  // telefone
            $v[21] = substr($linha,255,9);  // celular
            $v[22] = substr($linha,264,45); // email
            $v[23] = substr($linha,309,2);  // sit funcional
            $v[24] = substr($linha,311,8);  // dt admissao
            $v[25] = substr($linha,319,5);  // cod cargo
            $v[26] = substr($linha,324,2);  // cat eletro
            $v[27] = substr($linha,326,1);  // cat funcional
            $v[28] = substr($linha,327,1);  // filial
            $v[29] = substr($linha,328,8);  // dt obito
            $v[30] = substr($linha,336,8);  // dt demissao
            $v[31] = substr($linha,344,3);  // motivo
            $v[32] = substr($linha,347,4);  // lotacao
			echo "<br>$cont<br>------<br>";
			echo $v[1]."<br>";
			echo $v[2]."<br>";
			echo $v[3]."<br>";
			echo $v[4]."<br>";
			echo $v[5]."<br>";
			echo $v[6]."<br>";
			echo $v[7]."<br>";
			echo $v[8]."<br>";
			echo $v[9]."<br>";
			echo $v[10]."<br>";
		    // Consistência dos campos 
            if (! cfe_mascara($v[1],'99'))          { $errLinha = $errLinha . ";Campo 1: Numero incorreto"; }
            if ($v[1] <> $patroc)                   { $errLinha = $errLinha . ";Campo 1: Patrocinadora incorreta"; }
            if (! cfe_mascara($v[2],'9999999999'))  { $errLinha = $errLinha . ";Campo 2: Numero incorreto"; }
            if (! cfe_mascara($v[3],'99'))          { $errLinha = $errLinha . ";Campo 3: Numero incorreto"; }
            if (! eh_data($v[5], 'DDMMYYYY'))       { $errLinha = $errLinha . ";Campo 5: Data inválida"; }
            if (! cfe_mascara($v[6],'99999999999')) { $errLinha = $errLinha . ";Campo 6: Numero incorreto"; }
            if (($v[7] <> 'M') and ($v[7] <> 'F'))  { $errLinha = $errLinha . ";Campo 7: Sexo inválido"; }
            if (! cfe_mascara($v[8],'99'))          { $errLinha = $errLinha . ";Campo 8: Numero incorreto"; }
            if (! cfe_mascara($v[9],'99'))          { $errLinha = $errLinha . ";Campo 9: Numero incorreto"; }
            if (! cfe_mascara($v[10],'9999'))       { $errLinha = $errLinha . ";Campo 10: Numero incorreto"; }
            if (! cfe_mascara($v[17],'99999'))      { $errLinha = $errLinha . ";Campo 17: Numero incorreto"; }
            if (! cfe_mascara($v[18],'999'))        { $errLinha = $errLinha . ";Campo 18: Numero incorreto"; }
            if (! cfe_mascara($v[19],'9999'))       { $errLinha = $errLinha . ";Campo 19: Numero incorreto"; }
            if (! cfe_mascara($v[20],'99999999'))   { $errLinha = $errLinha . ";Campo 20: Numero incorreto"; }
            if (! cfe_mascara($v[21],'999999999'))  { $errLinha = $errLinha . ";Campo 21: Numero incorreto"; }
            if (! cfe_mascara($v[23],'99'))         { $errLinha = $errLinha . ";Campo 23: Numero incorreto"; }
            if (! eh_data($v[24], 'DDMMYYYY'))      { $errLinha = $errLinha . ";Campo 24: Data inválida"; }
            if (! cfe_mascara($v[25],'99999'))      { $errLinha = $errLinha . ";Campo 25: Numero incorreto"; }
            if ($v[26] <> '02')                     { $errLinha = $errLinha . ";Campo 26: Valor incorreto"; }
            if (! cfe_mascara($v[28],'9') )         { $errLinha = $errLinha . ";Campo 28: Numero incorreto"; }
            if (! eh_data($v[29], 'DDMMYYYY'))      { $errLinha = $errLinha . ";Campo 29: Data inválida"; }
            if (! eh_data($v[30], 'DDMMYYYY'))      { $errLinha = $errLinha . ";Campo 30: Data inválida"; }
            if (! cfe_mascara($v[31],'999'))        { $errLinha = $errLinha . ";Campo 31: Numero incorreto"; }
            if (! cfe_mascara($v[32],'9999'))       { $errLinha = $errLinha . ";Campo 32: Numero incorreto"; }
            if ($errLinha <> "") {
			   $errLinha = $cont . $errLinha;
               $erros = $erros + 1;
            }
            else {
               $sql =        " INSERT INTO edi.participantes (";
               $sql = $sql . "        emp               ";
               $sql = $sql . "        re_patroc         ";
               $sql = $sql . "        seq               ";
               $sql = $sql . "        nome              ";
               $sql = $sql . "        dt_nasc           ";
               $sql = $sql . "        cpf               ";
               $sql = $sql . "        sexo              "; 
               $sql = $sql . "        est_civil         ";
               $sql = $sql . "        grau_inst         ";
               $sql = $sql . "        banco             ";
               $sql = $sql . "        agencia           ";
               $sql = $sql . "        conta             ";
               $sql = $sql . "        logradouro        ";
               $sql = $sql . "        bairro            ";
               $sql = $sql . "        cidade            ";
               $sql = $sql . "        uf                ";
               $sql = $sql . "        cep               ";
               $sql = $sql . "        complemento_cep   ";
               $sql = $sql . "        ddd               ";
               $sql = $sql . "        telefone          ";
               $sql = $sql . "        celular           ";
               $sql = $sql . "        email             ";
               $sql = $sql . "        sit_funcional     ";
               $sql = $sql . "        dt_admissao       ";
               $sql = $sql . "        cod_cargo         ";
               $sql = $sql . "        cat_eletro        ";
               $sql = $sql . "        cat_funcional     ";
               $sql = $sql . "        filial            ";
               $sql = $sql . "        dt_obito          ";
               $sql = $sql . "        dt_demissao       ";
               $sql = $sql . "        motivo            ";
               $sql = $sql . "        lotacao           ";	
               $sql = $sql . " VALUES (";
			   $sql = $sql . "        " . $v[01] . ",";
			   $sql = $sql . "        " . $v[02] . ",";
			   $sql = $sql . "        " . $v[03] . ",";
			   $sql = $sql . "        '" . $v[04] . "',";
			   $sql = $sql . "        to_date('" . $v[05] . "','DDMMYYYY'),";
			   $sql = $sql . "        " . $v[06] . ",";
			   $sql = $sql . "        '" . $v[07] . "',";
			   $sql = $sql . "        " . $v[08] . ",";
			   $sql = $sql . "        " . $v[09] . ",";
			   $sql = $sql . "        " . $v[10] . ",";
			   $sql = $sql . "        '" . $v[11] . "',";
			   $sql = $sql . "        '" . $v[12] . "',";
			   $sql = $sql . "        '" . $v[13] . "',";
			   $sql = $sql . "        '" . $v[14] . "',";
			   $sql = $sql . "        '" . $v[15] . "',";
			   $sql = $sql . "        '" . $v[16] . "',";
			   $sql = $sql . "        " . $v[17] . ",";
			   $sql = $sql . "        " . $v[18] . ",";
			   $sql = $sql . "        " . $v[19] . ",";
			   $sql = $sql . "        " . $v[20] . ",";
			   $sql = $sql . "        " . $v[21] . ",";
			   $sql = $sql . "        '" . $v[22] . "',";
			   $sql = $sql . "        " . $v[23] . ",";
			   $sql = $sql . "        to_date('" . $v[24] . "','DDMMYYYY'),";
			   $sql = $sql . "        " . $v[25] . ",";
			   $sql = $sql . "        " . $v[26] . ",";
			   $sql = $sql . "        '" . $v[27] . "',";
			   $sql = $sql . "        " . $v[28] . ",";
			   $sql = $sql . "        to_date('" . $v[29] . "','DDMMYYYY'),";
			   $sql = $sql . "        to_date('" . $v[30] . "','DDMMYYYY'),";
			   $sql = $sql . "        " . $v[31] . ",";
			   $sql = $sql . "        " . $v[32];
			   $sql = $sql . ")";
			   $rs = pg_exec($db, $sql);
            }
         }
		 if ($erros == 1) {
		    $h_erros = fopen($arq.".logfceee", "w");
		 }
		 if ($errLinha <> "") {
		    fputs($h_erros, $errLinha."\r\n");
		 }
         $linha = fgets($h, 4096);
      }
      if ($erros > 0) {
         fclose($h_erros);
         pg_exec($cnx, 'rollback');
      }
	  else {
	     pg_exec($cnx, 'commit');
	  }
      fclose($h);
      return $cont;
   };
   
   function conta_linhas($arq) {
      $cont = 0;
      if (! $h = fopen($arq, 'r')) {
	     return false;
	  }
	  else {
	     while ($linha = fgets($h, 4096)) {
		    $cont = $cont + 1;
		 }
		 fclose($h);
         return $cont;
	  }
   }
?>