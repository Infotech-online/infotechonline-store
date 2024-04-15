<?php

/* 
This functionality is only available in development since the WooCommerce tables in production status changed their organization, 
they are no longer saved in wc_orders but in other tables pre-organized by WooCommerce.
*/
// Get Credit Orders ( Development )
function get_credit_orders() {

    global $wpdb;
    
    $orders = "
        SELECT o.id, o.status, o.total_amount, o.customer_id, o.billing_email, o.date_created_gmt, a.company, u.user_nicename
        FROM {$wpdb->prefix}wc_orders o
        INNER JOIN {$wpdb->prefix}wc_order_addresses a ON o.id = a.order_id
        INNER JOIN {$wpdb->prefix}users u ON o.customer_id = u.id
        WHERE o.payment_method = 'infotech_credit_payment'
    ";
    $orders = $wpdb->get_results($orders);

    return $orders;
}

// Get Cart Products

function get_cart_products() {
    
    global $wpdb;
    global $woocommerce;

    $current_user = wp_get_current_user();

    if ($current_user->ID !== 0) {

        $user_id = $current_user->ID;

        $session_table = $wpdb->prefix . 'woocommerce_sessions';

        $current_user = wp_get_current_user();
        $user_id = wp_get_current_user()->ID;

        // ID de la sesión del usuario (puedes obtenerlo según tu lógica de identificación de usuario)
        $user_session_id = $user_id;

        // Get session_value from the database
        $cart_data = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT session_value FROM $session_table WHERE session_key = $user_session_id"
            )
        );

        // echo $cart_data;
        $cart_contents = maybe_unserialize($cart_data);
        $cart_content = maybe_unserialize($cart_contents["cart"]);

        // Veify if session_var contains cart info
        if (count($cart_content) > 0) {

            foreach ($cart_content as $product) {

                $cart_data = array(
                    'product_id' => $product["product_id"],
                    'product_qty' => $product["quantity"],
                    'total' => $product["line_total"]
                );
            }

            return $cart_data;

        } else {
            return false;
        }

    } else {
        // User isn't logged in 
        $login_url = wp_login_url();

        // Redirect to login
        wp_redirect($login_url);
        exit;
    }
}

// Get Credit Oreders ( Production )

// Create Quotes Tables
function create_quote_tables() {
    
    global $wpdb;
    $quotes_table = $wpdb->prefix . 'quotes';
    $quotes_items_table = $wpdb->prefix . 'quotes_items';
    $quotes_user_information = $wpdb->prefix . 'quotes_user_information';

    // If the table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$quotes_table'") != $quotes_table) {
        $query = "CREATE TABLE $quotes_table (
            quote_id INT NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            creation_date DATETIME NOT NULL,
            expiry_date DATETIME NOT NULL,
            discount INT,
            taxes DECIMAL(3,2) NOT NULL,
            shipping INT NOT NULL,
            referred VARCHAR(100),
            PRIMARY KEY (quote_id)
        )";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($query);
    }

    // If the table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$quotes_items_table'") != $quotes_items_table) {
        $query = "CREATE TABLE $quotes_items_table (
            item_id INT NOT NULL AUTO_INCREMENT,
            quote_id INT NOT NULL,
            product_id VARCHAR(500) NOT NULL,
            product_qty INT NOT NULL,
            unit_price INT NOT NULL,
            PRIMARY KEY (item_id)
        )";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($query);
    }

    // If the table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$quotes_user_information'") != $quotes_user_information) {
        $query = "CREATE TABLE $quotes_user_information (
            information_id INT NOT NULL AUTO_INCREMENT,
            quote_id INT NOT NULL,
            employee_name VARCHAR(100),
            company VARCHAR(100) NOT NULL,
            company_address VARCHAR(200) NOT NULL,
            city VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(10) NOT NULL,
            PRIMARY KEY (information_id)
        )";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($query);
    }
}

function get_quotes($user_id = "") {

    global $wpdb;
    
    // Get quote basic info
    $quotes = "
        SELECT q.quote_id, q.expiry_date, q.creation_date, q.discount, q.taxes, q.shipping, q.user_id, u.user_nicename, u.user_email
        FROM {$wpdb->prefix}quotes q
        INNER JOIN {$wpdb->prefix}users u ON q.user_id = u.id
    ";

    if ($user_id != "") {
        // Get quote basic info
        $quotes = "
            SELECT q.quote_id, q.creation_date, q.expiry_date, q.discount, q.taxes, q.shipping, q.user_id, u.user_nicename, u.user_email
            FROM {$wpdb->prefix}quotes q
            INNER JOIN {$wpdb->prefix}users u ON q.user_id = u.id
            WHERE q.user_id = $user_id
        ";
    }
    
    $quotes = $wpdb->get_results($quotes);

    return $quotes;
}

