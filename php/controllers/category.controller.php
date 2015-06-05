<?php

class PWControllerCategory extends PWController{
    
    /**
 * gets an overview of the latest run including overview of status
 */
    public function list_all() {
        
        $user = UserHelper::get_current_user()->user;
        $run = $this->model->list_categories_for_user($user);
        output_json($run);
    }

     public function delete($id){
        $user = UserHelper::get_current_user()->user;
        $this->model->delete($id, $user);
        $retval = new stdClass();
        $retval->status = "ok";
        output_json($retval);
    }
    

    
}

