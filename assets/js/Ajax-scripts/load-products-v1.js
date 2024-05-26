// Juan Carlos Gallego Barona
/* This script is to load the products with AJAX depending on the category  
 * in which the user is located, with this we fix the deployment
 * of products and at the same time we do it with AJAX.
 * */

function load_products() {
	jQuery(document).ready(function($) {
		var ajaxurl = ajax_object.ajax_url;
		console.log(ajaxurl);

		var data = {
			'action': 'ajax_next_posts',
			'posts_per_page': 12,  // Número de productos por página
			'post_offset': 0     // Desplazamiento de la consulta
		};

		$.post(ajaxurl, data, function(response) {
			// Manejar la respuesta aquí
			var result = JSON.parse(response);
			var newPostsHTML = result[0];
			console.log(newPostsHTML)
			// Actualizar la página con los nuevos productos
			$('.product-grid_container div').append(newPostsHTML);
		});
	});
}

load_products();