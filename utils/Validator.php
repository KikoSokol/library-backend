<?php
require_once "model/NewBook.php";

class Validator
{
    public function validateNewBook(NewBook $newBook): array | bool
    {
        $result = array();

        if(is_null($newBook->getIsbn()))
            $result["isbn"] = "ISBN is empty";
        else if(is_numeric($newBook->getIsbn()))
            $result["isbn"] = "ISBN cannot be a number";
        else if(strlen($newBook->getIsbn()) === 0)
            $result["isbn"] = "ISBN is required";

        if(is_null($newBook->getTitle()))
            $result["title"] = "Title is empty";
        else if(!is_string($newBook->getTitle()))
            $result["title"] = "Title must be string";

        if(is_null($newBook->getPrice()))
            $result["price"] = "Price is required";
        else if(!is_double($newBook->getPrice()))
            $result["price"] = "Price must be double number";

        if(is_null($newBook->getCategory()))
            $result["category"] = "Category is required";
        else if(!is_int($newBook->getCategory()))
            $result["category"] = "Category must be int number";

        if(is_null($newBook->getAuthor()) && is_null($newBook->getFullName()))
        {
            $result["author"] = "Author is required";
        }

        if(is_null($newBook->getAuthor()) && !is_null($newBook->getFullName()))
        {
            if(is_numeric($newBook->getFullName()))
                $result["author"] = "New author must be name and surname, cannot be numeric";
            else if(strlen($newBook->getFullName()) === 0)
                $result["author"] = "New author name is empty";
        }
        else if(!is_null($newBook->getAuthor()) && is_null($newBook->getFullName()))
        {
            if(!is_int($newBook->getAuthor()))
                $result["author"] = "Author must be int";
        }

        if(sizeof($result) === 0)
            return true;

        return $result;
    }
}