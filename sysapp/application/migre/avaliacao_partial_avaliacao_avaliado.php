<? header("Content-Type: text/html; charset=iso-8859-1"); ?>
<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');

    include_once('inc/ePrev.Service.Projetos.php');

    class avaliacao_partial_avaliacao_avaliado
    {   #begin_class
		public $escolaridade_escolhida="";
        private $db;
        private $command;
        private $id;
        private $cd_usuario_logado;
        private $usuario_avaliado;
        private $usuario_avaliador;
        private $row;
        private $avaliacao=null;

        private $grau_ci;
        private $val_ci;
        private $grau_esc;
        private $val_esc;
        private $grau_ce;
        private $val_ce;
        private $grau_resp;
        private $val_resp;

        // collections
        private $result_comp_inst;
        private $result_escolaridade;
        private $result_comp_espec;
        private $result_resp;

        private $capas;
        public $capa;

        private $service;

        function avaliacao_partial_avaliacao_avaliado( $_db )
        {
            $this->db = $_db;
            $this->service = new service_projetos($_db);
            $this->avaliacao = new entity_projetos_avaliacao_extended();
            $this->requestParams();
            if ($this->command=="view") 
            {
				$this->load();
			}
            if ($this->command=="edit") 
            {
                $this->load();
            }
            if ($this->command=="ajax_espectativas") 
            {
                $this->ajax_espectativas_render();
		    	exit;
            }
            if ($this->command=="ajax_espectativas__save") 
            {
                $this->ajax_espectativas__save();
                exit;
            }
            if ($this->command=="ajax_espectativas__delete") 
            {
                $this->ajax_espectativas__delete();
                exit;
            }
            if ($this->command=="ajax_espectativas__lista") 
            {
                $this->load();
            	$this->espectativas_lista_render();
            	exit;
            }
            if( ! $this->pode_ver_essa_pagina( $this->capa ) )
            {
            	echo 'Sem permiss�o para acessar essa p�gina.';
            	exit;
            }
        }

	    private function pode_ver_essa_pagina( entity_projetos_avaliacao_capa_extended $capa )
	    {
	    	$sr = false;
			// se usuario logado � o pr�prio avaliado
	    	if( $this->cd_usuario_logado==$capa->get_cd_usuario_avaliado() )
	    	{
	    		$sr = true;
	    	}
			else
			{
		    	// se usuario logado � superior na avalia��o
		    	if( $this->cd_usuario_logado==$capa->get_cd_usuario_avaliador() )
	    		{
		    		$sr = true;
	    		}
	    		else
	    		{
	    			$sr = false;
	    			// se usu�rio logado � integrante do comite da avalia��o
	    			$integrante = new entity_projetos_avaliacao_comite_extended();
	    			foreach( $capa->comite as $integrante )
	    			{
	    				if($integrante)
	    				{
		    				if( $integrante->get_cd_usuario_avaliador()==$this->cd_usuario_logado )
		    				{
		    					$sr = true;
		    					break;
		    				}
	    				}
	    			}
	    		}
			}
	    	return $sr;
	    }

        function __destruct()
        {
            $this->db = null;
        }
        public function get_grau_ci()
        {
            return $this->grau_ci;
        }
        public function get_val_ci()
        {
            return $this->val_ci;
        }
        public function get_grau_esc()
        {
            return $this->grau_esc;
        }
        public function get_val_esc()
        {
            return $this->val_esc;
        }
        public function get_grau_ce()
        {
            return $this->grau_ce;
        }
        public function get_val_ce()
        {
            return $this->val_ce;
        }
        public function get_grau_resp()
        {
            return $this->grau_resp;
        }
        public function get_val_resp()
        {
            return $this->val_resp;
        }
        public function get_result_comp_inst()
        {
            return $this->result_comp_inst;
        }
        public function get_result_escolaridade()
        {
            return $this->result_escolaridade;
        }
        public function get_result_comp_espec()
        {
            return $this->result_comp_espec;
        }
        public function get_result_resp()
        {
            return $this->result_resp;
        }
        public function get_command()
        {
            return $this->command;
        }
        public function get_cd_avaliacao_capa()
        {
            return $this->id;
        }		
		
        function requestParams()
        {
            if(isset($_REQUEST["cd_avaliacao_selected_hidden"]))
        		$this->id = $_REQUEST["cd_avaliacao_selected_hidden"];
            if(isset($_REQUEST["ajax_command_hidden"]))
        		$this->command = utf8_decode( $_REQUEST["ajax_command_hidden"] );
        	
            $this->cd_usuario_logado = $_SESSION["Z"];
        }
        function load()
        {
            $service = new service_projetos($this->db);

            $filtro = new entity_projetos_avaliacao_capa_extended();
            $filtro->set_cd_avaliacao_capa( $this->id );
            $this->capas = $service->avaliacao_capa_FetchAll( $filtro );
            $this->capa = $this->capas[0];
            
            $find_aval=false;
            foreach($this->capa->avaliacoes as $avaliacao)
            {
                // encontra avalia��o do tipo 'A' que indica que foi realizada pelo Avaliado
                if (!is_null($avaliacao))
                {
                    if ($avaliacao->get_tipo()=="A")
                    {
                        $this->avaliacao = $avaliacao;
                        $find_aval=true;
                        break;
                    }
				}
            }
            $filtro = null;

            if ($find_aval==false)
            {
                // deve incluir avalia��o antes de prosseguir
				$this->avaliacao = new entity_projetos_avaliacao_extended();

                $this->avaliacao->set_cd_usuario_avaliador( $this->cd_usuario_logado );
                $this->avaliacao->set_cd_avaliacao_capa( $this->id );
                $this->avaliacao->set_tipo( "A" );

                $service->avaliacao_Insert( $this->avaliacao );
			}

            $service = null;

            $dal = new DBConnection();
            $dal->loadConnection( $this->db );

            // load grau_ci
            $dal->createQuery("

                SELECT SUM(grau) AS grau_ci 
                  FROM projetos.avaliacoes_comp_inst 
                 WHERE cd_avaliacao = {cd_avaliacao} 

            ");
            $dal->setAttribute("{cd_avaliacao}", $this->avaliacao->get_cd_avaliacao());
            $result_grau_ci = $dal->getResultset();
            $row_grau_ci = pg_fetch_array($result_grau_ci);
            $this->grau_ci = $row_grau_ci["grau_ci"];

            // load val_ci
            $dal->createQuery("

                SELECT COUNT(*) AS ocorr_ci 
                  FROM projetos.avaliacoes_comp_inst 
                 WHERE cd_avaliacao = {cd_avaliacao} 

            ");
            $dal->setAttribute("{cd_avaliacao}", $this->avaliacao->get_cd_avaliacao());
            $result_val_ci = $dal->getResultset();
            $row_val_ci = pg_fetch_array($result_val_ci);
            if ($row_val_ci["ocorr_ci"] == "0") 
            {   
                $this->val_ci = "N�o realizada!";
            }
            else 
            {
                $this->val_ci = number_format(($this->grau_ci / $row_val_ci['ocorr_ci']),2);
            }

            if ($this->capa->get_grau_escolaridade() == "" || $this->capa->get_grau_escolaridade()=="0") 
            {   
                $this->val_esc = "N�o realizada!";
            }
            else 
            {
                $this->val_esc = $this->capa->get_grau_escolaridade();
            }
            
            // load grau_ce
            $dal->createQuery("

                SELECT SUM(grau) AS grau_ce 
                  FROM projetos.avaliacoes_comp_espec 
                 WHERE cd_avaliacao = {cd_avaliacao} 

            ");
            $dal->setAttribute("{cd_avaliacao}", $this->avaliacao->get_cd_avaliacao());
            $result_grau_ce = $dal->getResultset();
            $row_grau_ce = pg_fetch_array($result_grau_ce);
            $this->grau_ce = $row_grau_ce["grau_ce"];

            // load val_ce
            $dal->createQuery("

                SELECT COUNT(*) AS ocorr_ce
                  FROM projetos.avaliacoes_comp_espec
                 WHERE cd_avaliacao = {cd_avaliacao} 

            ");
            $dal->setAttribute( "{cd_avaliacao}", $this->avaliacao->get_cd_avaliacao() );
            $result_val_ce = $dal->getResultset();
            $row_val_ce = pg_fetch_array($result_val_ce);
            if ($row_val_ce["ocorr_ce"] == "0") 
            {   
                $this->val_ce = "N�o realizada!";
            }
            else 
            {
                $this->val_ce = number_format( ($this->grau_ce / $row_val_ce['ocorr_ce']), 2 );
            }

            // load grau_resp
            $dal->createQuery("

                SELECT SUM(grau) AS grau_resp
                  FROM projetos.avaliacoes_responsabilidades 
                 WHERE cd_avaliacao = {cd_avaliacao} 

            ");
            $dal->setAttribute("{cd_avaliacao}", $this->avaliacao->get_cd_avaliacao());
            $result_grau_resp = $dal->getResultset();
            $row_grau_resp = pg_fetch_array($result_grau_resp);
            $this->grau_resp = $row_grau_resp["grau_resp"];

            // load val_resp
            $dal->createQuery("

                SELECT COUNT(*) AS ocorr_resp
                  FROM projetos.avaliacoes_responsabilidades
                 WHERE cd_avaliacao = {cd_avaliacao}

            ");
            $dal->setAttribute( "{cd_avaliacao}", $this->avaliacao->get_cd_avaliacao() );
            $result_val_resp = $dal->getResultset();
            $row_val_resp = pg_fetch_array($result_val_resp);
            if ($row_val_resp["ocorr_resp"] == "0") 
            {   
                $this->val_resp = "N�o realizada!";
            }
            else 
            {
                $this->val_resp = number_format( ($this->grau_resp / $row_val_resp['ocorr_resp']), 2 );
            }

            // load collection comp_inst
            $dal->createQuery("

                SELECT ci.cd_comp_inst AS cd_comp_inst
                     , nome_comp_inst
                     , desc_comp_inst
                  FROM projetos.comp_inst ci
                     , projetos.cargos_comp_inst cci
                 WHERE cci.cd_comp_inst = ci.cd_comp_inst 
                   AND cci.cd_cargo = {cd_cargo}
              ORDER BY nome_comp_inst

            ");
            $dal->setAttribute( "{cd_cargo}", $this->capa->get_cd_cargo() );
            $this->result_comp_inst = $dal->getResultset();

            // load collection escolaridade
            $dal->createQuery("

                SELECT 
					f.cd_escolaridade
                     , grau_percentual
                     , nivel
					 , nome_escolaridade
                     , e.ordem
                  FROM 
					projetos.familias_escolaridades f
                     , projetos.cargos c
                     , projetos.escolaridade e
                 WHERE 
					c.cd_cargo = {cd_cargo}
                   AND c.cd_familia = f.cd_familia 
                   AND f.cd_escolaridade = e.cd_escolaridade 
              ORDER BY grau_percentual DESC

            ");
            $dal->setAttribute( "{cd_cargo}", $this->capa->get_cd_cargo() );
            $this->result_escolaridade = $dal->getResultset();

            // load collection comp_espec
            $dal->createQuery("

                SELECT ce.cd_comp_espec
                     , nome_comp_espec
                     , desc_comp_espec 
                  FROM projetos.comp_espec ce
                     , projetos.cargos_comp_espec cce
                 WHERE cce.cd_comp_espec = ce.cd_comp_espec 
				   AND cce.cd_cargo = {cd_cargo}
             ORDER BY nome_comp_espec

            ");
            $dal->setAttribute( "{cd_cargo}", $this->capa->get_cd_cargo() );
            $this->result_comp_espec = $dal->getResultset();

            // load collection responsabilidades
            $dal->createQuery("

                SELECT r.cd_responsabilidade as cd_responsabilidade
                     , nome_responsabilidade
                     , desc_responsabilidade 
                  FROM projetos.responsabilidades r
                     , projetos.cargos_responsabilidades cr
                 WHERE cr.cd_responsabilidade = r.cd_responsabilidade 
                   AND cr.cd_cargo = {cd_cargo}
              ORDER BY nome_responsabilidade

            ");
            $dal->setAttribute( "{cd_cargo}", $this->capa->get_cd_cargo() );
            $this->result_resp = $dal->getResultset();

            $dal = null;

        }

		
        public function get_id()
        {
            return $this->id;
        }

        public function get_row()
        {
            return $this->row;
        }

        public function get_cd_usuario_logado()
        {
            return $this->cd_usuario_logado;
        }

        public function get_nome_usuario_logado()
        {
            if ($this->nome_usuario_logado=="") {
                $nome = "";
                $dal = new DBConnection();
                $dal->loadConnection($this->db);
                $dal->createQuery( "
                    SELECT nome 
                      FROM projetos.usuarios_controledi  
                      WHERE codigo = {codigo} 
                " );
                $dal->setAttribute( "{codigo}", $this->cd_usuario_logado );
                $result = $dal->getResultset();
                $row = pg_fetch_array($result);
                $nome = $row["nome"];
                $row = null;
                $dal = null;
                $this->nome_usuario_logado = $nome;
			}
            return $this->nome_usuario_logado;
        }

        public function get_avaliacao()
        {
            return $this->avaliacao;
        }

        public function grau_escolaridade_is_disabled($value_1, $value_2)
        {
            $ret = "";
            if($value_1 == $value_2)
            {
                $ret = "";
            }
            else
            {
                $ret = "disabled";
            }
            return $ret;
        }
        public function grau_escolaridade_valor($value_1, $value_2, $percentual)
        {
            $ret = "";
            if ($value_1 == $value_2)
            {
                $ret = number_format($percentual, 0, '.' ,'.');
            }
            return $ret;
        }

        public function grau_escolaridade_is_checked($nivel_1, $nivel_2, $grau_escolaridade, $percentual, $cd_escolaridade, $ordem)
        {
            $ret="";

            //if($this->capa->get_grau_escolaridade()!="" && $this->capa->get_grau_escolaridade()!=0)
            if($this->capa->get_status()!="A" and $this->capa->get_status()!="F")
            {
	            if ($grau_escolaridade == $percentual && $nivel_1 == $nivel_2)
	            {
	            	$this->escolaridade_escolhida = $nivel_1;
	                $ret = "checked";
	            }
            }
            else
            {
            	// buscar a escolaridade na tabela usuario_matriz
            	if($nivel_1==$nivel_2 && $this->capa->avaliado->usuario_matriz->cd_escolaridade==$cd_escolaridade )
            	{
            		$this->escolaridade_escolhida = $nivel_1;
            		$ret = "checked";
            	}
            	else
            	{
            		// ordem da escolaridade configurada em usuario_matriz para verificar
            		// se a escolaridade do usu�rio � superior a m�xima
            		$ordem_configurado = $this->get_ordem_escolaridade( $this->capa->avaliado->usuario_matriz->cd_escolaridade );

            		// echo $this->escolaridade_escolhida . "<br />";
            		if( ($this->escolaridade_escolhida=="") && intval($ordem_configurado)>intval($ordem) && $nivel_1==$nivel_2 )
            		{
            			$this->escolaridade_escolhida = $nivel_1;
            			$ret = "checked";
            		}
            	}
            }

            return $ret;
        }

        private function get_ordem_escolaridade($cd_escolaridade)
        {
        	$dal = new DBConnection();
            $dal->loadConnection( $this->db );
            $dal->createQuery("

                SELECT ordem
                  FROM projetos.escolaridade
                 WHERE cd_escolaridade = {cd_escolaridade} 

            ");
            $dal->setAttribute( "{cd_escolaridade}" , $cd_escolaridade );
            return $dal->getScalar();
        }

        public function get_escolaridade($cd_escolaridade)
        {
        	$dal = new DBConnection();
            $dal->loadConnection( $this->db );
            $dal->createQuery("

                SELECT *
                  FROM projetos.escolaridade
                 WHERE cd_escolaridade = {cd_escolaridade} 

            ");
            $dal->setAttribute( "{cd_escolaridade}" , $cd_escolaridade );
            
            $result = $dal->getResultset();
            if($row = pg_fetch_array($result))
            {
            	$ret = array( 
            				  'cd_escolaridade'=>$row['cd_escolaridade']
            				, 'nome_escolaridade'=>$row['nome_escolaridade']
            				, 'desc_escolaridade'=>$row['desc_escolaridade'] 
            				);
            }
            
            return $ret;
        }

        public function comp_inst_checked($cd_comp_inst, $value)
        {
            $dal = new DBConnection();
            $dal->loadConnection( $this->db );
            $dal->createQuery("
								SELECT ci.grau
								  FROM projetos.avaliacoes_comp_inst ci
								 WHERE ci.cd_avaliacao = {cd_avaliacao} 
								   AND ci.cd_comp_inst = {cd_comp_inst}
							");
            $dal->setAttribute( "{cd_avaliacao}" , $this->avaliacao->get_cd_avaliacao() );
            $dal->setAttribute( "{cd_comp_inst}" , $cd_comp_inst );
            $result = $dal->getResultset();

            if ($reg2 = pg_fetch_array($result))
            {
                if ($reg2['grau'] == $value)
                {
                    return "checked";
                }
            }
            else 
            {
                //$tpl->assign('cor_fundo', '#F0E0C7');
            } 
        }
		
		public function comp_inst_anterior($cd_comp_inst)
		{
			$dal = new DBConnection();
			$dal->loadConnection($this->db);
			$dal->createQuery("
								SELECT ci.grau,
									   projetos.avaliacao_item_resposta_anterior(a.cd_avaliacao_capa, ci.cd_comp_inst, 'CI') AS grau_anterior
								  FROM projetos.avaliacoes_comp_inst ci
								  JOIN projetos.avaliacao a
									ON a.cd_avaliacao = ci.cd_avaliacao
								 WHERE ci.cd_avaliacao = {cd_avaliacao} 
								   AND ci.cd_comp_inst = {cd_comp_inst}
							 ");
			$dal->setAttribute( "{cd_avaliacao}" , $this->avaliacao->get_cd_avaliacao() );
			$dal->setAttribute( "{cd_comp_inst}" , $cd_comp_inst );
			$result = $dal->getResultset();

			return pg_fetch_array($result);
		}	

        public function comp_espec_checked($cd_comp_espec, $value)
        {
            $dal = new DBConnection();
            $dal->loadConnection( $this->db );
            $dal->createQuery("

                SELECT grau 
                  FROM projetos.avaliacoes_comp_espec
                 WHERE cd_avaliacao = {cd_avaliacao} 
                   AND cd_comp_espec = {cd_comp_espec}

            ");
            $dal->setAttribute( "{cd_avaliacao}" , $this->avaliacao->get_cd_avaliacao() );
            $dal->setAttribute( "{cd_comp_espec}" , $cd_comp_espec );
            $result = $dal->getResultset();

            if ($reg2 = pg_fetch_array($result))
            {
                if ($reg2['grau'] == $value)
                {
                    return "checked";
                }
            }
            else 
            {
                //$tpl->assign('cor_fundo', '#F0E0C7');
            } 
        }
		
		public function comp_espec_anterior($cd_comp_espec)
		{
			$dal = new DBConnection();
			$dal->loadConnection($this->db);
			$dal->createQuery("
								SELECT ce.grau,
									   projetos.avaliacao_item_resposta_anterior(a.cd_avaliacao_capa, ce.cd_comp_espec, 'CE') AS grau_anterior
								  FROM projetos.avaliacoes_comp_espec ce
								  JOIN projetos.avaliacao a
									ON a.cd_avaliacao = ce.cd_avaliacao
								 WHERE ce.cd_avaliacao = {cd_avaliacao} 
								   AND ce.cd_comp_espec = {cd_comp_espec}
							 ");
			$dal->setAttribute( "{cd_avaliacao}" , $this->avaliacao->get_cd_avaliacao() );
			$dal->setAttribute( "{cd_comp_espec}" , $cd_comp_espec );
			$result = $dal->getResultset();
			
			return pg_fetch_array($result);
		}		

        public function responsabilidade_checked($cd_responsabilidade, $value)
        {
            $dal = new DBConnection();
            $dal->loadConnection( $this->db );
            $dal->createQuery("

                SELECT grau 
                  FROM projetos.avaliacoes_responsabilidades
                 WHERE cd_avaliacao = {cd_avaliacao} 
                   AND cd_responsabilidade = {cd_responsabilidade}

            ");
            $dal->setAttribute( "{cd_avaliacao}" , $this->avaliacao->get_cd_avaliacao() );
            $dal->setAttribute( "{cd_responsabilidade}" , $cd_responsabilidade );
            $result = $dal->getResultset();

            if ($reg2 = pg_fetch_array($result))
            {
                if ($reg2['grau'] == $value)
                {
                    return "checked";
                }
            }
            else 
            {
                //$tpl->assign('cor_fundo', '#F0E0C7');
            } 
        }
		
		public function responsabilidade_anterior($cd_responsabilidade)
		{
			$dal = new DBConnection();
			$dal->loadConnection($this->db);
			$dal->createQuery("
								SELECT r.grau,
									   projetos.avaliacao_item_resposta_anterior(a.cd_avaliacao_capa, r.cd_responsabilidade, 'RE') AS grau_anterior
								  FROM projetos.avaliacoes_responsabilidades r
								  JOIN projetos.avaliacao a
									ON a.cd_avaliacao = r.cd_avaliacao
								 WHERE r.cd_avaliacao = {cd_avaliacao} 
								   AND r.cd_responsabilidade = {cd_responsabilidade}
							 ");
			$dal->setAttribute( "{cd_avaliacao}" , $this->avaliacao->get_cd_avaliacao() );
			$dal->setAttribute( "{cd_responsabilidade}" , $cd_responsabilidade );
			$result = $dal->getResultset();
			
			return pg_fetch_array($result);
		}			
        
        public function is_avaliado()
        {
            $ret = (

                ($this->capa->get_status()=="A" && $this->capa->get_cd_usuario_avaliado()==$this->cd_usuario_logado)

            );
            return $ret;
        }
        
        public function is_avaliador()
        {
            $ret = (

                   $this->capa->get_status()=="F" 
                && $this->capa->get_cd_usuario_avaliador()==$this->cd_usuario_logado
                && $this->avaliador_possui_avaliacao()==false

            );
            return $ret;
        }

        public function avaliador_possui_avaliacao()
        {
            $ret = false;
            foreach( $this->capa->avaliacoes as $avaliacao )
            {
                if($avaliacao->get_cd_usuario_avaliador()==$this->cd_usuario_logado)
                {
                    $ret = true;
                    break;
                }
            }
            return $ret;
        }
        
        public function texto_conceito( $codigo )
        {
            $service = new service_projetos($this->db);
            $e = new entity_public_listas();
            $e->set_codigo( $codigo );
            $service->public_listas_load_by_pk($e);
            #return utf8_decode($e->get_descricao()); //'teste de texto de conceito';
            return ($e->get_descricao()); //'teste de texto de conceito';
        }
        
        public function pode_promover_horizontalmente()
        {
            // para esse ano n�o ter� promo��o horizontal
            // busca avalia��es dos dois anos anteriores e verifica se alguma delas resultou em promo��o.
            $service = new service_projetos($this->db);
    
            // verifica se existe avalia��o um ano atras
            $filter = new entity_projetos_avaliacao_capa_extended();
            $filter->set_dt_periodo( date('Y')-1 );
            $filter->set_cd_usuario_avaliado( $this->capa->get_cd_usuario_avaliado() );
            $avals = $service->avaliacao_capa_FetchAll( $filter );
            $aval = $avals[0];
            if(!is_null($aval))
            {
                // aqui indica que existe avaliacao a 1 ano atras para o avaliado
    
                // verifica se existe avalia��o dois anos atras
                $filter = new entity_projetos_avaliacao_capa_extended();
                $filter->set_dt_periodo( date('Y')-2 );
                $filter->set_cd_usuario_avaliado( $this->capa->get_cd_usuario_avaliado() );
                $avals = $service->avaliacao_capa_FetchAll( $filter );
                $aval = $avals[0];
                if(!is_null($aval))
                {
                    // aqui indica que existe avaliacao a 2 anos atras para o avaliado
                }
                else
                {
                    $return = false;
                    //echo( 'nao possui avalia��o de dois anos atras' );
                }
            }
            else
            {
                $return = false;
                //echo( 'nao possui avalia��o de um ano atras' );
            }
    
            //return $return;
            return false;
        }
        
        public function pode_promover_verticalmente()
        {
            // para esse ano n�o ter� promo��o horizontal
            // busca avalia��es dos dois anos anteriores e verifica se alguma delas resultou em promo��o.
            $service = new service_projetos($this->db);
    
            // verifica se existe avalia��o um ano atras
            $filter = new entity_projetos_avaliacao_capa_extended();
            $filter->set_dt_periodo( date('Y')-1 );
            $filter->set_cd_usuario_avaliado( $this->capa->get_cd_usuario_avaliado() );
            $avals = $service->avaliacao_capa_FetchAll( $filter );
            $aval = $avals[0];
            if(!is_null($aval))
            {
                // aqui indica que existe avaliacao a 1 ano atras para o avaliado
    
                // verifica se existe avalia��o dois anos atras
                $filter = new entity_projetos_avaliacao_capa_extended();
                $filter->set_dt_periodo( date('Y')-2 );
                $filter->set_cd_usuario_avaliado( $this->capa->get_cd_usuario_avaliado() );
                $avals = $service->avaliacao_capa_FetchAll( $filter );
                $aval = $avals[0];
                if(!is_null($aval))
                {
                    // aqui indica que existe avaliacao a 2 anos atras para o avaliado
                }
                else
                {
                    $return = false;
                    //echo( 'nao possui avalia��o de dois anos atras' );
                }
            }
            else
            {
                $return = false;
                //echo( 'nao possui avalia��o de um ano atras' );
            }
    
            //return $return;
            return false;
        }

        /**
         * O processo de avalia��o para ser completo precisa atender a uma regra definida em 10/6/2008 pelo Gilberto:
         * O Avaliado deve ter sido admitido antes do fechamento do processo de avalia��o do ano anterior,
         * caso contr�rio ele s� poder� preencher as expectativas.
         * ---- REGRA ALTERADA EM 14/07/2009 POR gsoares
         */
        public function is_complete_proccess()
        {
            $year = date('Y')-1;

            //////
            // 
            // OS 21982 de 14/07/2009 02:22 por gsoares
            // Somente abrir avalia��o para contratados depois de 30/06 do ano anterior
            // aos demais abrir apenas espectativas!

            $data_fechamento = '01/07/'.$year;

            // C�digo abaixo resgata a data de encerramento do per�odo anterior para decidir sobre as novas avalia��es
            // essa regra foi desativada pela OS 21982
            // $data_fechamento = $this->service->projetos__avaliacao_controle__get_dt_fechamento( $year );
            //
            /////

            $a_aux = explode( '/',  $this->capa->avaliado->usuario_matriz->dt_admissao );
            if( sizeof($a_aux)==3 )
            {
            	$dt_admissao = $a_aux[2] . $a_aux[1] . $a_aux[0];
	            $a_aux = explode( '/',  $data_fechamento );
	            $dt_fechamento = $a_aux[2] . $a_aux[1] . $a_aux[0];
	            return ( $dt_admissao < $dt_fechamento );
            }
            else
            {
            	return false;
            }
        }

        public function espectativas_lista_render()
        {
			$aspecto = new entity_projetos_avaliacao_aspecto();
			$avaliacao = new entity_projetos_avaliacao();
			$avaliacao = $this->get_avaliacao();
        	$output = '
			        		<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
								<thead>
									<tr>
									<td><b>Expectativas</b></td>
									<td></td>
									</tr>
								</thead>
								<tbody>

			';

        	if(isset($avaliacao->aspectos)) {
				foreach( $avaliacao->aspectos as $aspecto ){ 
	        		$output .= '
									<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
										<td>
											<b>Compet�ncia: </b>' . $aspecto->aspecto . '<br />
											<b>Resultado esperado: </b>' . $aspecto->resultado_esperado . '<br />
											<b>A��es de apoio: </b>' . $aspecto->acao . '
										</td>
										<td style="width:50px" align="right">
					';
					if($this->is_avaliado()) {
						$output .= '<nobr>
											<a href="javascript:void(0)" onclick="thisPage.aspecto_editar__Click(this);" registroId="' . $aspecto->cd_avaliacao_aspecto . '" url="avaliacao_partial_avaliacao_avaliado.php"><img src="img/avaliacao_manutencao.png" border="0" /></a>
											<a href="javascript:void(0)" sender="avaliado" onclick="thisPage.aspecto_deletar__Click(this);" registroId="' . $aspecto->cd_avaliacao_aspecto . '" url="avaliacao_partial_avaliacao_avaliado.php"><img src="img/avaliacao_deletar.png" border="0" /></a>
									</nobr>';
					}
					$output .= '
										</td>
									</tr>
		        	';
				}
			}
        	$output .= '
								</tbody>
							</table>
        	';
        	
        	echo $output;
        }
        
        public function ajax_espectativas_render()
        {
        	$aspecto = new entity_projetos_avaliacao_aspecto();
        	if( isset($_REQUEST["id"]) )
        	{
        		$aspecto->cd_avaliacao_aspecto = $_REQUEST["id"];
        		$this->service->avaliacao_aspecto__load_by_pk( $aspecto );
        	}
        	else
        	{
        		$aspecto->cd_avaliacao_aspecto = 0;
        		$aspecto->cd_avaliacao = $_REQUEST["cd_avaliacao"];
        	}
        	$output = '

		        <table>
			    	<tr style="display:none;">
			    		<td>
			    			<input id="cd_avaliacao_aspecto__hidden" type="text" value="' . $aspecto->cd_avaliacao_aspecto . '" />
			    			<input id="cd_avaliacao__hidden" type="text" value="' . $aspecto->cd_avaliacao . '" />
		    			</td>
	    			</tr>
			    	<tr><td>Compet�ncias</td></tr>
			    	<tr><td><textarea id="aspecto__text" style="width:600px;height:100px;">' . $aspecto->aspecto . '</textarea></td></tr>
			    	<tr><td>Resultado Esperado</td></tr>
			    	<tr><td><textarea id="resultado__text" style="width:600px;height:100px;">' . $aspecto->resultado_esperado . '</textarea></td></tr>
			    	<tr><td>A��es de Apoio</td></tr>
			    	<tr><td><textarea id="acao__text" style="width:600px;height:100px;">' . $aspecto->acao . '</textarea></td></tr>
		    	</table>
		    	<br />
				
			<a href="javascript:void(0)" onclick="thisPage.espectativa_salvar__Click(this);" url="avaliacao_partial_avaliacao_avaliado.php"><img src="img/avaliacao_salvar.png" border="0" /></a>
			<a href="javascript:void(0)" onclick="thisPage.espectativa__Hide(this);"><img src="img/avaliacao_voltar.png" border="0" /></a>				
				

        	
        	';
			
			/*
		    	<input type="button" value="Salvar" class="botao" onclick="thisPage.espectativa_salvar__Click(this);" url="avaliacao_partial_avaliacao_avaliado.php" />
		    	<input type="button" value="Voltar" class="botao" onclick="thisPage.espectativa__Hide(this);" />			
			*/
        	
        	echo $output;
        }
        
        public function ajax_espectativas__save()
        {
        	$entidade = new entity_projetos_avaliacao_aspecto();
        	
        	$entidade->cd_avaliacao_aspecto = $_REQUEST['id'];
        	$entidade->cd_avaliacao = $_REQUEST['cd_avaliacao'];
        	$entidade->aspecto = utf8_decode( $_REQUEST['aspecto'] );
        	$entidade->resultado_esperado = utf8_decode( $_REQUEST['resultado_esperado'] );
        	$entidade->acao = utf8_decode( $_REQUEST['acao'] );
        	
        	if($entidade->cd_avaliacao_aspecto=="0")
        	{
	        	$ret = $this->service->avaliacao_aspecto__insert($entidade);
        	}
        	else
        	{
        		$ret = $this->service->avaliacao_aspecto__update($entidade);
        	}

        	if($ret)
        		echo 'As Expectativas foram salvas com sucesso!<br /><br />
        		
				<a href="javascript:void(0)" onclick="thisPage.espectativa__Hide();  espectativa_carregar(\'avaliacao_partial_avaliacao_avaliado.php\');"><img src="img/avaliacao_voltar.png" border="0" /></a>
        		|
				<a href="javascript:void(0);" onclick="thisPage.nova_espectativa__Click(this);" url="avaliacao_partial_avaliacao_superior.php"><img src="img/avaliacao_nova_expectativa.png" border="0" /></a>
        		
        		';
        	else
        		echo 'N�o foi poss�vel gravar as expectativas!';
        }
        
        public function ajax_espectativas__delete()
        {
        	$ret = $this->service->avaliacao_aspecto__delete( $_REQUEST['id'] );
        	
        	if($ret)
        		echo 'true';
        	else
        		echo 'false';
        }
        
    } #end_class

    $esta = new avaliacao_partial_avaliacao_avaliado( $db );
    if($esta->get_command()=="ajax_espectativas")
    {
    	exit;
    }
    if($esta->get_command()=="ajax_espectativas__save")
    {
    	exit;
    }
    if($esta->get_command()=="ajax_espectativas__delete")
    {
    	exit;
    }
    if($esta->get_command()=="ajax_espectativas__lista")
    {
    	exit;
    }
?>
    <div id="message_panel"></div>    
	
	<?php
	
		function get_avaliacao_superior($esta)
        {
			global $db;
			
			$qr_sql = "
						SELECT cd_avaliacao, aspectos, resultado, acoes, cd_usuario_avaliador, 
                               tipo, dt_criacao, cd_avaliacao_capa, dt_conclusao
						  FROM projetos.avaliacao
						 WHERE cd_avaliacao_capa = ".intval($esta->capa->get_cd_avaliacao_capa())."
						   AND tipo = 'S'
					   ";
            $ob_data = pg_query($db, $qr_sql);
			$ar_reg_sup = pg_fetch_array($ob_data);
			return intval($ar_reg_sup['cd_avaliacao']);
        }		
	
		#echo $esta->capa->get_cd_avaliacao_capa();
		#echo "<BR><BR>";
		#echo get_avaliacao_superior($esta);
	?>
	

    <input type="hidden" name="status_hidden" id="status_hidden" value="<?= $esta->capa->get_status() ?>">
    <input type="hidden" name="status_original_hidden" id="status_original_hidden" value="<?= $esta->capa->get_status() ?>">
    <input type="hidden" name="cd_avaliacao_hidden" id="cd_avaliacao_hidden" value="<?= $esta->get_avaliacao()->get_cd_avaliacao() ?>">

    <!-- BEGIN: avaliacao__div -->
    <div id="avaliacao__div">

    <table cellpadding="0" cellpadding="0" align="center" style="width:700px">
    <tr>
        <th>
            <table cellpadding="0" cellspacing="0" style="width:690px" border="0">
            <tr>
            <td align="right" valign="middle">
                <? if($esta->is_avaliado()) { ?>
                    <a href="javascript:void(0)"><img id="save_image"
                               src="img/avaliacao_salvar_continuar.png" 
                               border="0" 
                               onclick="thisPage.save_and_continue_Click(this);" 
                               urlPartial="avaliacao_partial_avaliacao_avaliado_save.php"
                               contentPartial="message_panel"
                               /></a>
                <? } ?>
                <? if($esta->is_avaliador()) { ?>
					<!--
                    <a  href="javascript:void(0)"
                        id="save_by_avaliador_image"
                        onclick="thisPage.insert_and_continue_by_avaliador_Click(this);"
                        title="Salvar e Continuar"
                    ><img src="img/avaliacao_salvar_continuar.png" border="0" alt="Salvar e Continuar" /></a>
					-->
                    <a href="javascript:void(0)"
                        id="save_by_avaliador_image"
                        onclick="thisPage.insert_and_continue_by_avaliador_Click(this);"
                        title="Salvar e Continuar"
                    ><img id="save_image" src="img/avaliacao_salvar_continuar.png" border="0" /></a>					
					
                <? } ?>
                <a href="avaliacao_print.php?id=<?= $esta->get_id(); ?>&ida=<?= $esta->get_avaliacao()->get_cd_avaliacao(); ?>" target="_blank"><img id="print_image"
                               src="img/print_autoavaliacao.png" 
                               border="0" 
                               /></a>

				<?php
				if(intval(get_avaliacao_superior($esta)) > 0)
				{
				?>
                <a href="avaliacao_print.php?id=<?= $esta->get_id(); ?>&ida=<?= intval(get_avaliacao_superior($esta)); ?>" target="_blank"><img id="print_image"
                               src="img/print_avaliacao_superior.png" 
                               border="0" 
                               /></a>							   
				<?php
				}
				?>
            </td>
            </tr>
            </table>
        </th>
    </tr>

    <tr>
        <td>
            <table style="width:100%" cellpadding="0" cellpadding="0">

<!-- START QUADRO RESUMO -->

                      <tr> 
                        <td colspan="7" bgcolor="#F4F4F4"><table width="100%" border="0" cellspacing="2" cellpadding="3">
                            <tr> 
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Avalia��o N�m: </font></td>
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><?= $esta->get_avaliacao()->get_cd_avaliacao()?></strong></font></td>
                            </tr>
                            <tr> 
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Avaliador:</font></td>
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><?= $esta->capa->avaliador->get_cd_registro_empregado() ?>
                                - <?= $esta->capa->avaliador->get_nome()?></strong></font></td>
                            </tr>
                            <tr> 
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Avaliado:</font></td>
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><?= $esta->capa->avaliado->get_cd_registro_empregado()?>
                                - <?= $esta->capa->avaliado->get_nome()?></strong></font></td>
                            </tr>
                            <tr> 
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Cargo:</font></td>
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                              <strong><?= $esta->capa->cargo->get_desc_cargo() ?></strong></font></td>
                            </tr>
                            <tr>
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Fam&iacute;lia 
                                do cargo:</font></td>
                              <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><?= $esta->capa->cargo->get_familia()->get_nome_familia()?></strong></font></td>
                            </tr>
                          </table></td>
                      </tr>

                      <tr>
                        <td height="20px"></td>
                      </tr>

                      <? if( $esta->is_complete_proccess() ) : ?>

                      <!-- START BLOCK : CI -->
                      <tr bgcolor="#BFA260"> 
                        <td valign="top"><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Compet&ecirc;ncias 
                          Institucionais </font></strong></td>
                        <td colspan="6" valign="top" align="right"><font color="#FFFFFF" size="3" face="Arial, Helvetica, sans-serif"><strong> 
                          <?/*= $esta->get_val_ci() */?></strong></font></td>
						  <td bgcolor="#FFFFFF"></td>
                      </tr>
                      <!-- END BLOCK : CI -->

                      <tr bgcolor="#BFA260"> 
                        <td valign="top" bgcolor="#F4F4F4"></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CACI') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CACI') ?>' );"><u>A</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CBCI') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CBCI') ?>' );"><u>B</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CCCI') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CCCI') ?>' );"><u>C</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CDCI') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CDCI') ?>' );"><u>D</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CECI') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CECI') ?>' );"><u>E</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CFCI') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CFCI') ?>' );"><u>F</u></a></td>
						<td bgcolor="#FFFFFF"></td>
                      </tr>
		
                      <!-- START BLOCK : comp_inst -->
                      <?php 
						while ($rows_ci = pg_fetch_array($esta->get_result_comp_inst())) 
						{ 
							$id_lb = '_CI_resp_ant_'.$rows_ci["cd_comp_inst"];
					  ?>					  
                          <tr bgcolor="#F4F4F4" 
                            onmouseover="this.className='tb_resultado_selecionado';" 
                            onmouseout="this.className='';" 
                            > 
                            <td valign="top">
                                <a href="javascript:void(0);" 
                                    onclick="thisPage.load_mensagem( '1', '<?=$rows_ci["cd_comp_inst"]?>' )"
                                    ><img src="img/img_descricao.jpg" 
                                        border="0" 
                                        title="Clique para saber mais"
                                        ></a>
                                <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?=$rows_ci["nome_comp_inst"]?></font>
                            </td>
                            <td><input type="radio"
                                name="comp_inst<?=$rows_ci["cd_comp_inst"]?>"  
								onclick="setRespAnterior('<?=$id_lb?>',0)"
                                value="0"
                                <?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 0.0000 ) ?>
                                ></td>
                            <td><input type="radio" 
                                name="comp_inst<?=$rows_ci["cd_comp_inst"]?>" 
								onclick="setRespAnterior('<?=$id_lb?>',20)"
                                value="20" 
                                <?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 20.0000 ) ?> 
                                ></td>
                            <td><input type="radio" 
                                name="comp_inst<?=$rows_ci["cd_comp_inst"]?>" 
								onclick="setRespAnterior('<?=$id_lb?>',40)"
                                value="40" 
                                <?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 40.0000 ) ?>  
                                ></td>
                            <td><input type="radio" 
                                name="comp_inst<?=$rows_ci["cd_comp_inst"]?>" 
								onclick="setRespAnterior('<?=$id_lb?>',60)"
                                value="60" 
                                <?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 60.0000 ) ?> 
                                ></td>
                            <td><input type="radio" 
                                name="comp_inst<?=$rows_ci["cd_comp_inst"]?>" 
								onclick="setRespAnterior('<?=$id_lb?>',80)"
                                value="80" 
                                <?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 80.0000 ) ?>  
                                ></td>
                            <td><input type="radio" 
                                name="comp_inst<?=$rows_ci["cd_comp_inst"]?>" 
								onclick="setRespAnterior('<?=$id_lb?>',100)"
                                value="100" 
                                <?= $esta->comp_inst_checked( $rows_ci["cd_comp_inst"], 100.0000 ) ?>  
                                > 
                            </td>
							<td style="background-color: #FFFFFF; width: 20px; text-align:center; <?php echo ($esta->is_avaliador() ? "" : "display:none;"); ?>">
								<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
								<?php
									if($esta->is_avaliador())
									{
										$ar_ant = $esta->comp_inst_anterior($rows_ci["cd_comp_inst"]);
										echo '<input type="hidden" id="ed'.$id_lb.'" name="ed'.$id_lb.'" value="'.intval($ar_ant['grau_anterior']).'">';
									
										$letra = "";
										if ($ar_ant['grau_anterior'] != "")
										{										
											switch ($ar_ant['grau_anterior'])
											{
												case 0  : $letra = "A"; break;
												case 20 : $letra = "B"; break;
												case 40 : $letra = "C"; break;
												case 60 : $letra = "D"; break;
												case 80 : $letra = "E"; break;
												case 100: $letra = "F"; break;
											}
										}
										echo '<span id="lb'.$id_lb.'" class="'.($ar_ant['grau_anterior'] <> $ar_ant['grau'] ?  'label label-important' : 'label').'">'.$letra."</span>";
									}
								?>
								</font>
							</td>
                          </tr>
                      <? } ?>
                      <!-- END BLOCK : comp_inst -->

                      <tr>
                        <td height="20px"></td>
                      </tr>

                      <!-- START BLOCK : ES -->
                      <tr bgcolor="#BFA260"> 
                        <td valign="top" bgcolor="#BFA260"><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Escolaridade</font></strong></td>
                        <td colspan="6" valign="top" align="right"><font color="#FFFFFF" size="3" face="Arial, Helvetica, sans-serif"><strong><?/*= $esta->get_val_esc() */?></strong></font></td>
                      </tr>
                      <!-- END BLOCK : ES -->

                      <!-- START BLOCK : escolaridade -->
                      <? if($esta->capa->avaliado->usuario_matriz->cd_escolaridade=="") :?>
                      	<tr> 
                      <? else: ?>
                      	<tr style="display:none;"> 
                      <? endif; ?>
                       <?php
                       //ALTERA��O FEITA PARA UM CASO ESPECIFICO EM QUE A PESSOA QUE FOI FAZER A AVALIA��O N�O TINHA A ESCOLARIDAD MINIMA PARA O SEU CARGO
                                if($esta->get_grau_ce() == '0.0000')
                                {
                                    ?><input type="hidden" name="grau_ce_zero" value="0"><?php
                                }
                                else
                                {
                                    ?><input type="hidden" name="grau_ce_zero" value="1"><?php
                                }
                            ?>
                        <td colspan="7">
                        	<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#BFA260">
                            <tr bgcolor="#BFA260"> 
                              <td><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"></font></strong></td>
                              <td align="center"><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>%</strong></font></td>
                              <td align="center"><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">B&aacute;sico</font></strong></td>
                              <td align="center"><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Pleno</font></strong></td>
                              <td align="center"><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Excelente</font></strong></td>
                            </tr>
                            <? while ($rows_escolaridade = pg_fetch_array($esta->get_result_escolaridade())) { ?>
                              <tr bgcolor="#F4F4F4" 
                                    onmouseover="this.className='tb_resultado_selecionado';" 
                                    onmouseout="this.className='';" 
                                    >
                                  <td>
                                    <a href="javascript:void(0);" 
                                    onclick="thisPage.load_mensagem( '3', '<?= $rows_escolaridade["cd_escolaridade"]; ?>' )"
                                    ><img src="img/img_descricao.jpg" 
                                        border="0" 
                                        title="Clique para saber mais"
                                        ></a>
                                    <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?= $rows_escolaridade["nome_escolaridade"] ?></font>
                                  </td>
                                  <td align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">
                                    <?= $esta->grau_escolaridade_valor($rows_escolaridade["nivel"], "B", $rows_escolaridade["grau_percentual"]) ?>
                                    <?= $esta->grau_escolaridade_valor($rows_escolaridade["nivel"], "P", $rows_escolaridade["grau_percentual"]) ?>
                                    <?= $esta->grau_escolaridade_valor($rows_escolaridade["nivel"], "E", $rows_escolaridade["grau_percentual"]) ?>
                                    </font></td>
                                  <td align="center">
                                        <input type="radio"
                                            name="grau_escolaridade" 
                                            value="<?= $esta->grau_escolaridade_valor($rows_escolaridade["nivel"], "B", $rows_escolaridade["grau_percentual"]) ?>" 
                                            <?= $esta->grau_escolaridade_is_checked(
                                            		$rows_escolaridade["nivel"]
                                            		, "B"
                                            		, $esta->capa->get_grau_escolaridade()
                                            		, $rows_escolaridade["grau_percentual"]
                                            		, $rows_escolaridade["cd_escolaridade"]
                                            		, $rows_escolaridade["ordem"] ) ?> 
                                            <?= $esta->grau_escolaridade_is_disabled( $rows_escolaridade["nivel"], "B", $rows_escolaridade["grau_percentual"] ) ?> 
                                            ></td>
                                  <td align="center">
                                    <input type="radio" 
                                        name="grau_escolaridade" 
                                        value="<?= $esta->grau_escolaridade_valor($rows_escolaridade["nivel"], "P", $rows_escolaridade["grau_percentual"]) ?>" 
                                        <?= $esta->grau_escolaridade_is_checked(
                                        			$rows_escolaridade["nivel"]
                                        			,  "P"
                                        			, $esta->capa->get_grau_escolaridade()
                                        			, $rows_escolaridade["grau_percentual"]
                                            	 	, $rows_escolaridade["cd_escolaridade"]
                                            		, $rows_escolaridade["ordem"] ) ?> 
                                        <?= $esta->grau_escolaridade_is_disabled( $rows_escolaridade["nivel"], "P", $rows_escolaridade["grau_percentual"] ) ?> 
                                        ></td>
                                  <td align="center">
                                    <input type="radio" 
                                        name="grau_escolaridade" 
                                        value="<?= $esta->grau_escolaridade_valor($rows_escolaridade["nivel"], "E", $rows_escolaridade["grau_percentual"]) ?>" 
                                        <?= $esta->grau_escolaridade_is_checked(
                                        			$rows_escolaridade["nivel"]
                                        			, "E"
                                        			, $esta->capa->get_grau_escolaridade()
                                        			, $rows_escolaridade["grau_percentual"]
                                            		, $rows_escolaridade["cd_escolaridade"]
                                            		, $rows_escolaridade["ordem"] ) ?> 
                                        <?= $esta->grau_escolaridade_is_disabled( $rows_escolaridade["nivel"], "E", $rows_escolaridade["grau_percentual"] ) ?> 
                                        > 
                                  </td>
                                </tr>
                            <? } ?>
                          </table></td>
                      </tr>
                      
                      <? if($esta->capa->avaliado->usuario_matriz->cd_escolaridade=="") :?>
                      	<tr style="display:none;"> 
                      <? else: ?>
                      	<tr> 
                      <? endif; ?>
                        <td colspan="7">
					  		<? if($esta->capa->avaliado->usuario_matriz->cd_escolaridade=="") $esta->capa->avaliado->usuario_matriz->cd_escolaridade=0; ?>
                            <? $a_esc = $esta->get_escolaridade( $esta->capa->avaliado->usuario_matriz->cd_escolaridade ); ?>
					  		<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#BFA260">
	                            <tr bgcolor="#BFA260"> 
	                              <td><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif"></font></strong></td>
	                              <td align="center"><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">B&aacute;sico</font></strong></td>
	                              <td align="center"><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Pleno</font></strong></td>
	                              <td align="center"><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Excelente</font></strong></td>
	                            </tr>
	                            <tr bgcolor="#F4F4F4" 
                                    onmouseover="this.className='tb_resultado_selecionado';" 
                                    onmouseout="this.className='';" 
                                    >
                                  <td>
                                    <a href="javascript:void(0);" 
                                    onclick="thisPage.load_mensagem( '3', <?= $a_esc['cd_escolaridade'] ?> )"
                                    ><img src="img/img_descricao.jpg" 
                                        border="0" 
                                        title="Clique para saber mais"
                                        ></a>
                                    <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?= $a_esc['nome_escolaridade'] ?></font>
                                  </td>
                                  <td align="center"><? if($esta->escolaridade_escolhida=="B") { echo "<B>X</B>"; } ?></td>
                                  <td align="center"><? if($esta->escolaridade_escolhida=="P") { echo "<B>X</B>"; } ?></td>
                                  <td align="center"><? if($esta->escolaridade_escolhida=="E") { echo "<B>X</B>"; } ?></td>
                                </tr>
                            </table>
					  	
					  	</td>
					  </tr>
                      <!-- END BLOCK : escolaridade -->

                      <tr>
                        <td height="20px"></td>
                      </tr>

                      <!-- START BLOCK : CE -->
                      <tr bgcolor="#0046ad"> 
                        <td valign="top"><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Compet&ecirc;ncias 
                          Espec&iacute;ficas </font></strong></td>
                        <td colspan="6" valign="top" align="right">
                            <font color="#FFFFFF" size="3" face="Arial, Helvetica, sans-serif"><strong><?/*= $esta->get_val_ce() */?></strong></font>
                        </td>
                      </tr>
                      <!-- END BLOCK : CE -->

                      <tr bgcolor="#0046ad"> 
                        <td valign="top" bgcolor="#F4F4F4"></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CACE') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CACE') ?>' );"><u>A</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CBCE') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CBCE') ?>' );"><u>B</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CCCE') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CCCE') ?>' );"><u>C</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CDCE') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CDCE') ?>' );"><u>D</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CECE') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CECE') ?>' );"><u>E</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CFCE') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CFCE') ?>' );"><u>F</u></a></td>
                      </tr>

                      <!-- START BLOCK : comp_espec -->
                      <?php 
						while ($rows_comp_espec = pg_fetch_array($esta->get_result_comp_espec())) 
						{ 
							$id_lb = '_CE_resp_ant_'.$rows_comp_espec["cd_comp_espec"];
					  ?>					  
                      <tr bgcolor="#F4F4F4" 
                            onmouseover="this.className='tb_resultado_selecionado';" 
                            onmouseout="this.className='';" 
                            > 
                        <td valign="top">
                            <a href="javascript:void(0);" 
                                    onclick="thisPage.load_mensagem( '2', '<?=$rows_comp_espec["cd_comp_espec"]?>' )"
                                    ><img src="img/img_descricao.jpg" 
                                        border="0" 
                                        title="Clique para saber mais"
                                        ></a>
                            <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?= $rows_comp_espec["nome_comp_espec"] ?></font>
                        </td>
                        <td><input type="radio" 
                            name="comp_espec<?= $rows_comp_espec["cd_comp_espec"] ?>" value="0" onclick="setRespAnterior('<?=$id_lb?>',0)"
                            <?= $esta->comp_espec_checked( $rows_comp_espec["cd_comp_espec"], 0.0000 ) ?>
                            ></td>
                        <td><input type="radio" name="comp_espec<?= $rows_comp_espec["cd_comp_espec"] ?>" value="20" onclick="setRespAnterior('<?=$id_lb?>',20)"
                            <?= $esta->comp_espec_checked( $rows_comp_espec["cd_comp_espec"], 20.0000 ) ?> 
                            ></td>
                        <td><input type="radio" name="comp_espec<?= $rows_comp_espec["cd_comp_espec"] ?>" value="40" onclick="setRespAnterior('<?=$id_lb?>',40)"
                            <?= $esta->comp_espec_checked( $rows_comp_espec["cd_comp_espec"], 40.0000 ) ?> 
                            ></td>
                        <td><input type="radio" name="comp_espec<?= $rows_comp_espec["cd_comp_espec"] ?>" value="60" onclick="setRespAnterior('<?=$id_lb?>',60)"
                            <?= $esta->comp_espec_checked( $rows_comp_espec["cd_comp_espec"], 60.0000 ) ?> 
                            ></td>
                        <td><input type="radio" name="comp_espec<?= $rows_comp_espec["cd_comp_espec"] ?>" value="80" onclick="setRespAnterior('<?=$id_lb?>',80)"
                            <?= $esta->comp_espec_checked( $rows_comp_espec["cd_comp_espec"], 80.0000 ) ?> 
                            ></td>
                        <td><input type="radio" name="comp_espec<?= $rows_comp_espec["cd_comp_espec"] ?>" value="100" onclick="setRespAnterior('<?=$id_lb?>',100)"
                            <?= $esta->comp_espec_checked( $rows_comp_espec["cd_comp_espec"], 100.0000 ) ?> 
                            > 
                        </td>
							<td style="background-color: #FFFFFF; width: 20px; text-align:center; <?php echo ($esta->is_avaliador() ? "" : "display:none;"); ?>">
								<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
								<?php
									if($esta->is_avaliador())
									{
										$ar_ant = $esta->comp_espec_anterior($rows_comp_espec["cd_comp_espec"]);
										echo '<input type="hidden" id="ed'.$id_lb.'" name="ed'.$id_lb.'" value="'.intval($ar_ant['grau_anterior']).'">';
									
										$letra = "";
										if ($ar_ant['grau_anterior'] != "")
										{										
											switch ($ar_ant['grau_anterior'])
											{
												case 0  : $letra = "A"; break;
												case 20 : $letra = "B"; break;
												case 40 : $letra = "C"; break;
												case 60 : $letra = "D"; break;
												case 80 : $letra = "E"; break;
												case 100: $letra = "F"; break;
											}
										}
										echo '<span id="lb'.$id_lb.'" class="'.($ar_ant['grau_anterior'] <> $ar_ant['grau'] ?  'label label-important' : 'label').'">'.$letra."</span>";
									}
								?>
								</font>
							</td>						
                      </tr>
                      <? } ?>
                      <!-- END BLOCK : comp_espec -->

                      <tr>
                        <td height="20px"></td>
                      </tr>

                      <!-- START BLOCK : RE -->
                      <tr bgcolor="#0046ad"> 
                        <td valign="top"><strong><font color="#FFFFFF" size="2" face="Verdana, Arial, Helvetica, sans-serif">Responsabilidades</font></strong></td>
                        <td colspan="6" valign="top" align="right"><font color="#FFFFFF" size="3" face="Arial, Helvetica, sans-serif"><strong> 
                          <?/*= $esta->get_val_resp() */?></strong></font></td>
                      </tr>
                      <!-- END BLOCK : RE -->

                      <tr bgcolor="#0046ad"> 
                        <td valign="top" bgcolor="#F4F4F4"></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CARE') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CARE') ?>' );"><u>A</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CBRE') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CBRE') ?>' );"><u>B</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CCRE') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CCRE') ?>' );"><u>C</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CDRE') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CDRE') ?>' );"><u>D</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CERE') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CERE') ?>' );"><u>E</u></a></td>
                        <td align="center" class="font_table_title" title="<?= $esta->texto_conceito('CFRE') ?>"><a class="font_table_title" href="javascript:void(0)" onclick="thisPage.load_mensagem_conceito( '<?= $esta->texto_conceito('CFRE') ?>' );"><u>F</u></a></td>
                      </tr>

                      <!-- START BLOCK : responsabilidade -->
                      <?php
						while ($rows_resp = pg_fetch_array($esta->get_result_resp())) 
						{ 
							$id_lb = '_RE_resp_ant_'.$rows_resp["cd_responsabilidade"];
					  ?>					  
                      <tr bgcolor="#F4F4F4" 
                            onmouseover="this.className='tb_resultado_selecionado';" 
                            onmouseout="this.className='';" 
                            > 
                        <td valign="top">
                            <a href="javascript:void(0);" 
                                    onclick="thisPage.load_mensagem( '4', '<?=$rows_resp["cd_responsabilidade"]?>' )"
                                    ><img src="img/img_descricao.jpg" 
                                        border="0" 
                                        title="Clique para saber mais"
                                        ></a>
                            <font size="2" face="Verdana, Arial, Helvetica, sans-serif"><?= $rows_resp["nome_responsabilidade"] ?></font>
                        </td>
                        <td><input type="radio" name="responsabilidade<?= $rows_resp["cd_responsabilidade"] ?>" value="0" onclick="setRespAnterior('<?=$id_lb?>',0)"
                            <?= $esta->responsabilidade_checked( $rows_resp["cd_responsabilidade"], 0.0000 ) ?>
                            ></td>
                        <td><input type="radio" name="responsabilidade<?= $rows_resp["cd_responsabilidade"] ?>" value="20" onclick="setRespAnterior('<?=$id_lb?>',20)"
                            <?= $esta->responsabilidade_checked( $rows_resp["cd_responsabilidade"], 20.0000 ) ?>
                            ></td>
                        <td><input type="radio" name="responsabilidade<?= $rows_resp["cd_responsabilidade"] ?>" value="40" onclick="setRespAnterior('<?=$id_lb?>',40)"
                            <?= $esta->responsabilidade_checked( $rows_resp["cd_responsabilidade"], 40.0000 ) ?> 
                            ></td>
                        <td><input type="radio" name="responsabilidade<?= $rows_resp["cd_responsabilidade"] ?>" value="60" onclick="setRespAnterior('<?=$id_lb?>',60)"
                            <?= $esta->responsabilidade_checked( $rows_resp["cd_responsabilidade"], 60.0000 ) ?>
                            ></td>
                        <td><input type="radio" name="responsabilidade<?= $rows_resp["cd_responsabilidade"] ?>" value="80" onclick="setRespAnterior('<?=$id_lb?>',80)"
                            <?= $esta->responsabilidade_checked( $rows_resp["cd_responsabilidade"], 80.0000 ) ?> 
                            ></td>
                        <td><input type="radio" name="responsabilidade<?= $rows_resp["cd_responsabilidade"] ?>" value="100" onclick="setRespAnterior('<?=$id_lb?>',100)"
                            <?= $esta->responsabilidade_checked( $rows_resp["cd_responsabilidade"], 100.0000 ) ?> 
                            > 
                        </td>
							<td style="background-color: #FFFFFF; width: 20px; text-align:center; <?php echo ($esta->is_avaliador() ? "" : "display:none;"); ?>">
								<font size="2" face="Verdana, Arial, Helvetica, sans-serif">
								<?php
									if($esta->is_avaliador())
									{
										$ar_ant = $esta->responsabilidade_anterior($rows_resp["cd_responsabilidade"]);
										echo '<input type="hidden" id="ed'.$id_lb.'" name="ed'.$id_lb.'" value="'.intval($ar_ant['grau_anterior']).'">';
									
										$letra = "";
										if ($ar_ant['grau_anterior'] != "")
										{										
											switch ($ar_ant['grau_anterior'])
											{
												case 0  : $letra = "A"; break;
												case 20 : $letra = "B"; break;
												case 40 : $letra = "C"; break;
												case 60 : $letra = "D"; break;
												case 80 : $letra = "E"; break;
												case 100: $letra = "F"; break;
											}
										}
										echo '<span id="lb'.$id_lb.'" class="'.($ar_ant['grau_anterior'] <> $ar_ant['grau'] ?  'label label-important' : 'label').'">'.$letra."</span>";
									}
								?>
								</font>
							</td>							
                      </tr>
                      <? } ?>
                      <!-- END BLOCK : responsabilidade -->

                      <tr>
                        <td height="20px"></td>
                      </tr>

                      <? endif; ?>

					  <!-- BEGIN: EXPECTATIVAS -->
                      <tr> 
                        <td colspan="7" valign="top">
                        	<hr />
                        	<a name="expectativas"></a>
                        	<div id="espectativa_lista__div"><? $esta->espectativas_lista_render(); ?></div>
							<br />
							<? if($esta->is_avaliado()) : ?>
								<table width="100%"><tr><td align="right">
								<a href="javascript:void(0);" onclick="thisPage.nova_espectativa__Click(this);" url="avaliacao_partial_avaliacao_avaliado.php"><img src="img/avaliacao_nova_expectativa.png" border="0" /></a>
								</td></tr></table>
							<? endif; ?>
                        	<hr />
                       </td>
                      </tr>
					  <!-- END: EXPECTATIVAS -->
                    </table>
                    </td>
                </tr>

