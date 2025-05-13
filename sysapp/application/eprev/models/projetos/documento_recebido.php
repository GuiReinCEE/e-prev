<?php
class Documento_recebido extends Model
{
	function __construct()
	{
		parent::Model();
	}

	/**
	 * Retorna array para carregar um objeto dropdown html (SELECT) a partir do método "form_default_dropdown"
	 * 
	 * @param 	array	Parametros para busca
	 *
	 * @return	array	Lista para preencher dropdow ou FALSE em caso de erro
	 */
	function select_dropdown($cd_usuario_cadastro = 0)
	{
		$AND_WHERE = "";
		
		if((int)$cd_usuario_cadastro!=0)
		{
			$AND_WHERE = " AND d.cd_usuario_cadastro = {cd_usuario_cadastro} ";
			$AND_WHERE = str_replace( '{cd_usuario_cadastro}', (int)$cd_usuario_cadastro, $AND_WHERE );
		}
		
		$sql = "
			SELECT 
				d.cd_documento_recebido as value
				, d.cd_documento_recebido::varchar || ' - ' || t.ds_tipo || ' - ' || to_char(d.dt_cadastro, 'DD/MM/YYYY') as text
			FROM 
				projetos.documento_recebido d
				JOIN projetos.documento_recebido_tipo t
				ON d.cd_documento_recebido_tipo=t.cd_documento_recebido_tipo
			WHERE
				dt_envio IS NULL
				$AND_WHERE
			ORDER BY
				d.dt_cadastro DESC
		";

		$query = $this->db->query( $sql );
		if( $query )
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Adiciona um documento ao protocolo (documento_recebido_item)
	 *
	 * @param 	array 	campos para gravar na tabela
	 * @return 	boolean	TRUE sucesso, FALSE falha
	 */
	function adicionar_item($dados)
	{
		$sql = " 
			INSERT INTO projetos.documento_recebido_item(
	              cd_documento_recebido
	            , cd_empresa
	            , cd_registro_empregado
	            , seq_dependencia
	            , ds_observacao
	            , nr_folha
	            , cd_tipo_doc
	            , dt_cadastro
	            , cd_usuario_cadastro
	            , dt_exclusao
	            , cd_usuario_exclusao
	        )
		    VALUES
		    (
			      ?						-- cd_documento_recebido
			    , ?						-- cd_empresa
			    , ?						-- cd_registro_empregado
				, ?						-- seq_dependencia
				, ?						-- ds_observacao
				, ?						-- nr_folha
				, ?						-- cd_tipo_doc
				, CURRENT_TIMESTAMP		-- dt_cadastro
				, ?						-- cd_usuario_cadastro
				, null					-- dt_exclusao
				, null					-- cd_usuario_exclusao
				);
		";

		$bind = array(
			$dados['cd_documento_recebido']
			, $dados['cd_empresa'] 
			, $dados['cd_registro_empregado'] 
			, $dados['seq_dependencia'] 
			, $dados['ds_observacao'] 
			, $dados['nr_folha'] 
			, $dados['cd_tipo_doc'] 
			, $dados['cd_usuario_cadastro'] 
		);

		$query = $this->db->query( $sql, $bind, FALSE, TRUE );
		if($query)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	function lista_documento( &$result, &$count, $args=array() )
	{
		$this->load->library('pagination');

		// COUNT
		$sql_count = "

			SELECT 

				COUNT(*) AS qtd

			FROM 

				projetos.documento_recebido dr

				JOIN projetos.documento_recebido_tipo drt
				ON dr.cd_documento_recebido_tipo = drt.cd_documento_recebido_tipo

				JOIN projetos.usuarios_controledi cadastro
				ON cadastro.codigo = dr.cd_usuario_cadastro

		";

		$sql_select = "

			SELECT 

				TO_CHAR(dr.cd_documento_recebido, '000') AS cd_documento_recebido
				, TO_CHAR(dr.dt_cadastro, 'DD/MM/YYYY') AS dt_cadastro
				, drt.ds_tipo
				, dr.cd_usuario_cadastro
				, cadastro.nome as nome_usuario_cadastro

			FROM 

				projetos.documento_recebido dr
				JOIN projetos.documento_recebido_tipo drt
				ON dr.cd_documento_recebido_tipo = drt.cd_documento_recebido_tipo

				JOIN projetos.usuarios_controledi cadastro
				ON cadastro.codigo = dr.cd_usuario_cadastro

			ORDER BY dr.cd_documento_recebido

			LIMIT " . $this->pagination->per_page . " OFFSET " . $args["page"] . "

		";

		// ----------------------
		// RESULTADOS

		$query = $this->db->query($sql_count);
		$row = $query->row_array(0);
		$count = $row['qtd'];

		$this->setup_pagination($count);

		// RESULTS
		$result = $this->db->query($sql_select);
	}

	function lista_item( &$result, &$count, $args=array() )
	{
		$this->load->library('pagination');

		$WHERE = "";
		if(isset($args['cd_documento_recebido']))
		{
			$WHERE = " WHERE dri.cd_documento_recebido = {cd_documento_recebido} ";
			$WHERE = str_replace( "{cd_documento_recebido}", $this->db->escape( $args['cd_documento_recebido'] ), $WHERE );
		}

		// COUNT
		$sql_count = "

			SELECT 

				COUNT(*) as qtd

			FROM 

				projetos.documento_recebido dr
				JOIN projetos.documento_recebido_tipo drt
				ON dr.cd_documento_recebido_tipo = drt.cd_documento_recebido_tipo

				JOIN projetos.documento_recebido_item dri 
				ON dr.cd_documento_recebido = dri.cd_documento_recebido

				JOIN public.tipo_documentos td 
				ON dri.cd_tipo_doc = td.cd_tipo_doc 

				LEFT JOIN projetos.usuarios_controledi destino
				ON destino.codigo = dr.cd_usuario_destino 

				$WHERE

		";

		$sql_select = "
			SELECT 

				dri.cd_documento_recebido_item
				, TO_CHAR(dr.cd_documento_recebido, '000') AS cd_documento_recebido
				, TO_CHAR(dr.dt_cadastro, 'DD/MM/YYYY') AS dt_cadastro
				, drt.ds_tipo
				, td.nome_documento
				, dri.ds_observacao
				, dri.nr_folha
				, destino.nome as usuario_destino
				, dr.cd_usuario_destino
				, dr.dt_envio
				, to_char(dr.dt_ok, 'DD/MM/YYYY') as dt_recebimento

			FROM 

				projetos.documento_recebido dr
				JOIN projetos.documento_recebido_tipo drt
				ON dr.cd_documento_recebido_tipo = drt.cd_documento_recebido_tipo

				JOIN projetos.documento_recebido_item dri 
				ON dr.cd_documento_recebido = dri.cd_documento_recebido

				JOIN public.tipo_documentos td 
				ON dri.cd_tipo_doc = td.cd_tipo_doc 

				LEFT JOIN projetos.usuarios_controledi destino
				ON destino.codigo = dr.cd_usuario_destino
				
			$WHERE

			ORDER BY dr.cd_documento_recebido

			LIMIT " . $this->pagination->per_page . " OFFSET " . $args["page"] . ";
		";

		// ----------------------
		// RESULTADOS

		$query = $this->db->query($sql_count);
		$row = $query->row_array(0);
		$count = $row['qtd'];

		$this->setup_pagination($count);

		// RESULTS
		$result = $this->db->query($sql_select, FALSE, TRUE, FALSE);
	}

	function salvar_envio_item($args)
	{
		$sql = "
			UPDATE 
				projetos.documento_recebido
			SET 
				dt_envio = CURRENT_TIMESTAMP
				, cd_usuario_destino = {cd_usuario_destino}
			WHERE
				cd_documento_recebido = {cd_documento_recebido}
		";

		$sql = str_replace( "{cd_usuario_destino}", $this->db->escape($args['cd_usuario_destino']), $sql);
		$sql = str_replace( "{cd_documento_recebido}", $this->db->escape($args['cd_documento_recebido']), $sql);

		if( ($query = $this->db->query($sql)) === TRUE )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	private function setup_pagination($count)
	{
		// Setup pagination
		$config['enable_query_strings'] = FALSE;
		$config['base_url'] = $this->config->item('base_url') . 'index.php/sinprors/email_enviado/index';
		$config['per_page'] = 10000;
		$config['total_rows'] = $count;
		$this->pagination->initialize($config);
	}
}
?>