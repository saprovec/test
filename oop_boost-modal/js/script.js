var appForm = {
    form: {
        phone_pattern : /^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/,
        email_pattern : /^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/i,

        addClassError: function (element) {
            if(element.hasClass('correctly')){
                element.removeClass('correctly').addClass('error');
            } else {
                element.addClass('error');
            }
        },
        addClassCorrectly: function (element) {
            if(element.hasClass('error')){
                element.removeClass('error').addClass('correctly');
            } else {
                element.addClass('correctly');
            }
        },
        validate: function (formSample,formErrorMessage) {
            var flagValidate = true,
                str = [],
                formErrorMessageField = $(formErrorMessage + ' span.field'),
                context = this;
            formSample.each(function() {
                if(context.check($(this)) === false){
                    flagValidate = false;
                    str.push($(this).data('name'));
                }
            });
            formErrorMessageField.text(str.join(','));
            return flagValidate;
        },
        checkCheckbox: function (checkbox) {
            var context = this;
            if(checkbox.is(':checked')){
                context.addClassCorrectly(checkbox);
                return true;
            } else {
                context.addClassError(checkbox);
                return false;
            }
        },
        check: function (element) {
            var context = this;
            if(element.data('type')==='checkbox'){
                return context.checkCheckbox(element);
            } else if (element.val()) {
                if (this[element.data('type')+'_pattern'] === undefined ){
                    context.addClassCorrectly(element);
                    return true;
                } else if (this[element.data('type')+'_pattern'].test(element.val())){
                    context.addClassCorrectly(element);
                    return true;
                } else {
                    if(event.type === 'submit'){
                        var errorMessage = (element.data('errorMessage') !== undefined) ? element.data('errorMessage') : element.closest('form').data('errorMessage');
                        element.val('').attr('placeholder', errorMessage);
                    }
                    context.addClassError(element);
                    return false;
                }
            } else {
                element.attr('placeholder', 'Поле обязательно для заполнения');
                context.addClassError(element);
                return false;
            }
        },
        validateServer: function (response,formSample,formErrorMessage) {
            var str = [],
                context = this,
                formErrorMessageField = $(formErrorMessage + ' span.field');
            formSample.each(function() {
                if(response[$(this).attr('name')]){
                    context.addClassCorrectly($(this));
                } else {
                    context.addClassError($(this));
                    if($(this).val() === ''){
                        $(this).attr('placeholder', 'Поле обязательно для заполнения');
                    }
                    str.push($(this).data('name'));
                }
            });
            formErrorMessageField.text(str.join(','));
            if(str.length > 0){
                $(formErrorMessage).show();
            }
        },
        closeForm: function (modalForm,formSuccessMessage) {
            modalForm.fadeOut(400,function () {
                if($(this).css('display')==='block') {
                    $(this).hide("fast");
                }
                //$('div.overlay').fadeOut(400);
                $('div.modal-footer').slideUp("fast");
                $(formSuccessMessage).show("fast");
            });
        },
        openForm: function (modalForm) {
            $('div.overlay').fadeIn(400);
            $(modalForm).fadeIn(400,function () {
                if($(this).css('display')==='none') {
                    $(this).show();
                }
            });
            return false;
        },
        formAjax: function (formId,formSuccessMessage,formErrorMessage) {
            var context = this,
                modalForm = $('#' + formId),
                formSample = modalForm.find("*.required:not('[type=submit]')");
            if (context.validate(formSample,formErrorMessage) === false) {
                $(formErrorMessage).show('fast');
                return false;
            } else {
                $.ajax({
                    type: modalForm.attr('method'),
                    url: modalForm.attr('action'),
                    dataType: 'json',
                    data: modalForm.serialize()
                }).done(function(response) {
                    switch(response) {
                        case 'successfully':
                            context.closeForm(modalForm,formSuccessMessage);
                           // $(formSuccessMessage).show("slow");
                            break;
                        case 'error':
                            alert(modalForm.data('attention'));
                            break;
                        default:
                            context.validateServer(response,formSample,formErrorMessage);
                    }
                }).fail(function() {
                    alert(modalForm.data('disconnect'));
                });
                return false;
            }
        },
        onlyValidate: function (element,formSample,formErrorMessage) {
            var context = this;
            if (element.closest('form').hasClass('novalid')){
                if(element.hasClass('error')) {
                    element.removeClass('error');
                } else if(element.hasClass('correctly')) {
                    element.removeClass('correctly');
                } else return false;
            } else {
                context.check(element);
            }
            if(formSample.hasClass('error')){
                return false;
            } else {
                if(formErrorMessage.css('display')==='block') {
                    formErrorMessage.hide('fast');
                } else return false;
            }
        }
    },
    init: function () {
        var context = this,
            modalForm = $('form.feedback_form'),
            formErrorMessage = $('div.error-message'),
            formSample = $("form.feedback_form *.required:not('[type=submit]')");

        $('a.close, div.overlay').on('click', function(event){
            event.stopPropagation();
            context.form.closeForm(modalForm);
            return false;
        });

        formSample.on('blur', function () {
            context.form.onlyValidate($(this),formSample,formErrorMessage)
        });
    }
};

var appFormLoad = function () {
    jQuery.appForm = appForm;
    appForm.init();
};
var appFormInt = setInterval(function () {
    if (typeof jQuery !== 'function') return;
    clearInterval(appFormInt);
    setTimeout(appFormLoad, 0);
}, 50);
