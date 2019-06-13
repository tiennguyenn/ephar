var Form = {
    init: function() {
        Form.validateForm();
        Form.onClickSaveBtn();
    },
    validateForm: function() {
        $('#welcomeEmailForm').validate();
        $('#welcomesmsForm').validate();
    },
    onClickSaveBtn: function() {
        $('.saveBtn').click(function() {
            $(this).parents('form').first().submit();
        });
    }
};

$(document).ready(function() {
    Form.init();
});
