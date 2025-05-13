<?
   function envia_email($from, $to, $bc, $bcc, $subject, $msg)
	 { 
	    $prioridade = ( is_null($prioridade) ? 1 : $prioridade);
      // -- Constru��o do header do email --
      $headers .= "MIME-Version: 1.0\r\n";
      $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
      $headers .= "From: $from\r\n";
      $headers .= "To: $to\r\n";
      $headers .= (is_null($bc) ? '' : "Bc: $bc\r\n");
      $headers .= (is_null($bcc) ? '' : "Bcc: $bcc\r\n");
      $headers .= "X-Priority: $prioridade\r\n";
      $headers .= "X-MSMail-Priority: High\r\n";
			// -- x --
      return mail($toEmail, $subject, $msg, $headers);
   }
   
   /*
      Fun��o   : pad
      Descri��o: Preenche a string $txt fornecida com caracteres $c at� atingir o tamanho $tam.
                 $pos informa de os espa�os devem ser colocados � esquerda ("L") ou � direita ("D")
      Data     : 08/06/2005
      Autor    : J�lio C. Pereira
      
   */
   function pad($txt, $tam, $c, $pos) {
      $p = "";
      $t = strlen($txt);
      while ($t < $tam) { 
         $p.=$c;
         $t+=1;
      }
      return ($pos == 'R' ? $txt.$p : $p.$txt);
   }
   
   /*
      Fun��o   : lpad
      Descri��o: Preenche a string $txt fornecida com caracteres $c (� esquerda) at� atingir o tamanho $tam.
      Data     : 08/06/2005
      Autor    : J�lio C. Pereira
      
   */
   function lpad($txt, $tam, $c) {
      return pad($txt, $tam, $c, "L");
   }
   
   /*
      Fun��o   : rpad
      Descri��o: Preenche a string $txt fornecida com caracteres $c (� direita) at� atingir o tamanho $tam.
      Data     : 08/06/2005
      Autor    : J�lio C. Pereira
      
   */
   function rpad($txt, $tam, $c) {
      return pad($txt, $tam, $c, "R");
   }

   function getExtensionFile($arq) {
      $url_parts = parse_url($arq);
      $ext = preg_replace("/^.+\\.([^.]+)$", "\\1/", $url_parts['path']);
	  return $ext;
   }     

   function dataBr_Iso($dt) {
      // Pressup�e que a data esteja no formato DD/MM/AAAA
      // A melhor forma de gravar datas no PostgreSQL � utilizando 
      // uma string no formato DDDD-MM-AA. Esta fun��o justamente 
      // adequa a data a este formato
      $d = substr($dt, 0, 2);
      $m = substr($dt, 3, 2);
      $a = substr($dt, 6, 4);
      return $a.'-'.$m.'-'.$d;
   } 

   function datahoraBr_Iso($dt) {
      // Pressup�e que a data esteja no formato 'DD/MM/AAAA HH:MI'
      // A melhor forma de gravar datas no PostgreSQL � utilizando 
      // uma string no formato "DDDD-MM-AA HH:MI:SS". Esta fun��o
      // justamente adequa a data/hora a este formato.
      $d = substr($dt, 0, 2);
      $m = substr($dt, 3, 2);
      $a = substr($dt, 6, 4);
	  $hh = substr($dt, 11,2);
	  $mi = substr($dt, 14,2);
	  $ss = '00';
      return "$a-$m-$d $hh:$mi:$ss";
   } 

?>
