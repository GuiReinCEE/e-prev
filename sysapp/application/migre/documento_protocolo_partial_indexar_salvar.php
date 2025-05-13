<?php
include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/ePrev.Service.Projetos.php');
include_once('inc/ePrev.ADO.Projetos.documento_protocolo.php');

include 'oo/start.php';
using( array( 'projetos.documento_protocolo', 'projetos.documento_protocolo_item') );

class documento_protocolo_partial_item_salvar
{
	private $cd_documento_protocolo;
	private $confirmar_tambem = false;
	
	function __construct()
	{
		$this->requestParams();
		$this->start();
	}
	
	function requestParams()
	{
		$this->cd_documento_protocolo = $_POST['cd_documento_protocolo'];
		if(isset($_POST['command']))
		{
			$this->confirmar_tambem = ( $_POST['command']=="confirmar" );
		}
	}

	function start()
	{
		if( $this->consistente() )
		{
			$this->fazer_tudo();
		}
	}

	private function consistente()
	{
		if( $this->cd_documento_protocolo=="" )
		{
			echo "Código do documento incorreto!";
			return false;
		}
		else
		{
			return true;
		}
	}

	private function fazer_tudo()
	{
		while( list($key, $value) = each($_POST) )
		{
			if( strpos($key, "cd_documento_protocolo_item")>-1 )
			{
				// -----------------
				$cd_documento_protocolo_item = $value;
				$dt_indexacao = $_POST["dt_indexacao_".$value];
				$ds_observacao_indexacao = $_POST["observacao_text_".$value];

				$sql .= documento_protocolo_item::salvar_item_get_sql
							( 
							$cd_documento_protocolo_item
							, "S"
							, $dt_indexacao
							, $ds_observacao_indexacao
							, ""
							, "" 
							);
			}
		}

		if( $this->confirmar_tambem )
		{
			$sql .=
			" 
				UPDATE projetos.documento_protocolo 
				   SET dt_indexacao = CURRENT_TIMESTAMP
				       , cd_usuario_indexacao = " . pg_escape_string( $_SESSION["Z"] ) . "
				 WHERE cd_documento_protocolo = " . pg_escape_string($this->cd_documento_protocolo) . "
				;
			";
		}

		documento_protocolo_item::executar_sql( $sql );

		if( $this->confirmar_tambem )
		{
			header( 'Location:'.site_url('ecrm/protocolo_digitalizacao/index') );
			//header("location:documento_protocolo.php");
		}
		else
		{
			header("location:documento_protocolo_partial_indexar.php?cd=" . $this->cd_documento_protocolo."");
		}
	}
}

$o = new documento_protocolo_partial_item_salvar();
?>