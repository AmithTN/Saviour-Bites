// Add an event listener to handle form submission
document.querySelector('.elementor-form').addEventListener('submit', function (event) {
  event.preventDefault(); // Prevent the form from refreshing the page

  // Collect form data
  const formData = {
    fullname: document.getElementById('form-field-fullname').value,
    PhoneNo: document.getElementById('form-field-PhoneNo').value,
    email: document.getElementById('form-field-email').value,
    message: document.getElementById('form-field-message').value,
  };

  // Send the form data using EmailJS
  emailjs
    .send('service_xv2kvlp', 'template_98j8ldp', formData) // Replace with your actual EmailJS service and template IDs
    .then(
      function (response) {
        // Success message
        alert('Message sent successfully! We will get back to you shortly.');
        console.log('SUCCESS!', response.status, response.text);
      },
      function (error) {
        // Error message
        alert('Oops! Something went wrong. Please try again later.');
        console.log('FAILED...', error);
      }
    );

  // Optionally, reset the form after submission
  event.target.reset();
});
