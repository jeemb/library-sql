<?php

    date_default_timezone_set("America/Los_Angeles");
    require_once __DIR__."/../vendor/autoload.php";
    require_once __DIR__."/../src/Book.php";
    require_once __DIR__."/../src/Author.php";

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
        return $app['twig']->render('books.html.twig', ['books' => Books::getAll()]);
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
        return $app['twig']->render('book.html.twig', ['book' => $book, 'all_authors' => Author::getAll(), 'authors'=>$book->getAuthors()]);
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


    return $app;
?>
