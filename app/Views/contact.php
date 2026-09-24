<?php include(APPPATH . 'Views/components/header.php'); ?>

<section 
    class="breadcrumb-section <?php echo isset($is_contact_page) && $is_contact_page ? 'contact-breadcrumb' : ''; ?>"
    <?php if (isset($is_contact_page) && $is_contact_page): ?>
        style="background-image: url('<?php echo base_url('public/assest/images/contact.webp'); ?>'); background-size: cover; background-position: center; background-repeat: no-repeat; padding: 65px 0;"
    <?php endif; ?>
>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="page-title">
                    <span class="text-white">Contact Us</span>
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item text-white"><a href="<?php echo base_url(); ?>" class="text-white">Home</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Contact Us</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="flypped_contact py-5">
    <div class="container">
        <div class="row align-items-start">
            
            <div class="col-lg-7 col-md-12">
                <div class="contact-info">
                    
                    <p class="mb-2 text-muted small">
                        जैसाकि आपको पता है कि Flypped Hindi <a href="<?= base_url() ?>" style="text-decoration: none; font-weight: 600;">E-Magazine एक न्यूज़ पोर्टल</a> है जहाँ पर आपको विभिन्न प्रकार की सभी ख़बरें एक सरल भाषा में देखनें को मिलती है। यदि आपका हमारे लिए कोई सुझाव या फिर कोई सवाल हो तो आप हमसे उसके बारें में ज़रूर पूछें। आपका हर एक सुझाव हमारें लिए बहुत महत्वपूर्ण रहेगा।
                    </p>
                    
                    <p class="mb-2 text-muted small">
                        यदि आप हमारे द्वारा लिखे गए किसी आर्टिकल के बारें में कुछ पूछना चाहते हो तो आप हमसे नि:संकुच पूछ सकते हैं। कोई ऐसी जानकारी जो हमसे किसी आर्टिकल में छुट गई है तो आप उसके बारें में भी हमें बता सकते हैं। आपका फीडबैक हमारे लिए बहुत मायने रखता है। इससे हमें लगता है कि आप हमारे साथ जोड़े हुए है। यही सब बातें हमें और बेहतर काम करने के लिए प्रेरित करती है।
                    </p>
                    
                    <p class="mb-2 text-muted small">
                        यदि आप ब्रांड, एजेंसी या क्रिएटर हैं और हमारे साथ मिलकर PR या विज्ञापन से जुड़ा काम करना चाहते है तो हमसे संपर्क कर सकते है। इनके अलावा, यदि आपको किसी भी प्रकार की तकनीकी समस्या दिखे या फिर आप कंटेंट में किसी भी प्रकार का कोई सुधार चाहते है तो आप हमें ज़रूर बताएँ।
                    </p>
                    
                    <p class="mb-4 text-muted small">
                        24-48 घंटे के अंदर आपके द्वारा पूछे गए हर सवाल का जवाब दे दिया जाएगा। Flypped कभी भी आपसे पर्सनल, बैंक या OTP जानकारी नहीं माँगता है और न ही आपको किसी भी पोर्टल पर इसको शेयर करना चाहिए।
                    </p>
    
                    <div class="social-section mt-4 mb-4">
                        <h2 class="fw-bold">Follow Us</h2>
                        <p class="text-muted small">किसी भी प्रकार की लेटेस्ट न्यूज़ को देखने के लिए आज ही फॉलो करें:</p>
                        
                        <div class="d-flex flex-wrap gap-3">
                            <a href="https://www.instagram.com/hindiflypped/" target="_blank" class="social-btn instagram">
                                <i class="fab fa-instagram"></i> Instagram
                            </a>
                            <a href="https://www.facebook.com/flyppedhindi/" target="_blank" class="social-btn facebook">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </a>
                            <a href="https://www.youtube.com/@flyppedhindinews" target="_blank" class="social-btn youtube">
                                <i class="fab fa-youtube"></i> YouTube
                            </a>
                            <a href="https://x.com/flyppedhindi" target="_blank" class="social-btn twitter">
                                <i class="fab fa-twitter"></i> Twitter
                            </a>
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-5">
                        <div class="col-md-6">
                            <div class="contact-card h-100 p-3 border rounded shadow-sm">
                                <h3 class="fw-bold"><i class="fa fa-envelope text-warning me-2"></i> सामान्य पूछताछ</h3>
                                <a href="mailto:info@flyppedhindi.com" class="email-link small fw-bold">info@flyppedhindi.com</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="contact-card h-100 p-3 border rounded shadow-sm">
                                <h3 class="fw-bold"><i class="fa fa-headset text-success me-2"></i> सहायता और सपोर्ट</h3>
                                <a href="mailto:support@flyppedhindi.com" class="email-link small fw-bold">support@flyppedhindi.com</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="contact-card h-100 p-3 border rounded shadow-sm">
                                <h3 class="fw-bold"><i class="fa fa-newspaper text-primary me-2"></i> संपादकीय एवं स्टोरी सुझाव </h3>
                                <a href="mailto:editors@flyppedhindi.com" class="email-link small fw-bold">editors@flyppedhindi.com</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="contact-card h-100 p-3 border rounded shadow-sm">
                                <h3 class="fw-bold"><i class="fa fa-handshake text-danger me-2"></i>विज्ञापन एवं पार्टनरशिप</h3>
                                <a href="mailto:ads@flyppedhindi.com" class="email-link small fw-bold">ads@flyppedhindi.com</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-12">
                <div class="sidebar-wrapper sticky-top" style="top: 20px; z-index: 1;">
                    
                    <div class="contact-image mb-4 text-center">
                        <img src="<?php echo base_url('public/assest/images/sidebar_banner.webp'); ?>" 
                             alt="Contact Us" 
                             class="img-fluid rounded shadow-lg" 
                             style="max-height: 300px; width: 100%; object-fit: cover;">
                    </div>

                    <div class="exclusive-widget">
                        <div class="widget-header">
                            <h5 class="m-0 text-white">Exclusive Blog</h5>
                        </div>
                        <div class="widget-body">
                            <?php if (!empty($exclusive_news_posts)): ?>
                                <?php foreach ($exclusive_news_posts as $news): ?>
                                    <div class="news-item">
                                        <div class="news-thumb-wrapper">
                                            <img src="<?= $news['thumbnail_url'] ?? 'default-thumbnail.jpg'; ?>" 
                                                 class="news-thumb"
                                                 alt="<?= htmlspecialchars($news['post_title'] ?? 'Untitled'); ?>">
                                        </div>
                                        <div class="news-details">
                                            <h4 class="news-title">
                                                <a href="<?= $news['blog_detail_url'] ?? '#'; ?>">
                                                    <?= htmlspecialchars($news['post_title'] ?? 'Untitled'); ?>
                                                </a>
                                            </h4>
                                            <p class="news-date">
                                                <?= !empty($news['post_date']) ? date('F d, Y', strtotime($news['post_date'])) : ''; ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted text-center p-3">No blogs available.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="about-section pt-4 border-top mt-4">
            <h2 class="fw-bold mb-3">Know About Us</h2>
            <p class="text-muted mb-3">
                Flypped Hindi E-Magazine एक न्यूज़ पोर्टल है जो अपने सभी पाठकों तक लेटेस्ट ख़बरों को सबसे पहले पहुँचाती हैं। वर्ष 2017 में Fypped की शुरुआत हुई थी। जिसके बाद से हमनें निरंतर अपने पाठकों का विश्वास को बनाया रखा है। हम अपने कंटेंट के माध्यम से अपने सभी पाठकों को एक Value प्रदान करते हैं।
            </p>
            <p class="text-muted mb-3">
                हिंदी कंटेंट को पढ़ने व पसंद करने वाले पाठकों की संख्या बहुत अधिक है जबकि उनके लिए हिंदी कंटेंट की कमी थी इसलिए Flypped Hindi E-Magazine को डिजिटल दुनिया में लाया गया था ताकि हिंदी पढ़ने वाले पाठकों तक एक सही जानकारी पहुँचाई जा सकें।
            </p>
            
            <h3 class="fw-bold mb-2 text-dark" style=" font-size: 20px; ">हम इन विषयों पर जानकारी प्रदान करते हैं:</h3>
            
            <div class="d-flex flex-wrap gap-2 mb-4">
    <a href="<?= base_url('news') ?>" class="category-tag text-decoration-none">
        <i class="fas fa-newspaper me-1"></i> News
    </a>
    
    <a href="<?= base_url('technology') ?>" class="category-tag text-decoration-none">
        <i class="fas fa-microchip me-1"></i> Technology
    </a>
    
    <a href="<?= base_url('entertainment') ?>" class="category-tag text-decoration-none">
        <i class="fas fa-film me-1"></i> Entertainment
    </a>
    
    <a href="<?= base_url('lifestyle') ?>" class="category-tag text-decoration-none">
        <i class="fas fa-leaf me-1"></i> Lifestyle
    </a>
    
    <a href="<?= base_url('health-fitness') ?>" class="category-tag text-decoration-none">
        <i class="fas fa-heartbeat me-1"></i> Health
    </a>
    
    <a href="<?= base_url('education') ?>" class="category-tag text-decoration-none">
        <i class="fas fa-graduation-cap me-1"></i> Education
    </a>
    
    <a href="<?= base_url('relationship') ?>" class="category-tag text-decoration-none">
        <i class="fas fa-users me-1"></i> Relationships
    </a>
    
    <a href="<?= base_url('spiritual') ?>" class="category-tag text-decoration-none">
        <i class="fas fa-om me-1"></i> Spiritual
    </a>
