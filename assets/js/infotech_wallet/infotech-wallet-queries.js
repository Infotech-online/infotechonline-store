
// Esta clase gestiona el proceso de activación de un Bono

class ActivateBono {

    constructor(bono_id) {

        // Datos del bono
        this.bono_id = bono_id.toLowerCase();
        this.fecha_vencimiento = null;
        this.info_bono = null;
        this.saldo_bono = null;

        // Datos del usuario actual
        this.user_cc = "1109664949";
        this.user_records = [];

        this.get_bono_data();
    }

    get_user_records(fecha_vencimiento, saldo_bono) {
        
        let self = this

        jQuery(function ($) {

            $.ajax({

                url: `https://jgallego.pythonanywhere.com/api/wallet/registro_bono/${self.user_cc}`,
                type: 'GET',

                success: function(response) {

                    response = response["message"];

                    if (typeof response === "string") {
                        // Se establece la información de registros del usuario
                        response = [];
                    }

                    self.check_availability(response, fecha_vencimiento, saldo_bono);

                },

                error: function (request, status, error) {
                    console.log(request, status, error)
                }
            });
        }) 
    }

    get_bono_data() {

        let self = this

        jQuery(function ($) {

            $.ajax({

                url: `https://jgallego.pythonanywhere.com/api/wallet/bono/${self.bono_id}`,
                type: 'GET',

                success: function(response) {

                    if (response["message"].length > 0) {
                        
                        response = response["message"][0];
                        // Establecer información del bono
                        if (response["Fecha_vencimiento"] == null) {
                            this.fecha_vencimiento = false;
                        } else {
                            this.fecha_vencimiento = new Date(response["Fecha_vencimiento"]);
                        }

                        this.info_bono = response["Info_Bono"];
                        this.saldo_bono = response["Saldo"];

                        self.get_user_records(this.fecha_vencimiento, this.saldo_bono);

                    } else {
                        self.show_error_notify("Cupón no encontrado");
                    }
                },

                error: function (request, status, error) {
                    console.log(request, status, error)
                    self.show_error_notify("Cupón no encontrado");
                }
            });
        })
    }

    check_availability(user_records, fecha_vencimiento, saldo_bono) {

        let available = false;
        let used = false;

        // Si el bono ya fue usado por el cliente no podra volver a ser utilizado
        for (let i = 0; i < user_records.length; i++) {
            if (user_records[i]["Bono_idBono"] === this.bono_id.toLowerCase() && user_records[i]["Estado"] == "Activo") {
                used = true;
            }
        }

        // Si la fecha de vencimiento del bono ya paso no podra ser utilizado
        if (fecha_vencimiento == false || (new Date() < fecha_vencimiento && used == false)) {
            available = true
        }

        // Si despues de la verificación el bono esta disponible
        if (available == true && used == false) {

            // El bono puede activarse
            this.debug("bono disponible")

            // Se registra el balance en la base de datos
            this.register_balance(saldo_bono);
        } 
        if (available == false) {
            this.show_error_notify("El cupón no esta disponible");
        }
        if (used == true) {
            this.show_error_notify("El cupón ya fue usado");
        }
    }

    register_balance(saldo_bono) {

        let self = this

        jQuery(function ($) {

            $.ajax({

                url: `https://jgallego.pythonanywhere.com/api/wallet/registro_bono/create`,
                type: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },                
                data: JSON.stringify({
                    Cedula: parseInt(self.user_cc),
                    idBono: self.bono_id
                }),                  

                success: function(response) {
                    self.show_success_notify(saldo_bono);
                    new loadData();
                },

                error: function (request, status, error) {
                    self.show_error_notify("Cupón no encontrado");
                }
            });
        })
    }

    show_success_notify(balance) {

        // Se ocultan los mensajes de estado
        let modal_error_notify = document.querySelector(".modal-error-notify_container").style;
        modal_error_notify.display = "none";

        // Se muestra el nuevo mensaje
        let modal_success_notify = document.querySelector(".modal-success-notify_container").style;
        document.querySelector(".modal-success_notify_money").textContent = `+ $${balance}`;
        modal_success_notify.display = "flex";

        setTimeout(this.hide_modal, 2000);
    }

    show_error_notify(content) {      
        
        // Se ocultan los mensajes de estado
        let modal_success_notify = document.querySelector(".modal-success-notify_container").style;
        modal_success_notify.display = "none";
        
        // Se muestra el nuevo mensaje
        let modal_error_notify = document.querySelector(".modal-error-notify_container").style;
        document.querySelector(".modal-error_notify").textContent = content;
        modal_error_notify.display = "flex";
    }

    hide_modal() {
        let modal_success_notify = document.querySelector(".modal-success-notify_container").style;
        let modal_error_notify = document.querySelector(".modal-error-notify_container").style;
        let coupon_modal = document.querySelector(".modal_content").style;

        coupon_modal.display = "none";
        modal_success_notify.display = "none";
        modal_error_notify.display = "none";
    }

    debug(content) {
        console.log(content);
    }

}

