<?php
require_once "Database.php";
require_once "model/NewBook.php";
require_once "model/Author.php";
require_once "model/Category.php";
require_once "model/Book.php";

class Repository
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConn();
    }


    private function createName($name)
    {
        $nameArray = explode(" ", $name);
        $name = $nameArray[0];
        $surname = "";

        for($a = 1; $a < sizeof($nameArray);$a++)
        {
            $surname = $surname . " " . $nameArray[$a];
        }

        return [trim($name," "), trim($surname, " ")];
    }

    public function addNewBook(NewBook $newBook): bool|int|string
    {
        if($newBook->author == null && $newBook->fullName != null)
        {
//            $nameArray = explode(" ", $newBook->fullName);
//            $name = $nameArray[0];
//            $surname = "";

            $splitName = $this->createName($newBook->getFullName());

            $newAuthorId = $this->addNewAuthor($splitName[0], $splitName[1]);

            if($newAuthorId != -1)
            {
                $newBook->setAuthor($newAuthorId);
                return $this->addNewBookProcess($newBook);
            }
            else
                return -1;
        }
        else if($newBook->author != null && $newBook->fullName == null)
            return $this->addNewBookProcess($newBook);

        return -1;

    }

    public function addNewBookProcess(NewBook $newBook)
    {
        try {
            $sql = "INSERT INTO `book`(`isbn`, `title`, `price`, `category`, `author`) VALUES (:isbn,:title,:price,:category,:author)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("isbn",$newBook->isbn,PDO::PARAM_STR);
            $stmt->bindParam("title",$newBook->title,PDO::PARAM_STR);
            $stmt->bindParam("price",$newBook->price);
            $stmt->bindParam("category",$newBook->category);
            $stmt->bindParam("author",$newBook->author);

            $result = $stmt->execute();

            if($result)
                return $this->conn->lastInsertId();

        }
        catch (PDOException $e)
        {
            return -1;
        }
        return -1;
    }

    public function getBookById($id)
    {
        $sql = "SELECT * FROM `book` WHERE `id` = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS,"Book");
        return $stmt->fetch();
    }

    public function addNewAuthor($name, $surname)
    {
        try {
            $sql = "INSERT INTO `author`(`name`, `surname`) VALUES (:name,:surname)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("name",$name,PDO::PARAM_STR);
            $stmt->bindParam("surname",$surname,PDO::PARAM_STR);

            $result = $stmt->execute();

            if($result)
                return $this->conn->lastInsertId();

        }
        catch (PDOException $e)
        {
            return -1;
        }
        return -1;
    }
    
    public function getAllBooks()
    {
        $stmt = $this->conn->prepare("SELECT * FROM `book`");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS,"Book");
    }

    public function getBookByIsbn($isbn)
    {

        $sql = "SELECT * FROM `book` WHERE `isbn` = :isbn";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("isbn", $isbn, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS,"Book");
        return $stmt->fetch();
    }

    public function getAllBooksSortedByPrice($sort)
    {
        $sql = "";
        if($sort == "ASC")
            $sql = "SELECT * FROM `book` ORDER BY `price` ASC";
        else
            $sql = "SELECT * FROM `book` ORDER BY `price` DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS,"Book");
    }

    public function getAllBooksSortedByTitle($sort)
    {
        $sql = "";
        if($sort == "ASC")
            $sql = "SELECT * FROM `book` ORDER BY `title` ASC";
        else
            $sql = "SELECT * FROM `book` ORDER BY `title` DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS,"Book");
    }

    public function getAllBooksSortedByCategory($sort)
    {
        $sql = "";
        if($sort == "ASC")
            $sql = "SELECT book.id as id, book.isbn as isbn, book.title as title, book.price as price, book.category as category, book.author as author FROM `book` INNER JOIN category
ON book.category = category.id ORDER BY category.title ASC";
        else
            $sql = "SELECT book.id as id, book.isbn as isbn, book.title as title, book.price as price, book.category as category, book.author as author  FROM `book` INNER JOIN category
ON book.category = category.id ORDER BY category.title DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS,"Book");
    }

    public function getAllBooksSorted($what, $sort)
    {
        if($what === "author")
        {
            return $this->getAllBooksSortedByAuthorFullName($sort);
        }
        else if($what === "price")
        {
            return $this->getAllBooksSortedByPrice($sort);
        }

        else if($what === "title")
        {
            return $this->getAllBooksSortedByTitle($sort);
        }

        else if($what === "category")
        {
            return $this->getAllBooksSortedByCategory($sort);
        }

        return array();


    }

    private function getAllBooksSortedByAuthorFullName($sort)
    {
        $sql = "";
        $space = " ";
        if($sort == "ASC")
            $sql = "SELECT book.`id`, `title`, `isbn`, `price`, `category`, `author` FROM `book` INNER JOIN `author`
ON book.author = author.id ORDER BY CONCAT(author.name, :space, author.surname) ASC";
        else
            $sql = "SELECT book.`id`, `title`, `isbn`, `price`, `category`, `author` FROM `book` INNER JOIN `author`
ON book.author = author.id ORDER BY CONCAT(author.name, :space, author.surname) DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("space", $space, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS,"Book");
    }



    public function getAllCategory()
    {

        $sql = "SELECT * FROM `category`";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS,"Category");
    }

    public function getCategoryById($id)
    {

        $sql = "SELECT * FROM `category` WHERE `id` = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS,"Category");
        return $stmt->fetch();
    }

    public function getAllAutor()
    {

        $sql = "SELECT * FROM `author`";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS,"Author");
    }

    public function getAutorById($id)
    {
        $sql = "SELECT * FROM `author` WHERE `id` = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS,"Author");
        return $stmt->fetch();
    }

    public function getAutorByStartFullName($name)
    {
        $name = $name . "%";
        $space = " ";
        $sql = "SELECT * FROM `author` WHERE CONCAT(`name`,:space,`surname`) LIKE :name";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("name", $name, PDO::PARAM_STR);
        $stmt->bindParam("space", $space, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS,"Author");
    }

}