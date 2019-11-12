<?php

function delete_user()
{
    Append_Title("Delete User"); 
    $url= new URL(); 
    $url_array= $url->GetURLArray(); 
    $url=null; 

    if (array_key_exists('id',$url_array)){
        Append_Content("<h2> Delete User </h2>"); 
        Append_Content(delete_user_form()); 
        return true; 
    }
    Append_Content("<h2> Delete User | User Selection <h2>");
    Append_Content(show_users_table()); 
}

function show_users_table()
{
    $users_array= Security::Get_Users_Array(); 
    $roles_array= Security::Get_Roles_Array(true); 

    $table= "<table class='users-table' border=1>"; 
    $table.= "<thead> <tr> <th class='th-section-header' colspan=3> Users </th></tr>";
    $table.= "<tr> <th> Username </th> <th> Role </th><th> Action </th></tr> </thead>"; 
    foreach ($users_array as $user)
    {
        $table.= "<tr>"; 
        $table.= "<td> {$user['username']} </td>"; 
        foreach ($roles_array as $role)
        {
            if ($user['role'] == $role['roleid'])
            {
                $role_name = $role['role_display_name']; 
                break; 
            }
        }
        $table.= "<td>{$role_name}</td>"; 
        $link_button= "<a class='link_delete-button full' href=".CMS_BASE_URL."?q=delete/user/".$user['uid']."> Delete </a>"; 
        $table.= "<td>{$link_button}</td>"; 
        $table.= "</tr>"; 
    }
    $table.= "</table>"; 

    return $table; 
}
function delete_user_form()
{
    $url= new URL(); 
    $url_array= $url->GetURLArray();
    $url=null;

    if(array_key_exists('id',$url_array) && $url_array['id']);
    {
        $query = new Query(new DB());
        $query->SetTableName('user');
        $query->Select(['username']);
        $query->Where(['uid','=', $url_array['id']]);
        $query->Run();
        $result = $query->GetReturnedRows();
        $query = null;

        if($result[0][0] == $_SESSION['username'])
        {
            $html1="<h4 class = 'error-bar'>You can't delete the active account.";
            Append_Content($html1);
            Append_Content("<br>");
            Append_Content("<br></b><a class='link-button col-60' href=".CMS_BASE_URL."?q=remove/user> Go Back </a>");
        }
        else
        {   $query = new Query(new DB());
            $query->SetTableName('user');
            $query->Delete(['uid']);
            $query->Where(['uid','=', $url_array['id']]);
            $query->Run();
            $query = null;
            $html="<h4 class = 'success-bar'>You have deleted the user: ".$result[0][0];

            Append_Content($html);
            Append_Content("<br>");
            Append_Content("<br></b><a class='link-button col-60' href=".CMS_BASE_URL."?q=delete/user> Go Back </a>");
            
        }
    }
}
?>