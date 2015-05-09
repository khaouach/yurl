/**
 * Add a context menu
 * @param {type} settings
 * @returns {jQuery.fn@call;each}
 */
jQuery.fn.contextMenu = function(settings) {

    return this.each(function() {

        // Open context menu
        jQuery(this).on("contextmenu", function(e) {
            //open menu
            jQuery(settings.menuSelector)
                    .data("invokedOn", jQuery(e.target))
                    .show()
                    .css({
                        position: "absolute",
                        left: getLeftLocation(e),
                        top: getTopLocation(e)
                    })
                    .off('click')
                    .on('click', function(e) {
                        jQuery(this).hide();

                        var $invokedOn = jQuery(this).data("invokedOn");
                        var $selectedMenu = jQuery(e.target);

                        settings.menuSelected.call(this, $invokedOn, $selectedMenu);
                    });

            return false;
        });

        //make sure menu closes on any click
        jQuery(document).click(function() {
            jQuery(settings.menuSelector).hide();
        });
    });

    function getLeftLocation(e) {
        var mouseWidth = e.pageX;
        var pageWidth = jQuery(window).width();
        var menuWidth = jQuery(settings.menuSelector).width();

        
        // opening menu would pass the side of the page
        if (mouseWidth + menuWidth > pageWidth &&
                menuWidth < mouseWidth) {
            return mouseWidth - menuWidth;
        }
        
        var offset = jQuery("#left-sidebar").width();
        //console.log(offset);
        return (mouseWidth - offset);
    }

    function getTopLocation(e) {
        var mouseHeight = e.pageY;
        var pageHeight = jQuery(window).height();
        var menuHeight = jQuery(settings.menuSelector).height();

        // opening menu would pass the bottom of the page
        if (mouseHeight + menuHeight > pageHeight &&
                menuHeight < mouseHeight) {
            return mouseHeight - menuHeight;
        }
        
        var offset = jQuery("#top-navbar").height();
        //console.log(offset);
        return (mouseHeight - offset);
        
       // return mouseHeight;
    }

};
