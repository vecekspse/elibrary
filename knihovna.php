<?php require_once "config.php"; ?>
<?php
  $sql = "SELECT * FROM books WHERE active = 1 ORDER BY title LIMIT 5;";
  $stmt = $db->prepare($sql);
  $stmt->execute();
  $books = $stmt->fetchAll();
?>
<?php require_once "header.php"; ?>
<section class="bookcase py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-3">Knihovna</h2>
            <a href="pridat-knihu.php" class="btn btn-primary">Přidat novou knihu</a>
        </div>
        <?php if(!empty($books)) : ?>
        <div class="row">
            <?php foreach($books as $book): ?>
            <div class="col-md-3">
                <div class="card">
                    <img src="<?= $book["cover_url"]; ?>" class="card-img-top" alt="<?= $book["title"]; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= $book["title"]; ?></h5>
                        <p class="text-muted text-uppercase"><?= $book["author"]; ?></p>
                        <p class="card-text"><?= $book["content"]; ?></p>
                        <p>
                            <a class="btn btn-primary" href="upravit-knihu.php?id=<?= $book["id"]; ?>">Upravit knihu</a>
                        </p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p>
            Zatím tu žádné knihy nejsou.
        </p>
        <?php endif; ?>
    </div>
</section>



<?php require_once "footer.php"; ?>