</div>
        </div>
    </div>
</section>

<style>
    .flypped_contact {
        background-color: #fdfdfd;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    /* === Exclusive Widget Styles (Blue Box) === */
    .exclusive-widget {
        border: 2px solid #10b3d6; /* The cyan border color */
        border-radius: 6px;
        overflow: hidden;
        background: #fff;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .widget-header {
        background-color: #10b3d6; /* The cyan header background */
        padding: 12px;
        text-align: center;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .widget-body {
        padding: 15px;
    }
    .news-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f0f0f0;
    }
    .news-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    .news-thumb-wrapper {
        flex-shrink: 0;
        width: 100px;
        margin-right: 12px;
    }
    .news-thumb {
        width: 100%;
        height: 70px;
        object-fit: cover;
        border-radius: 5px;
    }
    .news-details {
        flex-grow: 1;
    }
    .news-title {
        font-size: 0.95rem;
        line-height: 1.3;
        margin-bottom: 5px;
        font-weight: 700;
    }
    .news-title a {
        color: #222;
        text-decoration: none;
        transition: color 0.2s;
    }
    .news-title a:hover {
        color: #10b3d6;
    }
    .news-date {
        font-size: 0.75rem;
        color: #888;
        margin: 0;
    }

    /* === Contact Cards === */
    .contact-card {
        background: #fff;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .contact-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important;
    }
    .contact-card h3 { /* Changed h6 to h3 to match your code's preference */
        font-size: 0.95rem;
        margin-bottom: 5px;
        color: #333;
    }
    .email-link {
        color: #555;
        text-decoration: none !important;
        transition: color 0.2s;
    }
    .email-link:hover {
        color: #f0ad4e;
    }

    /* === Category Tags === */
    .category-tag {
        background-color: #f0f2f5;
        color: #444;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid #e1e4e8;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s;
    }
    .category-tag:hover {
        background-color: #333;
        color: #fff;
        border-color: #333;
    }

    /* === Social Buttons === */
    .social-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        border-radius: 8px;
        color: white !important;
        text-decoration: none !important;
        font-weight: 600;
        font-size: 0.9rem;
        transition: transform 0.2s, opacity 0.2s;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        min-width: 140px;
    }
    .social-btn i {
        margin-right: 8px;
        font-size: 1.1rem;
    }
    .social-btn:hover {
        transform: translateY(-2px);
        opacity: 0.9;
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    .social-btn.instagram { background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); }
    .social-btn.facebook { background-color: #1877f2; }
    .social-btn.youtube { background-color: #ff0000; }
    .social-btn.twitter { background-color: #1da1f2; }

    @media (max-width: 768px) {
        .social-btn { flex: 1 1 45%; }
    }
</style>

<?php include(APPPATH . 'Views/components/footer.php'); ?>