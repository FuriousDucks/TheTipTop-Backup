$(document).ready(function () {
    $('#participate-form').submit(function (e) {
        let ticketNumber = $('#number').val();
        let pattern = new RegExp(/^[0-9]{7}$/);
        let alertMessage = $('#alert-message');
        if (!pattern.test(ticketNumber)) {
            e.preventDefault();
            alertMessage.removeClass('d-none');
            alertMessage.text('SVP, entrez un numéro de ticket valide');
            /* Swal.fire({
                title: 'N° de ticket non valide',
                message: 'SVP, entrez un numéro de ticket valide',
                icon: 'error',
                confirmButtonText: 'Ok'
            }); */
        }
    });
});