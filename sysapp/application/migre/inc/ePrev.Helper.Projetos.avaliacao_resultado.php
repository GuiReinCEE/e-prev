<?php
class helper_avaliacao_resultado
{ #begin_class
    private $db;
    private $command;
    private $id;
    private $cd_usuario_logado;
    private $usuario_avaliado;
    private $usuario_avaliador;
    private $row;

    private $grau_ci;
    private $val_ci;
    private $grau_esc;
    private $val_esc;
    private $grau_ce;
    private $val_ce;
    private $grau_resp;
    private $val_resp;
    private $grau_1;
    private $grau_2;
    private $grau_final;
    private $media_ci_comite;

    // Collections
    private $result_comp_inst;
    private $result_escolaridade;
    private $result_comp_espec;
    private $result_resp;

    // Entidades
    private $capas;
    public $capa;

    function helper_avaliacao_resultado( $_db, $cd_avaliacao, $cd_usuario_logado )
    {
        $this->db = $_db;

        $this->id = $cd_avaliacao;
        $this->cd_usuario_logado = $cd_usuario_logado;
    }

    function __destruct()
    {
        $this->db = null;
    }

    function load()
    {
        $filtro = new entity_projetos_avaliacao_capa_extended();
        $filtro->set_cd_avaliacao_capa( $this->id );

        $service = new service_projetos($this->db);
        $this->capas = $service->avaliacao_capa_FetchAll( $filtro );
        $this->capa = $this->capas[0];

        $this->capas = null;
        $filtro = null;
        $service = null;
    }

