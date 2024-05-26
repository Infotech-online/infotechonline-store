
let coupon_button = document.querySelector(".activate-coupon-modal_button");
let close_button = document.querySelectorAll(".modal-close_button");

let coupon_modal = document.querySelector(".modal_content").style;

let modal_activate_button = document.querySelector(".modal-activate_button");
let modal_success_notify = document.querySelector(".modal-success-notify_container").style;
let modal_error_notify = document.querySelector(".modal-error-notify_container").style;

coupon_button.addEventListener("click", function() {

    coupon_modal.display = "flex";
    let input = $(".bono_id_input");
    input.focus();
      
})

// Ocultar todas las notificaciones al cerrar el modal

close_button.forEach((element, key) => {
    element.addEventListener("click", function() {
        coupon_modal.display = "none";
        modal_success_notify.display = "none";
        modal_error_notify.display = "none";
    })
})