<!-- ===== Header Section ===== -->
<?php include(APPPATH . 'Views/header.php'); ?>

    <!-- ==== EDIT EVENT PAGE ==== -->
    <div class="main-panel">
    <div class="content-wrapper">
        <h1 class="page-title">Edit Event</h1>
        <form action="<?= base_url('/events/update/' . $event['id']) ?>" method="post" enctype="multipart/form-data" class="mmd_edit_event mb-5">
            <?= csrf_field() ?>

            <!-- Grid Container -->
            <div class="form-grid">
                <!-- Event Title -->
                <div class="form-group">
                    <label for="title">Event Title:</label>
                    <input type="text" name="title" id="title" class="form-control" value="<?= esc($event['title']) ?>" required>
                </div>

                <!-- Event Sub-title -->
                <div class="form-group">
                    <label for="sub_title">Sub-title:</label>
                    <input type="text" name="sub_title" id="sub_title" class="form-control" value="<?= esc($event['sub_title']) ?>">
                </div>

                <!-- Event Date -->
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" name="date" id="date" class="form-control" value="<?= esc($event['date']) ?>" required>
                </div>

                <!-- Event Time -->
                <div class="form-group">
                    <label for="time">Time:</label>
                    <input type="time" name="time" id="time" class="form-control" value="<?= esc($event['time']) ?>" required>
                </div>

                <!-- Event Location -->
                <div class="form-group">
                    <label for="location">Location:</label>
                    <input type="text" name="location" id="location" class="form-control" value="<?= esc($event['location']) ?>" required>
                </div>

                <!-- Event Price -->
                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="text" name="price" id="price" class="form-control" value="<?= esc($event['price']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="total_tickets" class="form-label">Total No. of Tickets:</label>
                    <input type="number" name="total_tickets" id="total_tickets" class="form-control" value="<?= esc($event['total_tickets']) ?>" required>
                </div>

            </div>

            <!-- Description (Full-width) -->
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" class="form-control" rows="5" required><?= esc($event['description']) ?></textarea>
            </div>

            <div class="form-grid">
                <!-- Upload New Event Image -->
                <div class="form-group">
                    <label for="image">Upload New Event Image:</label>
                    <input type="file" name="image" id="image" class="form-control">
                    <small>Leave empty to keep the existing image.</small>
                </div>

                <!-- Current Event Image -->
                <div class="form-group ">
                    <label>Current Featured Image:</label>
                    <div>
                        <?php if (!empty($event['image'])): ?>
                            <img src="<?= base_url('uploads/' . $event['image']) ?>" alt="Event Image" style="width: 200px; height: auto; border-radius: 5px; margin-top: 10px;">
                        <?php else: ?>
                            <p>No image uploaded for this event.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Update Event</button>
        </form>
    
</div>




<!-- ===== Footer Section ===== -->
<?php include(APPPATH . 'Views/footer.php'); ?>

<style>
      .mmd_edit_event {
        background: #fff;
        padding: 30px 70px;

      }

    /* Page Title */
    .page-title {
        font-size: 1.8rem;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    /* Form Grid */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr); /* 2 Columns */
        gap: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    label {
        font-weight: bold;
        color: #34495e;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 2px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        line-height: 25px;
        background: #ffffff;
    }

    .full-width {
        grid-column: span 2; /* Make the field span across both columns */
    }

    /* Button Styling */
    .btn-primary {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 1rem;
        font-weight: bold;
        transition: background-color 0.3s;
        display: block;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

</style>