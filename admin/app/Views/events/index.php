<!-- ===== Header Section ===== -->
<?php include(APPPATH . 'Views/header.php'); ?>


    <!-- ==== Events List Page ==== -->
<div class="main-panel">
    <div class="content-wrapper">
        <h1 class="page-title ">Manage Events</h1>
        <a href="<?= base_url('/events/create') ?>" class="btn btn-primary mb-5">Create New Event</a>
        
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= esc($event['title']) ?></td>
                            <td><?= esc($event['date']) ?></td>
                            <td><?= esc($event['time']) ?></td>
                            <td><?= esc($event['location']) ?></td>
                            <td>₹<?= esc(number_format($event['price'], 2)) ?></td>
                            <td>
                                <a href="<?= base_url('/events/edit/' . $event['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="<?= base_url('/events/delete/' . $event['id']) ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to delete this event?');">
                                   Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    
</div>



<!-- ===== Footer Section ===== -->
<?php include(APPPATH . 'Views/footer.php'); ?>
