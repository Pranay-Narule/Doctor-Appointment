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
				  		// $output =" Successfull! we will convey mail shortly";
				  		
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
						     // echo 'Mailer Error: ' . $mail->ErrorInfo;
						} else {
						    $output="Successfull! we will convey mail shortly";
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
   	$upload_error="Data Not Saved";
   }

	
	
}

function test_input($data){
	$data=trim($data);
	$data=stripslashes($data);
	$data=htmlspecialchars($data);
	return $data;
}




?>

<!DOCTYPE html>
<html>
<head>
	<title>Doctor Appointment</title>
	<link rel="stylesheet" type="text/css" href="/doc-app/style.css">
		<script type="text/javascript" src="js/jquery-3.1.0.js"></script>
	<script>
		$(document).ready(function(){
			
				var dtToday=new Date();

				var month=dtToday.getMonth()+1;
				var day=dtToday.getDate()+1;
				var year=dtToday.getFullYear();
				if(month<10)
					month='0'+month.toString();
				if(day<10)
					day='0'+day.toString();
				var maxDate=year+'-'+month+'-'+day;
				$('#dateController').attr('min',maxDate);
			
		})
	</script>

</head>

</head>
<body>

	
	<main>
		<div class="app_form">
			<div class="block">
				<div class="heading-block">
					<div class="heading">
						Book Appointment
					</div>
				</div>
				<form  role="form" action="<?=$_SERVER['PHP_SELF'];?>" enctype="multipart/form-data" method="POST">
					<div class="body">
							<span class="error"><?=$upload_error ?></span>
							<span class="success"><?=$output ?></span>
							<div class="input-values">
								<div class="label">Name Of Patient:</div>
							    <input type="text" name="name" value="<?=$name ?>" placeholder="Enter your full Name" >
							    <br>
							    <span class="error"><?=$full_name_error ?></span>
							</div>
							<div class="input-values">
								<div class="label">Age Of Patient:</div>
							    <input type="text" name="age" value="<?=$age ?>" placeholder="Enter your Age" >
							    <br>
							    <span class="error"><?=$age_error ?></span>
							</div>
							<div class="input-values">
								<div class="label">Date of Birth:</div>
							    <input type="date" name="dob" value="<?=$dob ?>">
							    <br>
							    <span class="error"><?=$dob_error ?></span>
							</div>
							<div class="input-values">
								<div class="label">Blood group:</div>
							    <select name="b-group" id=b-group>
							    	<option value="">Select</option>
							    	<option value="A+">A+</option>
							    	<option value="A-">A-</option>
							    	<option value="O+">O+</option>
							    	<option value="O-">O-</option>
							    	<option value="B+">B+</option>
							    	<option value="B-">B-</option>
							    	<option value="AB+">AB+</option>
							    	<option value="AB-">AB-</option>
							    </select>
							    		
							</div>
							<div class="input-values">
								<div class="label">Address:</div>
							    <textarea rows="1" placeholder="Your Address.." name="add"  ><?=$add ?></textarea>
							    <br>
							    <span class="error"><?=$add_error ?></span>
							</div>
							<div class="input-values">
								<div class="label">Mobile No.:</div>
							   	<input type="tel" name="contact" placeholder="Enter your contact number" value="<?=$contact?>" >
							   	<br>
							   	<span class="error"><?=$contact_error ?></span>
							</div>
							<div class="input-values">
								<div class="label">Email Id.:</div>
							   	<input type="email" name="email" placeholder="Enter your Email Id"  value="<?=$email?>" >
							   	<br>
							   	<span class="error"><?=$email_error ?></span>
							</div>
							<div class="input-values">
								<div class="label">Appointment:</div>
							    <input type="date" name="date-app" id="dateController" value="<?=$app ?>"  >
							    <br>
							    <span class="error"><?=$app_error ?></span>
							</div>
							<div class="input-values">
								<div class="label">Time:</div>
								<div class="radio-block">
									<input type="radio" id="tm10" name="tm" value="10AM">
									10:00AM-11:00AM
									<input type="radio" id="tm11" name="tm" value="11AM">
									11:00AM-12:00PM <br>
									<input type="radio" id="tm01" name="tm" value="01PM">
									01:00PM-:02:00PM
									<input type="radio" id="tm02" name="tm" value="02PM">
									02:00PM-:03:00PM<br>
									<input type="radio" id="tm03" name="tm" value="03PM">
									03:00PM-:04:00PM
									<input type="radio" id="tm05" name="tm" value="05PM">
									05:00PM-:06:00PM
								</div>
								<br>
								<span class="error"><?=$tm_error ?></span>
							    
							</div>

							<div class="input-values">
								<div class="label">Patient Report.:</div>
							   	<input type="file" name="file" >
							   	<br>
							   	<span class="error"><?=$file_error ?></span>
							</div>
						
					</div>
					<div class="button">
						<div class="submit-button">
							<input type="submit" name="submit">
						</div>
						
					</div>
				 </form>

				
			</div>
		</div>
	</main>

</body>
</html>
