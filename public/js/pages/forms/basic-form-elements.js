$(function () {
    //Textare auto growth
    autosize($('textarea.auto-growth'));

    //Datetimepicker plugin
    $('.datetimepicker').bootstrapMaterialDatePicker({
        format: 'dddd DD MMMM YYYY - HH:mm',
        clearButton: true,
        //weekStart: 1
    });

    $('.datepicker').bootstrapMaterialDatePicker({
        format: 'DD MMMM YYYY',
        clearButton: true,
        weekStart: 1,
        formatSubmit: 'yyyy-mm-dd',
        time: false
    });

    $('.timepicker').bootstrapMaterialDatePicker({
        format: 'HH:mm',
        clearButton: true,
        date: false
    });
});