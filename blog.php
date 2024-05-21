<?php
include 'database.php';

// Mengecek apakah permintaan adalah permintaan POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $title = isset($_POST['title']) ? $_POST['title'] : null;
    $description = isset($_POST['description']) ? $_POST['description'] : null;
    $link = isset($_POST['link']) ? $_POST['link'] : null;
    
    // Menyimpan gambar
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = file_get_contents($_FILES['image']['tmp_name']);
    }

    // Memeriksa apakah judul, deskripsi, dan link tidak null atau kosong
    if (!empty($title) && !empty($description) && !empty($link)) {
        // Mendapatkan ID terakhir dari tabel artikel
        $sql_get_last_id = "SELECT ID FROM artikel ORDER BY ID DESC LIMIT 1";
        $result = $conn->query($sql_get_last_id);
        if ($result->num_rows > 0) {
            $last_id_row = $result->fetch_assoc();
            $last_id = $last_id_row['ID'];
        } else {
            // Jika tabel kosong, mulai dari ID 1
            $last_id = 0;
        }
        
        // Menambahkan data baru dengan ID berikutnya
        $new_id = $last_id + 1;
        $sql_insert = "INSERT INTO artikel (ID, judul, gambar, deskripsi, link) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);
        
        // Menentukan tipe data untuk parameter biner
        $null = NULL;
        $stmt->bind_param("isbss", $new_id, $title, $null, $description, $link);
        $stmt->send_long_data(2, $image);
        
        // Eksekusi statement
        if ($stmt->execute()) {
            $message = "Artikel berhasil ditambahkan.";
            // Redirect ke halaman yang sama setelah penambahan artikel
            header("Location: {$_SERVER['PHP_SELF']}");
            exit();
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $message = "Judul, deskripsi, dan link tidak boleh kosong.";
    }
    
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blogger - Frico Putung</title>
    <link rel="stylesheet" href="blog.css" />
  </head>
  <body>
    <header>
      <nav>
        <a href="index.html">Home</a>
        <a href="gallery.html">Gallery</a>
        <a href="blog.php">Blog</a>
        <a href="contact.html">Contact</a>
      </nav>
    </header>

    <hr />

    <main>
      <h1>Selamat Datang di Blogger!</h1>
      <p id="description">
        Pada halaman ini Anda dapat menemukan konten menarik dan tentu saja
        terbaru, semoga bisa menambah wawasan terkait konten atau artikel yang
        telah disampaikan.
      </p>

      <div class="article-container">
        <?php
            include 'database.php';
            $sql = "SELECT * FROM artikel ORDER BY ID DESC";
            $result = $conn->query($sql); if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) { echo "
        <article>
          "; echo "
          <h2>" . $row["judul"] . "</h2>
          "; if ($row["gambar"]) { echo '<img src="data:image/jpeg;base64,' .
          base64_encode($row["gambar"]) . '" alt="Blog Post Thumbnail" />'; }
          echo "
          <p>
            " . $row["deskripsi"] . "<a href='" . $row["link"] . "' target='_blank'>
              Klik untuk melihat informasi lebih lanjut.</a
            >
          </p>
          "; echo "
        </article>
        "; } } else { echo "Tidak ada artikel."; } $conn->close(); ?>
      </div>

      <div class="add-article">
        <button id="addArticleBtn">Tambah Artikel Baru</button>
      </div>

      <div id="articleForm" style="display: none">
        <form
          action="blog.php"
          method="post"
          enctype="multipart/form-data"
        >
          <label for="title">Judul:</label>
          <input type="text" id="title" name="title" required /><br />

          <label for="image">Gambar:</label>
          <input type="file" id="image" name="image" /><br />

          <label for="description">Deskripsi:</label>
          <textarea id="description" name="description" required></textarea
          ><br />

          <label for="link">Link:</label>
          <input type="url" id="link" name="link" required /><br />

          <input type="submit" value="Tambahkan Artikel" />
        </form>
      </div>
    </main>

    <hr />

    <footer>
      <div>
        <p>&copy; 2024 - Frico Rama Putung</p>
      </div>
    </footer>

    <script src="blog.js"></script>
  </body>
</html>
