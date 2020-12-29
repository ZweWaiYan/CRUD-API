<?php

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");

    require '../db_connect.php';

    $request_method = $_SERVER["REQUEST_METHOD"];

    switch ($request_method) {
        case 'GET':
			if (!empty($_GET["id"])) {
                $id = intval($_GET["id"]);
				show($id);
			}else {
                index();
            }
			break;

        case 'POST':
			if (!empty($_GET["id"])) {
				$id = intval($_GET["id"]);
				update($id);
			}else {
				store();
			}
            break;
            
        case 'DELETE':
            $id = $_GET["id"];
            Destory($id);
            break;

        default:
			header("HTTP/1.0 405 Method Not Allowed");
			$response = array(
					'status' => "0",
					'status_message' => "Method Not Allowed"
				);
			echo json_encode($response);
            break;
    }

    //Read
    ////////////

    //for show all data
    function index(){
        global $pdo;
        $sql = "SELECT * FROM sub_category";
        $stmt = $pdo -> prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $sub_categories_arr = array();

        if ($stmt->rowCount() <= 0) {
            $sub_categories_arr["status"] = "0";
            $sub_categories_arr["status_message"] = "No Data";
        }else{
            $sub_categories_arr["status"] = "1";
            $sub_categories_arr["status_message"] = "200 OK";
            $sub_categories_arr["data"] = array();

            foreach ($rows as $row){ 
                $subcategory = array(
                    "id" => $row["id"],
                    "name" => $row["sub_category_name"],
                    "category_id" => $row["category_id"]
                );
                array_push($sub_categories_arr["data"],$subcategory);
            }
        }
        http_response_code(200);
        echo json_encode($sub_categories_arr);
    }

    //for category/1 function
    function show($id) {
        global $pdo;
        $sql = "SELECT sub_category.*,categories.category_name FROM sub_category INNER JOIN categories ON sub_category.category_id = categories.id where sub_category.category_id=:id";
        $stmt = $pdo -> prepare($sql);
        $stmt->bindParam(":id" , $id);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        // var_dump($row); die();

        $sub_categories_arr = array();

        if ($stmt->rowCount() <= 0) {
            $sub_categories_arr["status"] = "0";
            $sub_categories_arr["status_message"] = "No Data";
        }else{
            $sub_categories_arr["status"] = "1";
            $sub_categories_arr["status_message"] = "200 OK";
            $sub_categories_arr["data"] = array();

            foreach ($rows as $row){ 
                $subcategory = array(
                    "id" => $row["id"],
                    "name" => $row["sub_category_name"],
                    "category_name" => $row["category_name"]
                );
                array_push($sub_categories_arr["data"],$subcategory);
            }
        }
        http_response_code(200);
        echo json_encode($sub_categories_arr);
    }

    //Create
    ////////////
    
    function store(){
        global $pdo;
        $name = $_POST['sub_category_name'];
        $category_id = $_POST['category_id'];

        // $source_dir = "../image/";
		// $file_path = $source_dir.$image['name'];
		// $image_file = "/image/".$image['name'];

        // move_uploaded_file($image['tmp_name'], $file_path);
        
        // && !empty($file_path)
        if(!empty($name) && !empty($category_id)){
            $sql = "SELECT * FROM sub_category where sub_category_name=:name";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":name" , $name);
            $stmt->execute();

            if($stmt->rowCount()){
                $response = array(
                    'status' => "0",
                    'status_message' => "That name is already Added in Database"
                );
            }else{                                                          //,category_image             //:image
                $sql = "INSERT INTO sub_category(sub_category_name,category_id) VALUES (:name,:category_id)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":name",$name);
                $stmt->bindParam(":category_id", $category_id);
                //$stmt->bindParam(":image", $image_file);
                $stmt->execute();
                if($stmt->rowCount()){
                    $response = array(
                        'status' => "1",
                        'status_message' => "Sub Category is added successfully"
                    );
                }else{
                    $response = array(
                        'status' => "0",
                        'status_message' => "Sub Category cann't added to database"
                    );
                }
                }
            }else{
                $response = array(
                    'status' => "0",
                    'status_message' => "Sub Category || Category Id is required"
                );
            }

        echo json_encode($response);
    }   

    //Update
    function update($id){
        global $pdo;
        // $name = $_POST['sub_category_name'];
        // $category_name = $_POST['category_name'];

        if(!empty($_POST['sub_category_name']) && !empty($_POST['category_id'])){
            $name = $_POST['sub_category_name'];
            $category_id = $_POST['category_id'];
            $sql = "UPDATE sub_category SET category_id=:category_id , sub_category_name=:name where id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":name",$name);
            $stmt->bindParam(":category_id",$category_id);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
                  
            if ($stmt -> rowCount()) {
                $response = array(
                    'status' => "1",
                    'status_message' => "Category is updated"
                );
            }else{
                $response = array(
                    'status' => "0",
                    'status_message' => "Category is can't updated"
                );
            }
        }else{
            $response = array(
                'status' => "0",
                'status_message' => "Sub Category || Category Name is required"
            );
        }
           
        echo json_encode($response);
    }

    //Destory
    function Destory($id){
        global $pdo;

        $sql = "DELETE FROM sub_category where id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":id",$id);
        $stmt->execute();

        if($stmt->rowCount()){
            $response = array(
                'status' => "1",
                'status_message' => "Delete Successfully"
            );
        }else{
            $response = array(
                'status' => "0",
                'status_message' => "That Category is can't deleted"
            );
        }
        echo json_encode($response);
    }
?>