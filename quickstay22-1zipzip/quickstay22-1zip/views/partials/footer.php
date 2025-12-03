<footer>
    <div class="footer-content">
        <div class="footer-logo">
            <h3><i class="fa-solid fa-bed"></i> QuickStay</h3>
        </div>
        <p>Plateforme de location moderne – by Fatma Amri</p>
        <div class="social-links">
            <a href="#" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
            <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
            <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        </div>
        <p>© <?= date('Y') ?> QuickStay. Tous droits réservés.</p>
    </div>
</footer>
<style>
.footer-content {
    max-width: 1300px;
    margin: 0 auto;
    padding: 3em 2rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.2em;
    background: var(--color-surface);
    border-top: 2px solid var(--color-border);
}

.footer-logo h3 {
    font-family: var(--font-heading);
    font-size: 2rem;
    color: var(--color-primary-dark);
}

.social-links {
    display: flex;
    gap: 1.8em;
}
.social-links a {
    font-size: 1.6em;
    color: var(--color-gray);
    transition: var(--transition);
}

.social-links a:hover {
    color: var(--color-primary);
    transform: scale(1.3);
}

@media (max-width: 600px) {
    .footer-content { padding: 2em 1rem; }
    .footer-logo h3 { font-size: 1.5rem; }
    .social-links a { font-size: 1.4em; }
}
</style>
</body>
</html>