jQuery(function ($) {

    // Se carga la información de los movimientos
    new loadData();

    // Cuando se presiona la tecla enter
    let input = document.querySelector(".bono_id_input");

    input.addEventListener('keydown', (event) => {
        if (event.keyCode === 13) { // Tecla enter
            // Se obtiene el valor de la input del bono
            let bono_id = input.value;

            new ActivateBono(bono_id);            
        }
    });

    // Cuando se da click en el boton
    $(".modal-activate_button").click(function() {

        // Se obtiene el valor de la input del bono
        let bono_id = $(".bono_id_input").val()

        new ActivateBono(bono_id);
    })

})

class loadData {

    constructor() {
        this.user_cc = "1109664949";

        this.get_table_data();
        this.get_user_data();
    }

    // Se obtienen los movimientos de la persona
    get_table_data() {

        let self = this;

        jQuery(function($) {

            $.ajax({

                url: `https://jgallego.pythonanywhere.com/api/wallet/registro_movimiento/${self.user_cc}`,
                type: 'GET',                

                success: function(response) {

                    console.log(response)

                    if (typeof response["message"] === "string") {
                        response = [];
                    }

                    self.show_table_data(response["message"]);
                },

                error: function (request, status, error) {
                    console.log(error)
                }
            });

        })
    }

    get_user_data() {

        let self = this;

        jQuery(function($) {

            $.ajax({

                url: `https://jgallego.pythonanywhere.com/api/wallet/usuario/${self.user_cc}`,
                type: 'GET',

                success: function(response) {
                    self.get_fondo_data(response["message"][0]["Fondo_NIT"])
                    self.show_user_data(response["message"][0])
                },

                error: function (request, status, error) {
                    console.log(error)
                }
            });

        })
    }

    get_fondo_data(fondo_nit) {

        let self = this;

        jQuery(function($) {

            console.log(fondo_nit)

            $.ajax({

                url: `https://jgallego.pythonanywhere.com/api/wallet/fondo/${fondo_nit}`,
                type: "GET",

                success: function(response) {
                    self.show_fondo_data(response["message"][1][0])
                },
                error: function(request, status, error) {
                    console.log(request, status, error)
                }
            })
        })
    }

    show_table_data(data) {

        jQuery(function($) {

            data = data.reverse();

            // Se limpia todo el contenido dentro de la tabla
            $(".transactions_table tbody").html("");

            // Si el bono ya fue usado por el cliente no podra volver a ser utilizado
            for (let i = 0; i < data.length; i++) {

                let fecha = data[i]["Fecha"]
                fecha = new Date(fecha).toLocaleString();
                let accion = data[i]["Tipo_Accion"]
                let monto = data[i]["Monto"]

                // Debito
                if (parseInt(monto) > 0) {

                    let nuevo_movimiento = `
                    <tr class="movement_row debit">
                        <td>${fecha}</td>
                        <td>${accion}</td>
                        <td>Débito</td>
                        <td>+ $${monto.replace('.00', '')}</td>
                    </tr>
                    `;

                    $(".transactions_table tbody").append(nuevo_movimiento);
                    
                }
                // Credito
                if (parseInt(monto) < 0) {

                    let nuevo_movimiento = `
                    <tr class="movement_row credit">
                        <td>${fecha}</td>
                        <td>${accion}</td>
                        <td>Crédito</td>
                        <td>- $${monto.replace('-', '').replace('.00', '')}</td>
                    </tr>`;

                    $(".transactions_table tbody").append(nuevo_movimiento);
                }
            }
        })
    }

    show_user_data(data) {

        jQuery(function($) {

            $(".user-name").html(data["Nombre"].toUpperCase());
            $(".user-balance").html("$"+data["Saldo"].replace('.00', ''));
        })
    }

    show_fondo_data(data) {

        jQuery(function($) {

            $(".fondo-logo_container").html("");

            if (data["Nombre_legal"] == "Fonsecuritas") {

                let fonsecuritas = `
                <span class="fonsecuritas-logo">
                    <img src="https://www.pythonanywhere.com/user/JGallego/files/home/JGallego/infotechonline-dashboard/cdn/woocommerce-infotechonline/imgs/fonsecuritas_logo.svg"/>
                    Fonsecuritas
                </span>
                <span class="benefits">
                    2% Off y Envío Gratis
                </span>`;

                $(".fondo-logo_container").append(fonsecuritas);
            }
            
        })
    }
}