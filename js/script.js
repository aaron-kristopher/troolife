document.addEventListener("DOMContentLoaded", function() {

	el_autohide = document.querySelector('.autohide');

	// add padding-top to bady (if necessary)
	navbar_height = document.querySelector('.navbar').offsetHeight;
	document.body.style.paddingTop = navbar_height + 'px';

	if (el_autohide) {
		var last_scroll_top = 0;
		window.addEventListener('scroll', function() {
			let scroll_top = window.scrollY;
			if (scroll_top < last_scroll_top) {
				el_autohide.classList.remove('scrolled-down');
				el_autohide.classList.add('scrolled-up');
			}
			else {
				el_autohide.classList.remove('scrolled-up');
				el_autohide.classList.add('scrolled-down');
			}
			last_scroll_top = scroll_top;
		});
		// window.addEventListener
	}
	// if

});

(() => {
	'use strict'

	// Fetch all the forms we want to apply custom Bootstrap validation styles to
	const forms = document.querySelectorAll('.needs-validation')

	// Loop over them and prevent submission
	Array.from(forms).forEach(form => {
		form.addEventListener('submit', event => {
			if (!form.checkValidity()) {
				event.preventDefault()
				event.stopPropagation()
			}

			form.classList.add('was-validated')
		}, false)
	})
})()
