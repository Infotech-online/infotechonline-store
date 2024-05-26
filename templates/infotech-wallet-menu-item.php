
<!--<div class="infotech-wallet-content_container">

    <div class="content_container">
        
        <h3>Monedero Infotech Online</h3>

        <div class="fondo-logo_container">
            <span class="fonsecuritas-logo">Fonsecuritas</span>
        </div>

    </div>

</div>-->

<div class="infotech-wallet-content_container">

    <div class="content_container">
        
        <h4 class="user-name"></h4>

        <div class="fondo-logo_container">
        </div>

        <div class="line"></div>

        <div class="saldo-content_container">
            <div class="saldo_content">
                <span>Saldo: </span><span class="user-balance"></span>
            </div>
            <button type="button" class="activate-coupon-modal_button">
                <span class="button__text">Añadir Cupón</span>
                <span class="button__icon"><svg xmlns="http://www.w3.org/2000/svg" width="24" viewBox="0 0 24 24" stroke-width="2" stroke-linejoin="round" stroke-linecap="round" stroke="currentColor" height="24" fill="none" class="svg"><line y2="19" y1="5" x2="12" x1="12"></line><line y2="12" y1="12" x2="19" x1="5"></line></svg></span>
            </button>

        </div>

    </div>

    <div class="transactions-content_container">

        <table class="transactions_table">
            <thead>
                <tr class="header_row">
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Movimiento</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                <!--<tr>
                    <td colspan="3">
                        <div class="loader_container">
                            <div class="newtons-cradle">
                                <div class="newtons-cradle__dot"></div>
                                <div class="newtons-cradle__dot"></div>
                                <div class="newtons-cradle__dot"></div>
                                <div class="newtons-cradle__dot"></div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="movement_row credit">
                    <td>2024/05/02</td>
                    <td>Crédito</td>
                    <td>- $50.000</td>
                </tr>
                <tr class="movement_row debit">
                    <td>2024/04/30</td>
                    <td>Débito</td>
                    <td>+ $500.000</td>
                </tr>
                <tr class="movement_row debit">
                    <td>2024/04/30</td>
                    <td>Débito</td>
                    <td>+ $50.000</td>
                </tr>-->
                
            </tbody>
        </table>
    </div>
    <!--
    <div class="table-buttons_container">
        <span>Anterior </span>
        <span>Siguiente </span>
    </div>-->
</div>

<div class="modal_content">
    
    <div class="modal">
        <article class="modal-container">
            <header class="modal-container-header">
                <h3 class="modal-container-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512">
                        <rect x="48" y="144" width="416" height="288" rx="48" ry="48" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/>
                        <path d="M411.36 144v-30A50 50 0 00352 64.9L88.64 109.85A50 50 0 0048 159v49" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/>
                        <path d="M368 320a32 32 0 1132-32 32 32 0 01-32 32z"/>
                    </svg>
                    Registrar Saldo
                </h3>
                <button class="icon-button modal-close_button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                        <path fill="none" d="M0 0h24v24H0z" />
                        <path fill="currentColor" d="M12 10.586l4.95-4.95 1.414 1.414-4.95 4.95 4.95 4.95-1.414 1.414-4.95-4.95-4.95 4.95-1.414-1.414 4.95-4.95-4.95-4.95L7.05 5.636z" />
                    </svg>
                </button>
            </header>
            <section class="modal-container-body rtf">
                <span>Ingresar Código del Cupón</span>
                <input class="input bono_id_input">

                <div class="modal-error-notify_container" style="display: none;">
                    <span class="modal-error_notify">...</span>
                </div>
                
                <div class="modal-success-notify_container" style="display: none;">
                    <span class="modal-success_notify" style="color: rgb(82, 192, 88);">Cupón activado con éxito!</span>
                    <span class="modal-success_notify_money" style="color: rgb(82, 192, 88); font-size: 18px;">+ $50.000</span>    
                </div>
            </section>
            <footer class="modal-container-footer">
                <button class="button is-ghost modal-close_button">Cancelar</button>
                <button class="button is-primary modal-activate_button">Activar</button>
            </footer>
        </article>
    </div>
</div>