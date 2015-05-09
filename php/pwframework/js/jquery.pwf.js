/**
 * extra option for a bootbutton to prepend a confirmation dialog
 * @param {type} form
 * @param {type} selector
 * @param {type} unit
 * @param {type} action
 * @returns {undefined}
 */
bootbuttonSubmitConfirm = function(form, selector, unit, action, unit_id, action_id){
    
    var valueToCheck = jQuery(selector).val();
    
    
    if(typeof valueToCheck !== "undefined" && valueToCheck.length > 0){

        bootbox.confirm(confirm_label, function(result){
            if(result === true){
               jQuery("#" + unit_id).val(unit);
               jQuery("#" + action_id).val(action);		
               jQuery("[name=" + form + "]").submit();   
           }else{
               return;
           }

        });
    }else{
        bootbox.confirm(nothing_selected_label);
    }
};
/**
* extra option for a bootbutton Sets the task values and submits the specified form, no questions asked
 * @param {type} form
 * @param {type} selector
 * @param {type} unit
 * @param {type} action
 * @returns {undefined}
 */
bootbuttonSubmit = function(formName, unit, action, unit_id, action_id){    
    jQuery("#" + unit_id).val(unit);
    jQuery("#" + action_id).val(action);		
    var form = document.forms[formName];
    jQuery(form).submit();
    
};

