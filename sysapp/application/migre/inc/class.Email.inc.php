<?php
   /*************************************************************************
      Classe...: Email
	  Descrição: Classe para envio de emails
	  Autor....: Júlio Corrêa Pereira
	  Data.....: 23/07/2003
	  Alteração: 
   *************************************************************************/

   class Email {
      var $to;
	  var $cc;
	  var $bcc;
	  var $from;
	  var $subject;
	  var $body;
	  var $html;
	  var $headers;
	  var $prioridade;
	  var $xprioridade;
	  
	  function Email() { // Construtor
	     $this->to = "";
		 $this->cc = "";
		 $this->bcc = "";
		 $this->from = "";
		 $this->subject = "";
		 $this->body = "";
		 $this->html = false;
		 $this->headers = "";
		 $this->prioridade = 0; // Normal
		 $this->xprioridade = 3; // Normal
	  }
	  
/*
	  function SetFrom($email, $nome) {
	     $this->from = "$nome <$email>";
	  }
*/
	  
	  function SetFrom($email) {
	     $this->from = "$email";
	  }

	  function AddTO($email) {
	     if ($this->to <> "") {
		    $this->to .= ", ";
		 }
	     $this->to .= $email;
	  }
	  
	  function AddCC($email) {
	     if ($this->cc <> "") {
		    $this->cc .= ", ";
		 }
	     $this->cc .= $email;
	  } 
	  
	  function AddBCC($email){
	     if ($this->bcc <> "") {
		    $this->bcc .= ", ";
		 }
	     $this->bcc .= $email;
	  }
	  
	  function SetSubject($txt) {
	     $this->subject = $txt;
	  }
	  
	  function SetBody($msg) {
	     $this->body = $msg;
	  }
	  
	  function IsHtml() {
	     $this->html = true;
	  }
	  
	  function IsText() {
	     $this->html = false;
	  }
	  
	  function SetPriority($prioridade) { // Quanto menor, maior a prioridade
	     switch($prioridade) {
		    case 0: $this->prioridade = "urgent"; break;
		    case 1: $this->prioridade = "normal"; break;
		    case 2: $this->prioridade = "non-urgent"; break;
		 }
	  }
	  
	  function SetXPriority($prioridade) { // Quanto menor, maior a prioridade
	     $this->xprioridade = $prioridade; // 1 (Highest), 2 (High), 3 (Normal), 4 (Low), 5 (Lowest)
	  }
	  
	  function Send() {
		 // Envia email no formato HTML 
		 $this->headers = '';

	     if ($this->html == true) {
		    $this->headers .= "MIME-Version: 1.0\r\n";
            $this->headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		 }
         $this->headers .= ($this->from == '' ? '' : "from: " . $this->from . "\r\n");
//         $this->headers .= ($this->to   == '' ? '' : "to: " . $this->to . "\r\n");
		 $this->headers .= ($this->cc   == '' ? '' : "cc: " . $this->cc . "\r\n");
		 $this->headers .= ($this->bcc  == '' ? '' : "bcc: " . $this->bcc . "\r\n");
		 
         $this->headers .= "Priority: " . $this->prioridade . "\r\n";
         $this->headers .= "X-Priority: " . $this->xprioridade . "\r\n";

//		 echo "<pre>" . $this->headers . "</pre>";

         ####return mail($this->to, $this->subject, $this->body, $this->headers);
	  }
   }
?>