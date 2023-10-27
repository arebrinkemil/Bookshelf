<?php
require_once 'books.php';
require_once 'functions.php';

session_start();

if (!isset($_SESSION['originalBooks'])) {
    $_SESSION['originalBooks'] = $books;
    $_SESSION['sortedBooks'] = $books;
    $_SESSION['selectedSort'] = 'ori';
    $_SESSION['direction'] = true;
}

$searchQuery = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['search'])) {
        $searchQuery = $_POST['search'];
    }


    $_SESSION['sortedBooks'] = array_filter($_SESSION['originalBooks'], function ($book) use ($searchQuery) {
        return stripos($book['title'], $searchQuery) !== false || stripos($book['author'], $searchQuery) !== false;
    });


    if (isset($_POST['sorting'])) {
        $_SESSION['selectedSort'] = $_POST['sorting'];
        if ($_SESSION['selectedSort'] === 'alpha') {
            sort($_SESSION['sortedBooks']);
        } elseif ($_SESSION['selectedSort'] === 'rel') {
            uasort($_SESSION['sortedBooks'], function ($a, $b) {
                return $a['release'] <=> $b['release'];
            });
        } elseif ($_SESSION['selectedSort'] === 'page') {
            uasort($_SESSION['sortedBooks'], function ($a, $b) {
                return $a['pages'] <=> $b['pages'];
            });
        } elseif ($_SESSION['selectedSort'] === 'aut') {
            uasort($_SESSION['sortedBooks'], function ($a, $b) {
                return $a['author'] <=> $b['author'];
            });
        } elseif ($_SESSION['selectedSort'] === 'col') {
            uasort($_SESSION['sortedBooks'], function ($a, $b) {
                return $a['color'] <=> $b['color'];
            });
        } elseif ($_SESSION['selectedSort'] === 'ori') {
            $_SESSION['sortedBooks'] = $_SESSION['originalBooks'];
        }
    }

    if (array_key_exists('asc', $_POST)) {
        $_SESSION['direction'] = true;
    } elseif (array_key_exists('desc', $_POST)) {
        $_SESSION['direction'] = false;
    }

    $indexed_books = array_values($_SESSION['sortedBooks']);
    if (!$_SESSION['direction']) {
        $indexed_books = array_reverse($indexed_books);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="sorter">

        <h1>Select a Sort Method</h1>
        <form action="" method="post">
            <input type="submit" name="asc" class="button" value="ASC" />

            <input type="submit" name="desc" class="button" value="DESC" />
            <select name="sorting">
                <option value="ori" <?php echo ($_SESSION['selectedSort'] == 'ori' ? 'selected' : ''); ?>>Original</option>
                <option value="alpha" <?php echo ($_SESSION['selectedSort'] == 'alpha' ? 'selected' : ''); ?>>Alphabetical</option>
                <option value="rel" <?php echo ($_SESSION['selectedSort'] == 'rel' ? 'selected' : ''); ?>>Release</option>
                <option value="page" <?php echo ($_SESSION['selectedSort'] == 'page' ? 'selected' : ''); ?>>Pages</option>
                <option value="aut" <?php echo ($_SESSION['selectedSort'] == 'aut' ? 'selected' : ''); ?>>Author</option>
                <option value="col" <?php echo ($_SESSION['selectedSort'] == 'col' ? 'selected' : ''); ?>>Color</option>
            </select>
            <input type="text" name="search" placeholder="Search by title" value="<?php echo $searchQuery; ?>">
            <input type="submit" value="Submit">

        </form>

    </div>
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


</body>

</html>