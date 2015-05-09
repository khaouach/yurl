var yurlControllers = angular.module('yurlControllers', []);

yurlControllers.controller('LinkListCtrl', ['$scope', '$cookies', 'Links',
    function ($scope, $cookies, Links) {
    
        update_data(Links , $scope);
      

         //controller function :: 
        $scope.siteInDialog = function (site) {
            fnSiteInDialog(site);
        };
        
        //add a new Link
        $scope.addLink = function(){
            $scope.link = {                
                name : "",
                url : "http://www.",
                memo : "",
                id : ""
            };
            $("#link-modal").modal('show');
        };
        
        //add a new Link
        $scope.editLink = function(link){                        
            $scope.link = link;
            $("#link-modal").modal('show');
        };
        
         $scope.resetQuery1 = function () {
            $scope.query1 = "";
        };
        
        
        
        $scope.deleteLink = function(link){
            lnk = link;
            bootbox.confirm("This can't be undone! Do you want to continue", function(result){
                if(result){
                    Links.deleteLink(link, function(){
                        update_data(Links , $scope);
                    });
                }
            });
        };
        
        $scope.deleteCategory = function(cat){
            catg = cat;
            bootbox.confirm("This can't be undone and will delete all links of this category too! Do you want to continue", function(result){
                if(result){
            
                    Links.deleteCategory(catg, function(){
                        update_data(Links , $scope);
                    });
                }
            });
            
        };
        
        $scope.saveLink = function(){
            $link = $scope.link;
            if($scope.link.category===undefined){
                $link.category_name = $("#select2-category-container").html();
            }else{
                $link.category_id = $scope.link.category.id;
                $link.category_name = $scope.link.category.name;
            } 
            Links.saveLink($link, function(retval){
                update_data(Links , $scope);
            });
        };

    }]);



var update_data = function(Links, $scope){
    //inital load of items
        Links.listLinks(function (items) {
            $scope.links = items;
        });
        
        //inital load of items
        Links.listCategories(function (categories) {
            $scope.categories = categories;
        });
};