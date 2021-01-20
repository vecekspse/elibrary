<?php require_once "config.php"; ?>
<?php
if(isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    if(empty($email) || empty($password)) {
        setFlash("Není vyplněný e-mail nebo heslo!", "danger");
    } else {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $db->prepare($sql);
        $stmt->execute([":email" => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$user) {
            setFlash("Uživatel neexistuje!", "danger");
        } else if($user["active"] == 0) {
          setFlash("Uživatel je zablokovaný!", "danger");
        } else {
            if(password_verify($password, $user["password"])) {
                $_SESSION["identity"] = $user;
                setFlash("Podařilo se vám přihlásit.", "success");

                $sql = "UPDATE users set last_login = :last_login WHERE email = :email;";
                $stmt = $db->prepare($sql);
                $stmt->execute([":last_login" => date('Y-m-d H:i:s'), ":email" => $email]);
            } else {
                setFlash("Zadejte správné přihlašovací údaje!", "danger");
            }
        }
    }
}
?>
<?php require_once "header.php"; ?>


<section class="random-books py-5">
      <div class="container">
        <h2 class="mb-3">Mohlo by vás zajímat</h2>
        <div class="row">
          <div class="col-lg-2">
            <article class="book">
              <img src="assets/images/book.png" alt="" width="200" height="300">
              <div class="book-info">
                <h3 class="book-title">Název knihy</h3>
                <p>
                  Lorem ipsum dolor sit amet consectetur adipisicing elit. Facere, quasi? Natus illo atque modi accusamus doloribus! Tempora reprehenderit rerum adipisci.
                </p>
                <a href="#" class="btn btn-primary">Detail</a>
                <a href="#" class="btn btn-outline-primary">Do knihovny</a>
              </div>
            </article>
          </div>
          <div class="col-lg-2">
            <article class="book">
              <img src="assets/images/book.png" alt="" width="200" height="300">
              <div class="book-info">
                <h3 class="book-title">Název knihy</h3>
                <p>
                  Lorem ipsum dolor sit amet consectetur adipisicing elit. Facere, quasi? Natus illo atque modi accusamus doloribus! Tempora reprehenderit rerum adipisci.
                </p>
                <a href="#" class="btn btn-primary">Detail</a>
                <a href="#" class="btn btn-outline-primary">Do knihovny</a>
              </div>
            </article>
          </div>
          <div class="col-lg-2">
            <article class="book">
              <img src="assets/images/book.png" alt="" width="200" height="300">
              <div class="book-info">
                <h3 class="book-title">Název knihy</h3>
                <p>
                  Lorem ipsum dolor sit amet consectetur adipisicing elit. Facere, quasi? Natus illo atque modi accusamus doloribus! Tempora reprehenderit rerum adipisci.
                </p>
                <a href="#" class="btn btn-primary">Detail</a>
                <a href="#" class="btn btn-outline-primary">Do knihovny</a>
              </div>
            </article>
          </div>
          <div class="col-lg-2">
            <article class="book">
              <img src="assets/images/book.png" alt="" width="200" height="300">
              <div class="book-info">
                <h3 class="book-title">Název knihy</h3>
                <p>
                  Lorem ipsum dolor sit amet consectetur adipisicing elit. Facere, quasi? Natus illo atque modi accusamus doloribus! Tempora reprehenderit rerum adipisci.
                </p>
                <a href="#" class="btn btn-primary">Detail</a>
                <a href="#" class="btn btn-outline-primary">Do knihovny</a>
              </div>
            </article>
          </div>
          <div class="col-lg-2">
            <article class="book">
              <img src="assets/images/book.png" alt="" width="200" height="300">
              <div class="book-info">
                <h3 class="book-title">Název knihy</h3>
                <p>
                  Lorem ipsum dolor sit amet consectetur adipisicing elit. Facere, quasi? Natus illo atque modi accusamus doloribus! Tempora reprehenderit rerum adipisci.
                </p>
                <a href="#" class="btn btn-primary">Detail</a>
                <a href="#" class="btn btn-outline-primary">Do knihovny</a>
              </div>
            </article>
          </div>
          <div class="col-lg-2">
            <article class="book">
              <img src="assets/images/book.png" alt="" width="200" height="300">
              <div class="book-info">
                <h3 class="book-title">Název knihy</h3>
                <p>
                  Lorem ipsum dolor sit amet consectetur adipisicing elit. Facere, quasi? Natus illo atque modi accusamus doloribus! Tempora reprehenderit rerum adipisci.
                </p>
                <a href="#" class="btn btn-primary">Detail</a>
                <a href="#" class="btn btn-outline-primary">Do knihovny</a>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>
    <?php if(!isset($_SESSION["identity"])) : ?>
    <section class="login py-5">
      <div class="container">
        <div class="row justify-content-center" id="login">
          <div class="col-lg-6">
            <h4>Přihlašte se do aplikace</h4>
            <form action="index.php" method="POST">
              <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">E-mail</label>
                <input name="email" type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
              </div>
              <div class="mb-3">
                <label for="exampleInputPassword1" class="form-label">Heslo</label>
                <input name="password" type="password" class="form-control" id="exampleInputPassword1">
              </div>
              <div class="mb-3">
                <button name="login" type="submit" class="btn btn-primary">Přihlásit se do aplikace</button>
              </div>
              <div class="mb-3">
                <p>Ještě nemáte účet?</p>
                <div class="mb-3"><a href="registrace.php" class="btn btn-primary">Registrovat se</a></div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
    <?php else : ?>
    <section class="random-books py-5">
      <div class="container">
        <h2 class="mb-3">Vaše osobní knihovna</h2>
        <p class="alert alert-warning mb-3">
            Zatím jste si nepřidali žádné knížky do své knihovny.
        </p>
      </div>
    </section>
    <?php endif; ?>


<?php require_once "footer.php"; ?>