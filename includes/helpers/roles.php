<?php

function add_custom_role() {
    add_role(
        'company_infotech',
        'Company Infotech',
        array(
            'read' => true
        )
    );
}

// Hook the function to the 'init' action
add_action('init', 'add_custom_role');
