<?php
require_once 'books.php';
require_once 'functions.php';

session_start();

if (!isset($_SESSION['originalBooks'])) {
    $_SESSION['originalBooks'] = $books;
    $_SESSION['sortedBooks'] = $books;
}

$searchQuery = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['sorting'])) {
        $selectedSort = $_POST['sorting'];

        if ($selectedSort === 'alpha') {
            sort($_SESSION['sortedBooks']);
        } elseif ($selectedSort === 'rel') {
            uasort($_SESSION['sortedBooks'], function ($a, $b) {
                return $a['release'] <=> $b['release'];
            });
        } elseif ($selectedSort === 'page') {
            uasort($_SESSION['sortedBooks'], function ($a, $b) {
                return $a['pages'] <=> $b['pages'];
            });
        } elseif ($selectedSort === 'aut') {
            uasort($_SESSION['sortedBooks'], function ($a, $b) {
                return $a['author'] <=> $b['author'];
            });
        } elseif ($selectedSort === 'col') {
            uasort($_SESSION['sortedBooks'], function ($a, $b) {
                return $a['color'] <=> $b['color'];
            });
        } elseif ($selectedSort === 'ori') {
            $_SESSION['sortedBooks'] = $_SESSION['originalBooks'];
        }
    }

    if (isset($_POST['search'])) {
        $searchQuery = $_POST['search'];
    }
}

$_SESSION['sortedBooks'] = array_filter($_SESSION['sortedBooks'], function ($book) use ($searchQuery) {
    return stripos($book['title'], $searchQuery) !== false || stripos($book['author'], $searchQuery) !== false;
});


$indexed_books = array_values($_SESSION['sortedBooks']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class=" shelf">
        <section>
            <?php
            $totalBooks = count($indexed_books);
            for ($i = 0; $i < $totalBooks; $i += 15) {
            ?>
                <div class="row">
                    <?php
                    for ($j = $i; $j < $i + 15 && $j < $totalBooks; $j++) {
                    ?>
                        <div class="book" style="background-color:<?php echo $indexed_books[$j]['color'] ?> ;">
                            <div class="book-spine">
                                <p><?php echo $indexed_books[$j]['title']; ?></p>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            <?php
            }
            ?>
        </section>

    </div>

    <h1>Select a Sort Method</h1>
    <form action="" method="post">
        <select name="sorting">
            <option value="ori">Original</option>
            <option value="alpha">Alphabetical</option>
            <option value="rel">Release</option>
            <option value="page">Pages</option>
            <option value="aut">Author</option>
            <option value="col">Color</option>
        </select>
        <input type="text" name="search" placeholder="Search by title" value="<?php echo $searchQuery; ?>">
        <input type="submit" value="Submit">
    </form>
</body>

</html>