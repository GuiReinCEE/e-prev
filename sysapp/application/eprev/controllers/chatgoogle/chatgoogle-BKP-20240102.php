<?php
class Chatgoogle extends Controller
{
    var $API_URL_ORABKP = 'https://chat.googleapis.com/v1/spaces/AAAAiQMcQzw/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=S89vyyYbdv-elEGFMAGSyG95WYp76Dw25dxS45SpiOU'; 
    var $API_URL_ELETRO = 'https://chat.googleapis.com/v1/spaces/AAAAQa5P84A/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=2KcQ4NyNB2LPFcskS-2iY4UCwCKTwp7cyYoFfFqxVp0%3D'; 
    var $API_URL_EPREV  = 'https://chat.googleapis.com/v1/spaces/AAAA5HZJfiE/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=5UtKwJgRcIxHSGUxWvfrWb5GSeUQqV5Pj0I3AtortBk%3D'; 
    var $API_URL_INFRA  = 'https://chat.googleapis.com/v1/spaces/AAAAANFDaH8/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=IAeI7-9Jw3tQW0IsAr11tGmqiCD8x0QxcCt8CNGx1hc'; 

    var $token_eletro;
    var $token_eprev;
    var $token_infra;
    
    function __construct()
    {
        parent::Controller();

        $this->token_eletro = md5('integracaoenviareletro'); #"c0fc249a1c6850369fb3427fb9722ed1" 
        $this->token_eprev  = md5('integracaoenviareprev'); #"ef7b464876e696d6339e3def252ea917"
        $this->token_infra  = md5('integracaoenviarinfra'); #"b0a8b4ca27b21e8e30970a5503f6acac"
    }

    public function sendBotOracleBKP()
    {
        $this->sendBot($this->API_URL_ORABKP, $this->token_eletro);         
    }
    
    public function sendBotEletro()
    {
        $this->sendBot($this->API_URL_ELETRO, $this->token_eletro);         
    }

    public function sendBotEprev()
    {
        $this->sendBot($this->API_URL_EPREV, $this->token_eprev);           
    }
    
    public function sendBotInfra()
    {
        $this->sendBot($this->API_URL_INFRA, $this->token_infra);           
    }   
    
    private function enviarImagem($mensagem, $url_img, $url_api) 
    {
        $retorno = FALSE;
        try
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => $url_api,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{
                    "cardsV2": [
                        {
                            "card": {
                                "header": {
                                    "title": "'.$mensagem.'"
                                },                              
                                "sections": {
                                    "widgets": [
                                        {
                                            "image": {
                                                "imageUrl": "'.$url_img.'"
                                            }
                                        },
                                        {
                                          "divider": {}
                                        },
                                        {
                                          "textParagraph": {
                                            "text": "Veja <a href=\''.$url_img.'\'>aqui</a> a imagem original"
                                          }
                                        }
                                    ]
                                },
                            },
                        }
                    ]
              }',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json; charset=UTF-8'
              ),
            ));

            $output = curl_exec($curl);

            curl_close($curl);
            #echo $output;
            
            $retorno = TRUE;
        } 
        catch (Exception $e) 
        {
            $retorno = FALSE;
        }
        
        return $output;
        //return $retorno;
    }   
    
    private function enviarMensagem($mensagem, $url_api) 
    {
        $retorno = FALSE;
        try
        {
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => $url_api,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{
                "text": "'.$mensagem.'"
            }',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json; charset=UTF-8'
              ),
            ));

            $output = curl_exec($curl);

            curl_close($curl);
            #echo $output;
            
            $retorno = TRUE;
        } 
        catch (Exception $e) 
        {
            $retorno = FALSE;
        }
        
        return $output;
        //return $retorno;
    }   
    
    private function sendBot($_URL_API, $_TOKEN)
    {
        $_POST = array_merge($_POST, (array) json_decode(file_get_contents('php://input')));
        
        #print_r($_POST); exit;
        
        $args    = Array();
        $data    = Array();
        $ar_ret  = Array();
        $result  = null;
        
        $ar_ret["cd_msg"] = "0";
        $ar_ret["dt_log"]      = date("Y-m-d H:i:s");
        $ar_ret["fl_erro"]     = "N";
        $ar_ret["cd_erro"]     = "0";
        $ar_ret["retorno"]     = "";
    
        $args["token"]   = $this->input->post("token", TRUE); 
        $args["texto"]   = ($this->input->post("texto", TRUE));
        $args["url_img"] = trim($this->input->post("url_img", TRUE));
        
        #print_r($args); #exit;
        
        if($args["token"] == $_TOKEN)
        {
            $fl_campo_obrigatorio = TRUE;
            
            if(trim($args["texto"]) == "")
            {
                $fl_campo_obrigatorio = FALSE;
                $campo = "";
                $ar_ret["retorno"] = utf8_encode("ERRO: campo obrigatório TEXTO não informado");
            }
            elseif(strlen($args["texto"]) > 4000)
            {
                $fl_campo_obrigatorio = FALSE;
                $campo = "";
                $ar_ret["retorno"] = utf8_encode("ERRO: o tamanho máximo do campo TEXTO é de 4000 caracteres");
            }               
            
            if($fl_campo_obrigatorio)
            {
                if($args["url_img"] != "")
                {
                    $retorno = $this->enviarImagem($args["texto"], $args["url_img"], $_URL_API);
                }
                else
                {
                    $retorno = $this->enviarMensagem($args["texto"], $_URL_API);
                }
                
                #### TRATAR RETORNO ####
                
                #$ar_ret["retorno"]     = "Mensagem enviada com sucesso: ".$retorno." ".str_replace(chr(10),"",print_r($args,true));
                $ar_ret["cd_msg"] = "0";
                $ar_ret["retorno"]     = "Mensagem enviada com sucesso";
 
            }
            else
            {
                $ar_ret["fl_erro"] = "S";
                $ar_ret["cd_erro"] = "1";
            }           
        }
        else
        {
            $ar_ret["fl_erro"] = "S";
            $ar_ret["cd_erro"] = "3";
            $ar_ret["retorno"] = utf8_encode("ERRO: acesso nao permitido");         
        }
        
        echo json_encode($ar_ret);          
    }

}
?>