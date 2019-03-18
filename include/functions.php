<?php
// Reload the page
function reload_page()
{
    print("<script>location.reload()</script>");
}

// Get the databaase
function get_db()
{
    $db = new PDO('sqlite:db/credentialmanager.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

// Check if user exists
function check_uniqueness($db, $username)
{
    $check = $db->prepare("SELECT * FROM users WHERE username=:name");
    $check->bindParam(':name', $username);
    $result = $check->execute();

    if ($result)
    {
     	while ($row = $check->fetch())
        {
            return False;
        }
	return True;
    }

    return False;
}


// Add a user to the database
function add_user($db, $username, $password)
{
    $insert = $db->prepare("INSERT INTO users VALUES(:name, :pass)");
    $insert->bindParam(':name', $username);
    $insert->bindParam(':pass', $password);
    $insert->execute();

    print("<p>Created login for '{$username}'.</p>");
}

// Try and sign a user up
function signup($username, $password)
{
    try
    {
     	$db = get_db();
        if (check_uniqueness($db, $username))
        { add_user($db, $username, $password); }
        else
	{ print("<p>Username '{$username}' is already registered.</p>"); }
    }
    catch(PDOException $e)
    {
     	print($e->getMessage());
    }
}


// Create a session token
function create_token($username)
{
    setcookie("username", $username);
    setcookie("session", md5($username));
}

// Check a session token
function check_token($username, $session)
{
    return (md5($username) == $session);
}

// Destroy the session token
function logout()
{
    setcookie("username", "", time()-3600);
    setcookie("session", "", time()-3600);
}

function login($username, $password)
{
    logout();

    try
    {
     	$db = get_db();

        $check = $db->prepare("SELECT * FROM users WHERE username='".$username."' AND password='".$password."'");
        $result = $check->execute();

        while ($row = $check->fetch())
        {
            create_token($row['username']);
            return True;
        }

	return False;
    }
    catch(PDOException $e)
    {
     	print($e->getMessage());
    }
}

// Check if there is a user signed in
function check_signed_in()
{
    if (isset($_COOKIE['username'], $_COOKIE['session']))
    {
        if (check_token($_COOKIE['username'], $_COOKIE['session']))
     	{   return True; }
        else
        {
            logout();
        }
    }
    return False;
}



// Add entry to database
function add_content($username, $title, $content, $type, $ctr)
{
    try
    {
        $db = get_db();
        $insert = $db->prepare("INSERT INTO entries (type, title, content, username, ctr) VALUES(:type, :title, :cont, :name, :ctr)");
        $insert->bindParam(':type', $type);
        $insert->bindParam(':title', $title);
        $insert->bindParam(':name', $username);
        $insert->bindParam(':cont', $content);
        $insert->bindParam(':ctr', $ctr);
        $insert->execute();
        print("<p>Inserted Content.</p>");

    }
    catch(PDOException $e)
    {
        print($e->getMessage());
        print("error...");
    }
}

// Remove entry from database
function del_content($username, $contentid)
{
    try
    {
        $db = get_db();
        $stmnt = $db->prepare("DELETE FROM entries WHERE id=:id AND username=:name");
        $stmnt->bindParam(':name', $username);
        $stmnt->bindParam(':id', $contentid);
        $stmnt->execute();
        print("<p>Deleted Content.</p>");

    }
    catch(PDOException $e)
    {
        print($e->getMessage());
        print("error...");
    }
}




//Get all content
function get_content($username)
{
    try
    {
        $db = get_db();
        $stmnt = $db->query("SELECT * FROM entries WHERE username=?");
        $stmnt->execute([$username]);
        $results = $stmnt->fetchall();
        return $results;
    }
    catch(PDOException $e)
    {
        print($e->getMessage());
    }

}

//Get one entry
function get_entry($username, $id)
{
    try
    {
        $db = get_db();
        $stmnt = $db->query("SELECT * FROM entries WHERE username=? AND id=?");
        $stmnt->execute([$username, $id]);
	$results = $stmnt->fetch();
	return $results;
    }
    catch(PDOException $e)
    {
        print($e->getMessage());
    }

}


// Filter function from, adapted for current php version
// https://corpocrat.com/2009/07/28/filtering-escaping-post-data-from-inje$
function filter($data) {
    $data = trim(htmlentities(strip_tags($data)));
    if (get_magic_quotes_gpc())
        $data = stripslashes($data);
    $search = array("\\",  "\x00", "\n",  "\r",  "'",  
                    '"',
                    "\x1a");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", 
                     '\"',
                     "\\Z");
    $data = str_replace($search, $replace, $data);
    //$data = mysqli_real_escape_string($data);
    return $data;
}



?>
