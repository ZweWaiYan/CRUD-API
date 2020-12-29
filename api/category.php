<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");

    require '../db_connect.php';

    $request_method = $_SERVER["REQUEST_METHOD"];

    //Function call localhost/api/category or localhoset/api/category/1
    switch ($request_method) {
        case 'GET':
            if (!empty($_GET["id"])) {
                $id = intval($_GET["id"]);
                echo "$id"; die();
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
            //if user selected other method (not CRUD) it show message for user.
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
        //connect db_connect.php 
        global $pdo;
        //create query for show all data
        $sql = "SELECT * FROM categories";
        //run this query
        $stmt = $pdo -> prepare($sql);
        $stmt->execute();

        //show all data
        $rows = $stmt->fetchAll();

        //form json (Main Array)
        $categories_arr = array();
        
        if ($stmt->rowCount() <= 0) {
            // [""]=> key
            // {
            //     "status" : "0",
            //     "status_message" : "No Data"
            // }
            $categories_arr["status"] = 0;
            $categories_arr["status_message"] = "No Data";
        }else{
            $categories_arr["status"] = 1;
            $categories_arr["status_message"] = "200 OK";
            //(Main Array => Data)
            $categories_arr["data"] = array();

            //(Main Array => Data => Data with Array)
            foreach ($rows as $row){ 
                $category = array(
                    "id" => $row["id"],
                    "name" => $row["category_name"]
                );
                //push Array Data to Data
                           // to Insert            // want Insert
                array_push($categories_arr["data"],$category);
            }
        }
        http_response_code(200);
        //convert array to json
        echo json_encode($categories_arr);
    }

    //for category/1 function
    function show($id) {
		global $pdo;
		$sql = "SELECT * FROM categories where id=:id";
		$stmt = $pdo->prepare($sql);
		$stmt->bindParam(":id", $id);
		$stmt->execute();

		$rows = $stmt->fetchAll();

		$categories_arr = array();

		if ($stmt->rowCount() <=0 ) {
			$categories_arr["status"] = "0";
			$categories_arr["status_message"] = "Something went worng";
		}else {
			$categories_arr["status"] = "1";
			$categories_arr["status_message"] = "200 OK";
			$categories_arr["data"] = array();

			foreach ($rows as $row) {
				$category = array(
					"id" => $row["id"],
					"name" => $row["category_name"]
				);
				array_push($categories_arr["data"], $category);
			}
		}
		http_response_code(200);
		echo json_encode($categories_arr);
	}

    //Create
    ////////////
    
    function store(){
        global $pdo;
        $name = $_POST['category_name'];

        if(!empty($name)){
            $sql = "SELECT * FROM categories where category_name =:name";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":name" , $name);
            $stmt->execute();

            if($stmt->rowCount()){
                $response = array(
                    'status' => "0",
                    'status_message' => "That name is already Added in Database"
                );
            }else{
                $sql = "INSERT INTO categories(category_name) VALUES (:name)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":name",$name);
                $stmt->execute();
                if($stmt->rowCount()){
                    $response = array(
                        'status' => "1",
                        'status_message' => "Category is Added successfully"
                    );
                }else{
                    $response = array(
                        'status' => "0",
                        'status_message' => "Category can't Added to Database"
                    );
                }
                }
            }else{
                $response = array(
                    'status' => "0",
                    'status_message' => "Category is required"
                );
            }

        echo json_encode($response);
    }   

      //Update
      function update($id){
        global $pdo;
        $name = $_POST['category_name'];
        if(!empty($name)){
            $sql = "UPDATE categories SET category_name=:name where id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":name",$name);
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
                'status_message' => "Category name is required"
            );
        }
        echo json_encode($response);
    }

      //Destory
      function Destory($id){
        global $pdo;

        $sql = "DELETE FROM categories where id=:id";
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