function get_single_quotation($quotation_id) {

    global $wpdb;
    
    // Get quote basic info
    $quotation = "
        SELECT q.quote_id, q.creation_date, q.expiry_date, q.taxes, q.shipping, q.user_id, u.user_nicename, u.user_email
        FROM {$wpdb->prefix}quotes q
        INNER JOIN {$wpdb->prefix}users u ON q.user_id = u.id
        WHERE q.quote_id = $quotation_id
    ";

    $quotation_data = $wpdb->get_results($quotation);

    return $quotation_data;
}

function get_quote_items($quote_id) {
    
    global $wpdb;

    // Get quote items
    $quote_items = "
        SELECT q.product_id, q.product_qty, q.unit_price
        FROM {$wpdb->prefix}quotes_items q
        WHERE q.quote_id = $quote_id
    ";

    $quote_items = $wpdb->get_results($quote_items);

    $products = array();
    foreach ($quote_items as $item) {

        // Get product shipping classes
        $shipping_class = wc_get_product($item->product_id)->get_shipping_class();

        $product = wc_get_product($item->product_id);
        $new_product = [
            "product_sku" => $product->get_sku(),
            "product_name" => $product->get_name(),
            "quantity" => $item->product_qty,
            "unit_price" => $item->unit_price,
            "total_price" => intval($item->unit_price) * intval($item->product_qty),
            "shipping_class" => $shipping_class
        ];
        array_push($products, $new_product);
    }

    return $products;
}

function get_quote_user_information($quotation_id) {

    global $wpdb;
    
    // Get quote basic info 
    $query = "
        SELECT q.quote_id, q.employee_name, q.company, q.company_address, q.city, q.email, q.phone
        FROM {$wpdb->prefix}quotes_user_information q
        WHERE q.quote_id = $quotation_id
    ";

    $user_information = $wpdb->get_results($query);

    return $user_information;
}

function create_quote_log($employee_name, $company, $address, $city, $email, $phone, $referred) {

    $taxes = 1.19;
    $discount = 0;

    global $wpdb;
    global $woocommerce;

    $current_user = wp_get_current_user();

    if ($current_user->ID !== 0) {

        $user_id = $current_user->ID;

        $session_table = $wpdb->prefix . 'woocommerce_sessions';

        $current_user = wp_get_current_user();
        $user_id = wp_get_current_user()->ID;

        // ID de la sesión del usuario (puedes obtenerlo según tu lógica de identificación de usuario)
        $user_session_id = $user_id;

        // Get session_value from the database
        $cart_data = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT session_value FROM $session_table WHERE session_key = $user_session_id"
            )
        );

        // echo $cart_data;
        $cart_contents = maybe_unserialize($cart_data);
        $cart_content = maybe_unserialize($cart_contents["cart"]);

        // Veify if session_var contains cart info
        if (count($cart_content) > 0) {

            $base_shipping = 15000;
            $quotes_table = $wpdb->prefix . 'quotes';

            $data_to_insert = array(
                'user_id' => $user_id,
                'creation_date' => date('Y-m-d'),
                'expiry_date' => date('Y-m-d', strtotime('+1 days', strtotime(date('Y-m-d')))),
                'discount' => $discount,
                'taxes' => $taxes,
                'shipping' => $base_shipping,
                'referred' => $referred
            );

            // Insert the new quote data
            $wpdb->insert($quotes_table, $data_to_insert);

            $last_inserted_id = $wpdb->insert_id;

            $quotes_user_information = $wpdb->prefix . 'quotes_user_information';
            $user_information_data = array(
                'quote_id' => $last_inserted_id,
                'employee_name' => $employee_name,
                'company' => $company,
                'company_address' => $address,
                'city' => $city,
                'email' => $email,
                'phone' => $phone
            );

            $result = $wpdb->insert($quotes_user_information, $user_information_data);

            $quotes_items_table = $wpdb->prefix . 'quotes_items';
            $total = 0;
            $shipping_total = 0;

            foreach ($cart_content as $product) {

                $product_price = $product["line_total"] / $product["quantity"];
                $total += $product_price * $product["quantity"];

                $data_to_insert = array(
                    'quote_id' => $last_inserted_id,
                    'product_id' => $product["product_id"],
                    'product_qty' => $product["quantity"],
                    'unit_price' => $product_price
                );

                // Get product shipping classes
                $shipping_class = wc_get_product($product["product_id"])->get_shipping_class();
                
                if ($shipping_class == 'b-fee') {
                    $shipping_total += ($product_price * $product["quantity"]) * $taxes;
                    $new_shipping = ($shipping_total / $taxes) * 0.011;
                } else {
                    if ($new_shipping < $base_shipping) { $new_shipping = $base_shipping; }
                }

                // Insert the new quote item
                $wpdb->insert($quotes_items_table, $data_to_insert);
            }
            
            $update_shipping = "UPDATE $quotes_table SET shipping = $new_shipping WHERE quote_id = $last_inserted_id";
            $wpdb->query($update_shipping);

            return $last_inserted_id;

        } else {
            return false;
        }

    } else {
        // User isn't logged in 
        $login_url = wp_login_url();

        // Redirect to login
        wp_redirect($login_url);
        exit;
    }

}