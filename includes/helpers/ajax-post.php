<?php
// Agrega un manejador para procesar la solicitud del archivo PDF
add_action('wp_ajax_save_pdf', 'guardar_archivo_pdf');
add_action('wp_ajax_nopriv_save_pdf', 'guardar_archivo_pdf'); // Si no se requiere autenticación

function guardar_archivo_pdf() {
    $uploaded_file = $_FILES['pdfFile'];

    if ($uploaded_file['error'] === 0) {
        // Directorio donde deseas guardar el archivo
        $upload_dir = wp_upload_dir();
        $file_path = $upload_dir['path'] . '/' . $uploaded_file['name'];

        // Mueve el archivo PDF al directorio deseado en el servidor
        move_uploaded_file($uploaded_file['tmp_name'], $file_path);

        // Haz algo con el archivo guardado, como insertar su información en la base de datos o procesarlo de otra manera
        // ...
    }

    wp_die(); // Finaliza la respuesta AJAX
}