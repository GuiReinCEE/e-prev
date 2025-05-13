<?php

class showmenu extends Controller
{

    function __construct()
    {
        parent::Controller();
        
        CheckLogin();
        
        $this->load->model('projetos/menu_model');
    }
    
    function index($cd_menu_pai = 0)
    {
        if (usuario_id() == 251 OR usuario_id() == 170 OR usuario_id() == 339)
        {
            $data = array();
            $args = array();
            
            $args['cd_menu_pai'] = 2;
            $args['cd_menu'] = '';
            $args['fl_desativado'] = 'S';
            
            $data['cd_menu_pai'] = $cd_menu_pai;
            
            $this->menu_model->lista_menu($result, $args);
            $arr_menu = $result->result_array();
            
            $data['arr_menu'][] = array('text' => 'Start', 'value' => '2');
            
            foreach ($arr_menu as $item)
            {
                $data['arr_menu'][] = array('text' => $item['ds_menu'], 'value' => $item['cd_menu']);
            }
            
            $this->load->view('dev/index', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function index_result()
    {
        if (usuario_id() == 251 OR usuario_id() == 170 OR usuario_id() == 339)
        {
            $args = array();
                   
            $args['cd_menu'] = $this->input->post("cd_menu", TRUE);
            $args['fl_desativado'] = $this->input->post("fl_desativado", TRUE);
            
            manter_filtros($args);
            
            $return = '<script>
                $(function(){
                    var menu = $("ul#menu").simpletreeview({
						slide: true,
						speed: "fast",
						collapsed: true,
						expand: "0"
                    });                    
                })
                         
                </script>

                <ul id="menu" class="treeview">';
            
            $return .= $this->monta_menu($args['cd_menu'], '', $args['fl_desativado']);
            
            $return .= '</ul>';
            
            echo $return;
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function carrega()
    {
        if (usuario_id() == 251 OR usuario_id() == 170 OR usuario_id() == 339)
        {
            $data = array();
            $args = array();
            $result = null;
            
            $args['cd_menu'] = $this->input->post("cd_menu", TRUE);
            $args['fl_desativado'] = '';
            $args['cd_menu_pai'] = '';
            
            $this->menu_model->carrega($result, $args);
            $arr_menu = $result->row_array();
            
            if(intval($arr_menu['sub_menu']) > 0)
            {
                $args['cd_menu_pai'] = $arr_menu['cd_menu'];
                $args['fl_desativado'] = 'N';
                $args['cd_menu'] = '';
                
                $this->menu_model->lista_menu($result, $args);
                $arr_sub_menu = $result->result_array();
                
                
                $lista = '<script>
                                           
                        function subir()
                        {
                            $("#sortable .active").insertBefore($("#sortable .active").prev());
                        }

                        function descer()
                        {
                            $("#sortable .active").insertAfter($("#sortable .active").next());
                        }

                        $(function(){

                            $("#sortable li").click(function(){
                                $("#sortable li").css("color", "black"); 
                                $("#sortable li").css("font-weight", "normal");

                                $("#sortable li").removeClass("active"); 

                                $(this).css("color", "blue"); 
                                $(this).css("font-weight", "bold");

                                $(this).addClass("active"); 
                            });
                        });
                        </script>
                    <ul id="sortable">';
            
                foreach ($arr_sub_menu as $item)
                {
                    $lista .= '<li order="'.$item['cd_menu'].'"><span>'.$item['ds_menu'].'</span></li>';
                }

                $lista .= '</ul>'.br();
                
                $lista .= button_save("Subir", 'subir();', 'botao_disabled');
                $lista .= button_save("Descer", 'descer();', 'botao_disabled');

                $arr_menu['lista'] = $lista;
            }
            
                        
            $arr_menu = array_map("arrayToUTF8", $arr_menu);			
	    echo json_encode($arr_menu);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function monta_menu($cd_menu = '', $cd_menu_pai = '', $fl_desativado = 'N', $bol_link = true)
    {
        $args = array();
        $result = null;

        $args['cd_menu']   = $cd_menu;
        $args['cd_menu_pai']   = $cd_menu_pai;
        $args['fl_desativado'] = $fl_desativado;
        
        $this->menu_model->lista_menu($result, $args);
        $arr_menu = $result->result_array();

        $return = '';
        
        foreach ($arr_menu as $item)
        {
            
            if(trim($item['dt_desativado']) != '')
            {
                $color = 'red';
            } 
            else if(intval($item['sub_menu']) > 0 AND trim($item['dt_desativado']) == '')
            {
                $color = 'blue';
            }
            else
            {
                $color = 'black';
            }
            
            if($bol_link == true)
            {
                $return .= '<li><span ><a href="javascript:void(0);" id="li_cd_menu_'.$item['cd_menu'].'" style="color:'.$color.';" onclick="carrega($(this), '.$item['cd_menu'].')">'.$item['cd_menu'].' - '.$item['ds_menu'].'</a></span>';
            }
            else
            {
                $return .= '<li><span style="color:'.$color.';">'.$item['cd_menu'].' - '.$item['ds_menu'].'</span>';
            }
            
            
            if(intval($item['sub_menu']) > 0)
            {
                $return .= '<ul>'.$this->monta_menu('', $item['cd_menu'], $fl_desativado, $bol_link);
            }
            
            if(intval($item['sub_menu']) > 0)
            {
                $return .= '</ul>';
            }

            $return .= '</li>';
        }
        
        return $return;
    }
    
    function salvar()
    {
        if (usuario_id() == 251 OR usuario_id() == 170 OR usuario_id() == 339)
        {
            $data = array();
            $args = array();
            $result = null;
            
            $args['save'] = $this->input->post("save", TRUE);
            $args['cd_menu_pai'] = $this->input->post("cd_menu_pai", TRUE);
            $args['cd_menu'] = $this->input->post("cd_menu", TRUE);
            $args['ds_menu'] = $this->input->post("ds_menu", TRUE);
            $args['ds_href'] = $this->input->post("ds_href", TRUE);
            $args['nr_ordem'] = $this->input->post("nr_ordem", TRUE);
            $args['ds_resumo'] = $this->input->post("ds_resumo", TRUE);
			$args['cd_padrao'] = $this->input->post("cd_padrao", TRUE);
            
            $this->menu_model->salvar($result, $args);
            
            redirect("dev/showmenu/index/".$args['cd_padrao'] , "refresh");
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function desativar($cd_menu)
    {
        if (usuario_id() == 251 OR usuario_id() == 170 OR usuario_id() == 339)
        {
            $data = array();
            $args = array();
            $result = null;
            
            $args['cd_menu'] = $cd_menu;
            
            $this->menu_model->desativar($result, $args);
            
            redirect("dev/showmenu/index/" , "refresh");
            
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }

    function ordenacao()
    {
        if (usuario_id() == 251 OR usuario_id() == 170 OR usuario_id() == 339)
        {
            $data = array();
            $args = array();
            $result = null;
            
            $ordenacao = $this->input->post("ordenacao", TRUE);
            
            foreach ($ordenacao as $key => $item)
            {
                $args['nr_ordem'] = $key;
                $args['cd_menu']  = $item;

                $this->menu_model->ordenar($result, $args);
            }
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
    function mapa()
    {
        if (usuario_id() == 251 OR usuario_id() == 170 OR usuario_id() == 339)
        {
            $data = array();
            
            $data['menu_atividade'] = '<ul>'.$this->monta_menu('8', '', 'N', false).'</ul>';
            $data['menu_cadastro']  = '<ul>'.$this->monta_menu('40', '', 'N', false).'</ul>';
            $data['menu_ecrm']      = '<ul>'.$this->monta_menu('4', '', 'N', false).'</ul>';
			$data['menu_gestao']    = '<ul>'.$this->monta_menu('29', '', 'N', false).'</ul>';
			$data['menu_intranet']  = '<ul>'.$this->monta_menu('281', '', 'N', false).'</ul>';
			$data['menu_planos']    = '<ul>'.$this->monta_menu('16', '', 'N', false).'</ul>';
			$data['menu_servicos']  = '<ul>'.$this->monta_menu('31', '', 'N', false).'</ul>';
            
            $this->load->view('dev/mapa', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
    
}
?>