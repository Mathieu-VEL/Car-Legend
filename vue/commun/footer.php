<?php // pied de page principal du site 
?>
<footer class="site-footer">

    <?php // conteneur global pour organiser les colonnes du footer 
    ?>
    <div class="footer-container">

        <?php // première colonne : lien vers la page "qui sommes-nous" 
        ?>
        <div class="footer-column">
            <h4>À PROPOS</h4>
            <ul>
                <li><a href="index.php?page=apropos">Qui sommes-nous ?</a></li>
            </ul>
        </div>

        <?php // deuxième colonne : lien vers la page rgpd et mentions de confidentialité 
        ?>
        <div class="footer-column">
            <h4>LÉGAL</h4>
            <ul>
                <li><a href="index.php?page=rgpd">Confidentialité & RGPD</a></li>
            </ul>
        </div>

        <?php // troisième colonne : liens vers les réseaux sociaux 
        ?>
        <div class="footer-column">
            <h4>NOUS SUIVRE</h4>
            <ul class="social-icons">
                <li><a href="https://www.facebook.com/?locale=fr_FR">Facebook</a></li>
                <li><a href="https://www.instagram.com/">Instagram</a></li>
                <li><a href="https://discord.com/">Discord</a></li>
            </ul>
        </div>

    </div>

    <?php // bandeau inférieur du footer avec le copyright 
    ?>
    <div class="footer-bottom">
        <p>&copy; 2025 Car Legend – Tous droits réservés.</p>
    </div>
</footer>

<?php // fin du corps du document html 
?>
</body>

</html>