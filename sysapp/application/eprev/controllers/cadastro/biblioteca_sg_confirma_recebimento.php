<?php
class Biblioteca_sg_confirma_recebimento extends Controller
{
	function __construct()
    {
        parent::Controller();
		
		$this->load->model("projetos/biblioteca_sg_model");
    }

    function index($cd_biblioteca_livro_movimento)
	{
		$result = null;
		$args   = Array();
		$data   = Array();
		
		$args["cd_biblioteca_livro_movimento"] = $cd_biblioteca_livro_movimento;
		
		$this->biblioteca_sg_model->confirmar($result, $args);

		echo "<center>Recebimento Confirmado</center>";
	}
}