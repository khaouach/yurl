<?php

class PWControllerLink extends PWController{
    
    /**
 * gets an overview of the latest run including overview of status
 */
    public function list_all() {
        
        $user = UserHelper::get_current_user()->user;
        $run = $this->model->list_links_for_user($user);
        output_json($run);
    }
    
    public function delete($id){
        $this->model->delete($id);
        $retval = new stdClass();
        $retval->status = "ok";
        output_json($retval);
    }

    
    public function save($link){
        $username = UserHelper::get_current_user()->user;
        $now = new DateTime();
        if(!isset($link->category_id)){
            $categoryModel =  new PWModelCategory();
            $cat = new stdClass();
            $cat->id = null;
            $cat->name = $link->category_name;
            $cat->username = $username;
            $cat->last_update = $now->format(DateHelper::$DATETIME_FORMAT_SQL);
            $category = $categoryModel->save($cat);
            $link->category_id = $category->id;
        }
        unset($link->category_name);
        $link->last_update = $now->format(DateHelper::$DATETIME_FORMAT_SQL);
        $link->username= $username; 
        if(intval($link->id) > 0 ){
            $this->model->update($link);
        }else{
            $this->model->save($link);
        }
    }
    
   
    
}

