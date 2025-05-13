<?php
class chave extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();

        $this->load->view('ecrm/chave/index.php');
    }

    function listar()
    {
        CheckLogin();
        $this->load->model('projetos/Chaves_model');

        $data['collection'] = array();
        $result = null;

        // --------------------------
		// filtros ...

		$args=array();

		

		manter_filtros($args);

		// --------------------------
		// listar ...

        $this->Chaves_model->listar( $result, $args );

		$data['collection'] = $result->result_array();

        if( $result )
        {
            $data['collection'] = $result->result_array();
        }

        // --------------------------

        $this->load->view('ecrm/chave/partial_result', $data);
    }

	function detalhe($cd=0)
	{
		$sql = " SELECT * FROM projetos.chaves ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_chave={cd_chave} ";
			esc( "{cd_chave}", intval($cd), $sql );
			$query=$this->db->query($sql);
			$row=$query->row_array();
		}

		if($row) $data['row'] = $row;
		$this->load->view('ecrm/chave/detalhe', $data);
	}

	function salvar()
	{
		CheckLogin();

		$codigo=$this->input->post('cd_chave', TRUE);

		$args["ds_chave"] = $this->input->post("ds_chave",TRUE);
$args["cd_sala"] = $this->input->post("cd_sala",TRUE);
$args["cd_chave"] = $this->input->post("cd_chave",TRUE);


		if(intval($codigo)==0)
		{
			$sql="
			INSERT INTO projetos.chaves ( ds_chave 
, cd_sala 
) VALUES ( '{ds_chave}' 
, {cd_sala} 
)

			";
		}
		else
		{
			$sql="
			UPDATE projetos.chaves SET 
 ds_chave = '{ds_chave}' 
, cd_sala = {cd_sala} 
 WHERE 
cd_chave = {cd_chave} 
			";
		}

		esc("{ds_chave}", $args["ds_chave"], $sql, "str", FALSE);
esc("{cd_sala}", $args["cd_sala"], $sql, "int", FALSE);
esc("{cd_chave}", $args["cd_chave"], $sql, "int", FALSE);


		$query = $this->db->query($sql);

		redirect( "ecrm/chave", "refresh" );
	}

	function excluir($id)
	{
		CheckLogin();

		$sql = "
		DELETE FROM projetos.chaves
		WHERE md5(cd_chave::varchar)='{cd_chave}'
		";
		esc("{cd_usuario_exclusao}", usuario_id(), $sql, 'int');
		esc("{cd_chave}", $id, $sql, 'str');

		$query=$this->db->query($sql);

		// echo $sql;

		redirect( 'ecrm/chave', 'refresh' );
	}
}
?>