    function load_valores( $cd )
    {
        $dal = new DBConnection();
        $dal->loadConnection( $this->db );

        // VAL_ESC
        $dal->createQuery("

            SELECT grau_escolaridade 
              FROM projetos.avaliacao_capa
             WHERE cd_avaliacao_capa = {cd_avaliacao_capa} 

        ");
        $dal->setAttribute("{cd_avaliacao_capa}", $this->capa->get_cd_avaliacao_capa());
        $result_esc = $dal->getResultset();
        $row_esc = pg_fetch_array($result_esc);
        $this->val_esc = $row_esc["grau_escolaridade"];
        $row_esc = null;

        // load grau_ci
        $dal->createQuery("

            SELECT SUM(grau) AS grau_ci 
              FROM projetos.avaliacoes_comp_inst 
             WHERE cd_avaliacao = {cd_avaliacao} 

        ");
        $dal->setAttribute("{cd_avaliacao}", $cd);
        $result_grau_ci = $dal->getResultset();
        $row_grau_ci = pg_fetch_array($result_grau_ci);
        $this->grau_ci = $row_grau_ci["grau_ci"];

        // load val_ci
        $dal->createQuery("

            SELECT COUNT(*) AS ocorr_ci 
              FROM projetos.avaliacoes_comp_inst 
             WHERE cd_avaliacao = {cd_avaliacao} 

        ");
        $dal->setAttribute("{cd_avaliacao}", $cd);
        $result_val_ci = $dal->getResultset();
        $row_val_ci = pg_fetch_array($result_val_ci);
        if ($row_val_ci["ocorr_ci"] == "0") 
        {   
            $this->val_ci = "Nуo realizada!";
        }
        else 
        {
            $this->val_ci = number_format(($this->grau_ci / $row_val_ci['ocorr_ci']), 2);
        }
        
        // GRAU_CE e VAL_CE
        $dal->createQuery("

            SELECT SUM(grau) AS grau_ce 
              FROM projetos.avaliacoes_comp_espec 
             WHERE cd_avaliacao = {cd_avaliacao} 

        ");
        $dal->setAttribute("{cd_avaliacao}", $cd);
        $result_grau_ce = $dal->getResultset();
        $row_grau_ce = pg_fetch_array($result_grau_ce);
        $this->grau_ce = $row_grau_ce["grau_ce"];

        // load val_ce
        $dal->createQuery("

            SELECT COUNT(*) AS ocorr_ce
              FROM projetos.avaliacoes_comp_espec
             WHERE cd_avaliacao = {cd_avaliacao} 

        ");
        $dal->setAttribute( "{cd_avaliacao}", $cd );
        $result_val_ce = $dal->getResultset();
        // echo( $dal->getMessage() );
        $row_val_ce = pg_fetch_array($result_val_ce);
        if ($row_val_ce["ocorr_ce"] == "0") 
        {   
            $this->val_ce = "Nуo realizada!";
        }
        else 
        {
            $this->val_ce = number_format( ($this->grau_ce / $row_val_ce['ocorr_ce']), 2 );
        }
        

        // GRAU_RESP e VAL_RESP
        $dal->createQuery("

            SELECT SUM(grau) AS grau_resp
              FROM projetos.avaliacoes_responsabilidades 
             WHERE cd_avaliacao = {cd_avaliacao} 

        ");
        $dal->setAttribute("{cd_avaliacao}", $cd);
        $result_grau_resp = $dal->getResultset();
        $row_grau_resp = pg_fetch_array($result_grau_resp);
        $this->grau_resp = $row_grau_resp["grau_resp"];
        
        $dal->createQuery("

            SELECT COUNT(*) AS ocorr_resp
              FROM projetos.avaliacoes_responsabilidades
             WHERE cd_avaliacao = {cd_avaliacao}

        ");
        $dal->setAttribute( "{cd_avaliacao}", $cd);
        $result_val_resp = $dal->getResultset();
        $row_val_resp = pg_fetch_array($result_val_resp);
        if ($row_val_resp["ocorr_resp"] == "0") 
        {   
            $this->val_resp = "Nуo realizada!";
        }
        else 
        {
            $this->val_resp = number_format( ($this->grau_resp / $row_val_resp['ocorr_resp']), 2 );
        }
        
        $dal = null;
    }
    public function get_val_ci()
    {
        return $this->val_ci;
    }
    public function get_val_resp()
    {
        return $this->val_resp;
    }
    public function get_val_ce()
    {
        return $this->val_ce;
    }
    public function get_val_esc()
    {
        return $this->val_esc;
    }
    public function get_grau_1()
    {
        $this->grau_1 = ($this->val_ci+$this->val_esc)/2;
        return $this->grau_1;
    }
    public function get_grau_2()
    {
        $this->grau_2 = ($this->val_ce+$this->val_resp)/2;
        return $this->grau_2;
    }
    public function get_grau_final()
    {
        $this->grau_1 = ($this->val_ci+$this->val_esc)/2;
        $this->grau_2 = ($this->val_ce+$this->val_resp)/2;
        $resultado = ( (  $this->grau_1*40  ) + (  $this->grau_2*60  ) ) / 100;
        return $resultado;
    }

    public function get_id()
    {
        return $this->id;
    }

    public function get_cd_usuario_logado()
    {
        return $this->cd_usuario_logado;
    }

    public function calcula_media_ci_comite()
    {
        $idx=0;
        // Para cada avaliaчуo guarda em array a mщdia de grau de CI. Apenas para avaliaчѕes de Comite ou Superior
        foreach($this->capa->avaliacoes as $avaliacao)
        {
            if (!is_null($avaliacao))
            {
                if ($avaliacao->get_tipo()=="C" || $avaliacao->get_tipo()=="S")
                {
                    $grau=0;
                    foreach( $avaliacao->competencias_institucionais as $ci )
                    {
                    	if(isset($ci))
                    	{
                        	$grau += $ci->get_grau();
                    	}
                    }
                    $arr_CI[$idx] = $grau / sizeof($avaliacao->competencias_institucionais);
                    $idx++;
                }
            }
        }

        // Soma os graus de CI e divide pelo nњmero de avaliaчѕes encontradas (sizeof())
        $media_comite=0;
        foreach( $arr_CI as $media )
        {
        	if(isset($media))
        	{
            	$media_comite += $media;
        	}
        }
        $this->media_ci_comite = number_format( $media_comite / sizeof($arr_CI), 2 );
    }

    public function get_media_ci_comite()
    {
        return $this->media_ci_comite;
    }
    public function get_media_final_comite()
    {
        return number_format( ( (  $this->get_grau_media_comite_esc()*40  ) + (  $this->grau_2*60  ) ) / 100 , 2 );
    }
    public function get_grau_media_comite_esc()
    {
        return number_format( ($this->media_ci_comite+$this->capa->get_grau_escolaridade())/2 , 2 );
    }

    /**
     * Verifica se o usuсrio logado щ o responsсvel pelo comite
     */
    public function is_responsavel_comite()
    {
        $ret = false;
        $responsavel = "";
        foreach($this->capa->comite as $integrante)
        {
        	if(isset($integrante))
        	{
	            if($integrante->get_cd_usuario_avaliador()==$this->cd_usuario_logado)
	            {
	                $responsavel = $integrante->get_fl_responsavel();
	                break;
	            }
        	}
        }
        return ($responsavel=="S");
    }

    public function todo_comite_avaliou()
    {
        $contador=0;
        // Para cada integrante do comite verifica se tem uma avaliaчуo correspondente

        foreach( $this->capa->comite as $integrante )
        {
            if( !$integrante==null )
            {
                foreach( $this->capa->avaliacoes as $avaliacao )
                {
                	if(isset($avaliacao))
                	{
	                    if(   $integrante->get_cd_usuario_avaliador()==$avaliacao->get_cd_usuario_avaliador()
	                       && $avaliacao->get_dt_conclusao()!="")
	                    {
	                        $contador++;
	                    }
                	}
                }
            }
        }

        // retorna verdadeiro se o nro de integrantes for o mesmo que o nњmero de avaliaчѕes encontradas
        if($contador==sizeof($this->capa->comite))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}#end_class
?>