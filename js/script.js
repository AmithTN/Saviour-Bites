//Menu increase decrease     

function decreaseQuantity(id) {
    const quantityInput = document.getElementById(`quantity${id}`);
    let quantity = parseInt(quantityInput.value);
    if (quantity > 0) {                                 // Prevent quantity from going below 0
        quantityInput.value = quantity - 1;
        updateSubtotal(id);                             // Update subtotal after quantity change
        updateTotal();                                  // Update total
    }
}

// Function to increase quantity
function increaseQuantity(id) {
    const quantityInput = document.getElementById(`quantity${id}`);
    let quantity = parseInt(quantityInput.value);
    quantityInput.value = quantity + 1;
    updateSubtotal(id);                                 // Update subtotal after quantity change
    updateTotal();                                      // Update total
}

// Function to update subtotal when quantity changes
function updateSubtotal(id) {
    const quantityInput = document.getElementById(`quantity${id}`);
    const priceElement = quantityInput.getAttribute('data-price'); // Get price from the data attribute
    const price = parseInt(priceElement);
    const quantity = parseInt(quantityInput.value);
    const subtotal = quantity * price;

    // Update the subtotal display in the table
    document.getElementById(`subtotal${id}`).innerText = `₹${subtotal}`;
}

// Function to calculate total price for selected products
function calculateTotal() {
    let total = 0;

    // Loop through each product's subtotal and add it to the total
    for (let i = 1; i <= 25; i++) {      // Adjust '25' to match the number of products if needed
        const subtotalElement = document.getElementById(`subtotal${i}`);
        
        if (subtotalElement) {           // Check if the element exists
            const subtotalText = subtotalElement.innerText.replace('₹', '').trim();
            const subtotal = parseInt(subtotalText, 10) || 0;  // Parse subtotal as integer
            total += subtotal;
        } else {
            console.warn(`Element with ID subtotal${i} not found`);
        }
    }

    return total; // Return the final total amount
}

// Function to update the total display
function updateTotal() {
    const totalAmount = calculateTotal();
    const totalDisplay = document.getElementById('totalDisplay');  // Ensure you have an element to display the total
    if (totalDisplay) {
        totalDisplay.innerHTML = `<strong>Total :<span style="color: #409843;">₹${totalAmount}</span></strong>`; // Update total display
    } else {
        console.warn("Element with ID 'totalDisplay' not found");
    }
}

// Call updateTotal() whenever needed, for example after any quantity or subtotal change
updateTotal();




/*Submenu dropdown functionality to work on mobile or tablets */

document.querySelectorAll('.menu-item').forEach(item => 
{
    item.addEventListener('click', event => 
    {
        const submenu = item.querySelector('.submenu');
        if (submenu) 
        {
            submenu.classList.toggle('show');
        }
    });
});



   /*header for mobile navigation*/

 // Toggle main menu open/close

document.querySelector('.toggle-1').addEventListener('click', function() {
    const menuToggle = this;
    const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';

    // Toggle aria-expanded and the class to manage menu visibility

    menuToggle.setAttribute('aria-expanded', !isExpanded);
    document.body.classList.toggle('menu-open');                          // Toggles the "menu-open" class on the body or parent container
});


document.querySelectorAll('.accordion-menu').forEach(menuLink => {
    menuLink.addEventListener('click', function(event) {
        const chevronIcon = this.querySelector('.bi-chevron-down');
        
                                                                      // Check if the clicked element is the chevron (expand icon)
        if (event.target === chevronIcon) {
                                                                     // Prevent default action to stop the link from navigating when clicking the chevron
            event.preventDefault();
            
            // Toggle the submenu visibility
            let submenu = this.nextElementSibling;                  // Get the next element (submenu)
            if (submenu.style.display === 'block') {
                submenu.style.display = 'none';
            } else {
                submenu.style.display = 'block';
            }


            // Rotate the chevron icon
            chevronIcon.classList.toggle('rotated');
        } else {
         
            // Else, let the menu link perform its default behavior (navigation)
            // Do nothing here, so the default action (navigating) happens naturally
         
        }
    });
});


/*WhatsApp chat widget text*/

function updateWhatsAppText() {
    const waText = document.querySelector('.wa__btn_popup_txt span');
    const waContainer = document.querySelector('.wa__btn_popup_txt');

    if (waText) {
        waText.innerHTML = 'Place your order here via <strong>WhatsApp</strong>';
        waContainer.style.visibility = 'visible'; // Make it visible
    }
}

/*subscription*/

function updateSubscriptionText(subscriptionType, whatsappUrl) {
    // Define predefined message for each subscription type
    let predefinedMessage;
    if (subscriptionType === 'Daily') {
        predefinedMessage = "Hi, I’m interested in subscribing to the Daily plan with Savior Bites.";
    } else if (subscriptionType === 'Weekly') {
        predefinedMessage = "Hi, I’m interested in subscribing to the Weekly plan with Savior Bites.";
    } else if (subscriptionType === 'Monthly') {
        predefinedMessage = "Hi, I’m interested in subscribing to the Monthly plan with Savior Bites.";
    }

    // Append the message to the WhatsApp URL
    const whatsappUrlWithMessage = `${whatsappUrl}&text=${encodeURIComponent(predefinedMessage)}`;

    // Select the text container and update the display message
    const waText = document.querySelector('.wa__btn_popup_txt span');
    const waContainer = document.querySelector('.wa__btn_popup_txt');
    
    // Hide the container briefly to reset visibility
    waContainer.style.visibility = 'hidden';

    // Update the text content with subscription type
    waText.innerHTML = `Get your ${subscriptionType} subscription now via <strong>WhatsApp</strong>`;

    // Show the message for a short time before redirecting
    setTimeout(() => {
        waContainer.style.visibility = 'visible';
    }, 100); // Adjust the delay if necessary

    // Delay the redirect to WhatsApp with the custom message
    setTimeout(() => {
        window.location.href = whatsappUrlWithMessage;
    }, 1000); // 1-second delay before redirecting, adjust as needed
}
