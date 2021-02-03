<?php require_once "config.php"; ?>
<?php
$id = $_GET["id"];

if(!isset($_SESSION["identity"]) || $_SESSION["identity"]["id"] !== $id){
    setFlash("Nemáte právo prohlížet tuto stránku", "danger");
    header("Location: index.php");
    exit();    
}

$sql = "SELECT * FROM users WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->execute([":id" => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$user) {
    setFlash("Neznámý uživatel!", "danger");
    header("Location: index.php");
    exit();    
}

if(isset($_POST["changeUser"])) {
    $valid = true;

    $email = filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL);
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);

    $sql = "SELECT * FROM users WHERE (email = :email OR username = :username) AND id != :id;";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ":email" => $email,
        ":username" => $username,
        ":id" => $user["id"]
    ]);
    $userCheck = $stmt->fetch();

    if(!$email) {
        setFlash("E-mail musí být vyplněný a ve správném tvaru!", "danger");
        $valid = false;
    }
    if(!empty($userCheck) && $userCheck["email"] == $email) {
        setFlash("E-mail je obsazený!", "danger");
        $valid = false;
    }
    if(!empty($userCheck) && $userCheck["username"] == $username) {
        setFlash("Uživatelské jméno je zabrané!", "danger");
        $valid = false;
    }

    if($valid) {
        $sql = "UPDATE users SET email = :email, username = :username WHERE id = :id;";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ":email" => $email,
            ":username" => $username,
            ":id" => $_SESSION["identity"]["id"]
        ]);
        $_SESSION["identity"]["email"] = $email;
        $_SESSION["identity"]["username"] = $username;
        setFlash("Změna hesla byla úspěšná!", "success");

        header("Location: profil.php?id=".$user["id"]);
        exit();
    }
}
if(isset($_POST["changePassword"])) {
    $valid = true;

    $passwordOld = $_POST["passwordOld"];
    $passwordNew = $_POST["passwordNew"];
    $passwordNewVerify = $_POST["passwordNewVerify"];

    if(!password_verify($passwordOld, $user["password"])) {
        setFlash("Vaše původní heslo je špatné!", "danger");
        $valid = false;
    }

    if(password_verify($passwordNew, $user["password"])) {
        setFlash("Vaše nové heslo je stejné jako to staré!", "danger");
        $valid = false;
    }

    if(empty($passwordNewVerify) || empty($passwordNew) || empty($passwordOld)) {
        setFlash("Všechna pole musí být vyplněná!", "danger");
        $valid = false;
    }

    if(!empty($passwordNew) && ($passwordNew !== $passwordNewVerify)) {
        setFlash("Hesla nesouhlasí!", "danger");
        $valid = false;
    }

    if($valid) {
        $sql = "UPDATE users SET password = :password WHERE id = :id;";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ":password" => password_hash($passwordNew, PASSWORD_DEFAULT),
            ":id" => $_SESSION["identity"]["id"]
        ]);
        setFlash("Změna hesla byla úspěšná!", "success");
    }
}


if(isset($_GET["logout"])) {
    unset($_SESSION["identity"]);
    setFlash("Byli jste úspěšně odhlášeni!", "success");
    header("Location: index.php");
    exit();    

}
?>
<?php require_once "header.php"; ?>

<section class="profil py-5">
    <div class="container">
        <h2><?php echo $user["username"] . "(" . $user["email"] . ")" ; ?></h2>
        <p>Vytvořeno: <?php echo date("H:i:s j. n. Y", strtotime($user["created"])); ?></p>
        <p>Poslední přihlášení: <?php echo date("H:i:s j. n. Y", strtotime($user["last_login"])); ?></p>
        <p>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changePassword">
                Změna hesla
            </button>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changeUser">
                Změna údajů
            </button>
        </p>

        <a href="profil.php?id=<?php echo $user["id"]; ?>&logout">Odhlásit se</a>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="changePassword" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="profil.php?id=<?php echo $user["id"]; ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Změna hesla</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Vaše staré heslo</label>
                    <input name="passwordOld" type="password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nové heslo</label>
                    <input name="passwordNew" type="password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nové heslo znovu</label>
                    <input name="passwordNewVerify" type="password" class="form-control">
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zrušit</button>
                    <button name="changePassword" type="submit" class="btn btn-primary">Uložit změny</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="changeUser" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="profil.php?id=<?php echo $user["id"]; ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Změna uživatelských údajů</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Váš e-mail</label>
                    <input name="email" type="email" class="form-control" value="<?= $user["email"] ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Váše uživatelské jméno</label>
                    <input name="username" type="text" class="form-control" value="<?= $user["username"] ?>">
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zrušit</button>
                    <button name="changeUser" type="submit" class="btn btn-primary">Uložit změny</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once "footer.php"; ?>