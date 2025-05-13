<?php
class cadastro_instancia extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();

        $this->load->view('gestao/cadastro_instancia/index.php');
    }

    function listar()
    {
        CheckLogin();
        $this->load->model('projetos/Instancias_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$args=array();

		

		manter_filtros($args);

		// --------------------------
		// listar ...

        $this->Instancias_model->listar( $result, $args );

		$data['collection'] = $result->result_array();

        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------

        $this->load->view('gestao/cadastro_instancia/partial_result', $data);
    }

	function detalhe($cd=0)
	{
		if(CheckLogin())
		{
			$sql = " SELECT * FROM projetos.instancias ";
			$row=array();
			$query = $this->db->query( $sql . ' LIMIT 1 ' );
			$fields = $query->field_data();
			foreach( $fields as $field )
			{
				$row[$field->name] = '';
			}

			if( intval($cd)>0 )
			{
				$sql .= " WHERE cd_instancia={cd_instancia} ";
				esc( "{cd_instancia}", intval($cd), $sql );
				$query=$this->db->query($sql);
				$row=$query->row_array();
			}
	
			if($row) $data['row'] = $row;
			$this->load->view('gestao/cadastro_instancia/detalhe', $data);
		}
	}

	function salvar()
	{
		CheckLogin();

		$codigo=$this->input->post('cd_instancia', TRUE);

		$args["nome"] = $this->input->post("nome",TRUE);
		$args["cd_instancia"] = $this->input->post("cd_instancia",TRUE);

		if(intval($codigo)==0)
		{
			$sql="
			INSERT INTO projetos.instancias ( nome ) VALUES ( '{nome}' )
			";
		}
		else
		{
			$sql="
			UPDATE projetos.instancias SET nome = '{nome}' 
			WHERE cd_instancia = {cd_instancia} 
			";
		}

		esc("{nome}", $args["nome"], $sql, "str", FALSE);
		esc("{cd_instancia}", $args["cd_instancia"], $sql, "int", FALSE);

		$query = $this->db->query($sql);

		redirect( "gestao/cadastro_instancia", "refresh" );
	}
}
?>