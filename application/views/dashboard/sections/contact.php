<style>
    /* Styling khusus Sesi 3 */
    #section-contact {
        background-color: #f8fafc;
        display: flex;
        flex-direction: column;
        background-image: radial-gradient(circle at center, rgba(234, 88, 12, 0.05) 0%, transparent 70%);
    }

    .contact-container {
        background: #ffffff;
        border: 1px solid rgba(234, 88, 12, 0.2);
        padding: 80px 60px;
        border-radius: 40px;
        text-align: center;
        box-shadow: 0 30px 60px rgba(234, 88, 12, 0.1);
        max-width: 600px;
        width: 100%;
        position: relative;
        overflow: hidden;
    }

    /* Efek garis bercahaya di bagian atas kartu */
    .contact-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, transparent, var(--accent-color), transparent);
    }

    .contact-container h1 {
        font-size: 3.5rem;
        margin-bottom: 15px;
        color: var(--text-color);
        font-weight: 800;
    }

    .contact-container p {
        color: #64748b;
        font-size: 1.2rem;
        margin-bottom: 40px;
    }

    .btn-contact {
        display: inline-block;
        padding: 18px 50px;
        background: linear-gradient(90deg, #ea580c, #c2410c);
        color: #fff;
        text-decoration: none;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 600;
        letter-spacing: 2px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        text-transform: uppercase;
    }

    .btn-contact:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 25px rgba(234, 88, 12, 0.3);
    }
    
    footer {
        position: absolute;
        bottom: 20px;
        color: #444;
        font-size: 0.9rem;
    }
</style>

<!-- Sesi 3: Contact -->
<div class="section-wrapper" id="section-contact">
    <div class="contact-container">
        <h1>Mulai Proyek?</h1>
        <p>Mari berkolaborasi membangun masa depan digital yang lebih interaktif dan menarik bersama IFIK.</p>
        <a href="#" class="btn-contact">Hubungi Kami</a>
    </div>
    
    <footer>
        &copy; <?= date('Y') ?> IFIK Dashboard. All rights reserved.
    </footer>
</div>
