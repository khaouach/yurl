(function () {

   var yurlApp = angular.module('yurlApp', [ 
       'ngRoute',
       'ngCookies',
       'yurlAnimations',
       'yurlServices',
       'yurlControllers',
       'ui.bootstrap'
       ]);

    yurlApp.config(['$routeProvider',
        function ($routeProvider) {
            $routeProvider.
                    when('/items', {
                        templateUrl: 'app/partials/main-overview.html',
                        controller: 'LinkListCtrl'
                    }).
                    when('/items/:itemId', {
                        templateUrl: 'app/partials/item-details.html',
                        controller: 'LinkDetailCtrl'
                    }).
                    otherwise({
                        redirectTo: '/items'
                    });
        }]);
   
  

})();