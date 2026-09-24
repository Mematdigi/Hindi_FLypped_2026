<!-- ==== Header Section ==== -->
<?php include(APPPATH . 'Views/components/header.php'); ?>
<link rel="stylesheet" href="<?php echo base_url('public/assest/scss/style.css'); ?>">


<section class="event-detail-section">
    <div class="event-detail-container">
        <div class="event-detail-image">
            <img src="<?= base_url('admin/uploads/' . $event['image']) ?>" alt="<?= esc($event['title']) ?>" class="event-image">
        </div>
        <div class="event-detail-content">
            <h1 class="event-detail-title"><?= esc($event['title']) ?></h1>
            <p class="event-detail-subtitle"><?= esc($event['sub_title']) ?></p>

            <!-- Event Information -->
            <div class="event-detail-info">
                <div class="event-info-item">
                    <span class="icon calendar-icon">📅</span>
                    <span><?= esc($event['date']) ?></span>
                </div>
                <div class="event-info-item">
                    <span class="icon clock-icon">⏰</span>
                    <span><?= esc($event['time']) ?></span>
                </div>
                <div class="event-info-item">
                    <span class="icon location-icon">📍</span>
                    <span><?= esc($event['location']) ?></span>
                </div>
            </div>

            <!-- Ticket Availability -->
            <div class="ticket-status">
                <?php if ($event['total_tickets'] > 0): ?>
                    <?php if ($event['total_tickets'] <= 5): ?>
                        <span class="few-tickets">Few Tickets Left!</span>
                    <?php else: ?>
                        <span class="ticket-available">Tickets Available</span>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="houseful">Houseful</span>
                <?php endif; ?>
            </div>

            <div class="event-detail-footer">
                <?php if ($event['total_tickets'] > 0): ?>
                    <span class="event-price">₹<?= esc($event['price']) ?> onwards</span>
                    <button class="book-ticket-button" onclick="openTicketBox()">Book Tickets</button>
                <?php else: ?>
                    <span class="event-houseful">Tickets Sold Out</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Ticket Box -->
    <div class="ticket-box" id="ticketBox">
        <div class="ticket-box-header">
            <h3>Book Your Tickets</h3>
            <button class="close-button" onclick="closeTicketBox()">×</button>
        </div>
        <div class="ticket-box-content">
            <div class="ticket-counter">
                <button class="decrease-btn" onclick="updateTicketCount(-1)">-</button>
                <span id="ticketCount">1</span>
                <button class="increase-btn" onclick="updateTicketCount(1)">+</button>
            </div>
            <div class="ticket-price">
                <span id="totalPrice">₹<?= esc($event['price']) ?></span>
                <small id="ticketInfo">1 ticket added</small>
            </div>
            <button class="proceed-button" onclick="proceedToPay()">Proceed to Pay</button>
        </div>
    </div>
</section>


<!-- About Section -->
<section class="about-section">
    <div class="about_section_box">
        <h2>About</h2>
        <div class="about-content" id="aboutContent">
            <?= nl2br(esc($event['description'])) ?>
        </div>
        <button class="toggle-button" id="toggleButton" onclick="toggleAbout()">Show More</button>
    </div>
</section>


        
<!-- ==== Footer Section ==== -->
<?php include(APPPATH . 'Views/components/footer.php'); ?>

<script>
    let ticketPrice = <?= esc($event['price']) ?>; // Ticket price from backend
    let ticketCount = 1; // Default ticket count
    let totalTickets = <?= esc($event['total_tickets']) ?>; // Total tickets available

    function openTicketBox() {
        const ticketBox = document.getElementById('ticketBox');
        if (totalTickets > 0) {
            ticketBox.classList.add('active'); // Slide in the ticket box
        } else {
            alert('Tickets are sold out!');
        }
    }

    function closeTicketBox() {
        const ticketBox = document.getElementById('ticketBox');
        ticketBox.classList.remove('active'); // Slide out the ticket box
    }

    function updateTicketCount(change) {
        const countDisplay = document.getElementById('ticketCount');
        const priceDisplay = document.getElementById('totalPrice');
        const ticketInfo = document.getElementById('ticketInfo');

        // Update ticket count within allowed range
        const updatedCount = ticketCount + change;
        if (updatedCount > 0 && updatedCount <= totalTickets) {
            ticketCount = updatedCount;
            countDisplay.textContent = ticketCount;

            // Update total price
            const totalPrice = ticketCount * ticketPrice;
            priceDisplay.textContent = `₹${totalPrice.toLocaleString('en-IN')}`;

            // Update ticket info
            ticketInfo.textContent = `${ticketCount} ticket${ticketCount > 1 ? 's' : ''} added`;
        } else if (updatedCount > totalTickets) {
            alert('Only ' + totalTickets + ' tickets are available.');
        }
    }

    // function proceedToPay() {
    //     const totalAmount = ticketPrice * ticketCount * 100; // Amount in paise (Razorpay API works in INR paise)
    //     const options = {
    //         "key": "rzp_live_S0bV3KqHlmJZvU", // Replace with your Razorpay Key ID
    //         "amount": totalAmount, // Total amount in paise
    //         "currency": "INR",
    //         "name": "Event Booking",
    //         "description": `Booking ${ticketCount} ticket(s) for ₹${(totalAmount / 100).toLocaleString('en-IN')}`,
    //         "image": "https://your-logo-url.com", // Replace with your logo URL
    //         "handler": function (response) {
    //             // Callback function on successful payment
    //             alert("Payment Successful! Payment ID: " + response.razorpay_payment_id);

    //             // Add your server-side code here to store payment details in the database
    //             savePaymentDetails(response.razorpay_payment_id, totalAmount, ticketCount);
    //         },
    //         "prefill": {
    //             "name": "John Doe", // Replace with user's name
    //             "email": "john.doe@example.com", // Replace with user's email
    //             "contact": "9999999999" // Replace with user's phone number
    //         },
    //         "theme": {
    //             "color": "#007bff" // Customize theme color
    //         }
    //     };

    //     const rzp = new Razorpay(options);
    //     rzp.open();

    //     rzp.on('payment.failed', function (response) {
    //         // Handle payment failure
    //         alert("Payment Failed: " + response.error.description);
    //     });
    // }

    function savePaymentDetails(paymentId, amount, ticketsBooked) {
        // You can make an AJAX request to save payment details to the database
        console.log("Payment ID:", paymentId);
        console.log("Amount:", amount);
        console.log("Tickets Booked:", ticketsBooked);

        // Implement server-side saving logic here
        // Example AJAX call:
        fetch('<?= base_url('save-payment-details') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                paymentId: paymentId,
                amount: amount,
                ticketsBooked: ticketsBooked
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log("Payment details saved successfully:", data);
        })
        .catch(error => {
            console.error("Error saving payment details:", error);
        });
    }
</script>



