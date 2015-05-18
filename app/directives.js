var directives = angular.module('yurlDirectives', []);
directives.directive('showonhover',
   function() {       
      return {
         link : function(scope, element, attrs) {
             
            element.parent().bind('mouseenter', function() {
                element.parent().children().find(".btn-group").show();
            });
            element.parent().bind('mouseleave', function() {
                 element.parent().children().find(".btn-group").hide();
            });
       }
   };
});