var yurlServices = angular.module('yurlServices', ['ngResource']);

yurlServices.factory('Links', ['$resource',
  function($resource){
    return $resource(rootDataServer, {}, {
            
      listLinks : {
          method:'GET'
          , params:{            
            'unit': 'link',
            'act': 'list_all'}
          , isArray:true
      },
      
      saveLink : {
            method:'GET'
          , params:{            
            'unit': 'link',
            'act': 'save'}
          , isArray:false
      },
      
      deleteLink : {
          method:'GET'
          , params:{            
            'unit': 'link',
            'act': 'delete'}
          , isArray:false
      },
      
      deleteCategory : {
          method:'GET'
          , params:{            
            'unit': 'category',
            'act': 'delete'}
          , isArray:false
      },
      
      listCategories : {
          method:'GET'
          , params:{            
            'unit': 'category',
            'act': 'list_all'}
          , isArray:true
      }
      
      });
      
 }]);

