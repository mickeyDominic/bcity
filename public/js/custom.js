let spinner = '<div class="text-center">' +
    '     <div id="spinner" class="spinner-border" role="status">'+
    '         <span class="visually-hidden">Loading...</span>'+
    '     </div>'+
    '</div>';

let error = '<div class="text-center text-danger">' +
    '     <h4>Failed, please try again or reload page</h4>'+
    '</div>';

$(document).ready(function () {
    let reusableModalLG = $("#reusableModalLG");
    reusableModalLG.on('shown.bs.modal', function (event) {
        $('#reusableModalLG .modal-body #loading').hide();
        $("#reusableModalLG #reusableModalLGLabel").text(event.relatedTarget.getAttribute('data-bs-label'));
        let data = {
            'clientId': event.relatedTarget.getAttribute('data-bs-client'),
            'contactId': event.relatedTarget.getAttribute('data-bs-contact'),
            'reusableApp': event.relatedTarget.getAttribute('data-bs-app'),
        };
        postRequest('/reusable-modal/get-modal-content', data, 'reusableModalLG .modal-body #content');
    });

    reusableModalLG.on('hidden.bs.modal', function () {
        $("#reusableModalLG .modal-body #content").html(spinner);
        $("#reusableModalLG #reusableModalLGLabel").text('...');
    });
});

function postRequest(url, data, targetElement) {
    $("#"+targetElement).html(spinner);
    $.ajax({
        type: "POST",
        url: url,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: data,
        complete: function (response) {
            if (response.status !== 200) {
                $("#"+targetElement).html(error);
            }
        },
        success: function (result) {
            $("#"+targetElement).html(result);
        }
    });
}