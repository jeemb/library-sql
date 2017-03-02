<?php

    date_default_timezone_set("America/Los_Angeles");
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Book.php";
    require_once __DIR__."/../src/Author.php";
    require_once __DIR__."/../src/Patron.php";


    $app = new Silex\Application();

    $app['debug'] = true;

    $app->register(new Silex\Provider\TwigServiceProvider(), ['twig.path' => __DIR__."/../views"]);

    $server = "mysql:host=localhost:8889;dbname=library";
    $username = 'root';
    $password = 'root';
    $DB = new PDO($server, $username, $password);

    use Symfony\Component\HttpFoundation\Request;
    Request::enableHttpMethodPArameterOverride();


    $app->get('/', function() use ($app) {
        return $app['twig']->render('index.html.twig');
    });

    $app->get('/authors', function() use ($app) {
        return $app['twig']->render('authors.html.twig', ['authors' => Author::getAll()]);
    });

    $app->get('/books', function() use ($app) {
        return $app['twig']->render('books.html.twig', ['books' => Book::getAll()]);
    });

    $app->post('/add_book', function() use ($app) {
        $title = $_POST['title'];
        $total_copies = $_POST['total-copies'];
        $new_book = new Book($title, $total_copies, $total_copies, 0);
        $new_book->save();
        return $app['twig']->render('books.html.twig', ['books' => Book::getAll()]);
    });

    $app->get('/book/{id}', function($id) use ($app) {
        $book = Book::find($id);
        return $app['twig']->render('book.html.twig', ['book' => $book, 'all_authors' => Author::getAll(), 'authors'=>$book->getAuthors(), 'all_patrons' => Patron::getAll()]);
    });

    $app->get('/author/{id}', function($id) use ($app) {
        $author = Author::find($id);
        return $app['twig']->render('author.html.twig', ['author' => $author, 'all_books' => Book::getAll(), 'books'=>$author->getBooks()]);
    });

    $app->post('/add_author', function() use ($app) {
        $name = $_POST['name'];
        $new_author = new Author($name);
        $new_author->save();
        return $app['twig']->render('authors.html.twig', ['authors' => Author::getAll()]);
    });

    $app->post('/assign_author/{id}', function($id) use ($app) {
        $book = Book::find($id);
        $book->addAuthor($_POST['assign-author']);
        return $app->redirect("/book/".$id);
    });

    $app->post('/assign_book/{id}', function($id) use ($app) {
        $author = Author::find($id);
        $author->addBook($_POST['assign-book']);
        return $app->redirect("/author/".$id);
    });

    $app->post('/checkout_book/{id}', function($id) use ($app) {
        $book = Book::find($id);
        $patron_id = $_POST['checkout-book'];
        $book->checkout();
        return $app->redirect("/patron/".$patron_id);
    });

    $app->patch('/edit_book/{id}', function($id) use ($app) {
        $book = Book::find($id);
        $new_title = $_POST['title'];
        $new_total_copies = $_POST['total-copies'];
        $book->update($new_title, $new_total_copies);
        return $app->redirect("/book/".$id);
    });

    $app->patch('/edit_author/{id}', function($id) use ($app) {
        $author = Author::find($id);
        $new_name = $_POST['new-name'];
        $author->update($new_name);
        return $app->redirect("/author/".$id);
    });

    $app->delete('/delete_book/{id}', function($id) use ($app) {
        $book = Book::find($id);
        $book->delete();
        return $app->redirect("/books");
    });

    $app->delete('/delete_book/{id}', function($id) use ($app) {
        $book = Book::find($id);
        $book->delete();
        return $app->redirect("/books");
    });

    $app->delete('/delete_author/{id}', function($id) use ($app) {
        $author = Author::find($id);
        $author->delete();
        return $app->redirect("/authors");
    });

    $app->get('/patrons', function() use ($app) {
        $patrons = Patron::getAll();
        return $app['twig']->render('patrons.html.twig', ['all_patrons' => Patron::getAll()]);
    });

    $app->post('/add_patron', function() use ($app) {
        $name = $_POST['name'];
        $new_patron = new Patron($name);
        $new_patron->save();
        return $app->redirect("/patrons");
    });

    $app->get('/patron/{id}', function($id) use($app) {
        $patron = Patron::find($id);
        return $app['twig']->render('patron.html.twig', ['patron' => $patron, 'books' => $patron->getBooks(), 'overdue_books' => $patron->getOverdue(), 'booksout' => $patron->getBooksOut()]);
    });

    return $app;
?>
