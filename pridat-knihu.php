<?php require_once "config.php"; ?>
<?php
    if(isset($_POST["addBook"])) {
        $valid = true;

        $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_STRING);
        $author = filter_input(INPUT_POST, "author", FILTER_SANITIZE_STRING);
        $content = filter_input(INPUT_POST, "content", FILTER_SANITIZE_STRING);

        if(empty($title)) {
            setFlash("Název knihy musí být vyplněný!", "danger");
            $valid = false;
        }

        if(empty($author)) {
            setFlash("Autor knihy musí být vyplněný!", "danger");
            $valid = false;
        }

        $content = (empty($content)) ?  NULL : $content;

        if($valid) {
            if(!empty($_FILES["cover_url"]["name"])) {
                $fileUploadOk = true;
                $file = $_FILES["cover_url"];
                $fileName = "./uploads/" . uniqid() . "_" . $file["name"];
                if(!move_uploaded_file($file["tmp_name"], $fileName)) {
                    setFlash("Nepovedlo se uložit obrázek na server!", "danger");
                    $fileUploadOk = false;
                }
            }
            $fileName = ($fileUploadOk) ? $fileName : NULL;
            $sql = "INSERT INTO books (title, author, content, cover_url, active)
                    VALUES (:title, :author, :content, :cover_url, :active);";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ":title" => $title,
                ":author" => $author,
                ":content" => $content,
                ":cover_url" => $fileName,
                ":active" => 1
            ]);
            setFlash("Děkujeme za vložení nové knihy!", "success");
            header("Location: knihovna.php");
            exit();
        }


    }
?>
<?php require_once "header.php"; ?>


<section class="bookcase py-5">
      <div class="container">
      <div class="row justify-content-center">
          <div class="col-md-8">
            <h2 class="mb-3">Přidat novou knihu</h2>
            <form action="pridat-knihu.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Název knihy</label>
                    <input name="title" type="text" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Autor knihy</label>
                    <input name="author" type="text" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Obsah knihy <span class="text-muted">(nepovinné)</span></label>
                    <textarea name="content" class="form-control" rows="10"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Obálka knihy</label>
                    <input name="cover_url" type="file" class="form-control">
                </div>
                <button name="addBook" type="submit" class="btn btn-primary">Přidat knihu</button>
            </form>

          </div>
      </div>
      </div>
        



      </div>
    </section>



<?php require_once "footer.php"; ?>