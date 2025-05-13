<?php
   /*************************************************************************
      Classe...: Socket
	  Descriзгo: Classe abstrata para uso de comunicaзгo via sockets em PHP
	  Autor....: Jъlio Corrкa Pereira
	  Data.....: 23/07/2003
	  Alteraзгo: 
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
	  
	  function Socket() {
	     $this->errno = 0;
	     $this->errstr = "";
	     $this->timeout = 10;
		 $this->host = "";
		 $this->port = "";
		 $this->tambuffer = 1024;
	  }
	  
	  function SetTimeOut($tmp) {
	     $this->timeout = $tmp;
	  }
	  
	  function GetTimeOut() {
	     return $this->timeout;
	  }
	  
      /*************************************************************************
        Erro | Descriзгo
		----   ---------------------------------------------------------------
		E0     Nгo ocorreu erro
		E1     Listner nгo responde
	  *************************************************************************/

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
	  
	  function Connect() {
         if ($conn = fsockopen($this->host, $this->port, $this->errno, $this->errstr, $this->timeout)) {
            $this->h = $conn;
            return true;
         }
         else {
            return false;
		 }
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
				  $this->errstr = 'Listner nгo responde.';
                  break;
               }
               $ret = fgets($this->h, $this->tambuffer);
            }
            return $ret;
         } 
      }

	  function Put($msg) {
	     if ($this->h) {
            fputs($this->h, $msg);
         }
		 else {
		    
         }
	  }
	  
	  function Ask($msg) {
	     $this->Connect();
	     $this->Put($msg);
         return $this->Get($this->tambuffer);
      }
   }
?>