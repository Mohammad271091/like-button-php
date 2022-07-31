<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

 
    if (!isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(10));
    }
    
    require __DIR__ . '/vendor/autoload.php';
    use Carbon\Carbon;


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
        $sql_update = "UPDATE likes SET total_likes = total_likes + 1 , is_liked = NOT is_liked WHERE post_id = '$post_id'";
        $result = $conn->query($sql_update);

        $sql = "SELECT is_liked, total_likes FROM likes where post_id = '$post_id' ";
        $result = $conn->query($sql);
        if ($result->num_rows != 0) {
            while($row = $result->fetch_assoc()){
        //send updated value to frontend
               $data["is_liked"] = $row['is_liked'];
               $data["total_likes"] = $row['total_likes'];
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


   function shortNumber($num) 
{
    $units = ['', 'K', 'M', 'B', 'T'];
    for ($i = 0; $num >= 1000; $i++) {
        $num /= 1000;
    }
    return round($num, 1) . $units[$i];
}
 
// Handle the add post form 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    
    if($_POST['csrf'] === $_SESSION['csrf'])
   { 
    $email = filter_data($_POST['email']);
    $post = filter_data($_POST['post']);
    $time = Carbon::now();

     // add to database
     $sql_add = "INSERT INTO likes (`email`, `post`, `created_at`) VALUES ('$email', '$post', '$time')";
     $result = $conn->query($sql_add);
     header('location:./');
    }
    else{
        echo "Invalid request";
        exit();
    }
   

}

function filter_data($data){
    if (empty($data)) {
        exit('please fill all the required data');
    }
    trim($data);
    htmlspecialchars($data);
    stripslashes($data);
    return $data;
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
    <!-- MODAL  -->
<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
  Launch demo modal
</button> -->

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add Post</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
       <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']);?>" method="POST">
       <div class="mb-3">
        <label for="exampleFormControlInput1" class="form-label">Email address</label>
        <input type="email" class="form-control" name="email" placeholder="name@example.com">
        </div>
        <div class="mb-3">
        <label for="exampleFormControlTextarea1" class="form-label">Post content</label>
        <textarea class="form-control" name="post" rows="3" style="resize: none;"></textarea>
        </div>
        <input type="hidden" class="form-control" name="csrf" value="<?= $_SESSION['csrf']; ?>">
      </div>
      <div class="modal-footer">
          <button type="submit" name="submit" class="btn btn-primary">Post</button>
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- MODAL END  -->
        <div class="container">
         <div class="row justify-content-center">
             <div class="col-lg-6">
            
            <button class="btn btn-success mt-5" data-bs-toggle="modal" data-bs-target="#exampleModal">+ Add Post</button>

             <?php
             $sql = "SELECT is_liked, post_id, post, total_likes, created_at FROM likes";
             $result = $conn->query($sql);  
             while($row = $result->fetch_assoc()) {     
                echo '<div class="card mt-2">';
                echo '<div class="card-header"><b>Post ' .$row["post_id"].'</b>
                <span style="float:right; color:tomato;">'.Carbon::parse($row['created_at'])->diffForHumans().'</span></div>';
                echo '<div class="card-body">';
                echo $row['post'];
                echo "</div>";
                if ($row['is_liked'] == 1) {
                    echo '<div class="card-header">Like: <i class="fa-solid fa-thumbs-up" style="color:red" id="'.$row["post_id"].'"></i>
                    <span id = "likes'.$row["post_id"].'" style="float:right">Total Likes/Unlikes: '.$row['total_likes'].'</span>
                    </div>';
                }
                else
                {
                    echo '<div class="card-header">Like: <i class="fa-solid fa-thumbs-up" id="'.$row["post_id"].'"></i>
                    <span id = "likes'.$row["post_id"].'" style="float:right">Total Likes/Unlikes: '.$row['total_likes'].'</span>
                    </div>';
                }
                echo '</div><hr>';
             }
                $conn->close();
              ?>

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
                
                // parse json data from the server 
                data = JSON.parse(data);

                // get the total likes field updated 
                $('#likes'+post_id).html('Total Likes/Unlikes: '+ data.total_likes);

                // change the likes button color depending on the status 
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