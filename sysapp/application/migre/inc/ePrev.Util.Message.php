<?php

/**
 * TODO: Documentar a classe Message (ePrev.Util.Message)
 * 
 * @access public
 * @package ePrev
 * @subpackage Util
 */
class Message {

	/**
	 * TODO: Criar documenta��o da vari�vel privada $message
	 */
	private $message;

	/**
	 * Adiciona mensagem para logs da classe
	 * @param string $_ttl "T�tulo da mensagem"
	 * @param string $_msg "Mensagem para concatenar a mensagem principal"
	 * @global string $this->message
	 */
	public function addMessage($_ttl, $_msg)
	{
		$this->message .= "<br /><b>" . $_ttl . "</b> : " . $_msg . "<br />";
	}

	/**
	 * Propriedade de leitura da Mensagem
	 * @global string $this->message
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Destrutor ser� usado para fechar conex�es e dispor objetos que forem necess�rios
	 */
	function __destruct(){
		// do nothing
	}

}
?>