<?php
class indicador extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();

        $this->load->view('gestao/indicador/index.php');
    }

    function listar()
    {
        CheckLogin();
        $this->load->model('projetos/Raiz_indicadores_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$args=array();

		

		// --------------------------
		// listar ...

        $this->Raiz_indicadores_model->listar( $result, $args );

		$data['collection'] = $result->result_array();

        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------

        $this->load->view('gestao/indicador/partial_result', $data);
    }

	function detalhe($cd=0)
	{
		$sql = "  ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE ={} ";
			esc( "{}", intval($cd), $sql );
			$query=$this->db->query($sql);
			$row=$query->row_array();
		}

		if($row) $data['row'] = $row;
		$this->load->view('gestao/indicador/detalhe', $data);
	}

	function salvar()
	{
		CheckLogin();

		$codigo=$this->input->post('', TRUE);
		if(intval($codigo)==0)
		{
			$sql="
			
			";
		}
		else
		{
			$sql="
			
			";
		}

		

		$query = $this->db->query($sql);

		redirect( "gestao/indicador", "refresh" );
	}

	function excluir($id)
	{
		CheckLogin();

		$sql = "
		UPDATE 
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao}
		WHERE md5(::varchar)='{}'
		";
		esc("{cd_usuario_exclusao}", usuario_id(), $sql, 'int');
		esc("{}", $id, $sql, 'str');

		$query=$this->db->query($sql);

		// echo $sql;

		redirect( 'gestao/indicador', 'refresh' );
	}
}
?>