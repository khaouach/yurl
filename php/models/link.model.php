<?php

class PWModelLink{
    
    public function list_links_for_user($username) {
        
        $sqlCat = sprintf("select c.* from categories c where c.username = '%s'", $username);
        $categories = Database::inst()->loadObjectList($sqlCat, "id");
        
        $select = sprintf("select l.* from links l where l.username = '%s'", $username);
        $links =  Database::inst()->loadObjectList($select);
        
        foreach ($links as $link) {
            $cat = $categories[$link->category_id];          
            
            
         
            $words = explode(" ", $cat->name);
            $shortName = "";
            for($i = 0; $i < count($words);$i++){
                if(strlen($words[$i]) >= 1){
                    $shortName .=  strtoupper(substr($words[$i], 0, 1));                                       
                }
                if($i===1) break;
            }
            
            $cat->shortName = $shortName;
            
            $cat->links[] = $link;
    
        }
        $categories_array = array();
        foreach ($categories as $cat) {
            $categories_array[] =   $cat;
        }
        
        return $categories_array;
    }
    
    public function save($link){
        $link  = Database::inst()->insertObject($link, "links", "id");
//        sysout(Database::inst());
        return $link;
    }
    
      public function update($link){
        $link  = Database::inst()->updateObject($link, "links", "id");
//        sysout(Database::inst());
        return $link;
    }
    
    public function delete($id){
        $sql = sprintf("delete from links where id = %d", $id);
        return Database::inst()->execute($sql);
    }

}