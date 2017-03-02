
<?php

class Book
{
    private $title;
    private $total_copies;
    private $copies_in;
    private $copies_out;
    private $id;

    function __construct($title, $total_copies, $copies_in, $copies_out, $id = null)
    {
        $this->title = $title;
        $this->total_copies = $total_copies;
        $this->copies_in = $copies_in;
        $this->copies_out = $copies_out;
        $this->id = $id;
    }

    function getTitle()
    {
        return $this->title;
    }

    function setTitle($new_title)
    {
        $this->title = $new_title;
    }

    function getTotalCopies()
    {
        return $this->total_copies;
    }

    function setTotalCopies($new_total_copies)
    {
        $this->total_copies = $new_total_copies;
    }

    function getCopiesIn()
    {
        return $this->copies_in;
    }

    function setCopiesIn($new_copies_in)
    {
        $this->copies_in = $new_copies_in;
    }

    function getCopiesOut()
    {
        return $this->copies_out;
    }

    function setCopiesOut($new_copies_out)
    {
        $this->copies_out = $new_copies_out;
    }

    function getId()
    {
        return $this->id;
    }

    function save()
    {
        $GLOBALS['DB']->exec("INSERT INTO books (title, total_copies, copies_in, copies_out) VALUES ('{$this->getTitle()}', '{$this->getTotalCopies()}', '{$this->getCopiesIn()}', '{$this->getCopiesOut()}');");
        $this->id = $GLOBALS['DB']->lastInsertId();
    }

    function update($new_title, $new_total_copies)
    {
        if ($new_total_copies >= $this->copies_out)
        {
            $this->setTitle($new_title);
            $this->setCopiesIn($new_total_copies - $this->copies_out);
            $this->setTotalCopies($new_total_copies);
            $GLOBALS['DB']->exec("UPDATE books SET title = '{$this->title}', total_copies = {$this->total_copies}, copies_in = {$this->copies_in} WHERE id = {$this->id}");
        }
    }

    function delete()
    {
        $GLOBALS['DB']->exec("DELETE FROM books WHERE id = {$this->getId()};");
        $GLOBALS['DB']->exec("DELETE FROM books_authors WHERE book_id = {$this->getId()};");
    }

    function addAuthor($id)
    {

        $GLOBALS['DB']->exec("INSERT INTO books_authors (book_id, author_id) VALUES ({$this->getId()}, {$id});");
    }

    function getAuthors()
    {
        $returned_authors = $GLOBALS['DB']->query("SELECT authors.* FROM books
            JOIN books_authors ON (books_authors.book_id = books.id)
            JOIN authors ON (authors.id = books_authors.author_id)
            WHERE books.id = {$this->getId()};");
        if ($returned_authors) {
            return $returned_authors->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Author', ['name', 'id']);
        }

        return [];
    }
    function getPatrons()
    {
        $returned_patrons = $GLOBALS['DB']->query("SELECT patrons.* FROM books
            JOIN books_patrons ON (books_patrons.book_id = books.id)
            JOIN patrons ON (patrons.id = books_patrons.patron_id)
            WHERE books.id = {$this->getId()} AND returned = 0;");
        if ($returned_patrons) {
            return $returned_patrons->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Patron', ['name', 'id']);
        }

        return [];
    }

    function checkout($id)
    {
        $checkout_date = date('Y-m-d');
        $due_date = date("Y-m-d", strtotime("+14 days"));
        $false = 0;

        $this->setCopiesIn($this->getCopiesIn()-1);
        $this->setCopiesOut($this->getCopiesOut()+1);
        $GLOBALS['DB']->exec("UPDATE books SET copies_in = {$this->getCopiesIn()}, copies_out = {$this->getCopiesOut()};");

        $GLOBALS['DB']->exec("INSERT INTO books_patrons (book_id, patron_id, checkout_date, due_date, returned) VALUES ({$this->getId()}, {$id}, '{$checkout_date}', '{$due_date}', {$false});");

    }

    function addOverdue($id)
    {
        $checkout_date = date("Y-m-d", strtotime("-14 days"));
        $due_date = date('Y-m-d', strtotime("-1 days"));
        $false = "false";

        $this->setCopiesIn($this->getCopiesIn()-1);
        $this->setCopiesOut($this->getCopiesOut()+1);
        $GLOBALS['DB']->exec("UPDATE books SET copies_in = {$this->getCopiesIn()}, copies_out = {$this->getCopiesOut()};");

        $GLOBALS['DB']->exec("INSERT INTO books_patrons (book_id, patron_id, checkout_date, due_date, returned) VALUES ({$this->getId()}, {$id}, '{$checkout_date}', '{$due_date}', {$false});");

    }

    function returnCopy($id)
    {

        $this->setCopiesIn($this->getCopiesIn()+1);
        $this->setCopiesOut($this->getCopiesOut()-1);
        $GLOBALS['DB']->exec("UPDATE books SET copies_in = {$this->getCopiesIn()}, copies_out = {$this->getCopiesOut()};");

        $GLOBALS['DB']->exec("UPDATE books_patrons SET returned = 1 WHERE patron_id={$id} AND book_id = {$this->getId()};");

    }

    static function find($id)
    {
        $book = $GLOBALS['DB']->query("SELECT * FROM books WHERE id={$id}");
        return $book->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Book", ["title", "total_copies", "copies_in", "copies_out"])[0];
    }

    static function getAll()
    {
        $returned_books = $GLOBALS['DB']->query("SELECT * FROM books;");
        return $returned_books->fetchAll( PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Book", ["title", "total_copies", "copies_in", "copies_out"]);

    }

    static function deleteAll()
    {
        $GLOBALS['DB']->exec("DELETE FROM books;");
        $GLOBALS['DB']->exec("DELETE FROM books_authors;");
    }
}
?>
