<?php
include("../../../inc/includes.php");

Session::checkLoginUser();

class GitLabIssue{


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
                $this->createIssue();
                $this->OutputResponse();
            break;
            default:
                  $this->response["message"] = "método inválida!";
                  $this->OutputResponse();
            break;
        }
        
    }


   public function createIssue(){

        global $DB;
        
        $DB->beginTransaction();

        try
        {

            $this->createIssueRequiredField();

            $result = $DB->request('glpi_plugin_gitlab_integration', [
                'ticket_id' => $this->request->ticketId, 
                'gitlab_project_id' => $this->request->selectedProject
            ]);

            if($result->count() > 0){
                throw new \Exception("Ticket já possuí issue para o projeto!");
            }


            if (!class_exists('PluginGitlabIntegrationParameters')) {
                throw new \Exception("Plugin de integração não configurado!");
            }

            $DB->insert(
                'glpi_plugin_gitlab_integration',
                [
                    'ticket_id'         => $this->request->ticketId,
                    'gitlab_project_id' => $this->request->selectedProject,
                    'gitlab_member_id' => $_SESSION["glpiID"]
                ]
            );

        
    
            $title = "GLPI - ".$this->request->ticketId . ' / ' . $this->request->ticketName;
            $description = str_replace('&lt;p&gt;', '', str_replace('&lt;/p&gt;', '', $this->request->ticketContent));
            $description = str_replace('&lt;br&gt;', '<br><br><br>', $description);
            $description = str_replace('&lt;p style=\"padding-left: 40px;\"&gt;', '<p style="padding-left: 40px;">', $description);
            $description = str_replace('&lt;', '<', $description);
            $description = str_replace('&gt;', '>', $description);
            $dueDate = $this->request->ticketDueDate;
            $type =  $this->request->ticketType;
            $label =  $this->request->ticketLabel;


    
            $response = PluginGitlabIntegrationGitlabIntegration::CreateIssue(
                $this->request->selectedProject, 
                $title, 
                $description, 
                $dueDate, 
                $type, 
                $label
            );

            $issue = json_decode($response);

            $content = 'Criando Issue no gitlab para verificação <a href="'.$issue->web_url.'" target="blank">#'.$issue->iid.'</a>';

            $insertData =   [
                'items_id'         => $this->request->ticketId,
                'itemtype' => 'Ticket',
                'date' => date('Y-m-d H:i:s'),
                'users_id' => $_SESSION["glpiID"],
                'is_private' => 1,
                'requesttypes_id' => 1,
                'date_mod' => date('Y-m-d H:i:s'),
                'date_creation' => date('Y-m-d H:i:s'),
                'timeline_position' => 4,
                'sourceitems_id' => 0,
                'sourceof_items_id' => 0,
                'content' => $content
            ];

            $DB->insert(
                'glpi_itilfollowups',
                $insertData
            );

            $DB->commit();

            $this->response = [
                'success' => true,
                'message' => "Issue criada com sucesso!"
            ];

        }catch(\Exception $ex){

            $DB->rollback();
            $this->response["message"] = $ex->getMessage();
        }

    }


    private function createIssueRequiredField(){

        $fieldsRequired = [
            'selectedProject',
            'ticketId',
            'ticketName',
            'ticketContent',
            'ticketDueDate',
            'ticketType',
            'ticketLabel'
        ];

        $messageErros = [];

        foreach($fieldsRequired as $fieldRequired){
            if(!property_exists($this->request,$fieldRequired)){
                $messageErros[] = "Campo ".$fieldRequired."é obrigatório";
            }
        }

        if(count($messageErros) > 0){
             throw new \Exception(join("<br>",$messageErros));
        }
    }

    private function OutputResponse(){
        header("Content-type: application/json; charset=utf-8");
        echo json_encode($this->response);
    }
}

new GitLabIssue($_POST);


     
      

       
