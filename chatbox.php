<?php
session_start();
include("DBConnection.php");
include("links.php");
$users = mysqli_query($connect,"SELECT * FROM users WHERE Id= '".$_SESSION["userId"]."' ") 
or die("Failed to query database".mysql_error());
$user = mysqli_fetch_assoc($users);
?>

<!DOCTYPE html>
<html>
<head>
<title>My ChatBox</title>
<link rel="stylesheet" href="style.css">
</head>

    <body>
        <h1 style='background :linear-gradient(black,palevioletred); color:#fff; padding: 40px 300px; font-family:Arial Black'> WELCOME TO MY CHATBOX  </h1>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3" style='background :palevioletred;'>
                     <input type="text" id="fromUser" value= <?php echo $user["Id"]; ?> hidden />
                     <br></br>
                     <a style='background :black; padding: 5px 20px; color:#fff; border-radius:20px;'>  
                     <i class="fa fa-user"></i> YOUR ACCOUNT :  <?php echo $user["User"] ?></a>
                    <br></br>
                    <br>
                     <a style='background :blue; padding: 5px 20px; color:#fff;'>  
                     <i class ="fa fa-comment"></i> SEND MESSAGE TO :</a>
                     <br></br>
                     <ul>
                        <?php
                          $msgs = mysqli_query($connect,"SELECT * FROM users ") 
                          or die("Failed to query database".mysql_error());
                          while($msg = mysqli_fetch_assoc($msgs))
                          {
                              echo'<p <li style="background :white;padding: 5px 10px;"> <a href= "?toUser='.$msg["Id"].' ";>'.$msg["User"].'</a> </li> </p>';
                          }
                        ?>
                     </ul>
                     <a  href="index.php"; style='background:black; color:#fff;padding: 5px 10px; border-radius:20px;'><-Back</a>
                     <br></br>
                </div>
                <div class="col-md-7" style='background : LavenderBlush;'>
                   <div class = "wrapper" id="myForm" >
                       <div class = "title">
                        <?php
                           if(isset($_GET["toUser"]))
                           {
                             $userName = mysqli_query($connect,"SELECT * FROM users WHERE Id ='".$_GET["toUser"]."' ") 
                             or die("Failed to query database".mysql_error());
                             $uName = mysqli_fetch_assoc($userName);
                             echo '<input type="text" value='.$_GET["toUser"].' id="toUser" hidden/>';
                             echo $uName["User"];
                           }
                           else
                           {
                             $userName = mysqli_query($connect,"SELECT * FROM users ") 
                             or die("Failed to query database".mysql_error());
                             $uName = mysqli_fetch_assoc($userName);
                             $_SESSION["toUser"] = $uName["Id"];
                             echo '<input type="text" value='.$_SESSION["toUser"].' id="toUser" hidden/>';
                             echo $uName["User"];
                           }
                        ?>
                        <p  style='float:right;margin-top:17px; margin-right:15px;' 
                        onclick="closeForm()"> <i style=' padding: 2px 5px;background:red ; border-radius:5px' class="fa fa-close"></i>
                        </p>           
                </div>
                       <div class ="form" id="msgBody" style ="height:400px; overflow-y:scroll; overflow-x: hidden;">
                           <?php  
                              if(isset($_GET["toUser"]))
                              $chats = mysqli_query($connect,"SELECT * FROM messages WHERE (FromUser = '".$_SESSION["userId"]."' AND 
                             ToUser ='".$_GET["toUser"]."') OR (FromUser = '".$_GET["toUser"]."'  AND 
                             ToUser ='".$_SESSION["userId"]."' )")
                              or die("Failed to query database".mysql_error());
                             
                             else 
                              $chats = mysqli_query($connect,"SELECT * FROM messages WHERE (FromUser = '".$_SESSION["userId"]."' AND 
                             ToUser ='".$_SESSION["toUser"]."') OR (FromUser = '".$_SESSION["toUser"]."'  AND 
                             ToUser ='".$_SESSION["userId"]."' )")
                              or die("Failed to query database".mysql_error());
                              
                              while($chat = mysqli_fetch_assoc($chats))
                              {
                                  if($chat["FromUser"] == $_SESSION["userId"])
                                  {
                                    echo" <div style='text-align:right;'>
                                    <p style= 'background-color:Lavender;word-wrap:break-word; display:inline-block;padding:5px; border-radius:10px;max width:70%'>
                                       ".$chat["Message"]."
                                    </p>
                                    </div>";
                                  }
                                  else{
                                  echo" <div style='text-align:left;'>
                                  <p style= 'background-color:Aquamarine; word-wrap:break-word; display:inline-block;padding:5px; border-radius:10px;max width:70%'>
                                     ".$chat["Message"]."
                                  </p>
                                  </div>";
                              }
                              }
                           ?>
                          
                       </div>
                       <div class="typing-field">
                             <div class="input-data">
                                 <input id="message" type="text" placeholder="Type someting here.." required >
                                 <button  id="send">Send</button>
                             </div>
                       </div>                      
                    </div>  
                </div> 
                <div class="col-md-2" style='background : LavenderBlush;'>
                    <button style=' border-radius:20px; background:linear-gradient(plum,lightpink); margin-left:10px; margin-top:470px; padding:10px 10px;'onclick="openForm()"> <i class="fa fa-comment"></i> Chat with us!</button>   
                    </div>
            </div>
        </div>  
    </body>

<script type="text/javascript">
        
        $(document).ready(function(){
            $("#send").on("click",function(){
                $.ajax({
                    url:"insertMessage.php",
                    method:"POST",
                    data:{
                        fromUser: $("#fromUser").val(),
                        toUser: $("#toUser").val(),
                        message: $("#message").val(),
                    },
                    dataType:"text",
                    success:function(data)
                    {
                          $("#message").val("");
                    }
                });
            });

            setInterval(function(){
            $.ajax({
                url:"realTimeChat.php",
                method:"POST",
                data:{
                    fromUser:$("#fromUser").val(),
                    toUser:$("#toUser").val()
                },
                dataType:"text",
                success:function(data)
                {
                    $("#msgBody").html(data);
                }
            });
        },700);
    });
    </script>
    <script>
        function openForm() 
        {
           document.getElementById("myForm").style.display = "block";
        }

        function closeForm() 
        {
           document.getElementById("myForm").style.display = "none";
        }
    </script>
</html>