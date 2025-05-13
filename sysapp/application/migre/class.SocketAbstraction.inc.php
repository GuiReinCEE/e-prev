<?php
   /*************************************************************************
      Classe...: Socket
      Descrição: Classe abstrata para uso de comunicação via sockets em PHP
      Autor....: Júlio Corrêa Pereira
      Data.....: 23/07/2003
      Alteração: 
   *************************************************************************/
   
   class Socket {
      var $Socket;
      var $timeout;
      var $errno;
      var $errstr;
      var $tambuffer;
      var $retorno;
      var $h;
      var $host;
      var $port;
      var $connected;
      var $version;

      function Socket() { // construtor
         $this->version = "1.1";
         $this->errno = 0;
         $this->errstr = "";
         $this->timeout = 10;
         $this->connectTimeOut = 5;
         $this->host = "";
         $this->port = "";
         $this->tambuffer = 1024; // Em bytes. 0 (zero) para não ter limite de tamanho
         $this->connected = 'NO';
      }

      function SetConnectTimeOut($tmp) { // Timeout para conexão
         $this->connectTimeOut = $tmp;
      }
      
      function SetTimeOut($tmp) { // timeout para transferencia dos dados
         $this->timeout = $tmp;
      }
      
      function GetTimeOut() {
         return $this->timeout;
      }

      /*****************************************************************************************************************
        Erro | Descrição
        ----   --------------------------------------------------------------------------------------------------------
        E0     Não ocorreu erro
        E1     Listner não responde
        E2     Tamanho do buffer muito pequeno
        E3     Erro ao tentar escrever dados no socket
        E4     Timeout. Resposta muito grande ou timeout muito pequeno em função da latência do servidor
        E5     Ocorreu um erro não tratado durante a espera pela resposta do servidor
      *****************************************************************************************************************/

      function GetErrNo() {
         return $this->errno;
      }

      function GetErrStr() {
         return $this->errstr;
      }

      function SetRemotePort($p = "") {
        $this->port = $p;
      }

      function SetRemoteHost($h = "") {
         $this->host = $h;
      }
     
      function SetBufferLength($t = 1024) {
         $this->tambuffer = $t;
      }

      function GetBufferLength() {
         return $this->tambuffer;
      }

      function Error() {
         return ! ($this->GetErrNo() == '');
      }

      function Connect() {
//         try {
            if ($conn = fsockopen($this->host, $this->port, $this->errno, $this->errstr, $this->connectTimeOut)) {
               $this->h = $conn;
               stream_set_timeout($this->h, $this->GetTimeOut());
               $this->connected = 'YES';
               return true;
            }
            else {
               $this->connected = 'ERROR';
               return false;
            }
//         } catch (Exception $e) {
//            $this->connected = 'ERROR';
//            return false;
//         }
      }

      function Disconnect() {
         if ($this->h == true) {
            fclose($this->h);
         }
      }
    
      function Get() {
         $ret = '';
         $cont = 0;
         if ($this->h) {
            while (!feof($this->h)) {
               $cont++;
               if ($cont > 1) {
                  $ret = false;
                  $this->errno = 'E1';
                  $this->errstr = 'Listner não responde.';
                  break;
               }
               $ret = fgets($this->h, $this->tambuffer);
            }
            return $ret;
         } 
      }

      function Get2() {
         $ret = '';
         $c = '';
         $cont = 0;
         if ($this->h) {
            while ( (($c = fgetc($this->h)) !== false) and (!feof($this->h)) ) { // Le resposta, byte-a-byte, até chegar ao final do stream ou ocorrer timeout
               $ret .= $c;
            }
            $info = stream_get_meta_data($this->h);
            if ($info['eof'] == true) {
               if ($this->GetBufferLength() > 0) {
                  if (strlen($ret) > $this->GetBufferLength()) { // Testa se a resposta é maior que o tamanho especificado para o buffer
                     $this->errno = 'E2';
                     $this->errstr = 'Buffer muito pequeno';
                     $ret = '';
                  }
                  else {
                     $this->errno = '';
                     $this->errstr = '';
                  }
               }
            }
            elseif ($info['timed_out'] == true) {
               $this->errno  = 'E4';
               $this->errstr = 'Timeout. Resposta muito grande ou timeout muito pequeno em função da latência do servidor';
               $ret = '';
            }
            else {
               $this->errno  = 'E5';
               $this->errstr = 'Ocorreu um erro não tratado durante a espera pela resposta do servidor';
               $ret = '';
            }
            return $ret;
         } 
      }

      function Put($msg) {
         if ($this->h) {
            $this->errno = '';
            $this->errstr = '';
            if (!fputs($this->h, $msg)) {
               $this->errno  = 'E3';
               $this->errstr = 'Erro ao tentar escrever dados no socket';
            }
         }
      }

      function Ask($msg) {
         if ($this->connected == 'NO') {
            $this->Connect();
         }
         if ($this->connected == 'YES') {
            $this->Put($msg);
            return $this->Get();
         } 
         else {
            return '';
         }
      }

      function Ask2($msg) {
         if ($this->connected == 'NO') {
            $this->Connect();
         }
         if ($this->connected == 'YES') {
            $this->Put($msg);
            return $this->Get2();
         } 
         else {
            return '';
         }
      }

   }
?>
