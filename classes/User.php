<?php
  $filepath = realpath(dirname(__FILE__));
  include_once ($filepath.'/../lib/Session.php');
  include_once ($filepath.'/../lib/Database.php');
  include_once ($filepath.'/../helpers/Format.php');

class User{
    private $db;
    private $fm;
    public function __construct(){
        $this->db = new Database();
        $this->fm = new Format();


    }
    public function userRegistration($name, $username, $password, $email ){
     
      $name = $this->fm->validation($name);
      $username = $this->fm->validation($username);
      $password = $this->fm->validation($password);
      $email = $this->fm->validation($email);
      

      $name = mysqli_real_escape_string($this->db->link, $name);
      $username = mysqli_real_escape_string($this->db->link, $username);
      $email = mysqli_real_escape_string($this->db->link, $email);
      if($name == "" || $username == "" ||  $password == "" || $email == "") {
        echo "<span class='error'>Fields Must Not be Empty !</span>";
        exit();
      }else if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        echo "<span class='error'>Invalid Email Address !</span>";
        exit();
      }else{
        $chkquery = "SELECT * FROM tbl_user WHERE email = '$email'";
        $chkresult = $this->db->select($chkquery);
        if($chkresult != false) {
          echo "<span class='error'>Email Address Already Exist !</span>";
          exit();
        }else{
          $password = mysqli_real_escape_string($this->db->link, md5($password));
          $query = "INSERT INTO tbl_user(name, user_name, password, email) VALUES
          ('$name','$username', '$password', '$email')";
          $inserted_row = $this->db->insert($query);
          if($inserted_row){
            echo "<span class='success'>Registration Successfully !</span>";
          exit();
          }else{
            echo "<span class='error'>Error.. Not Registered !</span>";
            exit();
          }
        }
      }
      
      

    }
    public function userLogin($email, $password){
      $email = $this->fm->validation($email);
      $password = $this->fm->validation($password);
      $email = mysqli_real_escape_string($this->db->link, $email);
     
      if($password == "" || $email == "") {
        echo "empty";
        exit();
      } else{
        $password = mysqli_real_escape_string($this->db->link, md5($password));
          $query = "SELECT * FROM tbl_user WHERE email='$email' AND password='$password'";
          $result = $this->db->select($query);
          if($result != false) {
            $value = $result->fetch_assoc();
            if($value['status'] == '1'){
              echo "disable";
              exit();
            }else{
              Session::init();
              Session::set("login", true);
              Session::set("userId", $value['userId']);
              Session::set("user_name", $value['user_name']);
              Session::set("name", $value['name']);
            }
            }else{
              echo "error";
              exit();
          }

      }
      
    }
    public function getUserData($userid){
      $query = "SELECT * FROM tbl_user WHERE userId= '$userid'";
      $result = $this->db->select($query);
      return $result;
    }
    public function getAllUser(){
      $query = "SELECT * FROM tbl_user ORDER BY userId DESC";
      $result = $this->db->select($query);
      return $result;
    }
    public function updateUserData($userid, $data){
      $name = $this->fm->validation($data['name']);
      $username = $this->fm->validation($data['user_name']);
      $email = $this->fm->validation($data['email']);
      

      $name = mysqli_real_escape_string($this->db->link, $name);
      $username = mysqli_real_escape_string($this->db->link, $username);
      $email = mysqli_real_escape_string($this->db->link, $email);
      $query = "UPDATE tbl_user
                SET
                name = '$name',
                user_name = '$username',
                email = '$email'
                WHERE userId = '$userid'";
                $updated_row = $this->db->update($query);
                if ($updated_row){
                $msg = "<span class='success'>User Data Updated!</span>";
                return $msg;
                }else{
                  $msg = "<span class='error'>User Data Not Updated !</span>";
                return $msg;
                }
    }
    public function disableUser($userid){
      $query = "UPDATE tbl_user
                SET
                status = '1'
                WHERE userId = '$userid'";
                $updated_row = $this->db->update($query);
                if ($updated_row){
                $msg = "<span class='success'>User Disabled !</span>";
                return $msg;
                }else{
                  $msg = "<span class='error'>User Not Disabled !</span>";
                return $msg;
                }
    }
    public function enableUser($userid){

      $query = "UPDATE tbl_user
                SET
                status = '0'
                WHERE userId = '$userid'";
                $updated_row = $this->db->update($query);
                if ($updated_row){
                $msg = "<span class='success'>User Enable !</span>";
                return $msg;
                }else{
                  $msg = "<span class='error'>User Not Enable!</span>";
                return $msg;
                }

    }
    public function deleteUser($userid){
      $query = "DELETE FROM tbl_user WHERE userId = '$userid'";
      $deldata = $this->db->delete($query);
      if($deldata){
        $msg = "<span class='success'>User Removed !</span>";
                return $msg;
                }else{
                  $msg = "<span class='error'>Error...User Not Remove!</span>";
                return $msg;
      }

    }
}
?>