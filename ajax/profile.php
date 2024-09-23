<?php

use Symfony\Component\Console\Output\Output;

include("../../../inc/includes.php");


Session::checkLoginUser();

class GitLabProfifle{


    private $request;
    private $response = [
         "success" => false,
         "message" => ""
    ];

    public function __construct($request){

        $this->request  = (object) $request;

        if(empty($this->request->method)){
             $this->response["message"] = "método obrigatório!";
             $this->OutputResponse();
        }

        switch($this->request->method){
            case "create":
                $this->save($this->request->profileId);
                $this->OutputResponse();
            break;
            case "delete":
                $this->delete($this->request->ids);
                $this->OutputResponse();
            break;
            default:
                  $this->response["message"] = "método inválida!";
                  $this->OutputResponse();
            break;
        }
        
    }

    public function save($profileId){

        global $DB;

        $result = $DB->request('glpi_plugin_gitlab_profiles_users', ['profile_id' => [$profileId]]);
        if ($result->count() > 0) {
            $this->response["message"] = "O profile já tem permissão!";
        } else {
            $DB->insert(
                'glpi_plugin_gitlab_profiles_users',
                [
                    'profile_id' => $profileId,
                    'user_id'    => $_SESSION["glpiID"],
                    'created_at' => $_SESSION["glpi_currenttime"]
                ]
            );

            $this->response["message"] = "Permissão concedida com sucesso!";
            $this->response["success"] = true;
        }

    }

    public function delete($ids){

        global $DB;


        try{


            foreach($ids as $id){
                $DB->delete(
                    'glpi_plugin_gitlab_profiles_users',
                    [
                        'id' => $id
                    ]
                );
            }

            $this->response["message"] = "Permissão excluída com sucesso!";
            $this->response["success"] = true;
        } catch(\Exception $ex) {
            $this->response["message"] = "Erro ao excluir!";
        }

    }

    private function OutputResponse(){
        header("Content-type: application/json; charset=utf-8");
        echo json_encode($this->response);
    }
}

new GitLabProfifle($_POST);



