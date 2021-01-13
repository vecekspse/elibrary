<?php require_once "config.php"; ?>
<?php
$id = $_GET["id"];

if(!isset($_SESSION["identity"]) || $_SESSION["identity"]["id"] !== $id){
    setFlash("Nemáte právo prohlížet tuto stránku", "danger");
    header("Location: index.php");
}

$sql = "SELECT * FROM users WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->execute([":id" => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$user) {
    setFlash("Neznámý uživatel!", "danger");
    header("Location: index.php");
}

if(isset($_GET["logout"])) {
    unset($_SESSION["identity"]);
    setFlash("Byli jste úspěšně odhlášeni!", "success");
    header("Location: index.php");
}
?>
<?php require_once "header.php"; ?>

<section class="profil py-5">
    <div class="container">
        <h2><?php echo $user["username"] . "(" . $user["email"] . ")" ; ?></h2>
        <p>Vytvořeno: <?php echo date("H:i:s j. n. Y", strtotime($user["created"])); ?></p>
        <p>Poslední přihlášení: <?php echo date("H:i:s j. n. Y", strtotime($user["last_login"])); ?></p>
        <a href="profil.php?id=<?php echo $user["id"]; ?>&logout">Odhlásit se</a>
    </div>
</section>


<?php require_once "footer.php"; ?>