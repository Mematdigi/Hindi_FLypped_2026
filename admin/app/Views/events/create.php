<!-- ===== Header Section ===== -->
<?php include(APPPATH . 'Views/header.php'); ?>

    <div class="main-panel">
        <div class="content-wrapper form-container">
        <h1>Create New Event</h1>

        <form action="<?= base_url('/events/store') ?>" method="POST" enctype="multipart/form-data" class="row g-3">
            <?= csrf_field() ?>

            <!-- Event Title and Sub-title -->
            <div class="col-md-6">
                <label for="title" class="form-label">Event Title:</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Enter event title" required>
            </div>
            <div class="col-md-6">
                <label for="sub_title" class="form-label">Sub-title:</label>
                <input type="text" name="sub_title" id="sub_title" class="form-control" placeholder="Enter event sub-title">
            </div>

            <!-- Date and Time -->
            <div class="col-md-6">
                <label for="date" class="form-label">Date:</label>
                <input type="date" name="date" id="date" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="time" class="form-label">Time:</label>
                <input type="time" name="time" id="time" class="form-control">
            </div>

            <!-- Location and Price -->
            <div class="col-md-6">
                <label for="location" class="form-label">Location:</label>
                <input type="text" name="location" id="location" class="form-control" placeholder="Enter event location">
            </div>
            <div class="col-md-6">
                <label for="price" class="form-label">Price:</label>
                <input type="number" name="price" id="price" class="form-control" step="0.01" placeholder="Enter ticket price" required>
            </div>

            <!-- Total Tickets -->
            <div class="col-md-6">
                <label for="total_tickets" class="form-label">Total No. of Tickets:</label>
                <input type="number" name="total_tickets" id="total_tickets" class="form-control" placeholder="Enter total tickets" required>
            </div>

            <!-- Event Image -->
            <div class="col-md-12">
                <label for="image" class="form-label">Event Image:</label>
                <input type="file" name="image" id="image" class="form-control">
            </div>

            <!-- Description -->
            <div class="col-md-12">
                <label for="description" class="form-label">Description:</label>
                <textarea name="description" id="description" class="form-control" rows="5" placeholder="Enter event description" required></textarea>
            </div>

            <!-- Submit Button -->
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary w-100">Publish</button>
            </div>
        </form>


        </div>

    
      <!-- ===== Footer Section ===== -->
      <?php include(APPPATH . 'Views/footer.php'); ?>