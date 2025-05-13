<?php
class Relatorios_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_relatorio,
                   titulo,
                   TO_CHAR(dt_criacao, 'DD/MM/YYYY') AS dt_criacao,
                   divisao,
                   esquema,
                   tabela
              FROM projetos.relatorios 
             WHERE (cd_proprietario = ".intval($args['cd_usuario'])."
			    OR divisao = '".trim($args['cd_gerencia'])."' OR divisao = '".trim($args['cd_gerencia_ant'])."')
			   AND dt_exclusao IS NULL	
		     ORDER BY esquema, tabela;";

		$result = $this->db->query($qr_sql);
	}
	
	function listar_relatorio_dinamico( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_relatorio,
                   titulo,
                   TO_CHAR(dt_criacao, 'DD/MM/YYYY') AS dt_criacao,
                   divisao,
                   esquema,
                   tabela
              FROM projetos.relatorios 
			 WHERE dt_exclusao IS NULL
		     ORDER BY esquema, tabela;";

		$result = $this->db->query($qr_sql);
	}
	
	function esquema_tabela( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT table_schema || '.' || table_name AS value,
				   table_schema || '.' || table_name AS text
			  FROM information_schema.tables  
			 ORDER BY table_schema, table_name ;";

		$result = $this->db->query($qr_sql);
	}
	
	function restricao( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT trim(codigo) AS value, 
				   descricao AS text 
			  FROM public.listas 
			 WHERE categoria = 'REAC'
			 ORDER BY descricao ;";

		$result = $this->db->query($qr_sql);
	}
	
	function tipo( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT codigo AS value, 
				   descricao AS text 
			  FROM public.listas 
			 WHERE categoria = 'TPRL'
			 ORDER BY descricao ;";

		$result = $this->db->query($qr_sql);
	}
	
	function fonte( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT codigo AS value, 
				   descricao AS text
			  FROM public.listas 
			 WHERE categoria = 'FONT'
			 ORDER BY descricao;";

		$result = $this->db->query($qr_sql);
	}
	
	function sistema( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT codigo AS value, 
				   nome AS text
			  FROM projetos.projetos 	 
			 WHERE dt_exclusao IS NULL
			 ORDER bY nome ;";

		$result = $this->db->query($qr_sql);
	}

	function carrega( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT r.cd_relatorio, 
			       r.qr_sql,
				   r.esquema || '.' || r.tabela AS esquema_tabela, 
				   r.tipo, 
				   r.query, 
				   TO_CHAR(r.dt_criacao, 'DD/MM/YYYY') AS dt_inclusao, 
				   r.titulo, 
				   r.num_colunas, 
				   r.fonte, 
				   r.divisao, 
				   r.clausula_where, 
				   r.grupo, 
				   r.ordem, 
				   r.cd_proprietario, 
				   r.restricao_acesso, 
				   r.pos_x, 
				   r.largura, 
				   r.mostrar_sombreamento, 
				   r.tam_fonte, 
				   r.tam_fonte_titulo, 
				   r.mostrar_cabecalho, 
				   r.mostrar_linhas, 
				   r.orientacao, 
				   r.cd_projeto, 
				   r.especie,
				   CASE WHEN r.fonte = 'FAR' THEN 'Arial'
				        WHEN r.fonte = 'FCO' THEN 'Courier'
				        ELSE 'Times'
				   END AS fonte_real
			  FROM projetos.relatorios r
			 WHERE cd_relatorio = '".intval($args['cd_relatorio']). "';";
		
		$result = $this->db->query($qr_sql);
	}

	function salvar( &$result, $args=array() )
	{
		if(intval($args['cd_relatorio']) == 0)
		{
			$cd_relatorio = intval($this->db->get_new_id("projetos.relatorios", "cd_relatorio"));
		
			$qr_sql = "
				INSERT INTO projetos.relatorios 
				     ( 
					    cd_relatorio,
						cd_usuario, 
						qr_sql,
						esquema, 
						tabela, 
						query,	 
						clausula_where, 
						titulo, 
						ordem, 
						grupo, 
						tipo, 
						fonte, 
						divisao, 
						restricao_acesso, 
						cd_proprietario, 
						dt_atualizacao,	 
						dt_criacao, 
						pos_x, 
						largura, 
						tam_fonte, 
						tam_fonte_titulo, 
						orientacao, 
						cd_projeto, 
						especie,
						mostrar_sombreamento,
						mostrar_cabecalho, 
						mostrar_linhas
					  ) 
				 VALUES 
				      (
					    ".intval($cd_relatorio).",
						".intval($args['cd_usuario']).",
						".(trim($args['qr_sql']) != '' ? str_escape($args['qr_sql']) : "DEFAULT").",
						".(trim($args['esquema']) != '' ? str_escape($args['esquema']) : "DEFAULT").",
						".(trim($args['tabela']) != '' ? str_escape($args['tabela']) : "DEFAULT").",
						".(trim($args['query']) != '' ? str_escape($args['query']) : "DEFAULT").",
						".(trim($args['clausula_where']) != '' ? str_escape($args['clausula_where']) : "DEFAULT").",
						".(trim($args['titulo']) != '' ? str_escape($args['titulo']) : "DEFAULT").",
						".(trim($args['ordem']) != '' ? str_escape($args['ordem']) : "DEFAULT").",
						".(trim($args['grupo']) != '' ? str_escape($args['grupo']) : "DEFAULT").",
						".(trim($args['tipo']) != '' ? str_escape($args['tipo']) : "DEFAULT").",
						".(trim($args['fonte']) != '' ? str_escape($args['fonte']) : "DEFAULT").",
						".(trim($args['divisao']) != '' ? str_escape($args['divisao']) : "DEFAULT").",
						".(trim($args['restricao_acesso']) != '' ? str_escape($args['restricao_acesso']) : "DEFAULT").",
						".(trim($args['cd_proprietario']) != '' ? intval($args['cd_proprietario']) : "DEFAULT").",
						CURRENT_TIMESTAMP,
						CURRENT_TIMESTAMP,
						".(trim($args['pos_x']) != '' ? trim($args['pos_x']) : "DEFAULT").",
						".(trim($args['largura']) != '' ? trim($args['largura']) : "DEFAULT").",
						".(trim($args['tam_fonte']) != '' ? trim($args['tam_fonte']) : "DEFAULT").",
						".(trim($args['tam_fonte_titulo']) != '' ? trim($args['tam_fonte_titulo']) : "DEFAULT").",
						".(trim($args['orientacao']) != '' ? str_escape($args['orientacao']) : "DEFAULT").",
						".(trim($args['cd_projeto']) != '' ? intval($args['cd_projeto']) : "DEFAULT").",
						".(trim($args['especie']) != '' ? str_escape($args['especie']) : "DEFAULT").",
						".(trim($args['mostrar_sombreamento']) != '' ? str_escape($args['mostrar_sombreamento']) : "N").",
						".(trim($args['mostrar_cabecalho']) != '' ? str_escape($args['mostrar_cabecalho']) : "N").",
						".(trim($args['mostrar_linhas']) != '' ? str_escape($args['mostrar_linhas']) : "N")."
                      );";
		}
		else
		{
			$cd_relatorio = intval($args['cd_relatorio']);
			
			$qr_sql = "
				UPDATE projetos.relatorios 
				   SET cd_usuario           = ".intval($args['cd_usuario']).",
				       qr_sql   	        = ".(trim($args['qr_sql']) != '' ? str_escape($args['qr_sql']) : "DEFAULT").",
				       esquema	            = ".(trim($args['esquema']) != '' ? str_escape($args['esquema']) : "DEFAULT").", 
					   tabela	            = ".(trim($args['tabela']) != '' ? str_escape($args['tabela']) : "DEFAULT").", 
					   query	            = ".(trim($args['query']) != '' ? str_escape($args['query']) : "DEFAULT").", 
					   clausula_where       = ".(trim($args['clausula_where']) != '' ? str_escape($args['clausula_where']) : "DEFAULT").",
					   titulo     	        = ".(trim($args['titulo']) != '' ? str_escape($args['titulo']) : "DEFAULT").",
					   ordem                = ".(trim($args['ordem']) != '' ? str_escape($args['ordem']) : "DEFAULT").",
					   grupo	            = ".(trim($args['grupo']) != '' ? str_escape($args['grupo']) : "DEFAULT").",
					   tipo	                = ".(trim($args['tipo']) != '' ? str_escape($args['tipo']) : "DEFAULT").",
					   fonte	            = ".(trim($args['fonte']) != '' ? str_escape($args['fonte']) : "DEFAULT").",
					   divisao	            = ".(trim($args['divisao']) != '' ? str_escape($args['divisao']) : "DEFAULT").",
					   restricao_acesso	    = ".(trim($args['restricao_acesso']) != '' ? str_escape($args['restricao_acesso']) : "DEFAULT").",
					   cd_proprietario      = ".(trim($args['cd_proprietario']) != '' ? intval($args['cd_proprietario']) : "DEFAULT").",
					   dt_atualizacao	    = CURRENT_TIMESTAMP,	 
					   pos_x                = ".(trim($args['pos_x']) != '' ? trim($args['pos_x']) : "DEFAULT").",
					   largura	            = ".(trim($args['largura']) != '' ? trim($args['largura']) : "DEFAULT").",
					   tam_fonte	        = ".(trim($args['tam_fonte']) != '' ? trim($args['tam_fonte']) : "DEFAULT").",
					   tam_fonte_titulo	    = ".(trim($args['tam_fonte_titulo']) != '' ? trim($args['tam_fonte_titulo']) : "DEFAULT").",
					   orientacao           = ".(trim($args['orientacao']) != '' ? str_escape($args['orientacao']) : "DEFAULT").",
					   cd_projeto           = ".(trim($args['cd_projeto']) != '' ? intval($args['cd_projeto']) : "DEFAULT").",
					   especie              = ".(trim($args['especie']) != '' ? str_escape($args['especie']) : "DEFAULT").",
					   mostrar_sombreamento	= ".(trim($args['mostrar_sombreamento']) != '' ? str_escape($args['mostrar_sombreamento']) : "N").",  
					   mostrar_cabecalho	= ".(trim($args['mostrar_cabecalho']) != '' ? str_escape($args['mostrar_cabecalho']) : "N").",
					   mostrar_linhas	    = ".(trim($args['mostrar_linhas']) != '' ? str_escape($args['mostrar_linhas']) : "N")."
				 WHERE cd_relatorio = ".intval($cd_relatorio).";";
		}
		
		$result = $this->db->query($qr_sql);
		
		return $cd_relatorio;
	}
	
	function salvar_coluna(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.relatorios_colunas
			     (
                   cd_relatorio, 
				   cd_coluna, 
				   nome_coluna, 
				   alinhamento, 
				   largura
	 			 )
            VALUES 
			     (
				   ".intval($args['cd_relatorio']).",
				   ".intval($args['cd_coluna']).",
				   '".trim($args['nome_coluna'])."',
				   '".trim($args['alinhamento'])."',
				   '".trim($args['largura'])."'
				 );
			UPDATE projetos.relatorios 
			   SET num_colunas = (num_colunas+1)
			 WHERE cd_relatorio = ".intval($args['cd_relatorio']).";";
				 
		$result = $this->db->query($qr_sql);
	}
	
	function listar_colunas(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_relatorio, 
			       cd_coluna, 
				   nome_coluna, 
				   largura,
				   alinhamento AS align,
				   CASE WHEN alinhamento = 'L' THEN 'Esquerda'
				        WHEN alinhamento = 'C' THEN 'Centralizado'
				        ELSE 'Direita'
				   END AS alinhamento,
				   fl_somar
              FROM projetos.relatorios_colunas
			 WHERE cd_relatorio = ".intval($args['cd_relatorio'])."
			 ORDER BY cd_coluna;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_coluna(&$result, $args=array())
	{
		$qr_sql = "
			DELETE 
			  FROM projetos.relatorios_colunas
			 WHERE cd_coluna = ".intval($args['cd_coluna']).";
			UPDATE projetos.relatorios 
			   SET num_colunas = (num_colunas-1)
			 WHERE cd_relatorio = ".intval($args['cd_relatorio']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function execute_sql(&$result, $args=array())
	{
		$qr_sql = $args['query'].' FROM '.$args['esquema_tabela'].' '.$args['where'].' '.$args['grupo'].' '.$args['ordem'];
		
		$result = $this->db->query($qr_sql);
	}
	
	function qr_execute(&$result, $args=array())
	{
		$result = $this->db->query($args['qr_sql']);
	}	
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.relatorios
			   SET dt_exclusao = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_relatorio = ".intval($args['cd_relatorio']).";";
			 
		$result = $this->db->query($qr_sql);
	}
}
?>