var yurlControllers = angular.module('yurlControllers', []);

yurlControllers.controller('LinkListCtrl', ['$scope', '$cookies', 'Links',
    function ($scope, $cookies, Links) {

        update_data(Links, $scope);


        $scope.types = [{
              "id" : "link", "name" : "link"
        }, {
              "id" : "widget", "name" : "widget"
        }];

        //controller function :: 
        $scope.siteInDialog = function (site) {
            fnSiteInDialog(site);
        };

        //add a new Link
        $scope.addLink = function () {
            $scope.link = {
                name: "",
                url: "http://www.",
                memo: "",
                id: ""
            };
            
           openLinkModal($scope, $scope.link);
        };

        //add a new Link
        $scope.editLink = function (link) {

            if ($scope.categories !== undefined) {
                var result = $.grep($scope.categories, function (e) {
                    return e.id === link.category_id;
                });
                
                // not found
                if (result.length === 1) {
                    link.category = result[0];

                    // access the foo property using result[0].foo
                } else {
                    // multiple items found
                }

            }

            $scope.link = link;


            openLinkModal($scope, link);

        };

        $scope.toggleCategoryInput = function () {
            $("#category").toggle();
            $("#category-new").toggle();
        };

        $scope.resetQuery1 = function () {
            $scope.query1 = "";
        };



        $scope.deleteLink = function (link) {
            lnk = link;
            bootbox.confirm("This can't be undone! Do you want to continue", function (result) {
                if (result) {
                    Links.deleteLink(link, function () {
                        update_data(Links, $scope);
                    });
                }
            });
        };

        $scope.deleteCategory = function (cat) {
            catg = cat;
            bootbox.confirm("This can't be undone and will delete all links of this category too! Do you want to continue", function (result) {
                if (result) {

                    Links.deleteCategory(catg, function () {
                        update_data(Links, $scope);
                    });
                }
            });

        };
        
        $scope.minifyCategory = function (cat){
            
            $("#cat-dialog-" + cat.id).hide({duration : 500, direction : 'down'});
            $("#cat-button-" + cat.id).show({duration : 500, direction : 'down'});
            
        };
        
        $scope.showCategory = function (cat){
            
            $("#cat-dialog-" + cat.id).show({duration : 500, direction : 'down'});
            $("#cat-button-" + cat.id).hide({duration : 500, direction : 'down'});
            
        };

        $scope.saveLink = function () {
            var link = $scope.link;
            if ($scope.link.category === undefined) {
                //when a tag is selected
                link.category_name = $scope.newCategoryName;
            } else {
                link.category_id = $scope.link.category.id;
                link.category_name = $scope.link.category.name;
            }
            
            //reset new category field
            $scope.newCategoryName = "";
            
            
            //only encrypt when passphrase is filled
            if ($scope.passphrase !== undefined && $scope.passphrase.length > 0) {
                
                if(link.url_username_dec !== undefined && link.url_username_dec.length > 0){
                    var url_username = CryptoJS.AES.encrypt(link.url_username_dec, $scope.passphrase);
                    link.url_username = url_username.toString();                    
                }
                
                if(link.url_password_dec !== undefined && link.url_password_dec.length > 0){
                    var url_password = CryptoJS.AES.encrypt(link.url_password_dec, $scope.passphrase);
                    link.url_password = url_password.toString();                 
                }
            }

            Links.saveLink(link, function (retval) {
                update_data(Links, $scope);
            });
        };

    }]);



var update_data = function (Links, $scope) {
    //inital load of items
    Links.listLinks(function (items) {
        $scope.links = items;
    });

    //inital load of items
    Links.listCategories(function (categories) {
        $scope.categories = categories;
    });
};


openLinkModal = function ($scope, link) {
    
    //reset category new
    $scope.newCategoryName = "";
    
    if ($scope.passphrase !== undefined && $scope.passphrase.length > 0) {
        if(link.url_username !==undefined){
            link.url_username_dec = CryptoJS.AES.decrypt(link.url_username, $scope.passphrase).toString(CryptoJS.enc.Utf8);
        }
        if(link.url_password !==undefined){
            link.url_password_dec = CryptoJS.AES.decrypt(link.url_password, $scope.passphrase).toString(CryptoJS.enc.Utf8);
        }
        $("#username").show();
        $("#password").parent().show();
    } else {
        $("#username").hide();
        $("#password").parent().hide();
    }
    
      $("#category").show();
            $("#category-new").hide();

    $("#password").prop("type", "password");

    $("#link-modal").modal('show');
};