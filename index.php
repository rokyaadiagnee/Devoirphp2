<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="sidebar">
    <h1> Tableau de bord</h1>
        <ul>
            <li><a href="#Domicile">Domicile</a></li>
            <li><a href="ajouter_client.php">ajouter_client</a></li>
            <li><a href="gestion_comptes.php">gestion_comptes</a></li>
            <li><a href="releve_compte.php">releve_compte</a></li>
            
        </ul>
    </div>
    
</body>
</html>
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
}

.sidebar {
    width: 250px;
    background: #8418ae;
    color: #ecf0f1;
    height: 100vh;
    position: fixed;
    padding-top: 20px;
}

.sidebar h2 {
    text-align: center;
    margin-bottom: 30px;
}

.sidebar ul {
    list-style-type: none;
    padding: 0;
}

.sidebar ul li {
    padding: 15px;
    text-align: center;
}

.sidebar ul li a {
    color: #ecf0f1;
    text-decoration: none;
    display: block;
}

.sidebar ul li a:hover {
    background: #8418ae;
    transition: 0.3s;
}

.main-content {
    margin-left: 250px;
    padding: 20px;
    flex-grow: 1;
    background: #ecf0f1;
    height: 100vh;
    overflow-y: auto;
}

header {
    background: #3498db;
    color: #fff;
    padding: 20px;
    text-align: center;
}

section {
    margin: 20px 0;
}

section h2 {
    color: #2c3e50;
}

</style>