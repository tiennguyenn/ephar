var PatientList = {
    currentPatient: '',
    currentPatientHex: '',
    init: function() {
        jsCommon.pagingAjax();
        PatientList.onClickSorting();
        PatientList.onSubmitForm();
        PatientList.onClickFilter();
        PatientList.onClickDeleteButton();
        PatientList.bootstrap();
        this.initPatientNoteEvent();
    },
    bootstrap: function() {
        var start = moment().startOf('month');
        var end = moment().endOf('month');

        function cb(start, end) {
            $('#reportrange1 span, #reportrange2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#startDate').val(start.format('DD MMM YY'));
            $('#endDate').val(end.format('DD MMM YY'));
        }

        $('#reportrange1, #reportrange2').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);

        $(".select2, .select2-multiple").select2({
            width: null
        });
    },
    onSubmitForm: function() {
        $('#patientSearchForm').submit(function(event) {
            event.preventDefault();

            var url = $(this).data('url');
            var data = $(this).serialize();
            var dataType = 'html';
            var method = 'GET';

            var successCallback = function(data) {
                $("#tableContainer" ).html(data);
                PatientList._checkSortOrder();
            };
            var errorCallback = function() {};
            var loadingContainer = $("#tableContainer");
            jsDataService.callAPI(url, data, method, successCallback, errorCallback, loadingContainer, dataType);
            PatientList.initPatientNoteEvent();


        });

        $('#perPage').change(function() {
            $('#patientSearchForm').submit();
        });

        $('#resetBtn').click(function() {
            $('#isAdvanced').val(0);

            $('#country').val('');
            $(".select2").select2();

            var start = moment().startOf('month');
            var end = moment().endOf('month');

            function cb(start, end) {
                $('#reportrange1 span, #reportrange2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                $('#startDate').val(start.format('DD MMM YY'));
                $('#endDate').val(end.format('DD MMM YY'));
            }

            cb(start, end);

            $('#patientSearchForm').submit();
        });

        $('#updateBtn').click(function() {
            $('#isAdvanced').val(1);
            $('#patientSearchForm').submit();
        });
    },

    onClickFilter: function() {
        $('.caregiver').click(function() {
            var status = $(this).data('status') || 0;

            $('.caregiver').removeClass('active');
            $(this).addClass('active');

            var isAll = true;
            $('#patientSearchForm').find('input[name="caregiver[]"]').remove();
            $('.caregiver').each(function() {
                var value = $(this).data('status');
                if ('all' !== value && ('all' === status || $(this).hasClass('active'))) {
                  var html = '<input type="hidden" name="caregiver[]" value="'+value+'">';
                    $('#patientSearchForm').append(html);
                    isAll = false;
                }
            });

            if (isAll) {
                $('.caregiver').first().addClass('active');
            }

            $('#patientSearchForm').submit();
        });

        $('.patientStatus').click(function() {
            $('.patientStatus').removeClass('active');
            $(this).addClass('active');

            $('#patientSearchForm').find('input[name="deletedOn"]').remove();
            var length = $('.patientStatus.active').length;
            if (length) {
                var value = $('.patientStatus.active').data('value') || 0;
                if (length > 1) {
                    value = length;
                }
                var html = '<input type="hidden" name="deletedOn" value="'+value+'">';
                $('#patientSearchForm').append(html);
            } else {
                $('.patientStatus').first().addClass('active');
            }

            $('#patientSearchForm').submit();
        });
    },
    onClickSorting: function() {
        $('#tableContainer').on('click', '.sorting', function() {
            var list = [];
            list.push($(this).data('sort'));
            if ($(this).hasClass('sorting_asc')) {
                list.push('desc');
            } else {
                list.push('asc');
            }
            $('#sorting').val(list.join('-'));

            $('#patientSearchForm').submit();
        });
    },
    onClickDeleteButton: function() {
        $('#tableContainer').on('click', '.deleteBtn', function() {
            var href = $(this).data('url') || '';
            $('#confirmBtn').attr('href', href);
        });
    },
    _checkSortOrder: function() {
        var sorting = $('#sorting').val();
        if (!sorting) {
            return false;
        }
        var list = sorting.split('-');
        var sort = list[0];
        var order = list[1];

        $('.sorting').removeClass('sorting_desc');
        $('.sorting').removeClass('sorting_asc');

        if ('asc' === order) {
            $('th[data-sort="' + sort + '"]').removeClass('sorting_desc');
            $('th[data-sort="' + sort + '"]').addClass('sorting_asc');
        } else {
            $('th[data-sort="' + sort + '"]').removeClass('sorting_asc');
            $('th[data-sort="' + sort + '"]').addClass('sorting_desc');
        }
    },

    initPatientNoteEvent: function(){
        $("#tableContainer").find(".btn-patient-note").unbind("click");
        PatientList.currentPatient = '';
        PatientList.currentPatientHex = '';
        $("#export-patient-note").unbind("click");

        $("#tableContainer").find(".btn-patient-note").on('click', function () {
            PatientList.currentPatient = $(this).data('id');
            PatientList.currentPatientHex = $(this).data('hexid');
            PatientList.loadPatientNote();
        } );

        $("#export-patient-note").on('click', function (){
            PatientList.exportPatientNote();
        });

    },
    loadPatientNote: function () {
        if(PatientList.currentPatientHex == ''){
            return;
        }
        $.ajax({
            type: "POST",
            url: $("#load-patient-url").val(),
            data: {'patient': PatientList.currentPatientHex,'type': 1},
            beforeSend: function () {
            },
            success: function (data, textStatus, jqXHR) {
                PatientList.renderPatientNoteModal(data);

            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (typeof errorCallback == 'function')
                    return errorCallback(jqXHR, textStatus, errorThrown);
                return false;
            }
        });

    },
    renderPatientNoteModal: function (data) {

        var info =  this.renderPatientInfo(data.patientInformation);
        $("#modal-patient-note").find('.modal-body').first().find('.notes-patient-info').html(info);

        var note = this.renderPatientNote(data.notes);
        $("#modal-patient-note").find('.list-notes').html(note);

        this.resetAddNoteForm();
        $("#modal-patient-note").modal()
    },
    addPatientNote: function (note) {
        $.ajax({
            type: "POST",
            url: $("#load-patient-url").val(),
            data: {'patient': PatientList.currentPatientHex,'type': 2, 'note': note},
            beforeSend: function () {
            },
            success: function (data, textStatus, jqXHR) {
                if(data.success){
                    PatientList.loadPatientNote();
                }

            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (typeof errorCallback == 'function')
                    return errorCallback(jqXHR, textStatus, errorThrown);
                return false;
            }
        });
    },
    exportPatientNote: function () {
        var path = $("#export-patient-url").val();
        path = path.replace('patientId', PatientList.currentPatientHex);
        window.open(path);
    },
    deletePatientNote: function (id) {
        $.ajax({
            type: "POST",
            url: $("#load-patient-url").val(),
            data: {'patient': PatientList.currentPatientHex,'type': 3, 'note-id': id},
            beforeSend: function () {
            },
            success: function (data, textStatus, jqXHR) {
                if(data.success){
                    PatientList.loadPatientNote();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                if (typeof errorCallback == 'function')
                    return errorCallback(jqXHR, textStatus, errorThrown);
                return false;
            }
        });
    },
    renderPatientNote: function(notes){
        var result = '';
        $.each(notes,function () {
            result += '<div class="each-notes">'
                + '<div class="notes-head bg-light-blue clearfix">'
                + '<div class="pull-left"><span class="notes-date bold"><i>'+ this.time +'</i></span></div>'
                + '<div class="pull-right"><a href="javascript:;" data-id="'+this.id+'" class="notes-delete bold">Delete Note</a></div>'
                + '</div>'
                + '<div class="notes-body"><p>' + this.note
                + '</p></div>'
                + '</div>';
        });
        return result;
    },
    resetAddNoteForm: function () {
        $("#modal-patient-note").find( "#btn-add-note").unbind( "click" );
        $("#modal-patient-note").find("#note-content").val("");
        $("#modal-patient-note").find( ".notes-delete").unbind( "click" );


        $("#modal-patient-note").find( "#btn-add-note").on('click', function () {
            PatientList.addPatientNote($("#modal-patient-note").find("#note-content").val());
        });

        $("#modal-patient-note").find( ".notes-delete").on('click', function () {
            PatientList.deletePatientNote($(this).data('id'));
        });
    },

    renderPatientInfo: function (patientInformation) {
        var result = '<div class="info-item">'
            + '<span>Patient Name:</span>'
            + '<span class="bold"> '+ patientInformation.name + ' ('+ patientInformation.globalId + ')</span>'
            + '</div>'
            + '<div class="info-group">'
            + '<div class="info-item"><span>Gender:</span> <span class="bold"> ' + patientInformation.gender + ' </span></div>'
            + '<div class="info-item"><span>Age:</span> <span class="bold"> ' + patientInformation.age + ' </span></div>'
            + '<div class="info-item"><span>Country:</span> <span class="bold"> ' +  patientInformation.address +'</span></div>'
            + '</div>'
            + '<div class="info-item patient-medication-allergies">'
            + '<span>Known Medication Allergies: '+  patientInformation.allergies + '</span>'
            + '</div>'
            + '<div>'
            + '<strong>Diagnosis: ' +  patientInformation.diagnosis + '</strong>'
            + '</div>';

        return result;

    }
}

$(document).ready(function() {
    PatientList.init();
});