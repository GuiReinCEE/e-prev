<?php
class Log {

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
	public function addMessage($_ttl, $_msg, $_typ = 'message')
	{
		if($_typ=='message')
		{
			$this->message .= "<br /><b>" . $_ttl . "</b> : <pre>" . $_msg . "</pre><br />";
		}
		
		if($_typ=='error')
		{
			$this->message .= "<br /><b>" . $_ttl . "</b> : <font color='red'><pre>" . $_msg . "</pre></font><br />";
		}
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
	
	function persist()
	{
		// TODO: Implement
	}

}
?>