<?php
class email_enviado extends Controller 
{
	function __construct()
	{
		parent::Controller();
		if( ! CheckLogin() ) exit;
	}

	function index()
	{
		$this->load->view('sinprors/email_enviado/index');
	}

	function listar()
	{
		$this->load->model("projetos/Envia_emails");
		$count = 0;
		$args['page'] = $this->input->post('current_page');

		$args['cd_registro_empregado'] = $this->input->post("cd_registro_empregado");
		$args['inicio'] = $this->input->post("inicio");
		$args['fim'] = $this->input->post("fim");
		$args['assunto'] = $this->input->post("assunto");
		$args['cd_evento'] = $this->input->post("evento");

		$this->Envia_emails->lista_emails_sinprors( $result, $count, $args );

		$data['quantos'] = $count;
		$data['itens'] = $result->result();
		
		$this->load->view('sinprors/email_enviado/partial_result', $data);
	}
}
?>