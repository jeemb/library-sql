
<?php

class Patron
{
    private $name;
    private $id;

    function __construct($name, $id = null)
    {
        $this->name = $name;
        $this->id = $id;
    }

    function getName()
    {
        return $this->name;
    }

    function setName($new_name)
    {
        $this->name = $new_name;
    }

    function getId()
    {
        return $this->id;
    }

    function save()
    {
        $GLOBALS['DB']->exec("INSERT INTO patrons (name) VALUES ('{$this->getName()}');");
        $this->id = $GLOBALS['DB']->lastInsertId();
    }

    function update($name)
    {
        $this->setName($name);

        $GLOBALS['DB']->exec("UPDATE patrons SET name = '{$this->name}' WHERE id = {$this->id}");

    }

    function delete()
    {
        $GLOBALS['DB']->exec("DELETE FROM patrons WHERE id = {$this->getId()};");
        $GLOBALS['DB']->exec("DELETE FROM books_patrons WHERE patron_id = {$this->getId()};");
    }

    function getBooks()
    {
        $returned_books = $GLOBALS['DB']->query("SELECT books.* FROM patrons
            JOIN books_patrons ON (books_patrons.patron_id = patrons.id)
            JOIN books ON (books.id = books_patrons.book_id)
            WHERE patrons.id = {$this->getId()};");
        if ($returned_books) {
            return $returned_books->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Book', ['title', 'total_copies', 'copies_in', 'copies_out', 'id']);
        }

        return [];
    }

    function getBooksOut()
    {
        $returned_books = $GLOBALS['DB']->query("SELECT books.* FROM patrons
            JOIN books_patrons ON (books_patrons.patron_id = patrons.id)
            JOIN books ON (books.id = books_patrons.book_id)
            WHERE patrons.id = {$this->getId()} AND returned = 0;");
        if ($returned_books) {
            return $returned_books->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Book', ['title', 'total_copies', 'copies_in', 'copies_out', 'id']);
        }

        return [];
    }

    function getOverdue()
    {
        $overdue_books = [];
        $checkedout_books = $GLOBALS['DB']->query("SELECT * FROM patrons
            JOIN books_patrons ON (books_patrons.patron_id = patrons.id)
            JOIN books ON (books.id = books_patrons.book_id)
            WHERE patrons.id = {$this->getId()} AND returned = 0;");
        if ($checkedout_books) {
            foreach ($checkedout_books as $book) {
                if (strtotime(date('Y-m-d')) > strtotime($book['due_date'])) {
                    $overdue_book = new Book($book['title'], $book['total_copies'], $book['copies_in'], $book['copies_out'], $book['book_id']);
                    array_push($overdue_books, $overdue_book);
                }
            }
        }
        return $overdue_books;
    }

    static function find($id)
    {
        $patron = $GLOBALS['DB']->query("SELECT * FROM patrons WHERE id={$id}");
        return $patron->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Patron", ["name"])[0];
    }

    static function getAll()
    {
        $returned_patrons = $GLOBALS['DB']->query("SELECT * FROM patrons;");
        return $returned_patrons->fetchAll( PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Patron", ["name"]);

    }

    static function deleteAll()
    {
        $GLOBALS['DB']->exec("DELETE FROM books_patrons;");
        $GLOBALS['DB']->exec("DELETE FROM patrons;");
    }
}
?>
