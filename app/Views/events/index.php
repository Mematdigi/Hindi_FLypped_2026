<!-- ==== Header Section ==== -->
<?php include(APPPATH . 'Views/components/header.php'); ?>
    

    <!-- ==== Event Tickets Setion ==== -->
    <section class="events-section">
        <h2 class="events-title mb-5">Explore All Events</h2>
        <div class="events-grid mt-4">
            <?php foreach ($events as $event): ?>
                <div class="event-card">
                <img 
                        src="<?= base_url('admin/uploads/' . $event['image']) ?>" 
                        alt="<?= esc($event['title']) ?>" 
                        class="event-image" style="width:100%;">

                    <h3 class="event-title"><?= esc($event['title']) ?></h3>
                    <div class="event-content">
                        <p class="event-price"><?= esc($event['price']) ?>/- onwards</p>
                        <div class="event-details">
                            <div class="event-date-time">
                                <span class="event-date">
                                    <span class="icon calendar-icon">📅</span> 
                                    <?= esc($event['date']) ?>
                                </span>
                                <span class="event-time">
                                    <span class="icon clock-icon">⏰</span>
                                    <?= esc($event['time']) ?>
                                </span>
                            </div>
                        </div>
                        <a href="<?= site_url('events/' . $event['id']) ?>" class="event-link">
                            <span>&#10142;</span>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

<!-- ==== Footer Section ==== -->
<?php include(APPPATH . 'Views/components/footer.php'); ?>

