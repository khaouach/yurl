fnSiteInDialog = function (site) {
    var url = site.url;    
    var sWidth = $(window).width();
    var sHeight = $(window).height();
    var title = '<i class="fa fa-globe fa-2x" ></i> ' + site.name;

    var html = '<iframe width="100%" height="' + (sHeight * 0.7) + '" frameborder="0"  scrolling="n"  allowTransparency="false" src="' + url + '" ></iframe>';
    $('#site-modal .modal-dialog').width(sWidth * 0.8);
    $('#site-modal .modal-title').html(title);
    $('#site-modal .modal-body').html(html);
    $('#site-modal #remarks').html(site.remarks);
    $('#site-modal').modal('show');
};


var togglePassword = function(){
    $('#password').prop('type', function(idx, type) {
        if(type==='text'){
            return 'password';
        }else{
            return 'text';
        }
    });
};
        