<!-- END QUADRO RESUMO -->

            </table>
            
            <input type="hidden" name="end_objects" id="end_objects" />

            <BR />

            <center>
            <? if($esta->is_avaliador()) { ?>
                <?php
                $label_button_close_send = 'Confirmar';
                $js_button_close_send = 'save_by_avaliador_Click(this)';
                $js_command = 'insert_and_send';
                ?>
            <? } ?>

            <? if($esta->is_avaliado()) { ?>
                    <a href="javascript:void(0)"><img id="save_image"
                               src="img/avaliacao_salvar_continuar.png" 
                               border="0" 
                               onclick="thisPage.save_and_continue_Click(this);"
                               urlPartial="avaliacao_partial_avaliacao_avaliado_save.php"
                               contentPartial="message_panel"
                               /></a>			
			
				<!--
                <input
                        type="button"
                        value="Salvar e continuar"
                        style="width:200;"
                        onclick="thisPage.save_and_continue_Click(this);"
                        class="botao"
                    />
					
					<input
                    onclick="thisPage.save_close_and_send_Click(this);"
                    type="button"
                    value="Fechar e Encaminhar"
                    style="width:200;"
                    class="botao_vermelho" 
                />
				-->
				
                    <a href="javascript:void(0)"><img 
                               src="img/avaliacao_fechar_enviar.png" 
                               border="0" 
                               onclick="thisPage.save_close_and_send_Click(this);"
                               urlPartial="avaliacao_partial_avaliacao_avaliado_save.php"
                               contentPartial="message_panel"
                               /></a>				
				
            <? } ?>
            <? if($esta->is_avaliador()) { ?>
			
                    <a href="javascript:void(0)"
                        id="save_by_avaliador_image"
                        onclick="thisPage.insert_and_continue_by_avaliador_Click(this);"
                        title="Salvar e Continuar"
                    ><img id="save_image" src="img/avaliacao_salvar_continuar.png" border="0" /></a>			
			
				<!--
                <input
                        id="save_and_continue_by_avaliador_button"
                        type="button"
                        value="Salvar e continuar"
                        onclick="thisPage.insert_and_continue_by_avaliador_Click( this );"
                        class="botao"
                /> 
				-->
				
				<!--
				<input
                        type="button"
                        value="<?= $label_button_close_send; ?>"
                        onclick="thisPage.save_by_avaliador_Click(this);"
                        command="insert_and_send"
                        class="botao"
                />
				
				
                    <a href="javascript:void(0)"
                        onclick="thisPage.save_by_avaliador_Click(this);"
                        command="insert_and_send"
                        title="Confirmar"
                    ><img id="save_image" src="img/avaliacao_confirmar.png" border="0" /></a>					
				-->
		
            <? } ?>
            </center>
			<BR><BR><BR>

	</div>
	<!-- END: avaliacao__div -->

    <br>
    
    <!-- BEGIN: TELAS RELAMPAGO -->
    
    <!-- BEGIN: EXPECTATIVAS -->
    <center>
    <div id="aspecto_div" style="display:none;"></div>
    <center>
    <!-- END: EXPECTATIVAS -->

    <!-- END: TELAS RELAMPAGO -->

    <? $esta = null; ?>