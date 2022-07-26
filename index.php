<?php
    // connect to the database 
   $servername = "localhost";
   $username = "root";
   $password = "root";
   $dbname = "likes";
   
   // Create connection
   $conn = new mysqli($servername, $username, $password, $dbname);
   // Check connection
   if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
   }

   //get the current status and fetch them on page load 
   



   // get post_id from ajax and process
   if (!empty($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
   
        // make update query to invert the like status
        $sql_update = "UPDATE likes SET is_liked = NOT is_liked WHERE id = '$post_id'";
        $result = $conn->query($sql_update);

        $sql = "SELECT is_liked FROM likes where post_id = '$post_id' ";
        $result = $conn->query($sql);
        if ($result->num_rows != 0) {
            while($row = $result->fetch_assoc()){
        //send updated value to frontend
               $data["is_liked"] = $row['is_liked'];
                $data["post_id"] = $post_id;
                // echo $data['is_liked'];
                // echo $data['post_id'];
                echo json_encode($data);


    //   exit to not show the rest of the script on the server response 
      exit();
     }
   } else {
     echo "Post doesn't exist";
     exit();
   }
   $conn->close();
   }    
 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Posts</title>
    <!-- BOOTSTRAP CSS only -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <!-- Fontawesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
 <!-- BOOTSTRAP JavaScript Bundle with Popper -->
 <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
</head>
<style>
    i{
       color: black; 
       cursor:pointer;
    }

    i:hover{
        color:blueviolet;
    }

</style>
<body>
   <div class="container">
         <div class="row justify-content-center">
             <div class="col-md-8">

             <?php
             $sql = "SELECT is_liked, post_id, post FROM likes";
             $result = $conn->query($sql);  
             while($row = $result->fetch_assoc()) {     
                
                echo '<div class="card mt-5">';
                echo '<div class="card-header">Post' .$row["post_id"]. '</div>';
                echo '<div class="card-body">';
                echo $row['post'];
                echo "</div>";
                if ($row['is_liked'] == 1) {
                    echo '<div class="card-header">Like: <i class="fa-solid fa-thumbs-up" style="color:red" id="'.$row["post_id"].'"></i></div>';
                }
                else
                {
                    echo '<div class="card-header">Like: <i class="fa-solid fa-thumbs-up" id="'.$row["post_id"].'"></i></div>';
                }
                echo '</div><hr>';
             }
                $conn->close();
              ?>

                
           
             
             <div id="result" class="row justify-content-center">  
             </div>

        </div>
   </div>
</body>
</html>

<script>
    $(document).ready(function(){
        $("i").click(function(){
            let post_id = this.id;
            $.post("index.php", {
                post_id: post_id
            }, function(data,status){
                // $("#result").html(data);
                data = JSON.parse(data);
                if(data.is_liked == 1){
                    $('#'+post_id).css('color','red');  
                }    
                else{
                    $('#'+post_id).css('color', 'black');
                }
            });
        });
    });
</script>