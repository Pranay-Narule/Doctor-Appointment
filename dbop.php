<?php
include 'db_connection.php';
$conn = OpenCon();



 $output = '';


$full_name_error=$age_error=$dob_error=$add_error=$contact_error=$email_error=$app_error=$tm_error=$file_error="";
$full_name=$age_pat=$dob_dt=$app_dt="";
$name=$age=$dob=$add=$contact=$email=$app=$fileName=$b_group=$tm="";
$upload_error="";

if ($_SERVER["REQUEST_METHOD"]=="POST") 
{
	if(empty($_POST["name"]))
	{
		$full_name_error="Name is required";
	}
	else
	{
		$full_name=test_input($_POST["name"]);
		
		if(!preg_match("/^[a-zA-Z]+(?:\s[a-zA-Z]+)+$/",$full_name))
		{
			$full_name_error="only letter and space allowed!";
			$name=$full_name;
		}
		else
		{
			$name=$full_name;
		}
	}
	if(empty($_POST["age"]))
	{
		$age_error="age is required";
	}
	else
	{
		$age_pat=$_POST["age"];
		
		if(!preg_match("/^[0-9]+$/",$age_pat))
		{
			$age_error="only 1 to 100 accept";
		}
		else if ($_POST["age"] >100 && $_POST["age"] <0) 
		{
			$age_error = 'Age must between 1-100 digits';
		}
		else
		{
			
			$age=$age_pat;
		}
	}
	if(empty($_POST["dob"]))
	{
		$dob_error="dob is required";
	}
	else
	{
		$dob_dt=$_POST["dob"];
		$dob=$dob_dt;
		
	}

	$b_group=$_POST['b-group'];
	if(empty($_POST["add"]))
	{
		$add_error="address is required";
	}
	else
	{
		$add=$_POST["add"];
		
	}
	if(empty($_POST["contact"]))
	{
		$contact_error="contact is required";
	}
	else
	{
		$contact=test_input($_POST["contact"]);
		if(!preg_match("/^[0-9]{10}+$/",$contact))
		{
			$contact_error="enter valid contact";
		}
		else
		{
			$contact=$contact;
		}
	}

	if(empty($_POST["email"]))
	{
		$email_error="email is required";
	}
	else
	{
		$email=test_input($_POST["email"]);
		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$email_error="invalid email";
		}
		else
		{
			$email=$email;
		}
	}
	if(empty($_POST["date-app"]))
	{
		$app_error="Appointment date is required";
	}
	else
	{
		$app_dt=$_POST["date-app"];
		$app=$app_dt;
		
	}

	if(empty($_POST['tm'])){
		$tm_error = "Time is required.";
	}
	else
	{
		$tm=$_POST['tm'];

	}
	if(isset($_FILES['file']))
	{
		$file=$_FILES['file'];

		 // print_r($file);
		
		// $title = $_POST["title"];


		$fileName=$_FILES['file']['name'];
		$fileTmpName=$_FILES['file']['tmp_name'];
		$fileSize=$_FILES['file']['size'];
		$fileError=$_FILES['file']['error'];
		$fileType=$_FILES['file']['type'];

		$fileExt=explode('.', $fileName);
		$fileActualExt=strtolower(end($fileExt));

		$allowed=array('jpg','jpeg','png','pdf');

		if(in_array($fileActualExt, $allowed)){
			if($fileError===0){
				if ($fileSize<1000000) {
					$fileNameNew=uniqid('',true).".".$fileActualExt;
					$fileDestination='uploads/'.$fileName;
					move_uploaded_file($fileTmpName,$fileDestination);
					// $sql = "INSERT into fileup(title,image) VALUES('$title','$fileName')";
					// 	 if(mysqli_query($conn,$sql)){
					// 	 	// echo " success upload";
					// 	 }
					// 	 else{
					// 	 echo "Error";
					// 	 }
					
				}else{
					$upload_error= "your file is to big";
				}

			}else{
				$upload_error= "There was an error in uploading your file";
			}
		}else{
			$upload_error= "File type should be jpg/jpeg/png/pdf";
		}

	}
	

		
	


   if($name!="" || $age!="" || $dob!="" || $add!="" || $contact!="" || $email="" || $app!="" || $tm!="")
   {
   		$sql="SELECT * FROM `new-app` WHERE `app-date`='$app' AND `time`='$tm' ";
   		$result=$conn->query($sql);
   		if ($result)
   		{
   			if ($result->num_rows > 0) 
			{
            	$upload_error= 'Please get another date or time';
        	}
        	else
        	{
        		if($upload_error=="")
        		{

        		
	        		$sql = "INSERT INTO `new-app` (`name`,`age`, `dob`, `blood_group`, `address`, `mobile`, `email`, `app-date`, `time`, `report`) 
					VALUES('".$name."','".$age."','".$dob."','".$b_group."','".$add."','".$contact."', '".$email."', '".$app."', '".$tm."','".$fileName."')";

					if ($conn->query($sql) == TRUE) 
					{
				  		echo '<script>alert(" Successfull! we will convey mail shortly")</script>';
				  		
				  		$name=$name;
				  		$email=$email;
				  		$subject="Appointment Confirmation!";
				  		$message="Your appointment is Successfull on";
				  		$date=$app;
				  		$time=$tm;
				  		$from="dedicated.and.designated@gmail.com";
				  		require 'PHPMailerAutoload.php';
				  		try{

  
    
						$mail = new PHPMailer;
						$mail->isSMTP();
						// $mail->SMTPDebug = 4;                               // Enable verbose debug output

						$mail->isSMTP();                                      // Set mailer to use SMTP
						$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
						$mail->SMTPAuth = true;                               // Enable SMTP authentication
						$mail->Username = 'dedicated.and.designated@gmail.com';                 // SMTP username
						$mail->Password = 'njxgjwgspvtxbzwa';                           // SMTP password
						$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
						$mail->Port = 587;                                    // TCP port to connect to

						$mail->setFrom($from);
						$mail->addAddress($email);     // Add a recipient
						// $mail->addAddress('ellen@example.com');               // Name is optional
						$mail->addReplyTo($email);
						
						// $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
						// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
						$mail->isHTML(true);                                  // Set email format to HTML

						$mail->Subject = $subject;
						$mail->Body    = 'NAME:'.$name.','.$message.'<br>Date:'.$date.'<br>Time:'.$time.'<br>Please be before time.:';
						$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
						if(!$mail->send()) {
						    echo 'Message could not be sent.';
						    $output = '<div class="alert alert-success">
					                  <h5>Thankyou! for contacting us, We\'ll get back to you soon!</h5>
					                </div>';
					                 echo "<script>alert('Message could not sent')</script>";
					        
						     echo 'Mailer Error: ' . $mail->ErrorInfo;

						} else {
						    $output="Successfull! we will convey mail shortly";
						    echo "<script>alert('Your confirmation sent on your mail')</script>";
						    header("Location:index.html");
						}

					   }
					   catch (Exception $e) {
					      $output = '<div class="alert alert-danger">
					                  <h5>' . $e->getMessage() . '</h5>
					                </div>';
					    }

				         
				    }

				}
				else
				{
					$upload_error=$upload_error;
				}


        	}

        	
   		}
   }
   else
   {

   	echo "Booking not saved";
   }

	
	
}

function test_input($data){
	$data=trim($data);
	$data=stripslashes($data);
	$data=htmlspecialchars($data);
	return $data;
}




?>
