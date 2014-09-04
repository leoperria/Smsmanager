Ext.namespace("Ext.ux");

/**
 * This submit action is basically the same as the normal submit action,
 * only that it uses the fields getSubmitValue() to compose the values to submit,
 * instead of looping over the input-tags in the form-tag of the form.
 *
 * To use it, just use the OOSubmit-plugin on either a FormPanel or a BasicForm,
 * or explicitly call form.doAction('oosubmit');
 *
 * @param {Object} form
 * @param {Object} options
 * 
 * To use this, simply include the code somewhere in your page, 
 * and when you want to submit the form dont use form.submit() but form.doAction(’oosubmit’); 
 * This way you may always submit the form in the traditional way.
 */
Ext.ux.OOSubmitAction = function(form, options){
    Ext.ux.OOSubmitAction.superclass.constructor.call(this, form, options);
};

Ext.extend(Ext.ux.OOSubmitAction, Ext.form.Action.Submit, {
    /**
    * @cfg {boolean} clientValidation Determines whether a Form's fields are validated
    * in a final call to {@link Ext.form.BasicForm#isValid isValid} prior to submission.
    * Pass <tt>false</tt> in the Form's submit options to prevent this. If not defined, pre-submission field validation
    * is performed.
    */
    type : 'oosubmit',

    // private
    /**
     * This is nearly a copy of the original submit action run method
     */
    run : function(){
        var o = this.options;
        var method = this.getMethod();
        var isPost = method == 'POST';

        var params = this.options.params || {};
        if (isPost) Ext.applyIf(params, this.form.baseParams);

        //now add the form parameters
        this.form.items.each(function(field)
        {
            if (!field.disabled)
            {
                //check if the form item provides a specialized getSubmitValue() and use that if available
                if (typeof field.getSubmitValue == "function")
                    params[field.getName()] = field.getSubmitValue();
                else
                    params[field.getName()] = field.getValue();
                    
                if(field.getXType()=='checkbox'){
                   params[field.getName()]=(params[field.getName()])?1:0;
                }
            }
        });

        //convert params to get style if we are not post
        if (!isPost) params=Ext.urlEncode(params);

        if(o.clientValidation === false || this.form.isValid()){
            Ext.Ajax.request(Ext.apply(this.createCallback(o), {
                url:this.getUrl(!isPost),
                method: method,
                params:params, //add our values
                isUpload: this.form.fileUpload
            }));

        }else if (o.clientValidation !== false){ // client validation failed
            this.failureType = Ext.form.Action.CLIENT_INVALID;
            this.form.afterAction(this, false);
        }
    }

});
//add our action to the registry of known actions
Ext.form.Action.ACTION_TYPES['oosubmit'] = Ext.ux.OOSubmitAction;


/**
 * This plugin can be either used on BasicForm or FormPanel.
 * In both cases it changes the behaviour of submit() to use
 * the 'oosubmit' action instead of the 'submit' action.
 */
Ext.ux.OOSubmit=function(){

    this.init=function(_object)
    {
        var form=null;
        if (typeof _object.form=="object")
        { //we are a formpanel:
            form=_object.form;
        }
        else form=_object;

        //Save the old submit method:
        form.oldSubmit=form.submit;

        //create a new submit method which calls the oosubmit action per default:
        form.submit=function(options)
        {
              this.doAction('oosubmit', options);
              return this;
        };
    };

};


/**
 * Returns the submit value of the datefield, always in the same format,
 * regardless of display format.
 * The format returned is Y-m-d, because this is common format used in rdbms. (mysql for example)
 *
 */
Ext.form.DateField.prototype.getSubmitValue=function(){
    var v = this.getValue();
    console.log(v);
    if(v !==''){
       var date= new Date(v);
       return date.format("Y-m-d");
    }
    return v;
};