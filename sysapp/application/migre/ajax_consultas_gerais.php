<?php
include_once('inc/sessao.php');
include_once('inc/conexao.php');

class ajax_consultas_gerais
{
	private $db;
	private $metodo;

	function __construct($db)
	{
		$this->db=$db;
	}

	function request()
	{
		$this->metodo = $_POST['metodo'];
		//$this->metodo = 'get_descricao_atividade';
	}

	function start()
	{
		$this->request();

		if( $this->metodo=='get_descricao_atividade' )
		{
			echo $this->get_descricao_atividade($_POST['divisao'],$_POST['codigo']);
			//echo $this->get_descricao_atividade('GA','CATJ');
		}
	}

	private function get_descricao_atividade($divisao,$codigo)
	{
		$sql = "
					SELECT obs 
					  FROM public.listas 
					 WHERE categoria IN ('TPMN','TPAT')
					   AND divisao   = '".$divisao."' 
					   AND codigo    = '".$codigo."'
			   ";
		$result = pg_query($this->db, $sql);
		$reg = pg_fetch_array($result);

		return $reg['obs'];
	}
}

$o = new ajax_consultas_gerais($db);
$o->start();
?>