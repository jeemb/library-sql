
<?php

class Author
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
        $GLOBALS['DB']->exec("INSERT INTO authors (name) VALUES ('{$this->getName()}');");
        $this->id = $GLOBALS['DB']->lastInsertId();
    }

    function update($name)
    {
        $this->setName($name);

        $GLOBALS['DB']->exec("UPDATE authors SET name = '{$this->name}' WHERE id = {$this->id}");

    }

    function delete()
    {
        $GLOBALS['DB']->exec("DELETE FROM authors WHERE id = {$this->getId()};");
        $GLOBALS['DB']->exec("DELETE FROM books_authors WHERE author_id = {$this->getId()};");
    }

    function addBook($id)
    {

        $GLOBALS['DB']->exec("INSERT INTO books_authors (book_id, author_id) VALUES ({$id}, {$this->getId()});");
    }

    function getBooks()
    {
        $returned_books = $GLOBALS['DB']->query("SELECT books.* FROM authors
            JOIN books_authors ON (books_authors.author_id = authors.id)
            JOIN books ON (books.id = books_authors.book_id)
            WHERE authors.id = {$this->getId()};");
        if ($returned_books) {
            return $returned_books->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Book', ['title', 'total_copies', 'copies_in', 'copies_out', 'id']);
        }

        return [];
    }

    static function find($id)
    {
        $author = $GLOBALS['DB']->query("SELECT * FROM authors WHERE id={$id}");
        return $author->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Author", ["name"])[0];
    }

    static function getAll()
    {
        $returned_authors = $GLOBALS['DB']->query("SELECT * FROM authors;");
        return $returned_authors->fetchAll( PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, "Author", ["name"]);

    }

    static function deleteAll()
    {
        $GLOBALS['DB']->exec("DELETE FROM books_authors;");
        $GLOBALS['DB']->exec("DELETE FROM authors;");
    }
}
?>
