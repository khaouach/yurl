<?php

class PWControllerCategory extends PWController{
    
    /**
 * gets an overview of the latest run including overview of status
 */
    public function list_all() {
        
        $user = UserHelper::get_current_user()->user;
        $run = $this->model->list_categories_for_user($user);
        $this->output_json($run);
    }

     public function delete($id){
        $user = UserHelper::get_current_user()->user;
        $this->model->delete($id, $user);
        $retval = new stdClass();
        $retval->status = "ok";
        $this->output_json($retval);
    }
    

    private function output_json($data){
        header('Content-type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST');
        echo json_encode($data);
    }
    
}

