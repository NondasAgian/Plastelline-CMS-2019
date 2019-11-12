<?php 

function edit_user()
{
    Append_Title("Edit User");

     
    $url= new URL(); 
    $url_array= $url->GetURLArray(); 
    $url=null; 

    if (array_key_exists('id',$url_array)){
        Append_Content("<h2> Edit User </h2>"); 
         
        //return true; 
        if(array_key_exists('username', $_POST) && array_key_exists('password', $_POST) && array_key_exists('role', $_POST) && array_key_exists('submit', $_POST))
        {
            $query = new Query(new DB());
            $query->SetTableName('user');
            $query->Update(['username'=>$_POST['username'], 'password'=>Security::Password($_POST['password']), 'role'=>$_POST['role']]);
            $query->Where(['uid','=', $url_array['id']]);
            $query->Run();
            //$result = $query->getSQL();
            $query = null;
            
            //var_dump($result);
            $html="<h4 class='success-bar'>You succesfully edited User: ".$_POST['username'];
            Append_Content($html);
            Append_Content("<br>");
            Append_Content("<br></b><a class='link-button col-60' href=".CMS_BASE_URL."?q=edit/user> Go Back </a>");
        }
        else
        {
            Append_Content(edit_user_form());
        }

    }
    else
    {
        Append_Content("<h2> Edit User | User Selection <h2>");
        Append_Content(show_users_table()); 
    }
    
}

function show_users_table()
{
    $users_array= Security::Get_Users_Array(); 
    $roles_array= Security::Get_Roles_Array(true); 

    $table= "<table class='users-table' border=1>"; 
    $table.= "<thead> <tr> <th class='th-section-header' colspan=3> Users </th></tr>";
    $table.= "<tr> <th> Username </th> <th> Role </th><th> Action </th></tr> </thead>"; 
    foreach ($users_array as $user){
        $table.= "<tr>"; 
        $table.= "<td> {$user['username']} </td>"; 
        foreach ($roles_array as $role){
            if ($user['role'] == $role['roleid'])
            {
                $role_name = $role['role_display_name']; 
                break; 
            }
        }
        $table.= "<td>{$role_name}</td>"; 
        $link_button= "<a class='link-button full' href=".CMS_BASE_URL."?q=edit/user/".$user['uid']."> Edit </a>"; 
        $table.= "<td>{$link_button}</td>"; 
        $table.= "</tr>"; 
    }
    $table.= "</table>"; 

    return $table; 
}

function edit_user_form()
{
    $url= new URL(); 
    $url_array= $url->GetURLArray();
    if (array_key_exists('id',$url_array)){
        $uid= $url_array['id']; 
    }

    $user_array= Security::Get_User_From_id($uid); 

    /**
     * Get the roles of the user first to fill in the webform
     */
    $query= new Query(new DB()); 
    $query->SetTableName('roles');
    $query->Select(['role_display_name']);
    $query->Where(['roleid','=',$user_array['role']]); 
    $query->Run();
    $result= $query->GetReturnedRows(); 
    $role_name= $result[0]['role_display_name']; 
    $query=null; 

    $visible[]=$role_name;
    $values[]= $user_array['role']; 

    /**
     * Get the remaining roles of the system
     * Do not get the Anonymous user
     */
    $query= new Query(new DB());
    $query->SetTableName('roles');
    $query->Select(['roleid','role_display_name']);
    $query->Where(['roleid','>',1]);
    $query->AndClause(['roleid','!=',$user_array['role']]);
    $query->Run();
    $roles_array= $query->GetReturnedRows(); 

    foreach ($roles_array as $role){
        $visible[]= $role['role_display_name'];
        $values[]= $role['roleid'];
    }

    $form= new Webform('edit-user-form'); 
    $form->webform_textbox("Username",'username',$user_array['username'],true); 
    $form->webform_password_textbox('Set Password','password','(Unchanged if empty)',false); 
    $form->webform_option_menu("User Role",'role',$visible,$values);
    $form->webform_submit_button('Apply'); 
    //$result = array();
    $result = $_POST;

     

    //var_dump($result);
    return $form->webform_getForm();
}

?>