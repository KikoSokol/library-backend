<?php
require_once "Service.php";
require_once "model/dto/BookDto.php";
header("Access-Control-Allow-Origin: *");

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

$service = new Service();

header('Content-type: application/json');

$operation = "";

if(isset($_GET["operation"]))
    $operation = $_GET["operation"];


switch ($operation)
{
    case "addNewBook":
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        $newBook = new NewBook();
        $newBook->setIsbn($data->isbn);
        $newBook->setTitle($data->title);
        $newBook->setPrice($data->price);
        $newBook->setFullName($data->fullName);
        $newBook->setCategory($data->category);
        $newBook->setAuthor($data->author);

        $result = $service->addNewBook($newBook);

        if(!($result instanceof BookDto))
            header("HTTP/1.1 400 Bad Request");
        else
            header("HTTP/1.1 200 OK");
        echo json_encode($result);
        break;
    case "getAllBooks":
        echo json_encode($service->getAllBooks());
        break;
    case "getAllBooksSorted":
        echo json_encode($service->getAllBooksSorted($_GET["whatSort"],$_GET["sort"]));
        break;
    case "getAllCategory":
        $category = $service->getAllCategory();
        if($category === false)
        {
            header("HTTP/1.1 404 Not Found");
            echo "Category not found";
        }
        else
        {header("HTTP/1.1 200 OK");
            echo json_encode($category);
        }
        break;
    case "getAuthorForAutocomplete":
        echo json_encode($service->getAuthorByStartMatchFullName($_GET["fullName"]));
        break;
}
