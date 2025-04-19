<?php
// inclusion de l'en-tête du site (navigation, scripts, etc.)
require_once(RACINE . "/vue/commun/header.php");

// inclusion de la fonction getAvatarUrl pour afficher les avatars utilisateurs
require_once(RACINE . "/modele/uploadImage.php");
?>

<div class="messagerie-wrapper">

    <?php // colonne de gauche : liste des conversations 
    ?>
    <div class="conversations-list">
        <h3>Conversations</h3>
        <ul>
            <?php foreach ($conversations as $conv): ?>
                <?php // chaque élément représente une conversation 
                ?>
                <li class="<?= (isset($idAnnonce) && $conv['id_annonce'] == $idAnnonce && $conv['id_utilisateur'] == $idAutre) ? 'active' : '' ?>">
                    <a href="index.php?page=messages&action=conversation&id_annonce=<?= htmlspecialchars($conv['id_annonce']) ?>&id_autre=<?= htmlspecialchars($conv['id_utilisateur']) ?>#dernier">

                        <?php // avatar et infos de la personne avec qui on échange 
                        ?>
                        <div class="avatar-nom">
                            <img src="<?= htmlspecialchars(getAvatarUrl($conv['avatar'] ?? null)) ?>" class="avatar-message" alt="avatar utilisateur">
                            <div class="infos-user">
                                <div class="nom-utilisateur">
                                    <?= htmlspecialchars(strtoupper($conv['prenom'] . ' ' . $conv['nom'])) ?>
                                </div>
                                <div class="titre-annonce">
                                    <?= htmlspecialchars(strtoupper($conv['titre_annonce'])) ?>
                                </div>
                            </div>
                        </div>

                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php // colonne de droite : contenu de la conversation sélectionnée 
    ?>
    <div class="conversation-content">
        <?php if (!empty($messages)) : ?>
            <h3>Conversation</h3>

            <?php // fil de messages échangés 
            ?>
            <div class="messages-thread">
                <?php foreach ($messages as $i => $msg): ?>
                    <div class="message-bulle 
                        <?= $msg['id_expedie'] == $_SESSION['utilisateur']['id_utilisateur'] ? 'sent' : 'received' ?>"
                        <?= $i === array_key_last($messages) ? 'id="dernier"' : '' ?>>

                        <?php // prénom de l’expéditeur 
                        ?>
                        <div class="auteur"><?= htmlspecialchars($msg['expediteur_prenom']) ?></div>

                        <?php // contenu du message 
                        ?>
                        <div class="contenu"><?= nl2br(htmlspecialchars($msg['contenu'])) ?></div>

                        <?php // date d’envoi 
                        ?>
                        <div class="date"><?= htmlspecialchars($msg['date_envoi']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php // formulaire d'envoi de réponse 
            ?>
            <form method="POST" action="index.php?page=messages&action=envoyer" class="conversation-form" id="form-message">
                <input type="hidden" name="id_annonce" value="<?= htmlspecialchars($idAnnonce) ?>">
                <input type="hidden" name="id_recoit" value="<?= htmlspecialchars($idAutre) ?>">

                <label for="contenu">Votre message :</label>
                <textarea name="contenu" id="contenu-message" required></textarea>

                <button type="submit" class="btn-envoyer">Envoyer</button>
            </form>

        <?php else: ?>
            <?php // message affiché si aucune conversation n'est sélectionnée 
            ?>
            <p class="aucune-conversation">Sélectionnez une conversation à gauche</p>
        <?php endif; ?>
    </div>
</div>