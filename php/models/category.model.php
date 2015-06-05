<?php

class PWModelCategory{
    
    public function list_categories_for_user($username) {
        
        $sqlCat = sprintf("select c.* from categories c where c.username = '%s'", $username);
        $categories = Database::inst()->loadObjectList($sqlCat);
               
       
        
        return $categories;
    }
    
    public function save($category){
        $category  = Database::inst()->insertObject($category, "categories", "id");        
        return $category;
    }
    
    /**
     * Delete a category and its siblings
     * @param type $id
     */
    public function delete($id, $username){
        //first delete all the child links
        $sql1 = sprintf("delete from links where category_id = %d and username = '%s'", $id, $username);
        Database::inst()->execute($sql1);
        
        $sql2 = sprintf("delete from categories where id = %d and username = '%s'", $id, $username);
        Database::inst()->execute($sql2);
        
    }
    
   

}