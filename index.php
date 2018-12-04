<?php
    session_start();

    require_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'load.php');

    if (isset($_SESSION['username']) && isset($_SESSION['password'])) {
        $user = new \Kanba\User($_SESSION['username'], $_SESSION['password']);
    }
    $isLogged = false;
    if (isset($user) && $user->existsAndIsGood() === TRUE) {
        $isLogged = true;
    }
    if (!$isLogged) {
        header("Location: se-connecter.php");
    }

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= \Kanba\Configurator::getEntry("TITLE") ?> - Page d'accueil</title>
    <link href="/css/mymd.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
          rel="stylesheet">
    <link href="/css/index.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body data-kanba="<?= $kanba_id ?>">
<div class="side-nav">
    <a href="/" class="side-nav-main-title"><?= \Kanba\Configurator::getEntry("TITLE") ?></a>
    <span class="side-nav-title">Vos tableaux privés</span>
    <ul class="side-nav-section">
        <?php foreach ($user->getKanbas(true) as $k): ?>
        <li><a onclick="kanbaNav('<?= $k->getSlug() ?>')" class="side-nav-link"><?= $k->getTitle()?></a></li>
        <?php endforeach; ?>
    </ul>
    <span class="side-nav-title">Vos tableaux public</span>
    <ul class="side-nav-section">
        <?php foreach ($user->getKanbas(false) as $k): ?>
            <li><a class="side-nav-link"><?= $k->getTitle()?></a></li>
        <?php endforeach; ?>
    </ul>
</div>
    <header class="top-bar">
        <i class="hamburger material-icons" style="color: white;">menu</i>
        <span>Bonjour <?= $user->getName() ?></span>
        <a href="/logout.php"><i title="Se déconnecter" class="float-right material-icons">clear</i></a>
    </header>
    <div class="todo-edit not-visible">
        <form>
            <span class="subtitle1">Edition de la tâche</span>
            <i class="material-icons">clear</i>
            <br>
            <br>
            <div class="input not-empty" data-placeholder="Titre de la tâche">
                <input type="text" name="title" value="Bonjour 1">
            </div>
            <br>
            <div class="input not-empty no-control" data-placeholder="Date d'échéance">
                <input type="date" name="date" value="2018-11-15">
                <span>à </span>
                <input type="time" name="hour" value="12:12">
            </div>
            <br>
            <div class="input not-empty" data-placeholder="Déplacer vers">
                <select name="move" data-defaultvalue="1">
                    <option>Stories</option>
                    <option selected>Terminées</option>
                    <option>Liste 3</option>
                    <option>Liste 4</option>
                    <option>Liste 5</option>
                </select>
            </div>
            <br>
            <button type="button">Enregistrer</button>
            <br>
            <button type="reset">Réinitialiser</button>
            <br>
            <button type="button" class="btn-warning">Supprimer</button>
        </form>
    </div>
    <main>
        <div class="todo-list">
            <h6>Stories</h6>
            <ul>
                <li>
                    <div class="todo" data-list="0">
                        <span>Bonjour 1</span>
                        <div class="todo-time">
                            <i class="material-icons">access_time</i>
                            <span>27/11/2018</span>
                            <span>à</span>
                            <span>12:23</span>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="todo" data-list="0">
                        <span>Bonjour 2</span>
                        <div class="todo-time">
                            <i class="material-icons">access_time</i>
                            <span>28/11/2018</span>
                            <span>à</span>
                            <span>12:24</span>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="todo" data-list="0">
                        <span>Bonjour 3</span>
                        <div class="todo-time">
                            <i class="material-icons">access_time</i>
                            <span>29/11/2018</span>
                            <span>à</span>
                            <span>12:25</span>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="todo" data-list="0">
                        <span>Bonjour 1</span>
                        <div class="todo-time">
                            <i class="material-icons">access_time</i>
                            <span>27/11/2018</span>
                            <span> à </span>
                            <span>12:23</span>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="todo" data-list="0">
                        <span>Bonjour 1</span>
                        <div class="todo-time">
                            <i class="material-icons">access_time</i>
                            <span>27/11/2018</span>
                            <span> à </span>
                            <span>12:23</span>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="todo" data-list="0">
                        <span>Bonjour 1</span>
                        <div class="todo-time">
                            <i class="material-icons">access_time</i>
                            <span>27/11/2018</span>
                            <span> à </span>
                            <span>12:23</span>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <div class="todo-list todo-list-end">
            <h6>Terminées</h6>
            <ul>
                <li>
                    <div class="todo" data-list="1">
                        <span>Bonjour 1</span>
                        <div class="todo-time">
                            <i class="material-icons">access_time</i>
                            <span>27/12/2018</span>
                            <span>à</span>
                            <span>12:23</span>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="todo" data-list="1">
                        <span>Bonjour 2</span>
                        <div class="todo-time">
                            <i class="material-icons">access_time</i>
                            <span>28/12/2018</span>
                            <span>à</span>
                            <span>12:24</span>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="todo" data-list="1">
                        <span>Bonjour 3</span>
                        <div class="todo-time">
                            <i class="material-icons">access_time</i>
                            <span>29/12/2018</span>
                            <span>à</span>
                            <span>12:25</span>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="todo" data-list="1">
                        <span>Bonjour 1</span>
                        <div class="todo-time">
                            <i class="material-icons">access_time</i>
                            <span>27/11/2018</span>
                            <span> à </span>
                            <span>12:23</span>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="todo" data-list="1">
                        <span>Bonjour 1</span>
                        <div class="todo-time">
                            <i class="material-icons">access_time</i>
                            <span>27/11/2018</span>
                            <span> à </span>
                            <span>12:23</span>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="todo" data-list="1">
                        <span>Bonjour 1</span>
                        <div class="todo-time">
                            <i class="material-icons">access_time</i>
                            <span>27/11/2018</span>
                            <span> à </span>
                            <span>12:23</span>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </main>
    <div class="snackbar">Modification(s) enregistrée(s).</div>

    <script src="/js/todo_page.js"></script>
    <script src="/js/Kanba.js"></script>

    <script src="/js/mymd.js"></script>
</body>
</html>